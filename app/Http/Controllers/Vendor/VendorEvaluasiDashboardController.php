<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorAnnualEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorEvaluasiDashboardController extends Controller
{
    /**
     * Halaman Laporan Evaluasi Kinerja di Dashboard Vendor
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $year = (int) $request->get('year', date('Y'));

        // Vendor hanya dapat melihat Evaluasi Tahunan yang sudah disetujui (Approved)
        $evaluations = VendorAnnualEvaluation::where('vendor_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('year', 'desc')
            ->get();

        $selectedEvaluation = $evaluations->where('year', $year)->first();
        if (!$selectedEvaluation && $evaluations->isNotEmpty()) {
            $selectedEvaluation = $evaluations->first();
        }

        return view('vendor.evaluasi.index', compact('evaluations', 'selectedEvaluation', 'year'));
    }
}
