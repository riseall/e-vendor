<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\EvaluasiAnnualController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateAnnualVendorEvaluationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluasi:generate-annual {--year= : Tahun evaluasi yang akan digenerate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate / kalkulasi evaluasi tahunan vendor secara otomatis (maksimal 31 Januari tahun berikutnya)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $yearInput = $this->option('year');

        if ($yearInput) {
            $year = (int) $yearInput;
        } else {
            // Jika dijalankan otomatis di bulan Januari, default menghitung tahun kemarin (Y - 1)
            $currentMonth = (int) date('n');
            $year = ($currentMonth === 1) ? ((int) date('Y') - 1) : (int) date('Y');
        }

        $this->info("Memulai proses auto-generate evaluasi tahunan vendor untuk Tahun: {$year}...");
        Log::info("Command evaluasi:generate-annual dijalankan untuk tahun {$year}");

        $result = EvaluasiAnnualController::computeAnnualForYear($year);

        if (!$result['success']) {
            $this->warn($result['message']);
            return 0;
        }

        $count = $result['count'];
        $this->info("Berhasil! {$count} vendor berhasil dihitung evaluasi tahunannya untuk tahun {$year}.");
        Log::info("Command evaluasi:generate-annual berhasil memproses {$count} vendor untuk tahun {$year}");

        return 0;
    }
}
