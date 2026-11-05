<?php

namespace App\Http\Controllers;

use App\Models\VendorApplication;
use App\Models\VendorApplicationActivityLog;
use App\Services\VendorApplicationNotificationService;
use App\Services\VendorRequalificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorRekualifikasiController extends Controller
{
    /**
     * Initiate Vendor Requalification via Edit Profile / Data button (Vendor Trigger).
     */
    public function initiate(Request $request, VendorRequalificationService $requalificationService)
    {
        $user = Auth::user();

        $latestApproved = VendorApplication::where('user_id', $user->id)
            ->where('status', VendorApplication::STATUS_APPROVED)
            ->latest('approved_at')
            ->first();

        if (!$latestApproved) {
            return redirect()->back()
                ->with('error', __('Pembaruan profil/rekualifikasi hanya dapat dilakukan jika Anda sudah memiliki permohonan yang disetujui (Approved).'));
        }

        $requalificationService->createRequalificationDraft(
            $latestApproved,
            VendorApplication::REASON_VENDOR_INITIATIVE,
            'Inisiatif Vendor (Edit Profil)',
            $user->id
        );

        return redirect()->route('registrasi.index')
            ->with('info', __('Anda memasuki mode Pembaruan Profil (Rekualifikasi). Silakan ubah data yang diperlukan lalu klik Kirim Rekualifikasi, atau klik Batal jika tidak ada perubahan.'));
    }

    /**
     * Trigger Requalification by Admin/QA for an approved application.
     */
    public function triggerByAdmin(Request $request, $applicationId, VendorRequalificationService $requalificationService)
    {
        $application = VendorApplication::findOrFail($applicationId);

        if (strtolower((string)$application->status) !== strtolower(VendorApplication::STATUS_APPROVED)) {
            return redirect()->back()
                ->with('error', __('Rekualifikasi hanya dapat dipicu untuk permohonan berstatus Approved.'));
        }

        $request->validate([
            'note' => 'required|string|max:1000',
        ], [
            'note.required' => 'Catatan / alasan pemicuan rekualifikasi manual wajib diisi.',
        ]);

        $reason = $request->input('reason', VendorApplication::REASON_QA_TRIGGER);
        $adminNote = $request->input('note');

        $requalificationService->createRequalificationDraft(
            $application,
            $reason,
            $adminNote,
            Auth::id()
        );

        return redirect()->back()
            ->with('success', __('Permintaan Rekualifikasi berhasil dibuat dan notifikasi telah dikirimkan ke Vendor.'));
    }

    /**
     * Display listing of Requalifications.
     */
    public function index(Request $request)
    {
        $query = VendorApplication::where('type', VendorApplication::TYPE_REKUALIFIKASI)
            ->with(['user', 'parent.general', 'general', 'qualification']);

        if (Auth::user()->role === 'supplier') {
            $query->where('user_id', Auth::id());
        }

        // ponytail: client-side collection with get() is optimal for current volume; ceiling ~1000 rows, upgrade path to Yajra / serverSide: true
        $applications = $query->latest()->get();

        return view('admin.rekualifikasi.index', compact('applications'));
    }
}
