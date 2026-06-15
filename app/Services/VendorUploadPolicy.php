<?php

namespace App\Services;

use App\Models\VendorApplicationFile;
use Illuminate\Foundation\Http\FormRequest;

class VendorUploadPolicy
{
    public const MAX_FILE_KB = 5120;
    public const MAX_APPLICATION_BYTES = 50 * 1024 * 1024;

    public static function fileRule(): string
    {
        return 'file|mimes:pdf,jpg,jpeg,png|max:' . self::MAX_FILE_KB;
    }

    public static function validateTotalSize(FormRequest $request, $validator): void
    {
        $uploadedBytes = collect($request->allFiles())
            ->flatten()
            ->filter()
            ->sum(function ($file) {
                return method_exists($file, 'getSize') ? (int) $file->getSize() : 0;
            });

        $uploadedFields = self::uploadedFieldNames($request->allFiles());

        $existingBytes = VendorApplicationFile::where(
            'application_id',
            $request->input('application_id')
        )
            ->where('is_current', true)
            ->when($uploadedFields, function ($query) use ($uploadedFields) {
                $query->whereNotIn('field_name', $uploadedFields);
            })
            ->sum('file_size');

        if (($existingBytes + $uploadedBytes) > self::MAX_APPLICATION_BYTES) {
            $validator->errors()->add(
                'documents',
                'Total ukuran dokumen dalam satu permohonan maksimal 50 MB.'
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
