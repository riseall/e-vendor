<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 1. Auto verify permohonan setelah 10 hari kalender
        $schedule->command('vendor-applications:auto-verify')->dailyAt('08:00');

        // 2. Reminder verifikasi ke pengadaan (H-3 dan H-1)
        $schedule->command('vendor-applications:remind-verification')->dailyAt('08:00');

        // 3. Soft delete draf lama (> 90 hari tidak aktif)
        $schedule->command('vendor-applications:clean-drafts')->weeklyOn(1, '00:00');

        // 4. Trigger tahunan rekualifikasi vendor (1 November 08:00 WIB)
        $schedule->command('vendor-applications:trigger-rekualifikasi')->cron('0 8 1 11 *');

        // 5. Cek kedaluwarsa CDOB (H-30) untuk trigger rekualifikasi
        $schedule->command('vendor-applications:check-cdob-expiry')->dailyAt('08:00');

        // 6. Reminder rekualifikasi ke vendor (H-30, H-14, H-7, H-3)
        $schedule->command('vendor-applications:remind-rekualifikasi')->dailyAt('08:00');

        // 7. Tarik data PO receipt QAD bulan lalu & hitung skor bulanan (Setiap tgl 5 pukul 01:00 WIB)
        $schedule->command('evaluasi:pull-qad-data')->cron('0 1 5 * *');

        // 8. Cek penurunan skor evaluasi >= 20% untuk trigger rekualifikasi (Setiap tgl 5 pukul 02:00 WIB)
        $schedule->command('evaluasi:check-drop-score')->cron('0 2 5 * *');

        // 9. Generate evaluasi tahunan final per vendor (1 Februari 01:00 WIB)
        $schedule->command('evaluasi:generate-annual')->cron('0 1 1 2 *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
