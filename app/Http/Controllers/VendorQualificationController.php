<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExistingRiskAssessmentRequest;
use App\Http\Requests\StoreRiskAssessmentRequest;
use App\Models\User;
use App\Models\VendorApplication;
use App\Models\VendorApplicationProduct;
use App\Models\VendorQualification;
use App\Services\VendorApplicationNotificationService;
use App\Services\VendorApplicationWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorQualificationController extends Controller
{

    private const STATUS_OPTIONS = [
        'pending' => 'Belum Assessment (Antrean Aktif)',
        VendorApplication::STATUS_VERIFIED => 'Verified (Menunggu Assessment)',
        VendorApplication::STATUS_RISK_ASSESSED => 'Risk Assessed',
        VendorApplication::STATUS_AUDIT_REQUIRED => 'Audit Required',
        VendorApplication::STATUS_APPROVED => 'Approved',
        'all' => 'Semua Riwayat',
    ];

    public function index(Request $request): View
    {
        $this->authorizeQaAccess();

        $activeTab = $request->input('tab', 'new');
        $status = $request->input('status', 'pending');
        $search = trim((string) $request->input('q', ''));

        if ($activeTab === 'existing') {
            // Tab Vendor Eksisting (Kategori 1: Bahan Baku & Bahan Kemas)
            $query = VendorApplication::query()
                ->with(['user', 'general', 'categories', 'qualification', 'audits'])
                ->whereHas('categories', function ($q) {
                    $q->where('category_id', 1);
                })
                ->where(function ($q) {
                    $q->where('status', VendorApplication::STATUS_APPROVED)
                        ->orWhere(function ($sub) {
                            $sub->whereIn('status', [
                                VendorApplication::STATUS_AUDIT_REQUIRED,
                                VendorApplication::STATUS_RISK_ASSESSED,
                            ])->where(function ($typeQ) {
                                $typeQ->where('type', VendorApplication::TYPE_REKUALIFIKASI)
                                    ->orWhereHas('qualification', function ($qualQ) {
                                        $qualQ->where('assessment_type', 'existing');
                                    });
                            });
                        });
                })
                ->latest('approved_at')
                ->latest();

            if ($status === 'pending') {
                $query->whereDoesntHave('qualification', function ($q) {
                    $q->where('assessment_type', 'existing');
                });
            } elseif ($status !== 'all') {
                $query->where('status', $status);
            }

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('application_number', 'like', "%{$search}%")
                        ->orWhereHas('general', function ($general) use ($search) {
                            $general->where('nama_perusahaan', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user', function ($user) use ($search) {
                            $user->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            $countByLevel = [
                'low' => (clone $query)->whereHas('qualification', function ($q) {
                    $q->where('assessment_type', 'existing')->where('risk_level', 'low');
                })->count(),
                'medium' => (clone $query)->whereHas('qualification', function ($q) {
                    $q->where('assessment_type', 'existing')->where('risk_level', 'medium');
                })->count(),
                'high' => (clone $query)->whereHas('qualification', function ($q) {
                    $q->where('assessment_type', 'existing')->where('risk_level', 'high');
                })->count(),
                'very_high' => (clone $query)->whereHas('qualification', function ($q) {
                    $q->where('assessment_type', 'existing')->where('risk_level', 'very_high');
                })->count(),
            ];

            $applications = $query->paginate(10)->withQueryString();

            return view('admin.risk_assesment.index', [
                'activeTab'     => 'existing',
                'applications'  => $applications,
                'status'        => $status,
                'search'        => $search,
                'statusOptions' => [
                    'pending'                                => 'Belum Assessment (Antrean Aktif)',
                    VendorApplication::STATUS_APPROVED       => 'Approved (Menunggu Assessment)',
                    VendorApplication::STATUS_AUDIT_REQUIRED => 'Perlu Audit (Sudah Dinilai)',
                    'all'                                    => 'Semua Riwayat',
                ],
                'countByLevel'  => $countByLevel,
                'lowThreshold'  => (int) config('risk_assessment.existing.low_threshold', 84),
                'highThreshold' => (int) config('risk_assessment.existing.high_threshold', 336),
            ]);
        }

        // Tab Vendor Baru (Default)
        $query = VendorApplication::query()
            ->with(['user', 'general', 'categories', 'qualification', 'audits'])
            ->where('type', '!=', VendorApplication::TYPE_REKUALIFIKASI)
            ->whereDoesntHave('qualification', function ($q) {
                $q->where('assessment_type', 'existing');
            })
            ->whereIn('status', [
                VendorApplication::STATUS_VERIFIED,
                VendorApplication::STATUS_RISK_ASSESSED,
                VendorApplication::STATUS_AUDIT_REQUIRED,
                VendorApplication::STATUS_APPROVED,
            ])
            ->latest('verified_at')
            ->latest();

        if ($status === 'pending') {
            $query->where('status', VendorApplication::STATUS_VERIFIED)
                ->doesntHave('qualification');
        } elseif ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhereHas('general', function ($general) use ($search) {
                        $general->where('nama_perusahaan', 'like', "%{$search}%");
                        // ponytail: email_perusahaan is encrypted, removed from SQL LIKE
                    })
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $countByLevel = [
            'low' => (clone $query)->whereHas('qualification', function ($q) {
                $q->where('risk_level', 'low');
            })->count(),
            'medium' => (clone $query)->whereHas('qualification', function ($q) {
                $q->where('risk_level', 'medium');
            })->count(),
            'high' => (clone $query)->whereHas('qualification', function ($q) {
                $q->where('risk_level', 'high');
            })->count(),
        ];

        $applications = $query->paginate(10)->withQueryString();

        return view('admin.risk_assesment.index', [
            'activeTab'     => 'new',
            'applications'  => $applications,
            'status'        => $status,
            'search'        => $search,
            'statusOptions' => self::STATUS_OPTIONS,
            'countByLevel'  => $countByLevel,
            'lowThreshold'  => (int) config('risk_assessment.low_threshold', 88),
            'highThreshold' => (int) config('risk_assessment.high_threshold', 164),
        ]);
    }

    public function create(int $application_id): View
    {
        $this->authorizeQaAccess();

        $application = VendorApplication::with(['user', 'general', 'categories', 'documents', 'specBaku', 'qualification', 'products'])
            ->findOrFail($application_id);

        // abort_unless(
        //     in_array($application->status, [
        //         VendorApplication::STATUS_VERIFIED,
        //         VendorApplication::STATUS_RISK_ASSESSED,
        //         VendorApplication::STATUS_AUDIT_REQUIRED,
        //     ], true),
        //     403,
        //     'Risk assessment hanya dapat dilakukan setelah permohonan verified.'
        // );

        $autoScores = $this->automaticScores($application);

        return view('admin.risk_assesment.create', [
            'application' => $application,
            'qualification' => $application->qualification,
            'autoScores' => $autoScores,
            'lowThreshold' => (int) config('risk_assessment.low_threshold', 88),
            'highThreshold' => (int) config('risk_assessment.high_threshold', 164),
        ]);
    }

    public function store(
        StoreRiskAssessmentRequest $request,
        int $application_id,
        VendorApplicationWorkflowService $workflow,
        VendorApplicationNotificationService $notificationService
    ) {
        $this->authorizeQaAccess();

        $data = $request->validated();

        abort_unless((int) $data['vendor_application_id'] === $application_id, 422);

        $application = VendorApplication::with('qualification')->findOrFail($application_id);

        // abort_unless(
        //     in_array($application->status, [
        //         VendorApplication::STATUS_VERIFIED,
        //         VendorApplication::STATUS_RISK_ASSESSED,
        //         VendorApplication::STATUS_AUDIT_REQUIRED,
        //     ], true),
        //     403,
        //     'Risk assessment hanya dapat dilakukan setelah permohonan verified.'
        // );

        $qualification = DB::transaction(function () use ($application, $data, $workflow) {
            $application->loadMissing(['general', 'documents', 'specBaku', 'products']);
            $autoScores = $this->automaticScores($application);

            $scoreA = $autoScores['doc_score'] + (int) $data['score_safety_efficacy_attr'];
            $scoreB = $autoScores['traceability_score'] + $autoScores['supplier_type_score'];
            $scoreC = (int) $data['score_detectability_country'] + (int) $data['score_detectability_warning'];
            $scoreD = (int) $data['score_probability_function'];

            $lowThreshold = (int) config('risk_assessment.low_threshold', 88);
            $highThreshold = (int) config('risk_assessment.high_threshold', 164);
            $totalScore = ($scoreA + $scoreB) * ($scoreC + $scoreD);

            $riskLevel = 'low';
            if ($totalScore <= $lowThreshold) {
                $riskLevel = 'low';
            } elseif ($totalScore <= $highThreshold) {
                $riskLevel = 'medium';
            } else {
                $riskLevel = 'high';
            }



            // Snapshot label fungsi bahan: angka -> teks, supaya branching form
            // vendor tidak ikut berubah kalau config label diupdate di kemudian hari.
            $functionLabel = config('risk_assessment.material_functions.' . $scoreD);

            $qualification = VendorQualification::updateOrCreate(
                ['vendor_application_id' => $application->id],
                [
                    'score_safety_efficacy_doc' => $autoScores['doc_score'],
                    'score_safety_efficacy_attr' => (int) $data['score_safety_efficacy_attr'],
                    'score_safety_efficacy' => $scoreA,
                    'score_availability_trace' => $autoScores['traceability_score'],
                    'score_availability_type' => $autoScores['supplier_type_score'],
                    'score_availability' => $scoreB,
                    'score_detectability_country' => (int) $data['score_detectability_country'],
                    'score_detectability_warning' => (int) $data['score_detectability_warning'],
                    'score_detectability' => $scoreC,
                    'score_probability_function' => $scoreD,
                    'material_function_label' => $functionLabel,
                    'score_probability' => $scoreD,
                    'qa_pharmacist_id' => Auth::id(),
                    'qa_manager_id' => $data['qa_manager_id'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]
            );

            $workflow->transition(
                $application,
                VendorApplication::STATUS_RISK_ASSESSED,
                'risk_assessment_completed',
                [
                    'approved_by' => null,
                    'approved_at' => null,
                    'valid_until' => null,
                ],
                Auth::user(),
                [
                    'qualification_id' => $qualification->id,
                    'total_score' => $qualification->total_score,
                    'risk_level' => $qualification->risk_level,
                    'audit_type' => $qualification->audit_type,
                    'auto_scores' => $autoScores,
                ]
            );

            if ($qualification->risk_level === 'low') {
                $workflow->transition(
                    $application,
                    VendorApplication::STATUS_APPROVED,
                    'risk_assessment_low_approved',
                    [
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'valid_until' => now()->addYears(5)->toDateString(),
                    ],
                    Auth::user(),
                    ['qualification_id' => $qualification->id]
                );

                return $qualification;
            }

            $workflow->transition(
                $application,
                VendorApplication::STATUS_AUDIT_REQUIRED,
                'risk_assessment_audit_required',
                [],
                Auth::user(),
                [
                    'qualification_id' => $qualification->id,
                    'audit_type' => $qualification->audit_type,
                ]
            );
            return $qualification;
        });

        $notificationService->riskAssessmentResult($application, $qualification);

        return redirect()
            ->route('qa.risk-assessment.index')
            ->with('success', 'Risk assessment berhasil disimpan dan status vendor diperbarui.');
    }

    public function createExisting(int $application_id): View
    {
        $this->authorizeQaAccess();

        $application = VendorApplication::with([
            'user',
            'general',
            'categories',
            'documents',
            'specBaku',
            'qualification',
            'products',
        ])->findOrFail($application_id);

        $autoScores = $this->automaticScores($application);

        return view('admin.risk_assesment.create_existing', [
            'application'   => $application,
            'qualification' => $application->qualification,
            'autoScores'    => $autoScores,
            'sections'      => config('risk_assessment.existing_sections', []),
            'thresholds'    => config('risk_assessment.existing.levels', []),
        ]);
    }

    public function storeExisting(
        StoreExistingRiskAssessmentRequest $request,
        int $application_id,
        VendorApplicationWorkflowService $workflow,
        VendorApplicationNotificationService $notificationService
    ) {
        $this->authorizeQaAccess();

        $data = $request->validated();
        abort_unless((int) $data['vendor_application_id'] === $application_id, 422);

        $application = VendorApplication::with(['qualification', 'user', 'general'])->findOrFail($application_id);

        $qualification = DB::transaction(function () use ($application, $data, $workflow) {
            $scoreA = (int) $data['score_safety_efficacy_doc']
                + (int) $data['score_complaint_side_effect']
                + (int) $data['score_safety_efficacy_attr'];

            $scoreB = (int) $data['score_pareto']
                + (int) $data['score_purchase_volume']
                + (int) $data['score_availability_type']
                + (int) $data['score_nie_status'];

            $scoreC = (int) $data['score_audit_year']
                + (int) $data['score_audit_result']
                + (int) $data['score_audit_response']
                + (int) $data['score_complaint_count'];

            $scoreD = (int) $data['score_material_grade']
                + (int) $data['score_facility_variation']
                + (int) $data['score_material_purpose'];

            $totalScore = ($scoreA + $scoreB) * ($scoreC + $scoreD);

            $lowMax      = (int) config('risk_assessment.existing.low_threshold', 84);
            $mediumMax   = (int) config('risk_assessment.existing.medium_threshold', 168);
            $highMax     = (int) config('risk_assessment.existing.high_threshold', 336);

            if ($totalScore <= $lowMax) {
                $riskLevel              = 'low';
                $auditType              = 'on_desk';
                $periodYears            = 7;
                $needsQualityAgreement  = false;
            } elseif ($totalScore <= $mediumMax) {
                $riskLevel              = 'medium';
                $auditType              = 'on_desk';
                $periodYears            = 5;
                $needsQualityAgreement  = false;
            } elseif ($totalScore <= $highMax) {
                $riskLevel              = 'high';
                $auditType              = 'on_site';
                $periodYears            = 3;
                $needsQualityAgreement  = false;
            } else {
                $riskLevel              = 'very_high';
                $auditType              = 'on_site';
                $periodYears            = 2;
                $needsQualityAgreement  = true;
            }

            $qualification = VendorQualification::updateOrCreate(
                ['vendor_application_id' => $application->id],
                [
                    'assessment_type'              => 'existing',
                    'score_safety_efficacy_doc'    => (int) $data['score_safety_efficacy_doc'],
                    'score_complaint_side_effect'  => (int) $data['score_complaint_side_effect'],
                    'score_safety_efficacy_attr'   => (int) $data['score_safety_efficacy_attr'],
                    'score_safety_efficacy'        => $scoreA,

                    'score_pareto'                 => (int) $data['score_pareto'],
                    'score_purchase_volume'        => (int) $data['score_purchase_volume'],
                    'score_availability_type'      => (int) $data['score_availability_type'],
                    'score_nie_status'             => (int) $data['score_nie_status'],
                    'score_availability'           => $scoreB,

                    'score_audit_year'             => (int) $data['score_audit_year'],
                    'score_audit_result'           => (int) $data['score_audit_result'],
                    'score_audit_response'         => (int) $data['score_audit_response'],
                    'score_complaint_count'        => (int) $data['score_complaint_count'],
                    'score_detectability'          => $scoreC,

                    'score_material_grade'         => (int) $data['score_material_grade'],
                    'score_facility_variation'     => (int) $data['score_facility_variation'],
                    'score_material_purpose'       => (int) $data['score_material_purpose'],
                    'score_probability'            => $scoreD,

                    'total_score'                  => $totalScore,
                    'risk_level'                   => $riskLevel,
                    'audit_type'                   => $auditType,
                    're_audit_period_years'        => $periodYears,
                    'needs_quality_agreement'      => $needsQualityAgreement,

                    'qa_pharmacist_id'             => Auth::id(),
                    'qa_manager_id'                => $data['qa_manager_id'] ?? null,
                    'notes'                        => $data['notes'] ?? null,
                ]
            );

            // Memicu alur rekualifikasi pada vendor
            $application->update([
                'type'                   => VendorApplication::TYPE_REKUALIFIKASI,
                'requalification_reason' => VendorApplication::REASON_QA_TRIGGER,
            ]);

            $workflow->transition(
                $application,
                VendorApplication::STATUS_AUDIT_REQUIRED,
                'existing_risk_assessment_completed',
                [],
                Auth::user(),
                [
                    'qualification_id'         => $qualification->id,
                    'assessment_type'          => 'existing',
                    'total_score'              => $totalScore,
                    'risk_level'               => $riskLevel,
                    'audit_type'               => $auditType,
                    're_audit_period_years'    => $periodYears,
                    'needs_quality_agreement'  => $needsQualityAgreement,
                ]
            );

            return $qualification;
        });

        return redirect()
            ->route('qa.risk-assessment.index', ['tab' => 'existing'])
            ->with(
                'success',
                'Risk assessment vendor eksisting berhasil disimpan (Skor: ' . $qualification->total_score . ', ' . strtoupper(str_replace('_', ' ', $qualification->risk_level)) . '). Silakan klik tombol "Buat Audit" untuk mengatur kuesioner atau jadwal audit.'
            );
    }

    private function automaticScores(VendorApplication $application): array
    {
        $docChecklist = $this->documentCompletenessChecklist($application);
        $docScore = $this->documentCompletenessScore($docChecklist);
        $traceabilityScore = $this->traceabilityScore($application);
        $supplierTypeScore = $this->supplierTypeScore($application);

        return [
            'doc_score' => $docScore,
            'doc_label' => $this->documentScoreLabel($docScore),
            'doc_checklist' => $docChecklist,
            'traceability_score' => $traceabilityScore,
            'traceability_label' => $traceabilityScore === 1 ? 'Lengkap' : 'Tidak Lengkap',
            'supplier_type_score' => $supplierTypeScore,
            'supplier_type_label' => $this->supplierTypeLabel($application),
        ];
    }

    private function documentCompletenessChecklist(VendorApplication $application): array
    {
        $specBaku = $application->specBaku;
        $general = $application->general;
        $documents = $application->documents;
        $halalNotRequired = optional($general)->sertifikat_halal === 'no';
        $hasIsoData = !empty(optional($general)->iso_certificates) || !empty(optional($general)->iso_other);

        return [
            [
                'label' => 'Sertifikat CDOB/GDP',
                'fulfilled' => !empty(optional($specBaku)->q5_document),
            ],
            [
                'label' => 'Surat Izin PBF',
                'fulfilled' => !empty(optional($specBaku)->pbf_document),
            ],
            [
                'label' => 'Sertifikat Halal PBF',
                'fulfilled' => $documents->contains('field_name', 'dok_sertifikat_halal') || $halalNotRequired,
            ],
            [
                'label' => 'Sertifikat ISO',
                'fulfilled' => $documents->contains('field_name', 'iso_certificate') || $hasIsoData,
            ],
            [
                'label' => 'NIB (Nomor Induk Berusaha)',
                'fulfilled' => $documents->contains('field_name', 'dok_nib'),
            ],
            [
                'label' => 'NPWP Perusahaan',
                'fulfilled' => $documents->contains('field_name', 'dok_npwp'),
            ],
        ];
    }

    private function documentCompletenessScore(array $checklist): int
    {
        $checks = array_map(function ($item) {
            return !empty($item['fulfilled']);
        }, $checklist);

        $available = count(array_filter($checks));

        if ($available === count($checks)) {
            return 1;
        }

        if ($available > 0) {
            return 3;
        }

        return 4;
    }

    private function traceabilityScore(VendorApplication $application): int
    {
        $product = $this->availabilityRiskProduct($application);

        if ($product && !empty($product->file_surat_path)) {
            return 1;
        }

        return 4;
    }

    private function supplierTypeScore(VendorApplication $application): int
    {
        $label = strtolower($this->supplierTypeLabel($application));

        if (strpos($label, 'manufaktur') !== false) {
            return 1;
        }

        if (strpos($label, 'distributor') !== false) {
            return 2;
        }

        if (strpos($label, 'repacker') !== false) {
            return 3;
        }

        return 4;
    }

    private function supplierTypeLabel(VendorApplication $application): string
    {
        $product = $this->availabilityRiskProduct($application);

        if ($product && !empty($product->rantai_pasok)) {
            return $product->rantai_pasok;
        }

        if (optional($application->specBaku)->q1_is_manufacturer === 'yes') {
            return 'Manufaktur';
        }

        $status = optional($application->general)->status_perusahaan;

        if ($status) {
            return ucwords(str_replace(['_', '-'], ' ', $status));
        }

        return 'Trader';
    }

    // Logika untuk menentukan produk beresiko
    private function availabilityRiskProduct(VendorApplication $application): ?VendorApplicationProduct
    {
        $products = $application->products ?? collect();

        if ($products->isEmpty()) {
            return null;
        }

        return $products->sortByDesc(function (VendorApplicationProduct $product) {
            return $this->availabilityPriorityScore($product);
        })->first();
    }

    private function availabilityPriorityScore(VendorApplicationProduct $product): int
    {
        return $this->supplierTypeScoreFromLabel($product->rantai_pasok) * 10
            + ($product->file_surat_path ? 0 : 1);
    }

    private function supplierTypeScoreFromLabel(?string $label): int
    {
        $label = strtolower(trim((string) $label));

        if (strpos($label, 'manufaktur') !== false) {
            return 1;
        }

        if (strpos($label, 'distributor') !== false) {
            return 2;
        }

        if (strpos($label, 'repacker') !== false) {
            return 3;
        }

        return 4;
    }

    private function documentScoreLabel(int $score): string
    {
        if ($score === 1) {
            return 'Lengkap';
        }

        return $score === 3 ? 'Kurang Lengkap' : 'Tidak Lengkap / N/A';
    }

    private function authorizeQaAccess(): void
    {
        $user = Auth::user();

        abort_unless($user && $user->can('qa-risk-list'), 403);
    }
}
