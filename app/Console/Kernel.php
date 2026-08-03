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
        $schedule->command('vendor-applications:auto-verify')->dailyAt('08:00');
        $schedule->command('vendor-applications:remind-verification')->dailyAt('08:00');
        $schedule->command('vendor-applications:clean-drafts')->weeklyOn(1, '00:00');
        $schedule->command('vendor-applications:trigger-rekualifikasi')->cron('0 8 1 11 *');
        $schedule->command('vendor-applications:check-cdob-expiry')->dailyAt('08:00');
        $schedule->command('vendor-applications:remind-rekualifikasi')->dailyAt('08:00');
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
