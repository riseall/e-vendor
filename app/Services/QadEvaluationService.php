<?php

namespace App\Services;

use App\Models::User;
use Illuminate\Support\Facades\Log;

class QadEvaluationService
{
    /**
     * Menarik & mengkalkulasi skor akhir QAD untuk 3 Aspek (Delivery, Quality, Quantity).
     *
     * Formula PRD:
     * - Delivery (20%): (Kedatangan tepat waktu / Total kedatangan) * 100
     * - Quality  (20%): (Barang released / Total kedatangan) * 100
     * - Quantity (20%): (Barang tepat jumlah / Total kedatangan) * 100
     *
     * @param int $vendorId
     * @param int $month
     * @param int $year
     * @return array
     */
    public function fetchQadScores($vendorId, $month, $year)
    {
        // 1. Ambil data vendor
        $vendor = User::find($vendorId);

        // 2. Cek jika ada koneksi langsung ke DB QAD / API QAD (Placeholder untuk live integration)
        if (config('services.qad.enabled', false)) {
            return $this->fetchFromLiveQad($vendor, $month, $year);
        }

        // 3. Fallback / Mock Data Generator yang realistis berdasarkan ID vendor & periode
        return $this->generateMockQadScores($vendorId, $month, $year);
    }

    /**
     * Generate skor simulasi QAD yang deterministik dan konsisten per vendor & periode.
     */
    protected function generateMockQadScores($vendorId, $month, $year)
    {
        $seed = ($vendorId * 10000) + ($year * 100) + $month;
        mt_srand($seed);

        $totalArrivals = mt_rand(5, 25);
        
        $onTime = mt_rand(ceil($totalArrivals * 0.8), $totalArrivals);
        $released = mt_rand(ceil($totalArrivals * 0.85), $totalArrivals);
        $correctQuantity = mt_rand(ceil($totalArrivals * 0.9), $totalArrivals);

        $deliveryScore = round(($onTime / $totalArrivals) * 100, 2);
        $qualityScore  = round(($released / $totalArrivals) * 100, 2);
        $quantityScore = round(($correctQuantity / $totalArrivals) * 100, 2);

        mt_srand(); // Reset PRNG seed

        return [
            'delivery_score' => $deliveryScore,
            'quality_score'  => $qualityScore,
            'quantity_score' => $quantityScore,
            'raw_data' => [
                'total_arrivals'   => $totalArrivals,
                'on_time_arrivals' => $onTime,
                'released_items'   => $released,
                'correct_quantity' => $correctQuantity,
            ]
        ];
    }

    /**
     * Method stub untuk koneksi QAD sesungguhnya di kemudian hari.
     */
    protected function fetchFromLiveQad($vendor, $month, $year)
    {
        // Ponytail note: Upgrade path when QAD API/DB view is available
        // Example: DB::connection('qad')->table('po_receipts')->...
        Log::info("Fetching live QAD data for Vendor: {$vendor->name}, Periode: {$month}-{$year}");

        return [
            'delivery_score' => 100.00,
            'quality_score'  => 100.00,
            'quantity_score' => 100.00,
        ];
    }
}
