<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VendorApplication;
use App\Models\VendorAudit;
use App\Models\VendorAuditFinding;
use App\Models\VendorAuditQuestionnaireForm;
use App\Services\VendorApplicationNotificationService;
use App\Mail\VendorAuditResultNotification;
use App\Mail\VendorAuditOnDeskNotification;
use Illuminate\Support\Facades\Mail;
use App\Services\VendorApplicationWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            ->with(['application.general', 'application.user', 'qualification'])
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

        $questionnaireForms = VendorAuditQuestionnaireForm::optionsForMaterial()->values();

        return view('admin.audit.create', [
            'application'       => $application,
            'activeAudit'       => $activeAudit,
            'questionnaireForms'=> $questionnaireForms,
            'qaUsers'           => $this->qaUsers(),
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
            'qaLead',
        ])
            ->findOrFail($auditId);

        $progress = $audit->progressPercent();

        $questions = collect();
        if ($audit->audit_type === \App\Models\VendorAudit::TYPE_ON_DESK) {
            if ($audit->questionnaire_form_id) {
                $questions = \App\Models\VendorAuditQuestionTemplate::forForm($audit->questionnaire_form_id);
            } else {
                $categoryIds = $audit->application->categories->pluck('category_id')->all();
                $questions   = \App\Models\VendorAuditQuestionTemplate::forCategories($categoryIds);
            }
        }

        return view('admin.audit.show', [
            'audit'        => $audit,
            'progress'     => $progress,
            'application'  => $audit->application,
            'questions'    => $questions,
        ]);
    }

    /* ──────────────────────────────────────────────────────────────────
     |  STORE – QA membuat audit row dari hasil risk assessment
     * ────────────────────────────────────────────────────────────────*/
    public function store(Request $request, int $applicationId, VendorApplicationWorkflowService $workflow, VendorApplicationNotificationService $notification)
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
                'confirmed_schedule_at' => 'required|date|after:today',
                'audit_location'        => 'required|string|max:255',
                'audit_agenda'          => 'required|string|max:500',
                'auditor_team'          => 'required|array|min:1',
                'auditor_team.*.name'   => 'required|string|max:120',
                'auditor_team.*.role'   => 'required|string|max:80',
                'qa_lead_id'            => 'nullable|exists:users,id',
                'summary'               => 'nullable|string|max:1000',
            ]);
        } else {
            // Ponytail: on_desk wajib pilih form questionnaire. Add when: butuh form berbeda per
            // kategori material, ganti Rule::exists ke scope material_type.
            $data = $request->validate([
                'questionnaire_form_id' => 'required|exists:vendor_audit_questionnaire_forms,id',
                'qa_lead_id'            => 'nullable|exists:users,id',
                'summary'               => 'nullable|string|max:1000',
            ]);
        }

        $audit = DB::transaction(function () use ($application, $qualification, $auditType, $data, $workflow) {
            $payload = [
                'vendor_application_id'   => $application->id,
                'vendor_qualification_id' => $qualification->id,
                'audit_type'              => $auditType,
                'status'                  => $auditType === VendorAudit::TYPE_ON_DESK
                    ? VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS
                    : VendorAudit::STATUS_IN_PROGRESS,
                'qa_lead_id'              => $data['qa_lead_id'] ?? Auth::id(),
                'created_by'              => Auth::id(),
            ];

            if ($auditType === VendorAudit::TYPE_ON_SITE) {
                $payload = array_merge($payload, [
                    'confirmed_schedule_at' => Carbon::parse($data['confirmed_schedule_at'])->toDateTimeString(),
                    'audit_location'        => $data['audit_location'],
                    'audit_agenda'          => $data['audit_agenda'],
                    'auditor_team'          => $data['auditor_team'],
                    'summary'               => $data['summary'] ?? null,
                ]);
            } else {
                $payload = array_merge($payload, [
                    'questionnaire_form_id' => $data['questionnaire_form_id'],
                    'summary'               => $data['summary'] ?? null,
                ]);
            }

            $audit = VendorAudit::create($payload);


            $workflow->record(
                $application,
                $auditType === VendorAudit::TYPE_ON_DESK
                    ? 'audit_on_desk_started'
                    : 'audit_on_site_started',
                Auth::user(),
                ['audit_id' => $audit->id, 'audit_type' => $auditType]
            );

            return $audit;
        });

        if ($audit->audit_type === VendorAudit::TYPE_ON_SITE) {
            $notification->auditOnSiteScheduled($audit);
        }

        return redirect()
            ->route('qa.audit.show', $audit->id)
            ->with('success', 'Audit berhasil dibuat. Status: ' . $audit->status . '.');
    }

    /* ──────────────────────────────────────────────────────────────────
     |  VERIFY QUESTIONNAIRE (On Desk)
     * ────────────────────────────────────────────────────────────────*/
    public function verifyQuestionnaire(Request $request, int $auditId, VendorApplicationWorkflowService $workflow, VendorApplicationNotificationService $notification)
    {
        $this->authorizeQaAccess();

        $audit = VendorAudit::with('application')->findOrFail($auditId);
        abort_unless($audit->audit_type === VendorAudit::TYPE_ON_DESK, 404);
        abort_unless(
            in_array($audit->status, [
                VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED
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
            $categoryMap = [
                'approved' => 'terekomendasi',
                'rejected' => 'tdk_rekomendasi',
            ];
            $status = $verdictMap[$data['verdict']] ?? VendorAudit::STATUS_NEED_REVISION;
            $category = $categoryMap[$data['verdict']] ?? $audit->audit_result_category;

            $audit->update([
                'status'                       => $status,
                'audit_result_category'        => $category,
                'questionnaire_revision_notes' => $data['revision_notes'] ?? null,
                'summary'                      => $data['summary'] ?? $audit->summary,
                'completed_at'                 => in_array($status, [VendorAudit::STATUS_COMPLETED, VendorAudit::STATUS_REJECTED])
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

        // 👱‍♀️ Ponytail: Send On-Desk notification email via Service
        $notification->auditQuestionnaireVerified($audit);

        return redirect()
            ->route('qa.audit.show', $audit->id)
            ->with('success', 'Verifikasi questionnaire disimpan.');
    }




    public function storeResult(Request $request, int $auditId, VendorApplicationWorkflowService $workflow, VendorApplicationNotificationService $notification)
    {
        $this->authorizeQaAccess();

        $audit = VendorAudit::with('application')->findOrFail($auditId);
        abort_unless($audit->audit_type === VendorAudit::TYPE_ON_SITE, 404);
        abort_unless(
            in_array($audit->status, [
                VendorAudit::STATUS_SCHEDULE_CONFIRMED,
                VendorAudit::STATUS_IN_PROGRESS
            ], true),
            422
        );

        $data = $request->validate([
            'audit_result_file'     => 'required|file|max:10240',
            'audit_result_category' => 'required|in:terekomendasi,tdk_rekomendasi,on_hold',
            'summary'               => 'required|string|max:2000',
        ]);

        $path = $request->file('audit_result_file')->store('audit-results', 'public');

        $category = $data['audit_result_category'];
        $newAuditStatus = VendorAudit::STATUS_COMPLETED;
        $newAppStatus = VendorApplication::STATUS_APPROVED;
        $transitionReason = 'audit_final_approved';
        $attributes = [];

        if ($category === 'tdk_rekomendasi') {
            $newAuditStatus = VendorAudit::STATUS_REJECTED;
            $newAppStatus = VendorApplication::STATUS_REJECTED;
            $transitionReason = 'audit_final_rejected';
        } elseif ($category === 'on_hold') {
            $newAuditStatus = VendorAudit::STATUS_COMPLETED;
            $newAppStatus = VendorApplication::STATUS_ON_HOLD;
            $transitionReason = 'audit_final_on_hold';
        }

        if ($newAppStatus === VendorApplication::STATUS_APPROVED) {
            $attributes = [
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'valid_until' => now()->addYears(5)->toDateString(),
            ];
        }

        DB::transaction(function () use ($audit, $data, $path, $workflow, $newAuditStatus, $newAppStatus, $transitionReason, $attributes) {
            // Delete old file if exists
            if ($audit->audit_result_path && \Storage::disk('public')->exists($audit->audit_result_path)) {
                \Storage::disk('public')->delete($audit->audit_result_path);
            }

            $audit->update([
                'audit_result_path'     => $path,
                'audit_result_category' => $data['audit_result_category'],
                'summary'               => $data['summary'],
                'status'                => $newAuditStatus,
                'completed_at'          => now(),
            ]);

            // Transition application status
            $workflow->transition(
                $audit->application,
                $newAppStatus,
                $transitionReason,
                $attributes,
                Auth::user(),
                ['audit_id' => $audit->id, 'reason' => $data['summary'] ?? null]
            );
        });

        // Send email notification via Service
        $notification->auditOnSiteResult($audit);

        return redirect()
            ->route('qa.audit.show', $audit->id)
            ->with('success', 'Hasil audit berhasil diunggah dan status permohonan vendor telah diperbarui.');
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
