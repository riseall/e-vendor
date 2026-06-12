<?php

namespace App\Http\Controllers;

use App\Models\VendorApplication;
use App\Models\VendorApplicationVerificationItem;
use App\Services\ProcurementVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProcurementVerificationController extends Controller
{
    private const STATUS_OPTIONS = [
        'all' => 'Semua Status',
        VendorApplication::STATUS_SUBMITTED => 'Submitted',
        VendorApplication::STATUS_NEED_REVISION => 'Need Revision',
        VendorApplication::STATUS_VERIFIED => 'Verified',
    ];

    private const SHOW_RELATIONS = [
        'user',
        'general',
        'products',
        'documents',
        'categories',
        'verifier',
        'specBaku',
        'specVaria',
        'specTrans',
        'specKontraktor',
        'specPengujian',
        'specFacility',
        'specPelatihan',
        'specAgency',
        'verificationItems.verifier',
    ];

    private const VERIFICATION_ROLES = ['Super Admin', 'Admin IT', 'Procurement', 'Verifikator'];

    private ProcurementVerificationService $verificationService;

    public function __construct(ProcurementVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public function index(Request $request): View
    {
        $this->authorizeProcurementAccess();

        $status = $request->input('status', 'all');
        $search = $request->input('q');
        $query = $this->verificationService->indexQuery();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $this->verificationService->applyIndexSearch($query, $search);
        }

        $applications = $query->paginate(10)->withQueryString();
        $this->verificationService->decorateIndexApplications($applications->getCollection());
        $summaryCounts = $this->verificationService->summaryCounts();

        return view('admin.verifikasi.index', [
            'applications' => $applications,
            'status' => $status,
            'search' => $search,
            'statusOptions' => self::STATUS_OPTIONS,
            'totalApplicationsAll' => (int) $summaryCounts->sum(),
            'totalSubmittedAll' => (int) ($summaryCounts[VendorApplication::STATUS_SUBMITTED] ?? 0),
            'totalNeedRevisionAll' => (int) ($summaryCounts[VendorApplication::STATUS_NEED_REVISION] ?? 0),
            'totalVerifiedAll' => (int) ($summaryCounts[VendorApplication::STATUS_VERIFIED] ?? 0),
        ]);
    }

    public function show(VendorApplication $application): View
    {
        $this->authorizeProcurementAccess();

        $application->load(self::SHOW_RELATIONS);
        $this->verificationService->syncVerificationItems($application);
        $application->load('verificationItems.verifier');

        $sections = $this->verificationService->verificationSections($application);
        $summary = $this->verificationService->verificationSummary($sections);
        $deadline = $this->verificationService->verificationDeadline($application);

        return view('admin.verifikasi.show', [
            'application' => $application,
            'general' => $application->general,
            'deadline' => $deadline,
            'statusMeta' => $this->verificationService->showStatusMeta($application, $deadline),
            'itemStatus' => $this->verificationService->itemStatusMeta(),
            'verificationSections' => $sections,
            'verificationSummary' => $summary,
            'totalItems' => $summary['total'],
            'approvedItems' => $summary['approved'],
            'rejectedItems' => $summary['rejected'],
            'pendingItems' => $summary['pending'],
            'progressPct' => $summary['progress_pct'],
            'verificationItemRows' => $this->verificationService->verificationItemRows($application),
            'specificCategoryHeaders' => $this->verificationService->specificCategoryHeaders($application),
        ]);
    }

    public function verify(Request $request, VendorApplication $application)
    {
        $this->authorizeProcurementAccess();
        $this->verificationService->verifyApplication(
            $application,
            $request->input('admin_note')
        );

        return redirect()
            ->route('verifikasi.show', $application)
            ->with('success', 'Permohonan berhasil diverifikasi lengkap.');
    }

    public function requestRevision(Request $request, VendorApplication $application)
    {
        $this->authorizeProcurementAccess();

        $data = $request->validate([
            'admin_note' => 'required|string|max:2000',
            'revision_fields' => 'nullable|array',
            'revision_fields.*' => 'nullable|string|max:255',
            'revision_notes' => 'nullable|array',
            'revision_notes.*' => 'nullable|string|max:1000',
        ]);

        $revisionNotes = $this->verificationService->normalizeRevisionNotes(
            $request->input('revision_fields', []),
            $request->input('revision_notes', [])
        );

        $this->verificationService->requestApplicationRevision(
            $application,
            $data['admin_note'],
            $revisionNotes
        );

        return redirect()
            ->route('verifikasi.show', $application)
            ->with('success', 'Permohonan dikembalikan ke vendor untuk revisi.');
    }

    public function approveItem(
        Request $request,
        VendorApplication $application,
        VendorApplicationVerificationItem $item
    ) {
        $this->authorizeProcurementAccess();
        $this->verificationService->approveVerificationItem($application, $item);

        return $this->itemActionResponse(
            $request,
            $application,
            $item->item_label . ' disetujui.'
        );
    }

    public function rejectItem(
        Request $request,
        VendorApplication $application,
        VendorApplicationVerificationItem $item
    ) {
        $this->authorizeProcurementAccess();

        $data = $request->validate([
            'note' => 'required|string|max:1000',
            'revision_fields' => 'required|array|min:1',
            'revision_fields.*' => 'required|string|max:255',
            'revision_labels' => 'required|array|min:1',
            'revision_labels.*' => 'required|string|max:255',
        ]);

        $this->verificationService->rejectVerificationItem($application, $item, $data);

        return $this->itemActionResponse(
            $request,
            $application,
            $item->item_label . ' ditandai tidak setuju.'
        );
    }

    private function authorizeProcurementAccess(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasAnyRole(self::VERIFICATION_ROLES),
            403
        );
    }

    private function itemActionResponse(
        Request $request,
        VendorApplication $application,
        string $message
    ) {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('verifikasi.show', $application)
            ->with('success', $message);
    }
}
