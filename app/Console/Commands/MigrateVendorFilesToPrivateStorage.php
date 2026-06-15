<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Models\VendorApplicationFile;
use App\Services\VendorFileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateVendorFilesToPrivateStorage extends Command
{
    protected $signature = 'vendor-applications:migrate-files-private';
    protected $description = 'Move legacy vendor files from public storage to private storage and backfill file history.';

    private const SPECIFIC_FILE_FIELDS = [
        'specBaku' => ['q2_auth_letter'],
        'specVaria' => ['v1_auth_letter', 'v2_license_file', 'v6_kir_file'],
        'specTrans' => ['t1_safety_file', 't8_expert_cert'],
        'specKontraktor' => ['k1_cert_file'],
        'specPengujian' => [
            'l2_kan_file',
            'l2_cukb_file',
            'l2_iso17025_file',
            'l2_glp_file',
            'l2_bapeten_file',
        ],
        'specFacility' => ['f6_file'],
        'specAgency' => ['h1_association_file'],
    ];

    public function handle(): int
    {
        $moved = 0;
        $missing = 0;

        VendorApplication::withTrashed()
            ->with(array_merge(
                ['documents', 'products'],
                array_keys(self::SPECIFIC_FILE_FIELDS)
            ))
            ->chunkById(50, function ($applications) use (&$moved, &$missing) {
                foreach ($applications as $application) {
                    foreach ($application->documents as $document) {
                        $this->migratePath(
                            $application,
                            $document->file_path,
                            'general_document',
                            $document->field_name,
                            null,
                            $document->original_name,
                            $document->mime_type,
                            $document->file_size,
                            $moved,
                            $missing
                        );
                    }

                    foreach ($application->products as $product) {
                        foreach ([
                            'file_surat_path' => 'file_surat',
                            'tkdn_file_path' => 'tkdn_file',
                            'sni_file_path' => 'sni_file',
                            'halal_file_path' => 'halal_file',
                        ] as $column => $field) {
                            $this->migratePath(
                                $application,
                                $product->{$column},
                                'product',
                                $field,
                                $product->id,
                                null,
                                null,
                                null,
                                $moved,
                                $missing
                            );
                        }
                    }

                    foreach (self::SPECIFIC_FILE_FIELDS as $relation => $fields) {
                        $specific = $application->{$relation};
                        if (!$specific) {
                            continue;
                        }

                        foreach ($fields as $field) {
                            $this->migratePath(
                                $application,
                                $specific->{$field},
                                'specific',
                                $field,
                                null,
                                null,
                                null,
                                null,
                                $moved,
                                $missing
                            );
                        }
                    }
                }
            });

        $this->info("{$moved} file(s) moved to private storage; {$missing} path(s) not found.");

        return self::SUCCESS;
    }

    private function migratePath(
        VendorApplication $application,
        ?string $path,
        string $ownerType,
        string $field,
        ?int $ownerId,
        ?string $originalName,
        ?string $mimeType,
        $fileSize,
        int &$moved,
        int &$missing
    ): void {
        if (!$path) {
            return;
        }

        $private = Storage::disk(VendorFileService::DISK);
        $public = Storage::disk('public');

        if (!$private->exists($path)) {
            if (!$public->exists($path)) {
                $missing++;
                return;
            }

            $stream = $public->readStream($path);
            $private->put($path, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            $public->delete($path);
            $moved++;
        }

        VendorApplicationFile::firstOrCreate(
            [
                'application_id' => $application->id,
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'field_name' => $field,
                'file_path' => $path,
            ],
            [
                'original_name' => $originalName ?: basename($path),
                'mime_type' => $mimeType ?: $private->mimeType($path),
                'file_size' => $fileSize ?: $private->size($path),
                'is_current' => true,
            ]
        );
    }
}
