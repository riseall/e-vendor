<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\VendorApplication;
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
            ->with(['user', 'general', 'categories', 'qualification']);

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
}
