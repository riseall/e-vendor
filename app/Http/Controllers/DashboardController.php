<?php

namespace App\Http\Controllers;

use App\Models\VendorApplication;
use App\Models\VendorAppSpecBaku;
use App\Models\VendorAudit;
use App\Models\VendorQualification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Audit status → [label, css class] */
    private static $auditStatusMap = [
        'scheduled'                 => ['Scheduled',  'vnd-status--scheduled'],
        'schedule_proposed'         => ['Scheduled',  'vnd-status--scheduled'],
        'schedule_confirmed'        => ['Scheduled',  'vnd-status--scheduled'],
        'in_progress'               => ['In Progress', 'vnd-status--in-progress'],
        'questionnaire_in_progress' => ['In Progress', 'vnd-status--in-progress'],
        'findings_recorded'         => ['In Progress', 'vnd-status--in-progress'],
        'capa_in_progress'          => ['In Progress', 'vnd-status--in-progress'],
        'completed'                 => ['Completed',  'vnd-status--completed'],
        'rejected'                  => ['Overdue',    'vnd-status--rejected'],
    ];

    /** Document expiry status → [label, css class] */
    private static $docStatusMap = [
        'valid'         => ['Valid',         'vnd-status--verified'],
        'expiring_soon' => ['Expiring Soon', 'vnd-status--revision'],
        'expired'       => ['Expired',       'vnd-status--rejected'],
    ];

    public function index(): View
    {
        // ── 1. SUPPLIER OVERVIEW KPI ────────────────────────────────────
        $totalSupplier = VendorApplication::whereNotNull('application_number')->count();
        $approvedSupplier = VendorApplication::where('status', VendorApplication::STATUS_APPROVED)->count();
        $underQualification = VendorApplication::whereIn('status', [
            VendorApplication::STATUS_SUBMITTED,
            VendorApplication::STATUS_NEED_REVISION,
            VendorApplication::STATUS_VERIFIED,
            VendorApplication::STATUS_RISK_ASSESSED,
            VendorApplication::STATUS_AUDIT_REQUIRED,
            VendorApplication::STATUS_ON_HOLD
        ])->count();
        $suspendedSupplier = VendorApplication::where('status', VendorApplication::STATUS_REJECTED)->count();
        $requalificationDue = VendorQualification::whereNotNull('total_score')
            ->where('created_at', '<=', Carbon::now()->subMonths(11))
            ->count();

        // ── 2. SUPPLIER AUDIT MONITORING ────────────────────────────────
        $audits = VendorAudit::with([
            'application.general',
            'application.user',
            'application.categories',
        ])
            ->latest('confirmed_schedule_at')
            ->take(5)
            ->get()
            ->map(function (VendorAudit $a) {
                $app    = $a->application;
                $gen    = $app ? $app->general : null;
                $vendor = $gen ? $gen->nama_perusahaan : ($app && $app->user ? $app->user->name : '—');
                $cats   = $app ? $app->categories->pluck('category_id')
                    ->map(function ($id) {
                        return isset(VendorApplication::CATEGORY_LABELS[$id]) ? VendorApplication::CATEGORY_LABELS[$id] : null;
                    })
                    ->filter()
                    ->take(2)
                    ->values() : collect();

                list($statusLabel, $statusCls) = self::resolveStatus(
                    self::$auditStatusMap,
                    $a->status,
                    'vnd-status--muted'
                );

                return (object) [
                    'vendor_name'       => $vendor,
                    'category_chips'    => $cats,
                    'audit_type_label'  => self::auditTypeLabel($a->audit_type),
                    'last_audit_date'   => $a->questionnaire_submitted_at ? $a->questionnaire_submitted_at->format('d M Y') : '',
                    'next_audit_date'   => $a->confirmed_schedule_at ? $a->confirmed_schedule_at->format('d M Y') : '',
                    'status_label'      => $statusLabel,
                    'status_cls'        => $statusCls,
                    'auditor_display'   => $app->approved_by ?? '-',
                ];
            });

        // ── 3. DOCUMENT EXPIRY MONITORING (CDOB & SIPA APJ only) ────────
        // Sumber: vendor_app_spec_baku (field q5_* untuk CDOB, q6_* untuk SIPA APJ)
        $today = Carbon::now();

        $documents = VendorAppSpecBaku::with('application.general', 'application.user')
            ->where(function ($q) use ($today) {
                $q->whereNotNull('q5_valid_until')
                    ->orWhereNotNull('q6_valid_until');
            })
            ->get()
            ->flatMap(function (VendorAppSpecBaku $baku) use ($today) {
                $app    = $baku->application;
                $gen    = $app && $app->general ? $app->general : null;
                $supplier = $gen ? $gen->nama_perusahaan : ($app && $app->user ? $app->user->name : '-');

                $out = [];
                // CDOB
                if (!empty($baku->q5_valid_until)) {
                    $expiry = Carbon::parse($baku->q5_valid_until);
                    $out[] = self::mapExpiryRow(
                        $supplier,
                        'CDOB',
                        $baku->q5_issue_date,
                        $expiry,
                        $today
                    );
                }
                // SIPA APJ
                if (!empty($baku->q6_valid_until)) {
                    $expiry = Carbon::parse($baku->q6_valid_until);
                    $out[] = self::mapExpiryRow(
                        $supplier,
                        'SIPA APJ',
                        $baku->q6_issue_date,
                        $expiry,
                        $today
                    );
                }
                return $out;
            })
            ->sortBy('expiry_date') // soonest expiry first
            ->take(5)
            ->values();

        // ── 4. SUPPLIER PERFORMANCE (top 5) ─────────────────────────────
        $topPerformers = VendorQualification::with('application.general')
            ->whereNotNull('total_score')
            ->orderByDesc('total_score')
            ->take(5)
            ->get()
            ->map(function ($q) {
                $pct = (int) round(min(100, max(0, $q->total_score)));
                return (object) [
                    'name' => $q->application && $q->application->general
                        ? $q->application->general->nama_perusahaan
                        : '-',
                    'pct'  => $pct,
                ];
            });

        // ── 5. SUPPLIER RISK ASSESSMENT (single-query rollup) ───────────
        $riskRows = DB::table('vendor_qualifications')
            ->whereNotNull('risk_level')
            ->select('risk_level', DB::raw('count(*) as total'))
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level')
            ->toArray();

        $riskCounts = [
            'high'   => (int) ($riskRows['high'] ?? $riskRows['HIGH'] ?? 0),
            'medium' => (int) ($riskRows['medium'] ?? $riskRows['MEDIUM'] ?? 0),
            'low'    => (int) ($riskRows['low'] ?? $riskRows['LOW'] ?? 0),
        ];

        $riskTotal = array_sum($riskCounts);


        return view('admin.dashboard', compact(
            'totalSupplier',
            'approvedSupplier',
            'underQualification',
            'suspendedSupplier',
            'requalificationDue',
            'audits',
            'documents',
            'topPerformers',
            'riskCounts',
            'riskTotal'
        ));
    }

    /** Resolve a status map entry → [label, class]. */
    private static function resolveStatus(array $map, $key, $fallbackCls)
    {
        if (isset($map[$key])) {
            return $map[$key];
        }
        return [ucwords(str_replace('_', ' ', (string) $key)), $fallbackCls];
    }

    /**
     * Build a single expiry-monitoring row.
     */
    private static function mapExpiryRow(string $supplier, string $docType, $issueDate, Carbon $expiry, Carbon $today): object
    {
        $daysLeft = $today->diffInDays($expiry, false);

        if ($daysLeft < 0) {
            $status = 'expired';
        } elseif ($daysLeft <= 60) {
            $status = 'expiring_soon';
        } else {
            $status = 'valid';
        }

        list($statusLabel, $statusCls) = self::resolveStatus(self::$docStatusMap, $status, 'vnd-status--muted');

        return (object) [
            'supplier'     => $supplier,
            'doc_type'     => $docType,
            'issue_date'   => $issueDate ? Carbon::parse($issueDate)->format('d M Y') : '',
            'expiry_date'  => $expiry->format('d M Y'),
            'status_label' => $statusLabel,
            'status_cls'   => $statusCls,
        ];
    }

    /** Audit type → display label (PHP 7.3-safe). */
    private static function auditTypeLabel($t)
    {
        $map = [
            'on_desk' => 'On Desk',
            'on_site' => 'Onsite',
            'paper'   => 'Paper',
            'remote'  => 'Remote',
        ];
        return isset($map[$t]) ? $map[$t] : ucwords(str_replace('_', ' ', (string) $t));
    }
}
