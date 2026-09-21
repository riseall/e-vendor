<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorApplication;
use App\Models\VendorEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelReader;

class EvaluasiImportController extends Controller
{
    /**
     * Download Template Excel untuk Pengisian Nilai Manual QA
     */
    public function downloadTemplateQa()
    {
        $path = public_path('storage/templates/template_evaluasi.xlsx');
        if (!file_exists($path)) {
            $path = storage_path('app/public/templates/template_evaluasi.xlsx');
        }

        return response()->download($path, 'template_evaluasi.xlsx');
    }

    /**
     * Import Nilai Manual QA via File Excel (.xlsx / .csv) - Strict Match by Supplier Code
     */
    public function importQaExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        $month = (int) $request->input('month');
        $year = (int) $request->input('year');

        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['xlsx', 'csv', 'xls'])) {
                $extension = 'xlsx';
            }

            $rows = SimpleExcelReader::create($file->getRealPath(), $extension)->getRows();

            $approvedApplications = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)
                ->with('general')
                ->get();

            // Pemetaan ketat: HANYA berdasarkan Supplier Code QAD
            $vendorMapByCode = [];
            foreach ($approvedApplications as $app) {
                $code = trim($app->qad_supplier_code ?: optional($app->general)->qad_supplier_code);
                if (!empty($code)) {
                    $vendorMapByCode[strtolower($code)] = $app->user_id;
                }
            }

            $updatedCount = 0;

            DB::beginTransaction();

            foreach ($rows as $row) {
                // Ambil supplier code dari Excel
                $supplierCode = isset($row['supplier_code'])
                    ? strtolower(trim((string) $row['supplier_code']))
                    : (isset($row['kode_supplier']) ? strtolower(trim((string) $row['kode_supplier'])) : '');

                // Hanya proses jika supplier code terisi dan cocok
                if (empty($supplierCode) || !isset($vendorMapByCode[$supplierCode])) {
                    continue;
                }

                $vendorId = $vendorMapByCode[$supplierCode];

                $evaluation = VendorEvaluation::firstOrNew([
                    'vendor_id' => $vendorId,
                    'month' => $month,
                    'year' => $year,
                ]);

                // Nilai Complain
                $complainRaw = $row['complain_score'] ?? ($row['skor_komplain'] ?? null);
                if ($complainRaw !== null && is_numeric($complainRaw)) {
                    $evaluation->complain_score = max(0, min(100, floatval($complainRaw)));
                } elseif (!$evaluation->exists) {
                    $evaluation->complain_score = 0;
                }

                // Nilai Incoming Material
                $incomingRaw = $row['incoming_material_score'] ?? ($row['skor_incoming'] ?? null);
                if ($incomingRaw !== null && is_numeric($incomingRaw)) {
                    $evaluation->incoming_material_score = max(0, min(100, floatval($incomingRaw)));
                } elseif (!$evaluation->exists) {
                    $evaluation->incoming_material_score = 0;
                }

                // Nilai Safety & Environment
                $safetyRaw = $row['safety_environment_score'] ?? ($row['skor_safety'] ?? ($row['skor_k3'] ?? null));
                if ($safetyRaw !== null && is_numeric($safetyRaw)) {
                    $evaluation->safety_environment_score = max(0, min(100, floatval($safetyRaw)));
                } elseif (!$evaluation->exists) {
                    $evaluation->safety_environment_score = 0;
                }

                if (!$evaluation->exists) {
                    $evaluation->delivery_score = $evaluation->delivery_score ?? 0;
                    $evaluation->quality_score = $evaluation->quality_score ?? 0;
                    $evaluation->quantity_score = $evaluation->quantity_score ?? 0;
                    $evaluation->created_by = Auth::id();
                }

                $evaluation->updated_by = Auth::id();
                $evaluation->calculateTotalAndCategory();
                $evaluation->save();

                $updatedCount++;
            }

            DB::commit();

            if ($updatedCount === 0) {
                return redirect()->route('admin.evaluasi.index', [
                    'year' => $year,
                    'month' => $month,
                    'tab' => 'monthly'
                ])->with('warning', 'Tidak ada data vendor yang cocok dengan Supplier Code pada file Excel.');
            }

            return redirect()->route('admin.evaluasi.index', [
                'year' => $year,
                'month' => $month,
                'tab' => 'monthly'
            ])->with('success', "Berhasil mengimpor nilai manual QA untuk {$updatedCount} vendor berdasarkan Supplier Code.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.evaluasi.index', [
                'year' => $year,
                'month' => $month,
                'tab' => 'monthly'
            ])->with('error', 'Gagal mengimpor file Excel: ' . $e->getMessage());
        }
    }
}
