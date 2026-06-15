<?php

namespace App\Services;

use App\Models\{VendorApplication, VendorApplicationGeneral, VendorApplicationProduct, VendorApplicationDocument};
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Validation\ValidationException;

class VendorRegistrationService
{
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
                $this->processProducts($data['products'], $vendorApplicationId);
            }

            // Process Documents
            $documentFields = ['dok_nib', 'dok_npwp', 'dok_company_profile', 'dok_struktur_org', 'dok_sertifikat_halal', 'dok_akte_pendirian', 'dok_akte_direksi', 'dok_sppkp', 'dok_ktp_pj', 'dok_pernyataan_keaslian', 'dok_pakta_integritas', 'dok_bebas_perkara'];
            $this->processDocuments($data, $documentFields, $vendorApplicationId);

            return ['success' => true, 'application_id' => $vendorApplicationId];
        });
    }

    private function processProducts(array $products, int $appId): void
    {
        $incomingErpIds = collect($products)
            ->pluck('erp_product_id')
            ->filter()
            ->values()
            ->all();

        VendorApplicationProduct::where('application_id', $appId)
            ->whereNotIn('erp_product_id', $incomingErpIds)
            ->delete();

        foreach ($products as $index => $pData) {
            $product = VendorApplicationProduct::updateOrCreate(
                [
                    'application_id' => $appId,
                    'erp_product_id' => $pData['erp_product_id'],
                ],
                [
                    'product_name'   => $pData['product_name'],
                    'manufaktur'     => $pData['manufaktur'],
                    'rantai_pasok'   => $pData['rantai_pasok'],
                    'has_tkdn'       => $pData['has_tkdn'],
                    'tkdn_value'     => null,
                    'has_sni'        => $pData['has_sni'],
                    'sni_number'     => null,
                    'has_halal'      => $pData['has_halal'] ?? 'no',
                    'halal_number'   => null,
                ]
            );

            $this->processProductFile(
                $product,
                $index,
                $pData,
                'file_surat',
                'file_surat_path',
                'existing_file_surat',
                true
            );
            $this->processProductFile(
                $product,
                $index,
                $pData,
                'tkdn_file',
                'tkdn_file_path',
                'existing_tkdn_file',
                $pData['has_tkdn'] === 'yes'
            );
            $this->processProductFile(
                $product,
                $index,
                $pData,
                'sni_file',
                'sni_file_path',
                'existing_sni_file',
                $pData['has_sni'] === 'yes'
            );
            $this->processProductFile(
                $product,
                $index,
                $pData,
                'halal_file',
                'halal_file_path',
                'existing_halal_file',
                ($pData['has_halal'] ?? 'no') === 'yes'
            );
        }
    }

    private function processProductFile(
        VendorApplicationProduct $product,
        $index,
        array $data,
        string $input,
        string $column,
        string $existingInput,
        bool $enabled
    ): void {
        $currentPath = $product->{$column};

        if (!$enabled) {
            if ($currentPath) {
                Storage::disk('public')->delete($currentPath);
            }

            $product->update([$column => null]);
            return;
        }

        if (request()->hasFile("products.{$index}.{$input}")) {
            if ($currentPath) {
                Storage::disk('public')->delete($currentPath);
            }

            $path = $this->storeFile(
                request()->file("products.{$index}.{$input}"),
                $input === 'file_surat' ? 'vendor_products' : 'vendor_products/certificates',
                $input . '_'
            );
            $product->update([$column => $path]);
            return;
        }

        if (!empty($data[$existingInput])) {
            $product->update([$column => $data[$existingInput]]);
        }
    }

    private function processDocuments(array $data, array $fields, int $appId): void
    {
        foreach ($fields as $field) {
            if (request()->hasFile($field)) {
                $file = request()->file($field);
                $path = $this->storeFile($file, 'vendor_docs/general', $field . '_');

                VendorApplicationDocument::updateOrCreate(
                    ['application_id' => $appId, 'field_name' => $field],
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

    private function storeFile($file, $dir, $prefix): string
    {
        $name = $prefix . uniqid() . '.' . $file->getClientOriginalExtension();
        return Storage::disk('public')->putFileAs($dir, $file, $name);
    }
}
