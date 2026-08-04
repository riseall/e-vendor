<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Models\VendorApplicationActivityLog;
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
     * @return int
     */
    public function handle()
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
            $app->update([
                'type'                   => VendorApplication::TYPE_REKUALIFIKASI,
                'requalification_reason' => VendorApplication::REASON_EXPIRED_PERIOD,
            ]);
            if ($app->qualification) {
                $app->qualification()->delete();
            }

            // Log activity
            VendorApplicationActivityLog::create([
                'application_id' => $app->id,
                'user_id'        => null,
                'action'         => 'AUTO_TRIGGER_REKUALIFIKASI_EXPIRED',
                'status_before'  => VendorApplication::STATUS_APPROVED,
                'status_after'   => VendorApplication::STATUS_APPROVED,
                'ip_address'     => '127.0.0.1',
                'metadata'       => [
                    'valid_until' => $app->valid_until ? $app->valid_until->toDateString() : null,
                ],
            ]);

            $triggeredCount++;
            $this->info("Rekualifikasi otomatis dipicu untuk vendor: ID {$app->user_id} (App ID: {$app->id})");
        }

        $this->info("Pengecekan selesai. Total Rekualifikasi baru dipicu: {$triggeredCount}");

        return 0;
    }
}
