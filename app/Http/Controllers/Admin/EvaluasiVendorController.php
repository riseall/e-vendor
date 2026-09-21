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

        // Ambil daftar vendor yang sudah approved beserta QAD supplier code
        $approvedApplications = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)
            ->with('general')
            ->latest('approved_at')
            ->get()
            ->keyBy('user_id');

        $vendors = User::whereIn('id', $approvedApplications->keys())
            ->orderBy('name')
            ->get()
            ->map(function ($user) use ($approvedApplications) {
                $app = $approvedApplications->get($user->id);
                $user->qad_supplier_code = $app ? ($app->qad_supplier_code ?: optional($app->general)->qad_supplier_code) : null;
                return $user;
            });

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
                $evaluation->complain_score = floatval($data['complain_score'] ?? 0);
                $evaluation->incoming_material_score = floatval($data['incoming_material_score'] ?? 0);
                $evaluation->safety_environment_score = floatval($data['safety_environment_score'] ?? 0);
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
}
