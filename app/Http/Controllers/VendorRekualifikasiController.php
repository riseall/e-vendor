<?php

namespace App\Http\Controllers;

use App\Models\VendorApplication;
use App\Models\VendorApplicationActivityLog;
use App\Services\VendorApplicationNotificationService;
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

        $latestApproved->update([
            'type'                   => VendorApplication::TYPE_REKUALIFIKASI,
            'requalification_reason' => VendorApplication::REASON_VENDOR_INITIATIVE,
        ]);

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
            'type'                   => VendorApplication::TYPE_REKUALIFIKASI,
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

        app(VendorApplicationNotificationService::class)->requalificationTriggered($application, $reason);

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

        return view('admin.rekualifikasi.index', compact('applications'));
    }
}
