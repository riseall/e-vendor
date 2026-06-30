<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRiskAssessmentRequest;
use App\Models\User;
use App\Models\VendorApplication;
use App\Models\VendorQualification;
use App\Services\VendorApplicationNotificationService;
use App\Services\VendorApplicationWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorQualificationController extends Controller
{
    private const QA_ROLES = ['Super Admin', 'Admin IT', 'Quality Assurance', 'Apoteker', 'Specialist'];

    private const STATUS_OPTIONS = [
        'all' => 'Semua Status',
        'pending' => 'Belum Assessment',
        VendorApplication::STATUS_VERIFIED => 'Verified',
        VendorApplication::STATUS_RISK_ASSESSED => 'Risk Assessed',
        VendorApplication::STATUS_AUDIT_REQUIRED => 'Audit Required',
        VendorApplication::STATUS_APPROVED => 'Approved',
    ];

    public function index(Request $request): View
    {
        $this->authorizeQaAccess();

        $status = $request->input('status', 'all');
        $search = trim((string) $request->input('q', ''));

        $query = VendorApplication::query()
            ->with(['user', 'general', 'categories', 'qualification'])
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
                        $general->where('nama_perusahaan', 'like', "%{$search}%")
                            ->orWhere('email_perusahaan', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $applications = $query->paginate(10)->withQueryString();

        return view('admin.risk_assesment.index', [
            'applications' => $applications,
            'status' => $status,
            'search' => $search,
            'statusOptions' => self::STATUS_OPTIONS,
            'lowThreshold' => (int) config('risk_assessment.low_threshold', 88),
            'highThreshold' => (int) config('risk_assessment.high_threshold', 164),
        ]);
    }

    public function create(int $application_id): View
    {
        $this->authorizeQaAccess();

        $application = VendorApplication::with(['user', 'general', 'categories', 'documents', 'specBaku', 'qualification'])
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
            'qaManagers' => $this->qaManagers(),
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
            $application->loadMissing(['general', 'documents', 'specBaku']);
            $autoScores = $this->automaticScores($application);

            $scoreA = $autoScores['doc_score'] + (int) $data['score_safety_efficacy_attr'];
            $scoreB = $autoScores['traceability_score'] + $autoScores['supplier_type_score'];
            $scoreC = (int) $data['score_detectability_country'] + (int) $data['score_detectability_warning'];
            $scoreD = (int) $data['score_probability_function'];

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
                    'score_probability_function' => (int) $data['score_probability_function'],
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
                    'risk_level' => $qualification->risk_level,
                    'risk_rpn' => $qualification->total_score,
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

    private function qaManagers()
    {
        return User::role(self::QA_ROLES)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
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
        if (optional($application->specBaku)->q1_is_manufacturer === 'yes') {
            return 1;
        }

        if (optional($application->specBaku)->q2_is_sole_agent === 'yes' && optional($application->specBaku)->q2_auth_letter) {
            return 1;
        }

        return 4;
    }

    private function supplierTypeScore(VendorApplication $application): int
    {
        $label = strtolower($this->supplierTypeLabel($application));

        if (strpos($label, 'manufaktur') !== false || strpos($label, 'manufacturer') !== false) {
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
        if (optional($application->specBaku)->q1_is_manufacturer === 'yes') {
            return 'Manufaktur';
        }

        $status = optional($application->general)->status_perusahaan;

        if ($status) {
            return ucwords(str_replace(['_', '-'], ' ', $status));
        }

        return 'Trader';
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

        abort_unless($user && $user->hasAnyRole(self::QA_ROLES), 403);
    }
}
