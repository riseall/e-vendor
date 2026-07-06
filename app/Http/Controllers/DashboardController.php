<?php

namespace App\Http\Controllers;

use App\Models\VendorApplication;
use App\Models\VendorApplicationDocument;
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
            'qualification.qaManager',
            'auditorTeam',
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

                $auditorDisplay = '';
                if ($a->auditorTeam) {
                    $auditorDisplay = $a->auditorTeam->name;
                }
                if ($auditorDisplay === '' && !empty($a->auditor_team)) {
                    $auditorDisplay = $a->auditor_team;
                }
                if ($auditorDisplay === '' && $a->qualification && $a->qualification->qaManager) {
                    $auditorDisplay = $a->qualification->qaManager->name;
                }
                if ($auditorDisplay === '') {
                    $auditorDisplay = '—';
                }

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
                    'auditor_display'   => $auditorDisplay,
                ];
            });

        // ── 3. DOCUMENT EXPIRY MONITORING ───────────────────────────────
        $documents = VendorApplicationDocument::with('application.general', 'application.user')
            ->whereIn('field_name', [
                'iso_certificate',
                'gmp_certificate',
                'nib_document',
                'halal_certificate',
                'cpob_certificate',
                'cdob_certificate',
            ])
            ->latest()
            ->take(10)
            ->get()
            ->map(function (VendorApplicationDocument $doc) {
                $expiry = self::inferExpiry($doc);
                $daysLeft = Carbon::now()->diffInDays($expiry, false);

                if ($daysLeft < 0) {
                    $status = 'expired';
                } elseif ($daysLeft <= 60) {
                    $status = 'expiring_soon';
                } else {
                    $status = 'valid';
                }

                list($statusLabel, $statusCls) = self::resolveStatus(self::$docStatusMap, $status, 'vnd-status--muted');

                $app    = $doc->application;
                $gen    = $app && $app->general ? $app->general : null;
                $supplier = $gen ? $gen->nama_perusahaan : ($app && $app->user ? $app->user->name : '-');

                return (object) [
                    'supplier'     => $supplier,
                    'doc_type'     => strtoupper(str_replace('_', ' ', $doc->field_name)),
                    'issue_date'   => $doc->created_at ? $doc->created_at->format('d M Y') : '',
                    'expiry_date'  => $expiry ? $expiry->format('d M Y') : '',
                    'status_label' => $statusLabel,
                    'status_cls'   => $statusCls,
                ];
            })
            ->sortBy('expiry_date')
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
        $rows = DB::table('vendor_qualifications')
            ->whereNotNull('risk_level')
            ->select('risk_level', DB::raw('count(*) as total'))
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level')
            ->toArray();

        $riskCounts = [
            'high'   => (int) ($rows['high'] ?? $rows['HIGH'] ?? 0),
            'medium' => (int) ($rows['medium'] ?? $rows['MEDIUM'] ?? 0),
            'low'    => (int) ($rows['low'] ?? $rows['LOW'] ?? 0),
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

    /**
     * Best-effort expiry inference from the file name.
     * Replace with a dedicated `expiry_date` column when available.
     */
    private static function inferExpiry(VendorApplicationDocument $doc)
    {
        $name = strtolower($doc->original_name ? $doc->original_name : '');
        if (preg_match('/(\d{4})/', $name, $m)) {
            return Carbon::create((int) $m[1] + 3, 12, 31);
        }
        return $doc->created_at ? $doc->created_at->copy()->addYears(3) : null;
    }
}
