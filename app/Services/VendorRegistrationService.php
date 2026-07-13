<?php

namespace App\Services;

use App\Models\{VendorApplication, VendorApplicationGeneral, VendorApplicationProduct, VendorApplicationDocument};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendorRegistrationService
{
    private VendorFileService $fileService;

    public function __construct(VendorFileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function saveFormUmum(array $data, int $vendorApplicationId, string $action): array
    {
        return DB::transaction(function () use ($data, $vendorApplicationId, $action) {
            $application = VendorApplication::findOrFail($vendorApplicationId);

            if (!in_array($application->status, [
                VendorApplication::STATUS_DRAFT,
                VendorApplication::STATUS_NEED_REVISION,
            ])) {
                throw ValidationException::withMessages([
                    'application_id' => 'Permohonan yang sudah dikirim tidak dapat diubah.',
                ]);
            }

            $application->update([
                'status' => $application->status,
                'current_step' => max((int) $application->current_step, 8)
            ]);

            // Save General Info
            VendorApplicationGeneral::updateOrCreate(
                ['application_id' => $vendorApplicationId],
                $data
            );

            // Process Products 
            if (!empty($data['products'])) {
                $this->processProducts($data['products'], $application);
            }

            // Process Documents
            $documentFields = ['dok_nib', 'dok_npwp', 'dok_company_profile', 'dok_struktur_org', 'dok_sertifikat_halal', 'dok_akte_pendirian', 'dok_akte_direksi', 'dok_sppkp', 'dok_ktp_pj', 'dok_pernyataan_keaslian', 'dok_pakta_integritas', 'dok_bebas_perkara'];
            $this->processDocuments($data, $documentFields, $application);

            // Process ISO certificates (multi-file)
            $this->processIsoCertificates($data, $application);

            return ['success' => true, 'application_id' => $vendorApplicationId];
        });
    }

    private function processProducts(array $products, VendorApplication $application): void
    {
        $appId = $application->id;
        $incomingErpIds = collect($products)
            ->pluck('erp_product_id')
            ->filter()
            ->values()
            ->all();

        $removedProducts = VendorApplicationProduct::where('application_id', $appId)
            ->whereNotIn('erp_product_id', $incomingErpIds)
            ->get();

        foreach ($removedProducts as $removedProduct) {
            foreach (['file_surat', 'gmp_file', 'tkdn_file', 'sni_file', 'halal_file', 'bse_tse_file'] as $field) {
                $this->fileService->deactivate(
                    $application,
                    'product',
                    $field,
                    $removedProduct->id
                );
            }
            $removedProduct->delete();
        }

        foreach ($products as $index => $pData) {
            $product = VendorApplicationProduct::updateOrCreate(
                [
                    'application_id' => $appId,
                    'erp_product_id' => $pData['erp_product_id'],
                ],
                [
                    'product_name'   => $pData['product_name'],
                    'manufaktur'     => $pData['manufaktur'],
                    'negara'         => $pData['negara'] ?? null,
                    'rantai_pasok'   => $pData['rantai_pasok'],
                    'has_tkdn'       => $pData['has_tkdn'],
                    'tkdn_value'     => null,
                    'has_sni'        => $pData['has_sni'],
                    'sni_number'     => null,
                    'has_halal'      => $pData['has_halal'] ?? 'no',
                    'halal_number'   => null,
                    'has_bse_tse'    => $pData['has_bse_tse'] ?? 'no',
                ]
            );

            $this->processProductFile(
                $product,
                $application,
                $index,
                $pData,
                'file_surat',
                'file_surat_path',
                'existing_file_surat',
                true
            );
            $this->processProductFile(
                $product,
                $application,
                $index,
                $pData,
                'gmp_file',
                'gmp_file_path',
                'existing_gmp_file',
                true
            );
            $this->processProductFile(
                $product,
                $application,
                $index,
                $pData,
                'tkdn_file',
                'tkdn_file_path',
                'existing_tkdn_file',
                $pData['has_tkdn'] === 'yes'
            );
            $this->processProductFile(
                $product,
                $application,
                $index,
                $pData,
                'sni_file',
                'sni_file_path',
                'existing_sni_file',
                $pData['has_sni'] === 'yes'
            );
            $this->processProductFile(
                $product,
                $application,
                $index,
                $pData,
                'halal_file',
                'halal_file_path',
                'existing_halal_file',
                ($pData['has_halal'] ?? 'no') === 'yes'
            );
            $this->processProductFile(
                $product,
                $application,
                $index,
                $pData,
                'bse_tse_file',
                'bse_tse_file_path',
                'existing_bse_tse_file',
                ($pData['has_bse_tse'] ?? 'no') === 'yes'
            );
        }
    }

    private function processProductFile(
        VendorApplicationProduct $product,
        VendorApplication $application,
        $index,
        array $data,
        string $input,
        string $column,
        string $existingInput,
        bool $enabled
    ): void {
        $currentPath = $product->{$column};

        if (!$enabled) {
            $this->fileService->deactivate(
                $application,
                'product',
                $input,
                $product->id
            );
            $product->update([$column => null]);
            return;
        }

        if (request()->hasFile("products.{$index}.{$input}")) {
            $path = $this->fileService->store(
                $application,
                request()->file("products.{$index}.{$input}"),
                $input === 'file_surat' ? 'vendor_products' : 'vendor_products/certificates',
                'product',
                $input,
                $product->id,
                Auth::user()
            );
            $product->update([$column => $path]);
            return;
        }

        if (!empty($data[$existingInput])) {
            $product->update([$column => $data[$existingInput]]);
        }
    }

    private function processDocuments(array $data, array $fields, VendorApplication $application): void
    {
        foreach ($fields as $field) {
            if (request()->hasFile($field)) {
                $file = request()->file($field);
                $path = $this->fileService->store(
                    $application,
                    $file,
                    'vendor_docs/general',
                    'general_document',
                    $field,
                    null,
                    Auth::user()
                );

                VendorApplicationDocument::updateOrCreate(
                    ['application_id' => $application->id, 'field_name' => $field],
                    [
                        'original_name' => $file->getClientOriginalName(),
                        'file_path'     => $path,
                        'file_size'     => $file->getSize(),
                        'mime_type'     => $file->getMimeType(),
                    ]
                );
            }
        }
    }

    private function processIsoCertificates(array $data, VendorApplication $application): void
    {
        $fieldName = 'iso_certificate';

        // existing_iso_files tidak masuk validated() (tidak ada di rules FormRequest),
        // jadi baca langsung dari request()->input() — sama seperti iso_files di bawah.
        if (request()->has('existing_iso_files')) {
            $kept = collect((array) request()->input('existing_iso_files'))
                ->filter()
                ->values()
                ->all();

            $application->documents()
                ->where('field_name', $fieldName)
                ->whereNotIn('file_path', $kept)
                ->delete();
        }

        foreach ((array) request()->file('iso_files', []) as $file) {
            if (!$file) {
                continue;
            }

            $path = $this->fileService->store(
                $application,
                $file,
                'vendor_docs/iso',
                'general_document',
                $fieldName,
                null,
                Auth::user()
            );

            VendorApplicationDocument::create([
                'application_id' => $application->id,
                'field_name'     => $fieldName,
                'original_name'  => $file->getClientOriginalName(),
                'file_path'      => $path,
                'file_size'      => $file->getSize(),
                'mime_type'      => $file->getMimeType(),
            ]);
        }
    }
}
