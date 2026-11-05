<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Models\VendorApplicationDocument;
use App\Services\VendorRequalificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckCDOBExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor-applications:check-cdob-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check CDOB certificate expiry within 30 days and trigger automatic requalification draft';

    /**
     * Execute the console command.
     *
     * @param VendorRequalificationService $requalificationService
     * @return int
     */
    public function handle(VendorRequalificationService $requalificationService)
    {
        $this->info('Memulai pengecekan kadaluarsa sertifikat CDOB...');

        // Query approved applications
        $approvedApps = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)
            ->with(['specBaku', 'documents'])
            ->get();

        $triggeredCount = 0;

        foreach ($approvedApps as $parentApp) {
            // Guard clause: jika sudah ada rekualifikasi aktif, lewati
            if ($requalificationService->getActiveRequalification($parentApp->user_id)) {
                continue;
            }

            $isCdobExpiring = false;
            $specBaku = $parentApp->specBaku;

            // 1. Cek tanggal q5_valid_until (CDOB di specBaku) atau cdob_valid_until
            $expiryString = null;
            if ($specBaku) {
                $expiryString = $specBaku->q5_valid_until ?: ($specBaku->cdob_valid_until ?? null);
            }

            if (!empty($expiryString)) {
                $expiryDate = Carbon::parse($expiryString);
                $daysRemaining = (int) now()->diffInDays($expiryDate, false);
                // Jika masa berlaku tinggal 30 hari atau sudah lewat
                if ($daysRemaining <= 30) {
                    $isCdobExpiring = true;
                }
            }

            if ($isCdobExpiring) {
                $newDraft = $requalificationService->createRequalificationDraft(
                    $parentApp,
                    VendorApplication::REASON_CDOB_EXPIRY,
                    'Pemicuan otomatis karena masa berlaku sertifikat CDOB mendekati kedaluwarsa (<= 30 hari)'
                );

                $triggeredCount++;
                $this->info("Rekualifikasi CDOB dipicu untuk Vendor ID: {$parentApp->user_id} (Draf baru App ID: {$newDraft->id})");
            }
        }

        $this->info("Pengecekan CDOB selesai. Total Rekualifikasi dipicu: {$triggeredCount}");

        return 0;
    }
}
