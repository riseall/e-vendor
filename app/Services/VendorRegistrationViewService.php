<?php

namespace App\Services;

use App\Models\VendorApplication;
use App\Models\User;

class VendorRegistrationViewService
{
    private VendorFileService $fileService;

    public function __construct(VendorFileService $fileService)
    {
        $this->fileService = $fileService;
    }

    private const RELATIONS = [
        'general',
        'products',
        'categories',
        'documents',
        'specBaku',
        'specVaria',
        'specTrans',
        'specKontraktor',
        'specPengujian',
        'specFacility',
        'specPelatihan',
        'specAgency',
    ];

    private const SPECIFIC_RELATIONS = [
        1 => 'specBaku',
        2 => 'specVaria',
        3 => 'specTrans',
        4 => 'specKontraktor',
        5 => 'specPengujian',
        6 => 'specFacility',
        7 => 'specPelatihan',
        8 => 'specAgency',
    ];

    private const CATEGORY_VIEWS = [
        1 => 'admin.registrasi.category.1-baku',
        2 => 'admin.registrasi.category.2-varia',
        3 => 'admin.registrasi.category.3-trans',
        4 => 'admin.registrasi.category.4-kontraktor',
        5 => 'admin.registrasi.category.5-pengujian',
        6 => 'admin.registrasi.category.6-facility',
        7 => 'admin.registrasi.category.7-pelatihan',
        8 => 'admin.registrasi.category.8-agency',
    ];

    public function currentApplication(User $user): ?VendorApplication
    {
        return VendorApplication::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                VendorApplication::STATUS_DRAFT,
                VendorApplication::STATUS_SUBMITTED,
                VendorApplication::STATUS_NEED_REVISION,
                VendorApplication::STATUS_VERIFIED,
                VendorApplication::STATUS_RISK_ASSESSED,
                VendorApplication::STATUS_AUDIT_REQUIRED,
                VendorApplication::STATUS_ON_HOLD,
                VendorApplication::STATUS_APPROVED,
                VendorApplication::STATUS_REJECTED,
            ])
            ->with(self::RELATIONS)
            ->latest()
            ->first();
    }

    public function viewData(?VendorApplication $application): array
    {
        $status = optional($application)->status;
        $revisionNotes = $this->revisionNotes($application);
        $selectedCategories = $application ? $application->getCategoryIds() : [];

        $isEditMode = request()->boolean('edit') && $status === VendorApplication::STATUS_APPROVED;

        return [
            'application' => $application,
            'hasDraft' => (bool) $application,
            'draftStep' => optional($application)->current_step ?: 1,
            'applicationId' => optional($application)->id,
            'applicationStatus' => $status,
            'isProfileMode' => $application && $status !== VendorApplication::STATUS_DRAFT,
            'isEditMode' => $isEditMode,
            'isReadOnly' => in_array($status, [
                VendorApplication::STATUS_SUBMITTED,
                VendorApplication::STATUS_VERIFIED,
                VendorApplication::STATUS_RISK_ASSESSED,
                VendorApplication::STATUS_AUDIT_REQUIRED,
                VendorApplication::STATUS_ON_HOLD,
                VendorApplication::STATUS_APPROVED,
                VendorApplication::STATUS_REJECTED,
            ], true) && !$isEditMode,
            'isRevisionMode' => $status === VendorApplication::STATUS_NEED_REVISION,
            'draft' => $this->draftData($application),
            'uploadedDocs' => $this->uploadedDocuments($application),
            'uploadedIsoCertificates' => $this->uploadedIsoCertificates($application),
            'revisionNotes' => $revisionNotes,
            'selectedCategories' => $selectedCategories,
            'selectedCategoryLabels' => collect($selectedCategories)
                ->map(fn(int $categoryId) => VendorApplication::CATEGORY_LABELS[$categoryId] ?? "Kategori {$categoryId}")
                ->values()
                ->all(),
            'selectedCategorySections' => collect($selectedCategories)
                ->filter(fn(int $categoryId) => isset(self::CATEGORY_VIEWS[$categoryId]))
                ->map(function (int $categoryId) {
                    return [
                        'id' => $categoryId,
                        'label' => VendorApplication::CATEGORY_LABELS[$categoryId],
                        'view' => self::CATEGORY_VIEWS[$categoryId],
                    ];
                })
                ->values()
                ->all(),
            'statusPresentation' => $this->statusPresentation($status),
        ];
    }

    private function statusPresentation(?string $status): array
    {
        switch ($status) {
            case VendorApplication::STATUS_SUBMITTED:
                return [
                    'label' => 'Menunggu Verifikasi',
                    'class' => 'primary',
                    'description' => 'Data sudah dikirim dan sedang diperiksa oleh tim pengadaan.',
                ];
            case VendorApplication::STATUS_NEED_REVISION:
                return [
                    'label' => 'Perlu Revisi',
                    'class' => 'warning',
                    'description' => 'Perbaiki field yang memiliki catatan, lalu kirim ulang permohonan.',
                ];
            case VendorApplication::STATUS_VERIFIED:
                return [
                    'label' => 'Terverifikasi',
                    'class' => 'success',
                    'description' => 'Data registrasi sudah selesai diverifikasi dan menunggu risk assessment QA.',
                ];
            case VendorApplication::STATUS_RISK_ASSESSED:
                return [
                    'label' => 'Risk Assessed',
                    'class' => 'info',
                    'description' => 'QA sudah menyelesaikan risk assessment vendor.',
                ];
            case VendorApplication::STATUS_AUDIT_REQUIRED:
                return [
                    'label' => 'Perlu Audit',
                    'class' => 'warning',
                    'description' => 'Vendor perlu mengikuti proses audit QA sebelum direkomendasikan.',
                ];
            case VendorApplication::STATUS_ON_HOLD:
                return [
                    'label' => 'On Hold',
                    'class' => 'warning',
                    'description' => 'Permohonan ditunda menunggu tindak lanjut atau evaluasi dari tim QA.',
                ];
            case VendorApplication::STATUS_APPROVED:
                return [
                    'label' => 'Terekomendasi',
                    'class' => 'success',
                    'description' => 'Vendor telah disetujui.',
                ];
            case VendorApplication::STATUS_REJECTED:
                return [
                    'label' => 'Ditolak',
                    'class' => 'danger',
                    'description' => 'Permohonan vendor tidak disetujui.',
                ];
            default:
                return [
                    'label' => 'Draft',
                    'class' => 'secondary',
                    'description' => 'Data registrasi belum dikirim.',
                ];
        }
    }

    private function draftData(?VendorApplication $application): array
    {
        if (!$application) {
            return [];
        }

        $draft = [
            'categories' => $application->getCategoryIds(),
            'general' => $application->general,
            'admin_note' => $application->admin_note,
            'revision_notes' => $application->revision_notes,
            'products' => $application->products
                ->mapWithKeys(function ($item) {
                    return [$item->erp_product_id => $item->toArray()];
                })
                ->toArray(),
        ];

        foreach (self::SPECIFIC_RELATIONS as $categoryId => $relation) {
            if (!in_array($categoryId, $draft['categories'], true) || !$application->{$relation}) {
                continue;
            }

            $draft = array_merge($draft, $application->{$relation}->toArray());
        }

        return $draft;
    }

    private function uploadedDocuments(?VendorApplication $application): array
    {
        if (!$application) {
            return [];
        }

        return $application->documents
            ->keyBy('field_name')
            ->map(function ($document) use ($application) {
                return [
                    'original_name' => $document->original_name,
                    'url' => $this->fileService->url($application, $document->file_path),
                ];
            })
            ->toArray();
    }

    private function uploadedIsoCertificates(?VendorApplication $application): array
    {
        if (!$application) {
            return [];
        }

        return $application->documents
            ->where('field_name', 'iso_certificate')
            ->map(function ($document) use ($application) {
                return [
                    'id' => $document->id,
                    'original_name' => $document->original_name,
                    'file_path' => $document->file_path,
                    'url' => $this->fileService->url($application, $document->file_path),
                ];
            })
            ->values()
            ->all();
    }

    private function revisionNotes(?VendorApplication $application): array
    {
        return collect(optional($application)->revision_notes ?: [])
            ->mapWithKeys(function ($revision) {
                $field = $revision['field'] ?? null;

                return $field ? [$field => $revision['note'] ?? '-'] : [];
            })
            ->toArray();
    }
}
