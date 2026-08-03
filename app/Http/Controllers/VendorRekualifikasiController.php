<?php

namespace App\Http\Controllers;

use App\Models\VendorApplication;
use App\Models\VendorApplicationActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorRekualifikasiController extends Controller
{
    /**
     * Initiate Vendor Requalification via Edit Profile / Data button (Vendor Trigger).
     */
    public function initiate(Request $request)
    {
        $user = Auth::user();

        $latestApproved = VendorApplication::where('user_id', $user->id)
            ->where('status', VendorApplication::STATUS_APPROVED)
            ->latest()
            ->first();

        if (!$latestApproved) {
            return redirect()->back()
                ->with('error', __('Pembaruan profil/rekualifikasi hanya dapat dilakukan jika Anda sudah memiliki permohonan yang disetujui (Approved).'));
        }

        return redirect()->route('registrasi.index', ['edit' => 1])
            ->with('info', __('Anda memasuki mode Edit Profil (Rekualifikasi). Silakan ubah data yang diperlukan lalu klik Kirim Rekualifikasi, atau klik Batal jika tidak ada perubahan.'));
    }

    /**
     * Trigger Requalification by Admin/QA for an approved application.
     */
    public function triggerByAdmin(Request $request, $applicationId)
    {
        $application = VendorApplication::findOrFail($applicationId);

        if (strtolower((string)$application->status) !== strtolower(VendorApplication::STATUS_APPROVED)) {
            return redirect()->back()
                ->with('error', __('Rekualifikasi hanya dapat dipicu untuk permohonan berstatus Approved.'));
        }

        $reason = $request->input('reason', VendorApplication::REASON_QA_TRIGGER);
        $application->update([
            'requalification_reason' => $reason,
        ]);
        if ($application->qualification) {
            $application->qualification()->delete();
        }

        VendorApplicationActivityLog::create([
            'application_id' => $application->id,
            'user_id'        => Auth::id(),
            'action'         => 'REQUEST_REKUALIFIKASI_ADMIN',
            'status_before'  => VendorApplication::STATUS_APPROVED,
            'status_after'   => VendorApplication::STATUS_APPROVED,
            'ip_address'     => $request->ip(),
            'metadata'       => [
                'reason' => $reason,
            ],
        ]);

        return redirect()->back()
            ->with('success', __('Permintaan Rekualifikasi berhasil dikirimkan ke Vendor. Vendor dapat mengubah profil melalui halaman Registrasi/Profil.'));
    }

    /**
     * Display listing of Requalifications.
     */
    public function index(Request $request)
    {
        $query = VendorApplication::where('type', VendorApplication::TYPE_REKUALIFIKASI)
            ->with(['user', 'parent', 'general']);

        if (Auth::user()->role === 'supplier') {
            $query->where('user_id', Auth::id());
        }

        $applications = $query->latest()->paginate(15);

        $approvedVendors = collect();
        if (Auth::user()->role !== 'supplier') {
            $approvedVendors = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)
                ->with(['general', 'user'])
                ->get();
        }

        return view('admin.rekualifikasi.index', compact('applications', 'approvedVendors'));
    }

    /**
     * Display listing of Approved Suppliers for Procurement & QA with Requalification Trigger.
     */
    public function supplierIndex(Request $request)
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
