<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationSetting;
use App\Models\User;
use App\Models\VendorAnnualEvaluation;
use App\Models\VendorApplication;
use App\Models\VendorEvaluation;
use App\Services\QadEvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluasiVendorController extends Controller
{
    protected $qadService;

    public function __construct(QadEvaluationService $qadService)
    {
        $this->qadService = $qadService;
    }

    /**
     * Halaman Utama Evaluasi Vendor (Tab Evaluasi Bulanan & Tahunan)
     */
    public function index(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));
        $month = (int) $request->get('month', date('n'));
        $activeTab = $request->get('tab', 'monthly');

        // Ambil daftar vendor yang sudah approved
        $approvedVendorUserIds = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)
            ->pluck('user_id')
            ->unique();

        $vendors = User::whereIn('id', $approvedVendorUserIds)->get();

        // Data Evaluasi Bulanan
        $monthlyEvaluations = VendorEvaluation::where('year', $year)
            ->where('month', $month)
            ->get()
            ->keyBy('vendor_id');

        // Data Evaluasi Tahunan
        $annualEvaluations = VendorAnnualEvaluation::with('vendor')
            ->where('year', $year)
            ->get()
            ->keyBy('vendor_id');

        $settings = EvaluationSetting::getSettings();

        return view('admin.evaluasi.index', compact(
            'year',
            'month',
            'activeTab',
            'vendors',
            'monthlyEvaluations',
            'annualEvaluations',
            'settings'
        ));
    }

    /**
     * Sync data QAD otomatis (Delivery, Quality, Quantity) via AJAX / Request
     */
    public function fetchQadData(Request $request)
    {
        $vendorId = $request->input('vendor_id');
        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        $scores = $this->qadService->fetchQadScores($vendorId, $month, $year);

        return response()->json([
            'success' => true,
            'scores' => $scores
        ]);
    }

    /**
     * Simpan / Update Evaluasi Bulanan (Input Manual QA/Pengadaan & Auto QAD)
     */
    public function storeMonthly(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'delivery_score' => 'required|numeric|min:0|max:100',
            'quality_score' => 'required|numeric|min:0|max:100',
            'quantity_score' => 'required|numeric|min:0|max:100',
            'complain_score' => 'required|numeric|min:0|max:100',
            'incoming_material_score' => 'required|numeric|min:0|max:100',
            'safety_environment_score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $evaluation = VendorEvaluation::firstOrNew([
            'vendor_id' => $request->vendor_id,
            'month' => $request->month,
            'year' => $request->year,
        ]);

        $evaluation->delivery_score = $request->delivery_score;
        $evaluation->quality_score = $request->quality_score;
        $evaluation->quantity_score = $request->quantity_score;
        $evaluation->complain_score = $request->complain_score;
        $evaluation->incoming_material_score = $request->incoming_material_score;
        $evaluation->safety_environment_score = $request->safety_environment_score;
        $evaluation->notes = $request->notes;
        $evaluation->updated_by = Auth::id();

        if (!$evaluation->exists) {
            $evaluation->created_by = Auth::id();
        }

        $evaluation->calculateTotalAndCategory();
        $evaluation->save();

        return redirect()->route('admin.evaluasi.index', [
            'year' => $request->year,
            'month' => $request->month,
            'tab' => 'monthly'
        ])->with('success', 'Evaluasi bulanan berhasil disimpan.');
    }

    /**
     * Simpan Batch Grid Evaluasi Bulanan
     */
    public function storeBatchMonthly(Request $request)
    {
        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));
        $evaluationsData = $request->input('evaluations', []);

        DB::beginTransaction();
        try {
            foreach ($evaluationsData as $vendorId => $data) {
                if (!isset($data['enabled']) || !$data['enabled']) {
                    continue;
                }

                $evaluation = VendorEvaluation::firstOrNew([
                    'vendor_id' => $vendorId,
                    'month' => $month,
                    'year' => $year,
                ]);

                $evaluation->delivery_score = floatval($data['delivery_score'] ?? 0);
                $evaluation->quality_score = floatval($data['quality_score'] ?? 0);
                $evaluation->quantity_score = floatval($data['quantity_score'] ?? 0);
                $evaluation->complain_score = floatval($data['complain_score'] ?? 100);
                $evaluation->incoming_material_score = floatval($data['incoming_material_score'] ?? 100);
                $evaluation->safety_environment_score = floatval($data['safety_environment_score'] ?? 100);
                $evaluation->notes = $data['notes'] ?? null;
                $evaluation->updated_by = Auth::id();

                if (!$evaluation->exists) {
                    $evaluation->created_by = Auth::id();
                }

                $evaluation->calculateTotalAndCategory();
                $evaluation->save();
            }

            DB::commit();
            return redirect()->route('admin.evaluasi.index', ['year' => $year, 'month' => $month, 'tab' => 'monthly'])
                ->with('success', 'Batch evaluasi bulanan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan batch evaluasi: ' . $e->getMessage());
        }
    }

    /**
     * Generate / Kalkulasi Evaluasi Tahunan per Vendor
     */
    public function generateAnnual(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $settings = EvaluationSetting::getSettings();

        // Ambil vendor id yang memiliki evaluasi di tahun tersebut
        $vendorIds = VendorEvaluation::where('year', $year)
            ->pluck('vendor_id')
            ->unique();

        if ($vendorIds->isEmpty()) {
            return redirect()->back()->with('warning', "Belum ada data evaluasi bulanan untuk tahun $year.");
        }

        foreach ($vendorIds as $vendorId) {
            $monthlyEvals = VendorEvaluation::where('vendor_id', $vendorId)
                ->where('year', $year)
                ->get();

            if ($monthlyEvals->isEmpty()) {
                continue;
            }

            $count = $monthlyEvals->count();

            $avgDelivery = $monthlyEvals->avg('delivery_score');
            $avgQuality  = $monthlyEvals->avg('quality_score');
            $avgQuantity = $monthlyEvals->avg('quantity_score');
            $avgComplain = $monthlyEvals->avg('complain_score');
            $avgIncoming = $monthlyEvals->avg('incoming_material_score');
            $avgSafety   = $monthlyEvals->avg('safety_environment_score');

            // Kalkulasi final score terbobot
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
                'year' => $year,
            ]);

            $annual->delivery_score_avg = round($avgDelivery, 2);
            $annual->quality_score_avg = round($avgQuality, 2);
            $annual->quantity_score_avg = round($avgQuantity, 2);
            $annual->complain_score_avg = round($avgComplain, 2);
            $annual->incoming_material_score_avg = round($avgIncoming, 2);
            $annual->safety_environment_score_avg = round($avgSafety, 2);
            $annual->final_score = $finalScore;
            $annual->category = $category;

            // Deteksi Alert Peringatan 1: Penurunan Skor >= 20% dari tahun sebelumnya
            $prevYearAnnual = VendorAnnualEvaluation::where('vendor_id', $vendorId)
                ->where('year', $year - 1)
                ->first();

            if ($prevYearAnnual && $prevYearAnnual->final_score > 0) {
                $dropPercent = (($prevYearAnnual->final_score - $finalScore) / $prevYearAnnual->final_score) * 100;
                $annual->has_score_drop_alert = ($dropPercent >= 20.0);
            } else {
                $annual->has_score_drop_alert = false;
            }

            // Deteksi Alert Peringatan 2: 2 Tahun Berturut-turut KURANG
            if ($category === 'KURANG' && $prevYearAnnual && $prevYearAnnual->category === 'KURANG') {
                $annual->has_consecutive_low_alert = true;
            } else {
                $annual->has_consecutive_low_alert = false;
            }

            $annual->save();
        }

        return redirect()->route('admin.evaluasi.index', ['year' => $year, 'tab' => 'annual'])
            ->with('success', "Evaluasi tahunan untuk tahun $year berhasil digenerate.");
    }

    /**
     * Approval Evaluasi Tahunan oleh Manajer / GM Pengadaan
     */
    public function approveAnnual(Request $request, $id)
    {
        $annual = VendorAnnualEvaluation::findOrFail($id);
        $annual->status = 'approved';
        $annual->approved_by = Auth::id();
        $annual->approved_at = now();
        $annual->notes = $request->input('notes', $annual->notes);
        $annual->save();

        return redirect()->back()->with('success', "Evaluasi Tahunan Vendor berhasil disetujui (Approved).");
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

    /**
     * Halaman Settings Bobot & Threshold
     */
    public function settings()
    {
        $settings = EvaluationSetting::getSettings();
        return view('admin.evaluasi.settings', compact('settings'));
    }

    /**
     * Update Settings Bobot & Threshold
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'weight_delivery' => 'required|numeric|min:0|max:100',
            'weight_quality' => 'required|numeric|min:0|max:100',
            'weight_quantity' => 'required|numeric|min:0|max:100',
            'weight_complain' => 'required|numeric|min:0|max:100',
            'weight_incoming_material' => 'required|numeric|min:0|max:100',
            'weight_safety_environment' => 'required|numeric|min:0|max:100',
            'threshold_baik' => 'required|numeric|min:0|max:100',
            'threshold_cukup' => 'required|numeric|min:0|max:100',
        ]);

        $totalWeight = $request->weight_delivery + $request->weight_quality + $request->weight_quantity
                     + $request->weight_complain + $request->weight_incoming_material + $request->weight_safety_environment;

        if (abs($totalWeight - 100.0) > 0.01) {
            return redirect()->back()->with('error', "Total bobot 6 aspek harus tepat 100% (saat ini: {$totalWeight}%).");
        }

        $settings = EvaluationSetting::getSettings();
        $settings->update($request->all());

        return redirect()->route('admin.evaluasi.settings')->with('success', "Pengaturan bobot & threshold evaluasi berhasil diperbarui.");
    }
}
