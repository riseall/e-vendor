<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VendorAnnualEvaluationApprovedMail;
use App\Mail\VendorConsecutiveLowAlertMail;
use App\Models\EvaluationSetting;
use App\Models\User;
use App\Models\VendorAnnualEvaluation;
use App\Models\VendorApplication;
use App\Models\VendorEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EvaluasiAnnualController extends Controller
{
    /**
     * Hitung / kalkulasi evaluasi tahunan untuk tahun tertentu (Reusable untuk Controller & Artisan Command)
     *
     * @param int $year
     * @return array
     */
    public static function computeAnnualForYear(int $year): array
    {
        $settings = EvaluationSetting::getSettings();

        // Ambil vendor id yang memiliki data evaluasi bulanan di tahun tersebut
        $vendorIds = VendorEvaluation::where('year', $year)
            ->pluck('vendor_id')
            ->unique();

        if ($vendorIds->isEmpty()) {
            return [
                'success' => false,
                'count'   => 0,
                'message' => "Belum ada data evaluasi bulanan untuk tahun $year.",
            ];
        }

        $processedCount = 0;

        foreach ($vendorIds as $vendorId) {
            $monthlyEvals = VendorEvaluation::where('vendor_id', $vendorId)
                ->where('year', $year)
                ->get();

            if ($monthlyEvals->isEmpty()) {
                continue;
            }

            $avgDelivery = $monthlyEvals->avg('delivery_score');
            $avgQuality  = $monthlyEvals->avg('quality_score');
            $avgQuantity = $monthlyEvals->avg('quantity_score');
            $avgComplain = $monthlyEvals->avg('complain_score');
            $avgIncoming = $monthlyEvals->avg('incoming_material_score');
            $avgSafety   = $monthlyEvals->avg('safety_environment_score');

            // Kalkulasi final score terbobot sesuai pengaturan
            $finalScore = ($avgDelivery * ($settings->weight_delivery / 100))
                        + ($avgQuality * ($settings->weight_quality / 100))
                        + ($avgQuantity * ($settings->weight_quantity / 100))
                        + ($avgComplain * ($settings->weight_complain / 100))
                        + ($avgIncoming * ($settings->weight_incoming_material / 100))
                        + ($avgSafety * ($settings->weight_safety_environment / 100));

            $finalScore = round($finalScore, 2);

            if ($finalScore >= $settings->threshold_baik) {
                $category = 'BAIK';
            } elseif ($finalScore >= $settings->threshold_cukup) {
                $category = 'CUKUP';
            } else {
                $category = 'KURANG';
            }

            $annual = VendorAnnualEvaluation::firstOrNew([
                'vendor_id' => $vendorId,
                'year'      => $year,
            ]);

            $annual->delivery_score_avg = round($avgDelivery, 2);
            $annual->quality_score_avg = round($avgQuality, 2);
            $annual->quantity_score_avg = round($avgQuantity, 2);
            $annual->complain_score_avg = round($avgComplain, 2);
            $annual->incoming_material_score_avg = round($avgIncoming, 2);
            $annual->safety_environment_score_avg = round($avgSafety, 2);
            $annual->final_score = $finalScore;
            $annual->category = $category;

            // Deteksi Alert 1: Penurunan Skor >= 20% dari tahun sebelumnya
            $prevYearAnnual = VendorAnnualEvaluation::where('vendor_id', $vendorId)
                ->where('year', $year - 1)
                ->first();

            if ($prevYearAnnual && $prevYearAnnual->final_score > 0) {
                $dropPercent = (($prevYearAnnual->final_score - $finalScore) / $prevYearAnnual->final_score) * 100;
                $annual->has_score_drop_alert = ($dropPercent >= 20.0);
            } else {
                $annual->has_score_drop_alert = false;
            }

            // Deteksi Alert 2: 2 Tahun Berturut-turut KURANG
            if ($category === 'KURANG' && $prevYearAnnual && $prevYearAnnual->category === 'KURANG') {
                $annual->has_consecutive_low_alert = true;
            } else {
                $annual->has_consecutive_low_alert = false;
            }

            $annual->save();
            $processedCount++;
        }

        return [
            'success' => true,
            'count'   => $processedCount,
            'message' => "Evaluasi tahunan untuk tahun $year berhasil digenerate ({$processedCount} vendor).",
        ];
    }

    /**
     * Generate / Kalkulasi Evaluasi Tahunan per Vendor via Web Admin
     */
    public function generateAnnual(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $result = self::computeAnnualForYear($year);

        if (!$result['success']) {
            return redirect()->back()->with('warning', $result['message']);
        }

        return redirect()->route('admin.evaluasi.index', ['year' => $year, 'tab' => 'annual'])
            ->with('success', $result['message']);
    }

    /**
     * Tahap 1 Approval: Verifikasi Evaluasi Tahunan oleh Manajer Pengadaan
     */
    public function verifyManager(Request $request, $id)
    {
        $annual = VendorAnnualEvaluation::with('vendor')->findOrFail($id);

        $annual->status = VendorAnnualEvaluation::STATUS_VERIFIED_MANAGER;
        $annual->manager_approved_by = Auth::id();
        $annual->manager_approved_at = now();
        $annual->manager_notes = $request->input('notes', $annual->manager_notes);
        $annual->save();

        $vendorName = optional($annual->vendor)->name ?: 'Vendor';

        return redirect()->back()->with('success', "Evaluasi Tahunan Vendor {$vendorName} berhasil diverifikasi oleh Manajer Pengadaan. Menunggu pengesahan GM.");
    }

    /**
     * Tahap 2 Approval: Pengesahan Akhir Evaluasi Tahunan oleh GM Pengadaan
     * Setelah disahkan GM, status menjadi approved dan laporan otomatis terkirim ke vendor.
     */
    public function approveGm(Request $request, $id)
    {
        $annual = VendorAnnualEvaluation::with('vendor')->findOrFail($id);

        $annual->status = VendorAnnualEvaluation::STATUS_APPROVED;
        $annual->gm_approved_by = Auth::id();
        $annual->gm_approved_at = now();
        $annual->gm_notes = $request->input('notes', $annual->gm_notes);

        // Pertahankan backward compatibility
        $annual->approved_by = Auth::id();
        $annual->approved_at = now();
        $annual->notes = $request->input('notes', $annual->notes);
        $annual->save();

        $vendorName = optional($annual->vendor)->name ?: 'Vendor';

        // 1. Kirim laporan evaluasi otomatis ke vendor via Email
        try {
            if ($annual->vendor && !empty($annual->vendor->email)) {
                Mail::to($annual->vendor->email)->queue(new VendorAnnualEvaluationApprovedMail($annual));
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengirim email laporan evaluasi tahunan ke vendor ID {$annual->vendor_id}: " . $e->getMessage());
        }

        // 2. Jika kategori KURANG 2 tahun berturut-turut, kirim notifikasi khusus ke Pengadaan & QA
        if ($annual->has_consecutive_low_alert) {
            try {
                $officers = User::role(['Procurement', 'Quality Assurance', 'Super Admin'])
                    ->whereNotNull('email')
                    ->get();
                $emails = $officers->pluck('email')->unique()->filter()->values()->all();

                if (!empty($emails)) {
                    Mail::to($emails)->queue(new VendorConsecutiveLowAlertMail($annual));
                }
            } catch (\Exception $e) {
                Log::error("Gagal mengirim email peringatan 2 tahun berturut-turut ke Pengadaan & QA: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "Evaluasi Tahunan Vendor {$vendorName} resmi disahkan oleh GM Pengadaan. Laporan evaluasi otomatis terkirim ke vendor.");
    }

    /**
     * Alias backward compatibility untuk approval
     */
    public function approveAnnual(Request $request, $id)
    {
        return $this->approveGm($request, $id);
    }

    /**
     * Aksi Manual Peringatan Alert (Trigger Rekualifikasi / Ubah Status Vendor)
     */
    public function triggerAction(Request $request, $id)
    {
        $annual = VendorAnnualEvaluation::findOrFail($id);
        $action = $request->input('action_type'); // rekualifikasi, terminated, suspended, qualified_with_notes

        if ($action === 'rekualifikasi') {
            // Buat draft rekualifikasi di vendor_applications
            $latestApp = VendorApplication::where('user_id', $annual->vendor_id)
                ->orderBy('id', 'desc')
                ->first();

            $newApp = new VendorApplication();
            $newApp->user_id = $annual->vendor_id;
            $newApp->type = VendorApplication::TYPE_REKUALIFIKASI;
            $newApp->parent_id = $latestApp ? $latestApp->id : null;
            $newApp->requalification_reason = VendorApplication::REASON_EVALUATION_DROP;
            $newApp->application_number = 'REK-' . date('Ym') . '-' . sprintf('%04d', mt_rand(1, 9999));
            $newApp->status = VendorApplication::STATUS_DRAFT;
            $newApp->current_step = 1;
            $newApp->admin_note = "Triggered dari Evaluasi Tahunan {$annual->year} (Skor: {$annual->final_score})";
            $newApp->save();

            $annual->decision_status = 'rekualifikasi_triggered';
            $annual->save();

            return redirect()->back()->with('success', "Proses Rekualifikasi Vendor berhasil ditrigger.");
        } elseif (in_array($action, ['terminated', 'suspended', 'qualified_with_notes'])) {
            $annual->decision_status = $action;
            $annual->save();

            return redirect()->back()->with('success', "Status tindakan vendor berhasil diperbarui ke: " . strtoupper(str_replace('_', ' ', $action)));
        }

        return redirect()->back()->with('error', "Aksi tidak valid.");
    }
}
