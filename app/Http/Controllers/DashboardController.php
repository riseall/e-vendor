<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VendorAnnualEvaluation;
use App\Models\VendorApplication;
use App\Models\VendorAppSpecBaku;
use App\Models\VendorAudit;
use App\Models\VendorEvaluation;
use App\Models\VendorQualification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Audit status → [label, css class] */
    private static $auditStatusMap = [
        'scheduled'                 => ['Scheduled',  'vnd-status--scheduled'],
        'schedule_proposed'         => ['Scheduled',  'vnd-status--scheduled'],
        'schedule_confirmed'        => ['Scheduled',  'vnd-status--scheduled'],
        'in_progress'               => ['In Progress', 'vnd-status--in-progress'],
        'questionnaire_in_progress' => ['In Progress', 'vnd-status--in-progress'],
        'questionnaire_submitted'   => ['Submitted',   'vnd-status--in-progress'],
        'findings_recorded'         => ['In Progress', 'vnd-status--in-progress'],
        'capa_in_progress'          => ['In Progress', 'vnd-status--in-progress'],
        'need_revision'             => ['Need Revision', 'vnd-status--revision'],
        'completed'                 => ['Completed',  'vnd-status--completed'],
        'rejected'                  => ['Rejected',    'vnd-status--rejected'],
    ];

    /** Document expiry status → [label, css class] */
    private static $docStatusMap = [
        'valid'         => ['Valid',         'vnd-status--verified'],
        'expiring_soon' => ['Expiring Soon', 'vnd-status--revision'],
        'expired'       => ['Expired',       'vnd-status--rejected'],
    ];

    public function index(): View
    {
        $user = Auth::user();

        if ($this->isVendorUser($user)) {
            return $this->vendorDashboard($user);
        }

        return $this->internalDashboard();
    }

    /**
     * Akses langsung dashboard vendor (alias).
     */
    public function vendorIndex(): View
    {
        return $this->vendorDashboard(Auth::user());
    }

    /**
     * Dashboard untuk Internal Management / QA / Procurement.
     */
    private function internalDashboard(): View
    {
        // ── 1. SUPPLIER OVERVIEW KPI ────────────────────────────────────
        $totalSupplier = VendorApplication::whereNotNull('application_number')->count();
        $approvedSupplier = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)->count();
        $underQualification = VendorApplication::whereIn('status', [
            VendorApplication::STATUS_SUBMITTED,
            VendorApplication::STATUS_NEED_REVISION,
            VendorApplication::STATUS_VERIFIED,
            VendorApplication::STATUS_RISK_ASSESSED,
            VendorApplication::STATUS_AUDIT_REQUIRED,
            VendorApplication::STATUS_ON_HOLD
        ])->count();
        $suspendedSupplier = VendorApplication::where('status', VendorApplication::STATUS_REJECTED)->count();
        $requalificationDue = VendorQualification::whereNotNull('total_score')
            ->where('created_at', '<=', Carbon::now()->subMonths(11))
            ->count();

        // ── 2. SUPPLIER AUDIT MONITORING ────────────────────────────────
        $audits = VendorAudit::with([
            'application.general',
            'application.user',
            'application.categories',
        ])
            ->latest('confirmed_schedule_at')
            ->take(5)
            ->get()
            ->map(function (VendorAudit $a) {
                $app    = $a->application;
                $gen    = $app ? $app->general : null;
                $vendor = $gen ? $gen->nama_perusahaan : ($app && $app->user ? $app->user->name : '—');
                $cats   = $app ? $app->categories->pluck('category_id')
                    ->map(function ($id) {
                        return isset(VendorApplication::CATEGORY_LABELS[$id]) ? VendorApplication::CATEGORY_LABELS[$id] : null;
                    })
                    ->filter()
                    ->take(2)
                    ->values() : collect();

                list($statusLabel, $statusCls) = self::resolveStatus(
                    self::$auditStatusMap,
                    $a->status,
                    'vnd-status--muted'
                );

                return (object) [
                    'vendor_name'       => $vendor,
                    'category_chips'    => $cats,
                    'audit_type_label'  => self::auditTypeLabel($a->audit_type),
                    'last_audit_date'   => $a->questionnaire_submitted_at ? $a->questionnaire_submitted_at->format('d M Y') : '',
                    'next_audit_date'   => $a->confirmed_schedule_at ? $a->confirmed_schedule_at->format('d M Y') : '',
                    'status_label'      => $statusLabel,
                    'status_cls'        => $statusCls,
                    'auditor_display'   => $app->approved_by ?? '-',
                ];
            });

        // ── 3. DOCUMENT EXPIRY MONITORING (CDOB & SIPA APJ only) ────────
        // Sumber: vendor_app_spec_baku (field q5_* untuk CDOB, q6_* untuk SIPA APJ)
        $today = Carbon::now();

        $documents = VendorAppSpecBaku::with('application.general', 'application.user')
            ->whereHas('application', function ($q) {
                $q->where('status', VendorApplication::STATUS_APPROVED);
            })
            ->where(function ($q) use ($today) {
                $q->whereNotNull('q5_valid_until')
                    ->orWhereNotNull('q6_valid_until');
            })
            ->get()
            ->flatMap(function (VendorAppSpecBaku $baku) use ($today) {
                $app    = $baku->application;
                $gen    = $app && $app->general ? $app->general : null;
                $supplier = $gen ? $gen->nama_perusahaan : ($app && $app->user ? $app->user->name : '-');

                $out = [];
                // CDOB
                if (!empty($baku->q5_valid_until)) {
                    $expiry = Carbon::parse($baku->q5_valid_until);
                    $out[] = self::mapExpiryRow(
                        $supplier,
                        'CDOB',
                        $baku->q5_issue_date,
                        $expiry,
                        $today
                    );
                }
                // SIPA APJ
                if (!empty($baku->q6_valid_until)) {
                    $expiry = Carbon::parse($baku->q6_valid_until);
                    $out[] = self::mapExpiryRow(
                        $supplier,
                        'SIPA APJ',
                        $baku->q6_issue_date,
                        $expiry,
                        $today
                    );
                }
                return $out;
            })
            ->sortBy('expiry_raw') // soonest expiry first
            ->take(5)
            ->values();

        // ── 4. SUPPLIER PERFORMANCE (top 5 dari Evaluasi Tahunan) ────────
        $latestAnnualYear = VendorAnnualEvaluation::where('final_score', '>', 0)
            ->where('status', '!=', VendorAnnualEvaluation::STATUS_REJECTED)
            ->max('year');

        if ($latestAnnualYear) {
            $topEvals = VendorAnnualEvaluation::where('year', $latestAnnualYear)
                ->where('status', '!=', VendorAnnualEvaluation::STATUS_REJECTED)
                ->where('final_score', '>', 0)
                ->orderByDesc('final_score')
                ->take(5)
                ->get(['vendor_id', 'final_score', 'category']);

            $vIds = $topEvals->pluck('vendor_id')->unique();
            $users = User::whereIn('id', $vIds)->get()->keyBy('id');
            $apps  = VendorApplication::whereIn('user_id', $vIds)
                ->where('status', VendorApplication::STATUS_APPROVED)
                ->with('general')
                ->latest('approved_at')
                ->get()
                ->keyBy('user_id');

            $topPerformers = $topEvals->map(function ($ev) use ($users, $apps) {
                $app  = $apps->get($ev->vendor_id);
                $user = $users->get($ev->vendor_id);
                $name = ($app && $app->general && $app->general->nama_perusahaan)
                    ? $app->general->nama_perusahaan
                    : ($user ? $user->name : '-');

                $pct = (int) round(min(100, max(0, $ev->final_score)));

                if ($pct >= 80) {
                    $color = 'green';
                } elseif ($pct >= 60) {
                    $color = 'amber';
                } else {
                    $color = 'red';
                }

                return (object) [
                    'name'  => $name,
                    'pct'   => $pct,
                    'color' => $color,
                ];
            });
        } else {
            $topPerformers = collect();
        }

        // ── 5. SUPPLIER RISK ASSESSMENT (single-query rollup) ───────────
        $riskRows = DB::table('vendor_qualifications')
            ->whereNotNull('risk_level')
            ->select('risk_level', DB::raw('count(*) as total'))
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level')
            ->toArray();

        $riskCounts = [
            'high'   => (int) ($riskRows['high'] ?? $riskRows['HIGH'] ?? 0),
            'medium' => (int) ($riskRows['medium'] ?? $riskRows['MEDIUM'] ?? 0),
            'low'    => (int) ($riskRows['low'] ?? $riskRows['LOW'] ?? 0),
        ];

        $riskTotal = array_sum($riskCounts);

        return view('admin.dashboard', compact(
            'totalSupplier',
            'approvedSupplier',
            'underQualification',
            'suspendedSupplier',
            'requalificationDue',
            'audits',
            'documents',
            'topPerformers',
            'riskCounts',
            'riskTotal'
        ));
    }

    /**
     * Tampilan Dashboard khusus untuk Vendor / User Eksternal.
     */
    private function vendorDashboard(?User $user): View
    {
        if (!$user) {
            abort(401);
        }

        $today = Carbon::today();

        // 1. Permohonan Vendor Terkini
        $application = VendorApplication::where('user_id', $user->id)
            ->with([
                'general',
                'categories',
                'qualification',
                'specBaku',
                'documents',
            ])
            ->latest()
            ->first();

        $general = $application ? $application->general : null;
        $companyName = ($general && !empty($general->nama_perusahaan))
            ? $general->nama_perusahaan
            : $user->name;

        $status = $application ? $application->status : 'not_registered';

        // Status metadata
        $statusInfoMap = [
            'not_registered' => [
                'label'       => 'Belum Mendaftar',
                'short_label' => 'Belum Daftar',
                'badge_class' => 'label-light-secondary',
                'stat_type'   => 'info',
                'color'       => 'secondary',
                'desc'        => 'Anda belum mengisi formulir pendaftaran rekanan. Klik Mulai Pendaftaran untuk melengkapi data perusahaan Anda.',
            ],
            VendorApplication::STATUS_DRAFT => [
                'label'       => 'Draft Permohonan',
                'short_label' => 'Draf',
                'badge_class' => 'label-light-warning',
                'stat_type'   => 'warning',
                'color'       => 'warning',
                'desc'        => 'Formulir pendaftaran masih berstatus draf. Silakan lengkapi seluruh isian dan dokumen lalu klik Kirim Permohonan.',
            ],
            VendorApplication::STATUS_SUBMITTED => [
                'label'       => 'Menunggu Verifikasi',
                'short_label' => 'Verifikasi',
                'badge_class' => 'label-light-primary',
                'stat_type'   => 'primary',
                'color'       => 'primary',
                'desc'        => 'Permohonan pendaftaran telah dikirimkan dan sedang dalam proses verifikasi dokumen oleh Tim Pengadaan PT Phapros Tbk.',
            ],
            VendorApplication::STATUS_NEED_REVISION => [
                'label'       => 'Perlu Revisi Dokumen',
                'short_label' => 'Perlu Revisi',
                'badge_class' => 'label-light-danger',
                'stat_type'   => 'danger',
                'color'       => 'danger',
                'desc'        => 'Terdapat catatan perbaikan dokumen dari Tim Verifikator. Mohon segera periksa catatan dan lakukan revisi perbaikan.',
            ],
            VendorApplication::STATUS_VERIFIED => [
                'label'       => 'Terverifikasi Pengadaan',
                'short_label' => 'Terverifikasi',
                'badge_class' => 'label-light-info',
                'stat_type'   => 'info',
                'color'       => 'info',
                'desc'        => 'Dokumen administratif telah diverifikasi oleh Tim Pengadaan. Proses selanjutnya adalah Penilaian Risiko oleh Tim QA.',
            ],
            VendorApplication::STATUS_RISK_ASSESSED => [
                'label'       => 'Penilaian Risiko Selesai',
                'short_label' => 'Risk Assessed',
                'badge_class' => 'label-light-info',
                'stat_type'   => 'info',
                'color'       => 'info',
                'desc'        => 'Penilaian risiko mutu dan kapabilitas telah dinilai oleh Tim QA. Menunggu tahapan penetapan rekanan atau audit.',
            ],
            VendorApplication::STATUS_AUDIT_REQUIRED => [
                'label'       => 'Audit Diperlukan',
                'short_label' => 'Audit',
                'badge_class' => 'label-light-warning',
                'stat_type'   => 'warning',
                'color'       => 'warning',
                'desc'        => 'Permohonan memerlukan tahap audit vendor (On-Desk / On-Site) sebelum dapat disahkan sebagai rekanan approved.',
            ],
            VendorApplication::STATUS_ON_HOLD => [
                'label'       => 'Ditangguhkan (On Hold)',
                'short_label' => 'On Hold',
                'badge_class' => 'label-light-dark',
                'stat_type'   => 'warning',
                'color'       => 'dark',
                'desc'        => 'Proses permohonan kemitraan sedang ditangguhkan sementara waktu oleh Tim Evaluator PT Phapros Tbk.',
            ],
            VendorApplication::STATUS_APPROVED => [
                'label'       => 'Rekanan Terdaftar (Approved)',
                'short_label' => 'Approved',
                'badge_class' => 'label-light-success',
                'stat_type'   => 'success',
                'color'       => 'success',
                'desc'        => 'Selamat! Perusahaan Anda telah resmi terdaftar dan disetujui (Approved) sebagai Rekanan Resmi PT Phapros Tbk.',
            ],
            VendorApplication::STATUS_REJECTED => [
                'label'       => 'Permohonan Ditolak',
                'short_label' => 'Ditolak',
                'badge_class' => 'label-light-danger',
                'stat_type'   => 'danger',
                'color'       => 'danger',
                'desc'        => 'Permohonan pendaftaran rekanan belum dapat disetujui.',
            ],
        ];

        $statusInfo = isset($statusInfoMap[$status]) 
            ? $statusInfoMap[$status] 
            : [
                'label'       => ucwords(str_replace('_', ' ', (string) $status)),
                'short_label' => ucwords(str_replace('_', ' ', (string) $status)),
                'badge_class' => 'label-light-secondary',
                'stat_type'   => 'primary',
                'color'       => 'secondary',
                'desc'        => '',
            ];

        // 2. Stepper Tahapan Kualifikasi
        $pipelineSteps = [
            [
                'step'   => 1,
                'title'  => 'Pendaftaran & Berkas',
                'desc'   => 'Pengisian data umum & spesifik',
                'icon'   => 'fas fa-file-alt',
                'status' => 'pending',
                'date'   => ($application && $application->submitted_at) ? $application->submitted_at->format('d M Y') : null,
            ],
            [
                'step'   => 2,
                'title'  => 'Verifikasi Pengadaan',
                'desc'   => 'Pemeriksaan berkas administrasi',
                'icon'   => 'fas fa-clipboard-check',
                'status' => 'pending',
                'date'   => ($application && $application->verified_at) ? $application->verified_at->format('d M Y') : null,
            ],
            [
                'step'   => 3,
                'title'  => 'Penilaian Risiko QA',
                'desc'   => 'Evaluasi kualifikasi mutu',
                'icon'   => 'fas fa-shield-alt',
                'status' => 'pending',
                'date'   => ($application && $application->qualification && $application->qualification->created_at) ? $application->qualification->created_at->format('d M Y') : null,
            ],
            [
                'step'   => 4,
                'title'  => 'Audit Supplier',
                'desc'   => 'Audit On-Desk / On-Site',
                'icon'   => 'fas fa-search',
                'status' => 'pending',
                'date'   => null,
            ],
            [
                'step'   => 5,
                'title'  => 'Penetapan Rekanan',
                'desc'   => 'Penerbitan status Approved',
                'icon'   => 'fas fa-check-circle',
                'status' => 'pending',
                'date'   => ($application && $application->approved_at) ? $application->approved_at->format('d M Y') : null,
            ],
        ];

        if (!$application || $status === VendorApplication::STATUS_DRAFT) {
            $pipelineSteps[0]['status'] = 'current';
        } elseif ($status === VendorApplication::STATUS_SUBMITTED) {
            $pipelineSteps[0]['status'] = 'completed';
            $pipelineSteps[1]['status'] = 'current';
        } elseif ($status === VendorApplication::STATUS_NEED_REVISION) {
            $pipelineSteps[0]['status'] = 'completed';
            $pipelineSteps[1]['status'] = 'danger';
        } elseif ($status === VendorApplication::STATUS_VERIFIED) {
            $pipelineSteps[0]['status'] = 'completed';
            $pipelineSteps[1]['status'] = 'completed';
            $pipelineSteps[2]['status'] = 'current';
        } elseif ($status === VendorApplication::STATUS_RISK_ASSESSED) {
            $pipelineSteps[0]['status'] = 'completed';
            $pipelineSteps[1]['status'] = 'completed';
            $pipelineSteps[2]['status'] = 'completed';
            $pipelineSteps[3]['status'] = 'current';
        } elseif ($status === VendorApplication::STATUS_AUDIT_REQUIRED) {
            $pipelineSteps[0]['status'] = 'completed';
            $pipelineSteps[1]['status'] = 'completed';
            $pipelineSteps[2]['status'] = 'completed';
            $pipelineSteps[3]['status'] = 'current';
        } elseif ($status === VendorApplication::STATUS_APPROVED) {
            $pipelineSteps[0]['status'] = 'completed';
            $pipelineSteps[1]['status'] = 'completed';
            $pipelineSteps[2]['status'] = 'completed';
            $pipelineSteps[3]['status'] = 'completed';
            $pipelineSteps[4]['status'] = 'completed';
        } elseif ($status === VendorApplication::STATUS_REJECTED) {
            $pipelineSteps[0]['status'] = 'completed';
            $pipelineSteps[1]['status'] = 'completed';
            $pipelineSteps[4]['status'] = 'danger';
        }

        // 3. Catatan Revisi (jika status need_revision)
        $revisionNotes = [];
        if ($application && $status === VendorApplication::STATUS_NEED_REVISION) {
            if (!empty($application->admin_note)) {
                $revisionNotes[] = [
                    'field' => 'Catatan Verifikator',
                    'note'  => $application->admin_note,
                ];
            }
            if (!empty($application->revision_notes) && is_array($application->revision_notes)) {
                foreach ($application->revision_notes as $f => $n) {
                    $revisionNotes[] = [
                        'field' => ucwords(str_replace('_', ' ', (string) $f)),
                        'note'  => is_array($n) ? implode(', ', $n) : (string) $n,
                    ];
                }
            }
        }

        // 4. Masa Berlaku & Validitas
        $daysUntilExpiry = null;
        $validityStatus = 'none';
        $validUntilDisplay = '-';

        if ($application && $application->valid_until) {
            $validUntil = Carbon::parse($application->valid_until);
            $validUntilDisplay = $validUntil->format('d M Y');
            $daysUntilExpiry = (int) $today->diffInDays($validUntil, false);

            if ($daysUntilExpiry < 0) {
                $validityStatus = 'expired';
            } elseif ($daysUntilExpiry <= 60) {
                $validityStatus = 'expiring_soon';
            } else {
                $validityStatus = 'valid';
            }
        }

        // 5. Monitoring Dokumen Legalitas (Khusus CDOB & SIPA APJ)
        $monitoredDocuments = [];
        $isBaku = false;
        if ($application) {
            if ($application->specBaku) {
                $isBaku = true;
            } elseif ($application->categories && $application->categories->contains('category_id', 1)) {
                $isBaku = true;
            }
        }

        if ($isBaku) {
            $baku = $application ? $application->specBaku : null;

            // 1. Sertifikat CDOB
            $q5Path = ($baku && !empty($baku->q5_document))
                ? $baku->q5_document
                : ($application && $application->documents ? optional($application->documents->firstWhere('field_name', 'q5_document'))->file_path : null);
            $expiryQ5 = ($baku && !empty($baku->q5_valid_until)) ? Carbon::parse($baku->q5_valid_until) : null;
            $daysQ5 = $expiryQ5 ? (int) $today->diffInDays($expiryQ5, false) : null;
            $statusQ5 = 'unuploaded';
            if ($expiryQ5) {
                $statusQ5 = $daysQ5 < 0 ? 'expired' : ($daysQ5 <= 60 ? 'expiring_soon' : 'valid');
            } elseif ($q5Path) {
                $statusQ5 = 'uploaded';
            }

            $monitoredDocuments[] = (object) [
                'name'        => 'Sertifikat CDOB',
                'doc_type'    => 'Cara Distribusi Obat yang Baik',
                'issue_date'  => ($baku && $baku->q5_issue_date) ? Carbon::parse($baku->q5_issue_date)->format('d M Y') : '-',
                'expiry_date' => $expiryQ5 ? $expiryQ5->format('d M Y') : '-',
                'days_left'   => $daysQ5,
                'status'      => $statusQ5,
                'file_url'    => ($q5Path && $application) ? route('registrasi.files.show', ['application' => $application->id, 'file' => Crypt::encryptString($q5Path)]) : null,
            ];

            // 2. SIPA APJ
            $q6Path = ($baku && !empty($baku->q6_document))
                ? $baku->q6_document
                : ($application && $application->documents ? optional($application->documents->firstWhere('field_name', 'q6_document'))->file_path : null);
            $expiryQ6 = ($baku && !empty($baku->q6_valid_until)) ? Carbon::parse($baku->q6_valid_until) : null;
            $daysQ6 = $expiryQ6 ? (int) $today->diffInDays($expiryQ6, false) : null;
            $statusQ6 = 'unuploaded';
            if ($expiryQ6) {
                $statusQ6 = $daysQ6 < 0 ? 'expired' : ($daysQ6 <= 60 ? 'expiring_soon' : 'valid');
            } elseif ($q6Path) {
                $statusQ6 = 'uploaded';
            }

            $monitoredDocuments[] = (object) [
                'name'        => 'SIPA APJ',
                'doc_type'    => 'Surat Izin Praktik Apoteker (APJ)',
                'issue_date'  => ($baku && $baku->q6_issue_date) ? Carbon::parse($baku->q6_issue_date)->format('d M Y') : '-',
                'expiry_date' => $expiryQ6 ? $expiryQ6->format('d M Y') : '-',
                'days_left'   => $daysQ6,
                'status'      => $statusQ6,
                'file_url'    => ($q6Path && $application) ? route('registrasi.files.show', ['application' => $application->id, 'file' => Crypt::encryptString($q6Path)]) : null,
            ];
        }

        // 6. Riwayat Audit Vendor
        $audits = VendorAudit::whereHas('application', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function (VendorAudit $a) {
                list($statusLabel, $statusCls) = self::resolveStatus(
                    self::$auditStatusMap,
                    $a->status,
                    'vnd-status--muted'
                );

                return (object) [
                    'id'               => $a->id,
                    'audit_type'       => $a->audit_type,
                    'audit_type_label' => self::auditTypeLabel($a->audit_type),
                    'status'           => $a->status,
                    'status_label'     => $statusLabel,
                    'status_cls'       => $statusCls,
                    'schedule_date'    => $a->confirmed_schedule_at ? $a->confirmed_schedule_at->format('d M Y') : ($a->questionnaire_submitted_at ? $a->questionnaire_submitted_at->format('d M Y') : '-'),
                    'summary'          => $a->summary ?: '-',
                    'result_category'  => $a->audit_result_category ?: '-',
                    'can_fill_questionnaire' => ($a->audit_type === VendorAudit::TYPE_ON_DESK && in_array($a->status, [
                        VendorAudit::STATUS_SCHEDULED,
                        VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS,
                        VendorAudit::STATUS_NEED_REVISION,
                    ])),
                ];
            });

        $activeAudit = $audits->first(function ($a) {
            return !in_array($a->status, [VendorAudit::STATUS_COMPLETED, VendorAudit::STATUS_REJECTED]);
        });

        // 7. Evaluasi Kinerja Tahunan
        $evaluations = VendorAnnualEvaluation::where('vendor_id', $user->id)
            ->where('status', VendorAnnualEvaluation::STATUS_APPROVED)
            ->orderByDesc('year')
            ->get();

        $latestEvaluation = $evaluations->first();

        // 8. Kategori Terdaftar
        $categories = collect();
        if ($application && $application->categories) {
            $categories = $application->categories->pluck('category_id')
                ->map(function ($id) {
                    return isset(VendorApplication::CATEGORY_LABELS[$id]) ? VendorApplication::CATEGORY_LABELS[$id] : null;
                })
                ->filter()
                ->values();
        }

        return view('vendor.dashboard', compact(
            'user',
            'application',
            'general',
            'companyName',
            'status',
            'statusInfo',
            'pipelineSteps',
            'revisionNotes',
            'daysUntilExpiry',
            'validityStatus',
            'validUntilDisplay',
            'monitoredDocuments',
            'audits',
            'activeAudit',
            'evaluations',
            'latestEvaluation',
            'categories'
        ));
    }

    /**
     * Determine if current user is an external vendor/supplier.
     *
     * ponytail: Single role check & fallback; ceiling is dual-role users, upgrade path to explicit guard/permission.
     */
    private function isVendorUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->hasRole('Supplier') || $user->hasRole('Vendor')) {
            return true;
        }

        $internalRoles = [
            'Super Admin',
            'Admin IT',
            'Verifikator',
            'Procurement',
            'Quality Assurance',
            'Apoteker',
            'Specialist',
        ];

        return !$user->hasAnyRole($internalRoles);
    }

    /** Resolve a status map entry → [label, class]. */
    private static function resolveStatus(array $map, $key, $fallbackCls)
    {
        if (isset($map[$key])) {
            return $map[$key];
        }
        return [ucwords(str_replace('_', ' ', (string) $key)), $fallbackCls];
    }

    /**
     * Build a single expiry-monitoring row.
     */
    private static function mapExpiryRow(string $supplier, string $docType, $issueDate, Carbon $expiry, Carbon $today): object
    {
        $daysLeft = $today->diffInDays($expiry, false);

        if ($daysLeft < 0) {
            $status = 'expired';
        } elseif ($daysLeft <= 60) {
            $status = 'expiring_soon';
        } else {
            $status = 'valid';
        }

        list($statusLabel, $statusCls) = self::resolveStatus(self::$docStatusMap, $status, 'vnd-status--muted');

        return (object) [
            'supplier'     => $supplier,
            'doc_type'     => $docType,
            'issue_date'   => $issueDate ? Carbon::parse($issueDate)->format('d M Y') : '',
            'issue_raw'    => $issueDate ? Carbon::parse($issueDate)->format('Y-m-d') : '',
            'expiry_date'  => $expiry->format('d M Y'),
            'expiry_raw'   => $expiry->format('Y-m-d'),
            'status_label' => $statusLabel,
            'status_cls'   => $statusCls,
        ];
    }

    /** Audit type → display label (PHP 7.3-safe). */
    private static function auditTypeLabel($t)
    {
        $map = [
            'on_desk' => 'On Desk',
            'on_site' => 'Onsite',
            'paper'   => 'Paper',
            'remote'  => 'Remote',
        ];
        return isset($map[$t]) ? $map[$t] : ucwords(str_replace('_', ' ', (string) $t));
    }
}
