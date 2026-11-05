<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\VendorAnnualEvaluation;
use App\Models\VendorApplication;
use App\Models\VendorEvaluation;
use Illuminate\Http\Request;

class VendorSupplierController extends Controller
{
    /**
     * Display listing of Approved Suppliers for Procurement & QA.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        $query = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)
            ->whereDoesntHave('rekualifikasiChildren', function ($q) {
                $q->where('status', VendorApplication::STATUS_APPROVED);
            })
            ->with(['user', 'general', 'categories', 'qualification', 'rekualifikasiChildren']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhereHas('general', function ($g) use ($search) {
                        $g->where('nama_perusahaan', 'like', "%{$search}%");
                        // ponytail: email_perusahaan is encrypted, removed from SQL LIKE
                    })
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $suppliers = $query->latest('approved_at')->paginate(15)->withQueryString();
        $currencies = Currency::orderBy('code')->get();

        return view('admin.supplier.index', compact('suppliers', 'search', 'currencies'));
    }

    /**
     * Update Data Master QAD (Kode Supplier, Supplier Type, Currency) untuk Supplier Approved.
     */
    public function updateQadCode(Request $request, $id)
    {
        $request->validate([
            'qad_supplier_code' => 'required|string|max:100',
            'supplier_type'     => 'nullable|string|max:100',
            'currency'          => 'nullable|string|max:10',
        ]);

        $app = VendorApplication::findOrFail($id);
        $qadCode = trim($request->input('qad_supplier_code'));
        $supplierType = $request->input('supplier_type') ? trim($request->input('supplier_type')) : null;
        $currency = $request->input('currency') ? trim($request->input('currency')) : 'IDR';

        $dataToUpdate = [
            'qad_supplier_code' => $qadCode,
            'supplier_type'     => $supplierType,
            'currency'          => $currency,
        ];

        $app->update($dataToUpdate);

        if ($app->general) {
            $app->general->update($dataToUpdate);
        }

        return redirect()->back()->with('success', "Data QAD Supplier ({$qadCode}) berhasil disimpan.");
    }

    /**
     * Display comprehensive Vendor Dossier (Summary from Registration to Evaluation).
     */
    public function show($id)
    {
        $app = VendorApplication::with([
            'user',
            'general',
            'categories',
            'products',
            'documents',
            'verifier',
            'qualification',
            'audits.questionnaireForm',
            'audits.qaLead',
            'audits.creator',
            'specBaku',
            'specVaria',
            'specTrans',
            'specKontraktor',
            'specPengujian',
            'specFacility',
            'specPelatihan',
            'specAgency',
            'rekualifikasiChildren',
            'parent',
        ])->findOrFail($id);

        $general = $app->general;

        // Ambil riwayat evaluasi tahunan & bulanan untuk vendor ini
        $annualEvaluations = VendorAnnualEvaluation::where('vendor_id', $app->user_id)
            ->orderByDesc('year')
            ->get();

        $monthlyEvaluations = VendorEvaluation::where('vendor_id', $app->user_id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('admin.supplier.show', compact('app', 'general', 'annualEvaluations', 'monthlyEvaluations'));
    }
}
