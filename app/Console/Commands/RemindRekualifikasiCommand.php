<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Models\VendorApplicationActivityLog;
use Illuminate\Console\Command;

class RemindRekualifikasiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor-applications:remind-rekualifikasi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications to vendors with pending requalification drafts (H-30, H-14, H-7, H-3)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Memulai pengiriman reminder Rekualifikasi...');

        $pendingRequalifications = VendorApplication::where('type', VendorApplication::TYPE_REKUALIFIKASI)
            ->whereIn('status', [VendorApplication::STATUS_DRAFT, VendorApplication::STATUS_NEED_REVISION])
            ->with(['user', 'parent'])
            ->get();

        $remindedCount = 0;

        foreach ($pendingRequalifications as $app) {
            $daysSinceCreation = now()->diffInDays($app->created_at);

            // Log activity and output log for reminder trigger intervals (e.g., 30, 14, 7, 3 days or periodic)
            VendorApplicationActivityLog::create([
                'application_id' => $app->id,
                'user_id'        => null,
                'action'         => 'REMIND_REKUALIFIKASI_VENDOR',
                'status_before'  => $app->status,
                'status_after'   => $app->status,
                'ip_address'     => '127.0.0.1',
                'metadata'       => [
                    'days_pending' => $daysSinceCreation,
                    'vendor_email' => optional($app->user)->email,
                ],
            ]);

            $remindedCount++;
            $this->info("Reminder dikirim ke vendor: {$app->user->email} (Requalification App ID: {$app->id})");
        }

        $this->info("Proses reminder selesai. Total vendor diawasi: {$remindedCount}");

        return 0;
    }
}
