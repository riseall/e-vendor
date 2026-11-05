<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Services\VendorRequalificationService;
use Illuminate\Console\Command;

class TriggerRekualifikasiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor-applications:trigger-rekualifikasi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger automatic draft requalification for vendors whose qualification expires within 60 days or annual period';

    /**
     * Execute the console command.
     *
     * @param VendorRequalificationService $requalificationService
     * @return int
     */
    public function handle(VendorRequalificationService $requalificationService)
    {
        $this->info('Memulai pengecekan pemicuan Rekualifikasi otomatis...');

        // Find approved applications whose valid_until date is within 60 days from now or in past
        $thresholdDate = now()->addDays(60);

        $expiringApplications = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)
            ->whereNotNull('valid_until')
            ->where('valid_until', '<=', $thresholdDate)
            ->get();

        $triggeredCount = 0;

        foreach ($expiringApplications as $app) {
            // Guard clause: jangan trigger jika vendor sudah punya rekualifikasi aktif
            if ($requalificationService->getActiveRequalification($app->user_id)) {
                continue;
            }

            $newDraft = $requalificationService->createRequalificationDraft(
                $app,
                VendorApplication::REASON_EXPIRED_PERIOD,
                'Pemicuan otomatis karena masa berlaku kualifikasi berakhir dalam 60 hari'
            );

            $triggeredCount++;
            $this->info("Rekualifikasi otomatis dipicu untuk vendor ID: {$app->user_id} (Draf baru App ID: {$newDraft->id})");
        }

        $this->info("Pengecekan selesai. Total Rekualifikasi baru dipicu: {$triggeredCount}");

        return 0;
    }
}
