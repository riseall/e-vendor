<?php

namespace App\Console\Commands;

use App\Models\EvaluationSetting;
use App\Models\VendorApplication;
use App\Models\VendorEvaluation;
use App\Services\QadEvaluationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PullEvaluasiDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluasi:pull-qad-data {--month= : Bulan data evaluasi} {--year= : Tahun data evaluasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarik data evaluasi QAD (Delivery, Quality, Quantity) bulan lalu dan hitung skor bulanan per vendor';

    /**
     * Execute the console command.
     *
     * @param QadEvaluationService $qadService
     * @return int
     */
    public function handle(QadEvaluationService $qadService)
    {
        // Tentukan periode default: bulan lalu (sesuai PRD 8.2: Data ditarik tgl 5 untuk bulan sebelumnya)
        $monthOption = $this->option('month');
        $yearOption  = $this->option('year');

        if ($monthOption && $yearOption) {
            $month = (int) $monthOption;
            $year  = (int) $yearOption;
        } else {
            $lastMonth = Carbon::now()->subMonth();
            $month = (int) $lastMonth->format('n');
            $year  = (int) $lastMonth->format('Y');
        }

        $this->info("Memulai penarikan data evaluasi QAD untuk Periode: Bulan {$month} Tahun {$year}...");
        Log::info("Command evaluasi:pull-qad-data dijalankan untuk periode {$month}-{$year}");

        // Ambil semua vendor approved yang unik
        $approvedVendors = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)
            ->latest('approved_at')
            ->get()
            ->unique('user_id');

        if ($approvedVendors->isEmpty()) {
            $this->warn('Tidak ada vendor berstatus Approved yang ditemukan.');
            return 0;
        }

        $settings = EvaluationSetting::getSettings();
        $processedCount = 0;

        foreach ($approvedVendors as $app) {
            $vendorId = $app->user_id;

            try {
                $scores = $qadService->fetchQadScores($vendorId, $month, $year);

                $evaluation = VendorEvaluation::firstOrNew([
                    'vendor_id' => $vendorId,
                    'month'     => $month,
                    'year'      => $year,
                ]);

                // Update skor otomatis dari QAD
                $evaluation->delivery_score = $scores['delivery_score'] ?? 0;
                $evaluation->quality_score  = $scores['quality_score'] ?? 0;
                $evaluation->quantity_score = $scores['quantity_score'] ?? 0;

                // Nilai default untuk aspek manual jika baru dibuat
                if (!$evaluation->exists) {
                    $evaluation->complain_score = 100; // Default tanpa komplain jika belum ada komplain QA
                    $evaluation->incoming_material_score = 100;
                    $evaluation->safety_environment_score = 100;
                }

                $evaluation->calculateTotalAndCategory($settings);
                $evaluation->save();

                $processedCount++;
            } catch (\Exception $e) {
                Log::error("Gagal menarik data QAD vendor ID {$vendorId}: " . $e->getMessage());
                $this->warn("Gagal memproses Vendor ID {$vendorId}: " . $e->getMessage());
            }
        }

        $this->info("Penarikan data QAD selesai. Total {$processedCount} vendor berhasil diperbarui skor bulanannya.");
        Log::info("Command evaluasi:pull-qad-data berhasil memproses {$processedCount} vendor untuk periode {$month}-{$year}");

        return 0;
    }
}

