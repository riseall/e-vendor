<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Models\VendorApplicationActivityLog;
use App\Models\VendorApplicationDocument;
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
    protected $description = 'Check CDOB certificate expiry within 30 days and trigger automatic requalification';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Memulai pengecekan kadaluarsa sertifikat CDOB...');

        // Query approved applications that have CDOB document or general CDOB field expiring
        $approvedApps = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)->get();

        $triggeredCount = 0;

        foreach ($approvedApps as $parentApp) {
            // Check if CDOB document exists and is expiring
            $cdobDoc = VendorApplicationDocument::where('application_id', $parentApp->id)
                ->where(function ($q) {
                    $q->where('field_name', 'like', '%cdob%')
                        ->orWhere('document_type', 'like', '%cdob%');
                })
                ->first();

            // Also check specBaku / general CDOB expiry if present
            $specBaku = $parentApp->specBaku;
            $isCdobExpiring = false;

            if ($specBaku && !empty($specBaku->cdob_valid_until)) {
                $expiryDate = \Carbon\Carbon::parse($specBaku->cdob_valid_until);
                if ($expiryDate->diffInDays(now(), false) >= -30) {
                    $isCdobExpiring = true;
                }
            }

            if ($isCdobExpiring) {
                $parentApp->update([
                    'requalification_reason' => VendorApplication::REASON_CDOB_EXPIRY,
                ]);

                VendorApplicationActivityLog::create([
                    'application_id' => $parentApp->id,
                    'user_id'        => null,
                    'action'         => 'AUTO_TRIGGER_REKUALIFIKASI_CDOB',
                    'status_before'  => VendorApplication::STATUS_APPROVED,
                    'status_after'   => VendorApplication::STATUS_APPROVED,
                    'ip_address'     => '127.0.0.1',
                    'metadata'       => [
                        'reason' => 'cdob_expiry',
                    ],
                ]);

                $triggeredCount++;
                $this->info("Rekualifikasi CDOB dipicu untuk App ID: {$parentApp->id}");
            }
        }

        $this->info("Pengecekan CDOB selesai. Total Rekualifikasi dipicu: {$triggeredCount}");

        return 0;
    }
}
