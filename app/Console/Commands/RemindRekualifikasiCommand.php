<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Models\VendorApplicationActivityLog;
use App\Services\VendorApplicationNotificationService;
use Carbon\Carbon;
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
     * @param VendorApplicationNotificationService $notificationService
     * @return int
     */
    public function handle(VendorApplicationNotificationService $notificationService)
    {
        $this->info('Memulai pengecekan reminder Rekualifikasi...');

        $pendingRequalifications = VendorApplication::where('type', VendorApplication::TYPE_REKUALIFIKASI)
            ->whereIn('status', [VendorApplication::STATUS_DRAFT, VendorApplication::STATUS_NEED_REVISION])
            ->with(['user', 'parent', 'general'])
            ->get();

        $remindedCount = 0;
        $intervals = [30, 14, 7, 3];

        foreach ($pendingRequalifications as $app) {
            // Hitung sisa hari berdasarkan valid_until pada induk (parent) approved
            $daysRemaining = null;
            if ($app->parent && $app->parent->valid_until) {
                $daysRemaining = (int) now()->diffInDays(Carbon::parse($app->parent->valid_until), false);
            }

            // Jika valid_until ada, cek interval H-30, H-14, H-7, H-3
            $shouldRemind = false;
            if ($daysRemaining !== null) {
                if (in_array($daysRemaining, $intervals, true)) {
                    $shouldRemind = true;
                }
            } else {
                // Fallback jika tidak ada valid_until: cek interval hari sejak draf dibuat (misal 14 atau 30 hari)
                $daysPending = (int) now()->diffInDays($app->created_at);
                if (in_array($daysPending, [7, 14, 30], true)) {
                    $shouldRemind = true;
                }
            }

            if (!$shouldRemind) {
                continue;
            }

            // Cek apakah sudah pernah dikirimi reminder pada hari ini
            $alreadyRemindedToday = VendorApplicationActivityLog::where('application_id', $app->id)
                ->where('action', 'REMIND_REKUALIFIKASI_VENDOR')
                ->whereDate('created_at', today())
                ->exists();

            if ($alreadyRemindedToday) {
                continue;
            }

            // Kirim notifikasi email ke vendor
            try {
                $reasonLabel = VendorApplication::REASON_LABELS[$app->requalification_reason] ?? ($app->requalification_reason ?: 'Rekualifikasi Berkala');
                $notificationService->requalificationTriggered($app, $reasonLabel);
            } catch (\Exception $e) {
                $this->warn("Gagal mengirim email reminder ke App ID {$app->id}: " . $e->getMessage());
            }

            // Log activity
            VendorApplicationActivityLog::create([
                'application_id' => $app->id,
                'user_id'        => null,
                'action'         => 'REMIND_REKUALIFIKASI_VENDOR',
                'status_before'  => $app->status,
                'status_after'   => $app->status,
                'ip_address'     => '127.0.0.1',
                'metadata'       => [
                    'days_remaining' => $daysRemaining,
                    'vendor_email'   => optional($app->user)->email,
                ],
            ]);

            $remindedCount++;
            $this->info("Reminder rekualifikasi berhasil dikirim ke vendor: " . optional($app->user)->email . " (App ID: {$app->id})");
        }

        $this->info("Proses reminder selesai. Total reminder terkirim: {$remindedCount}");

        return 0;
    }
}
