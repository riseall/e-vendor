<?php

namespace App\Services;

use App\Models\VendorApplication;
use App\Models\VendorApplicationCategory;
use App\Models\VendorApplicationDocument;
use App\Models\VendorApplicationGeneral;
use App\Models\VendorApplicationProduct;
use App\Models\VendorApplicationActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorRequalificationService
{
    /**
     * Cari permohonan rekualifikasi yang masih aktif/berjalan untuk vendor tertentu.
     *
     * @param int $userId
     * @return VendorApplication|null
     */
    public function getActiveRequalification(int $userId): ?VendorApplication
    {
        return VendorApplication::where('user_id', $userId)
            ->where('type', VendorApplication::TYPE_REKUALIFIKASI)
            ->whereNotIn('status', [
                VendorApplication::STATUS_APPROVED,
                VendorApplication::STATUS_REJECTED,
            ])
            ->latest('id')
            ->first();
    }

    /**
     * Buat draf rekualifikasi baru (baris baru) dengan relasi parent_id ke permohonan approved sebelumnya.
     *
     * @param VendorApplication $approvedApp Permohonan approved yang menjadi acuan rekualifikasi
     * @param string $reason Alasan pemicuan rekualifikasi
     * @param string|null $adminNote Catatan opsional dari admin
     * @param int|null $triggeredByUserId ID user yang memicu (null jika via cron/sistem)
     * @return VendorApplication
     */
    public function createRequalificationDraft(
        VendorApplication $approvedApp,
        string $reason,
        ?string $adminNote = null,
        ?int $triggeredByUserId = null
    ): VendorApplication {
        // 1. Guard clause: Jika sudah ada rekualifikasi aktif yang sedang berjalan, jangan buat duplikat
        $existing = $this->getActiveRequalification($approvedApp->user_id);
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($approvedApp, $reason, $adminNote, $triggeredByUserId) {
            // 2. Buat record baru di vendor_applications
            $newApp = new VendorApplication();
            $newApp->user_id = $approvedApp->user_id;
            $newApp->parent_id = $approvedApp->id;
            $newApp->type = VendorApplication::TYPE_REKUALIFIKASI;
            $newApp->status = VendorApplication::STATUS_DRAFT;
            $newApp->requalification_reason = $reason;
            $newApp->current_step = 1;
            $newApp->admin_note = $adminNote;
            $newApp->save();

            // Set nomor permohonan rekualifikasi unik
            $newApp->application_number = 'REK-' . date('Ym') . '-' . str_pad((string) $newApp->id, 5, '0', STR_PAD_LEFT);
            $newApp->save();

            // 3. Salin/Pre-fill Data Kategori
            $approvedApp->loadMissing(['categories', 'general', 'products', 'documents']);
            foreach ($approvedApp->categories as $cat) {
                VendorApplicationCategory::create([
                    'application_id' => $newApp->id,
                    'category_id'    => $cat->category_id,
                ]);
            }

            // 4. Salin/Pre-fill Data Formulir Umum (General)
            if ($approvedApp->general) {
                $generalClone = $approvedApp->general->replicate();
                $generalClone->application_id = $newApp->id;
                $generalClone->save();
            }

            // 5. Salin/Pre-fill Data Produk
            foreach ($approvedApp->products as $product) {
                $productClone = $product->replicate();
                $productClone->application_id = $newApp->id;
                $productClone->save();
            }

            // 6. Salin/Pre-fill Dokumen (menggunakan file_path yang sama di storage)
            foreach ($approvedApp->documents as $doc) {
                $docClone = $doc->replicate();
                $docClone->application_id = $newApp->id;
                $docClone->save();
            }

            // 7. Salin/Pre-fill Formulir Spesifik Kategori
            $specRelations = [
                'specBaku', 'specVaria', 'specTrans', 'specKontraktor',
                'specPengujian', 'specFacility', 'specPelatihan', 'specAgency'
            ];
            foreach ($specRelations as $rel) {
                $specModel = $approvedApp->$rel;
                if ($specModel) {
                    $specClone = $specModel->replicate();
                    $specClone->application_id = $newApp->id;
                    $specClone->save();
                }
            }

            // 8. Catat Audit Log
            VendorApplicationActivityLog::create([
                'application_id' => $newApp->id,
                'user_id'        => $triggeredByUserId,
                'action'         => 'rekualifikasi_draft_created',
                'status_before'  => null,
                'status_after'   => VendorApplication::STATUS_DRAFT,
                'ip_address'     => request() ? request()->ip() : '127.0.0.1',
                'metadata'       => [
                    'parent_id'  => $approvedApp->id,
                    'reason'     => $reason,
                    'admin_note' => $adminNote,
                ],
            ]);

            // 9. Kirim Notifikasi Email ke Vendor
            try {
                app(VendorApplicationNotificationService::class)->requalificationTriggered($newApp, $reason);
            } catch (\Exception $e) {
                Log::error("Gagal mengirim notifikasi requalificationTriggered untuk App ID {$newApp->id}: " . $e->getMessage());
            }

            return $newApp;
        });
    }
}

