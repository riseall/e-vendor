<?php

namespace App\Services;

use App\Models\VendorApplicationFile;
use Illuminate\Foundation\Http\FormRequest;

class VendorUploadPolicy
{
    public const MAX_FILE_KB = 5120;
    public const MAX_APPLICATION_BYTES = 200 * 1024 * 1024;

    public static function fileRule(): string
    {
        return 'file|mimes:pdf,jpg,jpeg,png|max:' . self::MAX_FILE_KB;
    }

    public static function validateTotalSize(FormRequest $request, $validator): void
    {
        $totalBytes = 0;
        $countedPaths = [];

        $walk = function ($items) use (&$walk, &$totalBytes, &$countedPaths) {
            foreach ($items as $key => $item) {
                if (is_array($item)) {
                    $walk($item);
                    continue;
                }
                
                // Skip 'existing_xxx' if 'xxx' is provided (meaning the file is being replaced)
                if (is_string($key) && str_starts_with($key, 'existing_')) {
                    $baseKey = substr($key, 9);
                    if (!empty($items[$baseKey])) {
                        continue; // File replaced, don't count the old one
                    }
                }

                if ($item instanceof \Illuminate\Http\UploadedFile) {
                    $totalBytes += $item->getSize();
                } elseif (is_string($item) && !empty($item)) {
                    if (isset($countedPaths[$item])) continue;

                    // Check if it's a temp file (ajax-auto-upload)
                    if (str_starts_with($item, 'temp_vendor/')) {
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($item)) {
                            $totalBytes += \Illuminate\Support\Facades\Storage::disk('public')->size($item);
                            $countedPaths[$item] = true;
                        }
                    }
                    // Check if it's an existing file
                    elseif (str_starts_with($item, 'vendor_documents/') || str_starts_with($item, 'vendor_products/') || str_starts_with($item, 'vendor_iso/') || str_starts_with($item, 'vendor_')) {
                        if (\Illuminate\Support\Facades\Storage::disk(\App\Services\VendorFileService::DISK)->exists($item)) {
                            $totalBytes += \Illuminate\Support\Facades\Storage::disk(\App\Services\VendorFileService::DISK)->size($item);
                            $countedPaths[$item] = true;
                        }
                    }
                }
            }
        };

        $walk($request->all());

        if ($totalBytes > self::MAX_APPLICATION_BYTES) {
            $pathsDebug = implode(', ', array_keys($countedPaths));
            $validator->errors()->add(
                'documents',
                'Total ukuran dokumen dalam satu permohonan maksimal 50 MB. (Terhitung: ' . round($totalBytes / 1024 / 1024, 2) . ' MB) Paths: ' . substr($pathsDebug, 0, 200)
            );
        }
    }

    private static function uploadedFieldNames(array $files): array
    {
        $fields = [];

        $walk = function (array $items) use (&$walk, &$fields) {
            foreach ($items as $key => $value) {
                if (is_array($value)) {
                    $walk($value);
                    continue;
                }

                if ($value) {
                    $fields[] = (string) $key;
                }
            }
        };

        $walk($files);

        return array_values(array_unique($fields));
    }
}
