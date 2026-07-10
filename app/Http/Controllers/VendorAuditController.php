<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VendorApplication;
use App\Models\VendorAudit;
use App\Models\VendorAuditFinding;
use App\Models\VendorAuditQuestionTemplate;
use App\Models\VendorQualification;
use App\Services\VendorApplicationNotificationService;
use App\Services\VendorApplicationWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VendorAuditController extends Controller
{
    private const QA_ROLES = ['Super Admin', 'Admin IT', 'Quality Assurance', 'Apoteker', 'Specialist'];

    private const STATUS_OPTIONS = [
        'all'        => 'Semua Status',
        'scheduled'  => 'Baru Terjadwal',
        'in_progress' => 'Berjalan',
        'need_revision' => 'Perlu Revisi',
        'completed'  => 'Selesai',
        'rejected'   => 'Ditolak',
    ];

    /* ──────────────────────────────────────────────────────────────────
     |  INDEX – daftar semua audit (QA)
     * ────────────────────────────────────────────────────────────────*/
    public function index(Request $request): View
    {
        $this->authorizeQaAccess();

        $status = $request->input('status', 'all');
        $type   = $request->input('type', 'all');
        $search = trim((string) $request->input('q', ''));

        $query = VendorAudit::query()
            ->with(['application.general', 'application.user', 'qualification', 'findings'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($type !== 'all') {
            $query->where('audit_type', $type);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('application', function ($app) use ($search) {
                    $app->where('application_number', 'like', "%{$search}%")
                        ->orWhereHas('general', function ($g) use ($search) {
                            $g->where('nama_perusahaan', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            });
        }

        $audits = $query->paginate(10)->withQueryString();

        $countByType = [
            'on_desk' => (clone $query)->where('audit_type', 'on_desk')->count(),
            'on_site' => (clone $query)->where('audit_type', 'on_site')->count(),
        ];

        $countByStatus = [
            'scheduled'  => VendorAudit::where('status', VendorAudit::STATUS_SCHEDULED)->count(),
            'in_progress' => VendorAudit::whereIn('status', [
                VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS,
                VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED,
                VendorAudit::STATUS_SCHEDULE_PROPOSED,
                VendorAudit::STATUS_SCHEDULE_CONFIRMED,
                VendorAudit::STATUS_IN_PROGRESS,
                VendorAudit::STATUS_FINDINGS_RECORDED,
                VendorAudit::STATUS_CAPA_PROGRESS,
                VendorAudit::STATUS_CAPA_SUBMITTED,
                VendorAudit::STATUS_CAPA_REVISED,
            ])->count(),
            'need_revision' => VendorAudit::where('status', VendorAudit::STATUS_NEED_REVISION)->count(),
            'completed' => VendorAudit::where('status', VendorAudit::STATUS_COMPLETED)->count(),
        ];

        return view('admin.audit.index', [
            'audits'        => $audits,
            'status'        => $status,
            'type'          => $type,
            'search'        => $search,
            'statusOptions' => self::STATUS_OPTIONS,
            'countByType'   => $countByType,
            'countByStatus' => $countByStatus,
        ]);
    }

    /* ──────────────────────────────────────────────────────────────────
     |  CREATE / SHOW – QA setup audit (jadwal, tim, dsb)
     * ────────────────────────────────────────────────────────────────*/
    public function create(int $applicationId): View
    {
        $this->authorizeQaAccess();

        $application = VendorApplication::with([
            'user',
            'general',
            'categories',
            'qualification',
            'audits.findings.capas',
        ])
            ->findOrFail($applicationId);

        abort_unless(
            in_array($application->status, [
                VendorApplication::STATUS_AUDIT_REQUIRED,
                VendorApplication::STATUS_RISK_ASSESSED,
            ], true),
            403,
            'Audit hanya dapat dimulai setelah status audit_required.'
        );

        $activeAudit = $application->audits()
            ->whereNotIn('status', [VendorAudit::STATUS_COMPLETED, VendorAudit::STATUS_REJECTED])
            ->latest()
            ->first();

        $templates = $activeAudit
            ? collect()
            : VendorAuditQuestionTemplate::forCategories(
                $application->categories->pluck('category_id')->all()
            );

        return view('admin.audit.create', [
            'application'  => $application,
            'activeAudit'  => $activeAudit,
            'templates'    => $templates,
            'qaUsers'      => $this->qaUsers(),
        ]);
    }

    public function show(int $auditId): View
    {
        $this->authorizeQaAccess();

        $audit = VendorAudit::with([
            'application.user',
            'application.general',
            'application.categories',
            'application.qualification',
            'findings.capas',
            'qaLead',
        ])
            ->findOrFail($auditId);

        $progress = $audit->progressPercent();

        return view('admin.audit.show', [
            'audit'        => $audit,
            'progress'     => $progress,
            'application'  => $audit->application,
            'findings'     => $audit->findings,
            'capaProgress' => $this->capaProgress($audit),
        ]);
    }

    /* ──────────────────────────────────────────────────────────────────
     |  STORE – QA membuat audit row dari hasil risk assessment
     * ────────────────────────────────────────────────────────────────*/
    public function store(Request $request, int $applicationId, VendorApplicationWorkflowService $workflow)
    {
        $this->authorizeQaAccess();

        $application = VendorApplication::with('qualification')->findOrFail($applicationId);

        abort_unless(
            in_array($application->status, [
                VendorApplication::STATUS_AUDIT_REQUIRED,
                VendorApplication::STATUS_RISK_ASSESSED,
            ], true),
            403
        );

        $qualification = $application->qualification;
        abort_unless($qualification, 404, 'Risk assessment belum dilakukan.');

        $auditType = $qualification->audit_type;
        abort_unless($auditType, 422, 'Tipe audit tidak ditemukan dari risk assessment.');

        // Validasi berbeda untuk on_desk vs on_site
        if ($auditType === VendorAudit::TYPE_ON_SITE) {
            $data = $request->validate([
                'proposed_schedules'   => 'required|array|min:1',
                'proposed_schedules.*' => 'required|date|after:today',
                'audit_location'       => 'required|string|max:255',
                'audit_agenda'         => 'required|string|max:500',
                'auditor_team'         => 'required|array|min:1',
                'auditor_team.*.name'  => 'required|string|max:120',
                'auditor_team.*.role'  => 'required|string|max:80',
                'qa_lead_id'           => 'nullable|exists:users,id',
                'summary'              => 'nullable|string|max:1000',
            ]);
        } else {
            $data = $request->validate([
                'qa_lead_id' => 'nullable|exists:users,id',
                'summary'    => 'nullable|string|max:1000',
            ]);
        }

        $audit = DB::transaction(function () use ($application, $qualification, $auditType, $data, $workflow) {
            $payload = [
                'vendor_application_id'   => $application->id,
                'vendor_qualification_id' => $qualification->id,
                'audit_type'              => $auditType,
                'status'                  => $auditType === VendorAudit::TYPE_ON_DESK
                    ? VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS
                    : VendorAudit::STATUS_SCHEDULE_PROPOSED,
                'qa_lead_id'              => $data['qa_lead_id'] ?? Auth::id(),
                'created_by'              => Auth::id(),
            ];

            if ($auditType === VendorAudit::TYPE_ON_SITE) {
                $payload = array_merge($payload, [
                    'proposed_schedules' => array_map(
                        fn($dt) => Carbon::parse($dt)->toDateTimeString(),
                        $data['proposed_schedules']
                    ),
                    'audit_location'     => $data['audit_location'],
                    'audit_agenda'       => $data['audit_agenda'],
                    'auditor_team'       => $data['auditor_team'],
                    'summary'            => $data['summary'] ?? null,
                ]);
            } else {
                $payload['summary'] = $data['summary'] ?? null;
            }

            $audit = VendorAudit::create($payload);

            $workflow->record(
                $application,
                $auditType === VendorAudit::TYPE_ON_DESK
                    ? 'audit_on_desk_started'
                    : 'audit_on_site_proposed',
                Auth::user(),
                ['audit_id' => $audit->id, 'audit_type' => $auditType]
            );

            return $audit;
        });

        return redirect()
            ->route('qa.audit.show', $audit->id)
            ->with('success', 'Audit berhasil dibuat. Status: ' . $audit->status . '.');
    }

    /* ──────────────────────────────────────────────────────────────────
     |  VERIFY QUESTIONNAIRE (On Desk)
     * ────────────────────────────────────────────────────────────────*/
    public function verifyQuestionnaire(Request $request, int $auditId, VendorApplicationWorkflowService $workflow)
    {
        $this->authorizeQaAccess();

        $audit = VendorAudit::with('application')->findOrFail($auditId);
        abort_unless($audit->audit_type === VendorAudit::TYPE_ON_DESK, 404);
        abort_unless(
            in_array($audit->status, [
                VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED,
                VendorAudit::STATUS_CAPA_REVISED,
            ], true),
            422,
            'Questionnaire belum di-submit vendor.'
        );

        $data = $request->validate([
            'verdict'          => 'required|in:approved,rejected,need_revision',
            'revision_notes'   => 'nullable|array',
            'revision_notes.*' => 'nullable|string|max:1000',
            'summary'          => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($audit, $data, $workflow) {
            $verdictMap = [
                'approved'       => VendorAudit::STATUS_COMPLETED,
                'rejected'       => VendorAudit::STATUS_REJECTED,
                'need_revision'  => VendorAudit::STATUS_NEED_REVISION,
            ];
            $status = $verdictMap[$data['verdict']] ?? VendorAudit::STATUS_NEED_REVISION;

            $audit->update([
                'status'                     => $status,
                'questionnaire_revision_notes' => $data['revision_notes'] ?? null,
                'summary'                    => $data['summary'] ?? $audit->summary,
                'completed_at'               => in_array($status, [VendorAudit::STATUS_COMPLETED, VendorAudit::STATUS_REJECTED])
                    ? now()
                    : $audit->completed_at,
            ]);

            $application = $audit->application;

            if ($status === VendorAudit::STATUS_COMPLETED) {
                $workflow->transition(
                    $application,
                    VendorApplication::STATUS_APPROVED,
                    'audit_on_desk_approved',
                    [
                        'approved_by'  => Auth::id(),
                        'approved_at'  => now(),
                        'valid_until'  => now()->addYears(5)->toDateString(),
                    ],
                    Auth::user(),
                    ['audit_id' => $audit->id]
                );
            } elseif ($status === VendorAudit::STATUS_REJECTED) {
                $workflow->transition(
                    $application,
                    VendorApplication::STATUS_REJECTED,
                    'audit_on_desk_rejected',
                    [],
                    Auth::user(),
                    ['audit_id' => $audit->id]
                );
            } else {
                $workflow->record($application, 'audit_on_desk_need_revision', Auth::user(), [
                    'audit_id' => $audit->id,
                    'notes'    => $data['revision_notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('qa.audit.show', $audit->id)
            ->with('success', 'Verifikasi questionnaire disimpan.');
    }

    /* ──────────────────────────────────────────────────────────────────
     |  CONFIRM SCHEDULE (vendor) – dipanggil QA setelah vendor pilih tanggal
     * ────────────────────────────────────────────────────────────────*/
    public function confirmSchedule(Request $request, int $auditId, VendorApplicationWorkflowService $workflow)
    {
        $this->authorizeQaAccess();

        $audit = VendorAudit::findOrFail($auditId);
        abort_unless($audit->audit_type === VendorAudit::TYPE_ON_SITE, 404);
        abort_unless($audit->status === VendorAudit::STATUS_SCHEDULE_PROPOSED, 422);

        $data = $request->validate([
            'confirmed_schedule_at' => 'required|date',
        ]);

        $audit->update([
            'status'                 => VendorAudit::STATUS_SCHEDULE_CONFIRMED,
            'confirmed_schedule_at'  => Carbon::parse($data['confirmed_schedule_at']),
        ]);

        $workflow->record($audit->application, 'audit_on_site_schedule_confirmed', Auth::user(), [
            'audit_id' => $audit->id,
            'schedule' => $data['confirmed_schedule_at'],
        ]);

        return redirect()
            ->route('qa.audit.show', $audit->id)
            ->with('success', 'Jadwal audit on-site dikonfirmasi.');
    }

    /* ──────────────────────────────────────────────────────────────────
     |  GENERATE SURAT PEMBERITAHUAN AUDIT (PDF)
     * ────────────────────────────────────────────────────────────────*/
    public function generateLetter(int $auditId, VendorApplicationWorkflowService $workflow)
    {
        $this->authorizeQaAccess();

        $audit = VendorAudit::with([
            'application.general',
            'application.user',
            'application.categories',
            'qaLead',
        ])
            ->findOrFail($auditId);

        abort_unless(
            $audit->audit_type === VendorAudit::TYPE_ON_SITE
                && in_array($audit->status, [
                    VendorAudit::STATUS_SCHEDULE_CONFIRMED,
                    VendorAudit::STATUS_IN_PROGRESS,
                ], true),
            422,
            'Surat hanya bisa dibuat setelah vendor konfirmasi jadwal.'
        );

        $html = view('admin.audit.letter', [
            'audit' => $audit,
            'application' => $audit->application,
        ])->render();

        $filename = 'audit-letter-' . $audit->id . '-' . Str::slug((string) $audit->application->application_number) . '.html';
        $path = 'audit-letters/' . $filename;

        Storage::disk('public')->put($path, $html);

        $audit->update([
            'audit_letter_path'    => $path,
            'audit_letter_sent_at' => now(),
        ]);

        $workflow->record($audit->application, 'audit_letter_generated', Auth::user(), [
            'audit_id' => $audit->id,
            'path'     => $path,
        ]);

        return redirect()
            ->route('qa.audit.show', $audit->id)
            ->with('success', 'Surat pemberitahuan audit berhasil di-generate.');
    }

    public function downloadLetter(int $auditId)
    {
        $this->authorizeQaAccess();

        $audit = VendorAudit::findOrFail($auditId);
        abort_unless($audit->audit_letter_path && Storage::disk('public')->exists($audit->audit_letter_path), 404);

        return Storage::disk('public')->download($audit->audit_letter_path);
    }

    /* ──────────────────────────────────────────────────────────────────
     |  RECORD FINDINGS (On Site)
     * ────────────────────────────────────────────────────────────────*/
    public function storeFindings(Request $request, int $auditId, VendorApplicationWorkflowService $workflow)
    {
        $this->authorizeQaAccess();

        $audit = VendorAudit::findOrFail($auditId);
        abort_unless($audit->audit_type === VendorAudit::TYPE_ON_SITE, 404);
        abort_unless(
            in_array($audit->status, [
                VendorAudit::STATUS_SCHEDULE_CONFIRMED,
                VendorAudit::STATUS_IN_PROGRESS,
                VendorAudit::STATUS_FINDINGS_RECORDED,
            ], true),
            422
        );

        $data = $request->validate([
            'findings'                 => 'required|array|min:1',
            'findings.*.category'      => 'required|string|max:80',
            'findings.*.description'   => 'required|string',
            'findings.*.evidence_reference' => 'nullable|string|max:500',
            'findings.*.capa_deadline' => 'required|date|after_or_equal:today',
        ]);

        DB::transaction(function () use ($audit, $data, $workflow) {
            foreach ($data['findings'] as $row) {
                VendorAuditFinding::create([
                    'vendor_audit_id'   => $audit->id,
                    'category'          => $row['category'],
                    'description'       => $row['description'],
                    'evidence_reference' => $row['evidence_reference'] ?? null,
                    'capa_deadline'     => $row['capa_deadline'],
                    'status'            => VendorAuditFinding::STATUS_OPEN,
                ]);
            }

            $audit->update([
                'status' => VendorAudit::STATUS_FINDINGS_RECORDED,
            ]);

            $workflow->transition(
                $audit->application,
                VendorApplication::STATUS_ON_HOLD,
                'audit_on_site_findings_recorded',
                [],
                Auth::user(),
                ['audit_id' => $audit->id, 'finding_count' => count($data['findings'])]
            );
        });

        return redirect()
            ->route('qa.audit.show', $audit->id)
            ->with('success', 'Temuan audit berhasil disimpan. Vendor akan menerima notifikasi untuk mengisi CAPA.');
    }

    /* ──────────────────────────────────────────────────────────────────
     |  VERIFY CAPA per item
     * ────────────────────────────────────────────────────────────────*/
    public function verifyCapa(Request $request, int $capaId, VendorApplicationWorkflowService $workflow)
    {
        $this->authorizeQaAccess();

        $capa = VendorAuditCapa::with('finding.audit')->findOrFail($capaId);
        $audit = $capa->finding->audit;

        $data = $request->validate([
            'verdict' => 'required|in:approved,rejected',
            'qa_note' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($capa, $data, $audit, $workflow) {
            $capa->update([
                'qa_verdict'  => $data['verdict'],
                'qa_note'     => $data['qa_note'] ?? null,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            $finding = $capa->finding;
            if ($data['verdict'] === 'approved') {
                $finding->update(['status' => VendorAuditFinding::STATUS_CLOSED]);
            } else {
                $finding->update(['status' => VendorAuditFinding::STATUS_OPEN]);
            }

            // Recalculate aggregate status
            $this->recalculateAuditStatus($audit, $workflow);
        });

        return redirect()
            ->route('qa.audit.show', $audit->id)
            ->with('success', 'Verifikasi CAPA disimpan.');
    }

    /* ──────────────────────────────────────────────────────────────────
     |  APPROVE / REJECT audit (final decision)
     * ────────────────────────────────────────────────────────────────*/
    public function finalize(Request $request, int $auditId, VendorApplicationWorkflowService $workflow)
    {
        $this->authorizeQaAccess();

        $audit = VendorAudit::with('application')->findOrFail($auditId);

        $data = $request->validate([
            'decision' => 'required|in:approved,rejected',
            'summary'  => 'nullable|string|max:1000',
        ]);

        abort_unless(
            in_array($audit->status, [
                VendorAudit::STATUS_CAPA_SUBMITTED,
                VendorAudit::STATUS_FINDINGS_RECORDED,
                VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED,
            ], true),
            422
        );

        $newStatus = $data['decision'] === 'approved'
            ? VendorAudit::STATUS_COMPLETED
            : VendorAudit::STATUS_REJECTED;

        $application = $audit->application;

        DB::transaction(function () use ($audit, $newStatus, $data, $workflow, $application) {
            $audit->update([
                'status'        => $newStatus,
                'summary'       => $data['summary'] ?? $audit->summary,
                'completed_at'  => now(),
            ]);

            if ($newStatus === VendorAudit::STATUS_COMPLETED) {
                $workflow->transition(
                    $application,
                    VendorApplication::STATUS_APPROVED,
                    'audit_final_approved',
                    [
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'valid_until' => now()->addYears(5)->toDateString(),
                    ],
                    Auth::user(),
                    ['audit_id' => $audit->id]
                );
            } else {
                $workflow->transition(
                    $application,
                    VendorApplication::STATUS_REJECTED,
                    'audit_final_rejected',
                    [],
                    Auth::user(),
                    ['audit_id' => $audit->id, 'reason' => $data['summary'] ?? null]
                );
            }
        });

        return redirect()
            ->route('qa.audit.show', $audit->id)
            ->with('success', 'Keputusan akhir audit disimpan.');
    }

    /* ──────────────────────────────────────────────────────────────────
     |  HELPERS
     * ────────────────────────────────────────────────────────────────*/
    private function recalculateAuditStatus(VendorAudit $audit, VendorApplicationWorkflowService $workflow): void
    {
        $findings = $audit->findings()->with('latestCapa')->get();

        if ($findings->isEmpty()) {
            return;
        }

        $allClosed = $findings->every(fn($f) => $f->status === VendorAuditFinding::STATUS_CLOSED);
        $anyRejected = $findings->contains(
            fn($f) =>
            optional($f->latestCapa)->qa_verdict === VendorAuditCapa::VERDICT_REJECTED
        );

        if ($allClosed) {
            $audit->update(['status' => VendorAudit::STATUS_COMPLETED]);
            $workflow->transition(
                $audit->application,
                VendorApplication::STATUS_APPROVED,
                'audit_all_capas_approved',
                [
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'valid_until' => now()->addYears(5)->toDateString(),
                ],
                Auth::user(),
                ['audit_id' => $audit->id]
            );
        } elseif ($anyRejected) {
            $audit->update(['status' => VendorAudit::STATUS_CAPA_REVISED]);
        }
    }

    private function capaProgress(VendorAudit $audit): array
    {
        $findings = $audit->findings;
        $total = $findings->count();
        $closed = $findings->where('status', VendorAuditFinding::STATUS_CLOSED)->count();

        return [
            'total'   => $total,
            'closed'  => $closed,
            'percent' => $total === 0 ? 0 : (int) round($closed / $total * 100),
        ];
    }

    private function qaUsers()
    {
        return User::role(self::QA_ROLES)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    private function authorizeQaAccess(): void
    {
        $user = Auth::user();
        abort_unless($user && $user->hasAnyRole(self::QA_ROLES), 403);
    }
}
