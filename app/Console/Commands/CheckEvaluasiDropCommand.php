<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Models\VendorEvaluation;
use App\Services\VendorRequalificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckEvaluasiDropCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluasi:check-drop-score {--month= : Bulan evaluasi yang dicek} {--year= : Tahun evaluasi yang dicek}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek penurunan skor evaluasi vendor (>= 20%) dan picu draf rekualifikasi otomatis';

    /**
     * Execute the console command.
     *
     * @param VendorRequalificationService $requalificationService
     * @return int
     */
    public function handle(VendorRequalificationService $requalificationService)
    {
        $monthOption = $this->option('month');
        $yearOption  = $this->option('year');

        if ($monthOption && $yearOption) {
            $currentMonth = (int) $monthOption;
            $currentYear  = (int) $yearOption;
        } else {
            $lastMonth = Carbon::now()->subMonth();
            $currentMonth = (int) $lastMonth->format('n');
            $currentYear  = (int) $lastMonth->format('Y');
        }

        // Tentukan periode sebelumnya
        $prevMonth = ($currentMonth === 1) ? 12 : ($currentMonth - 1);
        $prevYear  = ($currentMonth === 1) ? ($currentYear - 1) : $currentYear;

        $this->info("Memeriksa penurunan skor evaluasi: Periode {$currentMonth}-{$currentYear} vs {$prevMonth}-{$prevYear}...");
        Log::info("Command evaluasi:check-drop-score dijalankan untuk periode {$currentMonth}-{$currentYear}");

        $currentEvaluations = VendorEvaluation::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->get();

        $triggeredCount = 0;

        foreach ($currentEvaluations as $curr) {
            $vendorId = $curr->vendor_id;

            // Cari evaluasi periode sebelumnya
            $prev = VendorEvaluation::where('vendor_id', $vendorId)
                ->where('month', $prevMonth)
                ->where('year', $prevYear)
                ->first();

            if (!$prev || $prev->total_score <= 0 || $curr->total_score <= 0) {
                continue;
            }

            // Hitung persentase penurunan skor
            $dropPercent = (($prev->total_score - $curr->total_score) / $prev->total_score) * 100;

            if ($dropPercent >= 20.0) {
                // Cari aplikasi approved vendor terkini
                $approvedApp = VendorApplication::where('user_id', $vendorId)
                    ->where('status', VendorApplication::STATUS_APPROVED)
                    ->latest('approved_at')
                    ->first();

                if (!$approvedApp) {
                    continue;
                }

                // Guard: Jangan picu jika sudah ada rekualifikasi aktif
                if ($requalificationService->getActiveRequalification($vendorId)) {
                    continue;
                }

                $dropFormatted = number_format($dropPercent, 1);
                $note = "Penurunan performa skor bulanan sebesar {$dropFormatted}% (dari {$prev->total_score} ke {$curr->total_score})";

                $newDraft = $requalificationService->createRequalificationDraft(
                    $approvedApp,
                    VendorApplication::REASON_EVALUATION_DROP,
                    $note
                );

                $triggeredCount++;
                $this->info("Rekualifikasi dipicu untuk Vendor ID: {$vendorId} karena skor drop {$dropFormatted}% (App ID: {$newDraft->id})");
            }
        }

        $this->info("Pemeriksaan selesai. Total rekualifikasi dipicu: {$triggeredCount}");
        Log::info("Command evaluasi:check-drop-score selesai. Total dipicu: {$triggeredCount}");

        return 0;
    }
}

