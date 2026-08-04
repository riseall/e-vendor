<?php

namespace App\Http\Controllers;

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
                        $g->where('nama_perusahaan', 'like', "%{$search}%")
                            ->orWhere('email_perusahaan', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $suppliers = $query->latest('approved_at')->paginate(15)->withQueryString();

        return view('admin.supplier.index', compact('suppliers', 'search'));
    }
}
