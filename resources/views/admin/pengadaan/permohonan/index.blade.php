@extends('layouts.app', ['title' => 'Verifikasi Pengadaan'])

@section('breadcrumb', 'Pengadaan')
@section('step', 'Verifikasi')
@section('page_title', 'Verifikasi Permohonan Vendor')
@section('page_desc', 'Kelola permohonan vendor yang sudah dikirim dan tentukan apakah data lengkap atau perlu revisi.')

@push('style')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        /* ══════════════════════════════════════════════
               DESIGN TOKENS — warna utama tetap pakai
               primary #005db6 dari sistem Metronic existing
            ══════════════════════════════════════════════ */
        :root {
            --vp-blue: #005db6;
            --vp-blue-lt: #e8f1fb;
            --vp-amber: #f59e0b;
            --vp-amber-lt: #fff8e7;
            --vp-green: #2e7d32;
            --vp-green-lt: #e8f5e9;
            --vp-red: #c62828;
            --vp-red-lt: #fdecea;
            --vp-ink: #1e2a3b;
            --vp-muted: #6b7a96;
            --vp-border: #e6eaf2;
            --vp-surface: #f5f7fb;
            --vp-white: #ffffff;
        }

        /* ── Stat Cards ─────────────────────────────── */
        .vp-stat {
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1.5px solid transparent;
            transition: transform .15s, box-shadow .15s;
        }

        .vp-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
        }

        .vp-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.3rem;
        }

        .vp-stat-num {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1;
        }

        .vp-stat-lbl {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-top: .2rem;
        }

        .vp-stat--total {
            background: var(--vp-blue-lt);
            border-color: #c2d9f5;
        }

        .vp-stat--total .vp-stat-icon {
            background: var(--vp-blue);
        }

        .vp-stat--total .vp-stat-num {
            color: var(--vp-blue);
        }

        .vp-stat--total .vp-stat-lbl {
            color: #3d6fa8;
        }

        .vp-stat--pending {
            background: var(--vp-amber-lt);
            border-color: #fcd97a;
        }

        .vp-stat--pending .vp-stat-icon {
            background: var(--vp-amber);
        }

        .vp-stat--pending .vp-stat-num {
            color: #b45309;
        }

        .vp-stat--pending .vp-stat-lbl {
            color: #92620a;
        }

        .vp-stat--done {
            background: var(--vp-green-lt);
            border-color: #a5d6a7;
        }

        .vp-stat--done .vp-stat-icon {
            background: var(--vp-green);
        }

        .vp-stat--done .vp-stat-num {
            color: var(--vp-green);
        }

        .vp-stat--done .vp-stat-lbl {
            color: #2d6e30;
        }

        /* ── Main Card ──────────────────────────────── */
        .vp-card {
            border: 1.5px solid var(--vp-border);
            border-radius: 16px;
            background: var(--vp-white);
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        .vp-card-head {
            padding: 1.1rem 1.5rem;
            border-bottom: 1.5px solid var(--vp-border);
            background: var(--vp-surface);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .vp-card-title {
            font-size: .88rem;
            font-weight: 800;
            color: var(--vp-ink);
            text-transform: uppercase;
            letter-spacing: .8px;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .vp-card-title-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--vp-blue);
            flex-shrink: 0;
        }

        /* ── Filter Bar ─────────────────────────────── */
        .vp-filter {
            display: flex;
            align-items: center;
            gap: .6rem;
            flex-wrap: wrap;
        }

        .vp-filter .form-control,
        .vp-filter .form-control-sm {
            border-radius: 8px;
            border: 1.5px solid var(--vp-border);
            font-size: .82rem;
            height: 34px;
            color: var(--vp-ink);
            background: var(--vp-white);
            transition: border-color .15s, box-shadow .15s;
        }

        .vp-filter .form-control:focus {
            border-color: var(--vp-blue);
            box-shadow: 0 0 0 3px rgba(0, 93, 182, .12);
        }

        .vp-filter .input-group-text {
            border: 1.5px solid var(--vp-border);
            border-left: 0;
            border-radius: 0 8px 8px 0;
            background: var(--vp-surface);
            color: var(--vp-muted);
        }

        .vp-filter .input-group input {
            border-right: 0;
            border-radius: 8px 0 0 8px;
        }

        .vp-btn-filter {
            height: 34px;
            padding: 0 1rem;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 700;
            border: none;
            background: var(--vp-blue);
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            transition: background .15s, box-shadow .15s;
        }

        .vp-btn-filter:hover {
            background: #004f9e;
            box-shadow: 0 3px 8px rgba(0, 93, 182, .3);
        }

        .vp-btn-reset {
            height: 34px;
            padding: 0 .9rem;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 700;
            border: 1.5px solid var(--vp-border);
            background: var(--vp-white);
            color: var(--vp-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: border-color .15s, color .15s;
        }

        .vp-btn-reset:hover {
            border-color: var(--vp-blue);
            color: var(--vp-blue);
            text-decoration: none;
        }

        /* ── Table ──────────────────────────────────── */
        #tbl-permohonan {
            width: 100% !important;
        }

        #tbl-permohonan thead th {
            background: var(--vp-surface);
            border-bottom: 2px solid var(--vp-border);
            color: var(--vp-muted);
            font-size: .7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .6px;
            padding: .85rem 1rem;
            white-space: nowrap;
            vertical-align: middle;
        }

        #tbl-permohonan thead th.sorting::after,
        #tbl-permohonan thead th.sorting_asc::after,
        #tbl-permohonan thead th.sorting_desc::after {
            opacity: .5;
        }

        #tbl-permohonan tbody tr {
            transition: background .1s;
        }

        #tbl-permohonan tbody tr:hover {
            background: #f0f5ff !important;
        }

        #tbl-permohonan tbody td {
            padding: .9rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--vp-border);
            border-top: none;
            color: var(--vp-ink);
            font-size: .85rem;
        }

        /* ── App Number ─────────────────────────────── */
        .vp-appnum {
            font-family: 'SFMono-Regular', Consolas, monospace;
            font-size: .78rem;
            font-weight: 700;
            color: var(--vp-blue);
            background: var(--vp-blue-lt);
            border-radius: 6px;
            padding: .25rem .55rem;
            letter-spacing: .3px;
        }

        /* ── Avatar Chip ─────────────────────────────── */
        .vp-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #005db6, #3b82f6);
            color: #fff;
            font-size: .72rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        /* ── Status Badge ───────────────────────────── */
        .vp-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            border-radius: 20px;
            padding: .3rem .75rem;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
        }

        .vp-badge i {
            font-size: .55rem;
        }

        .vp-badge--submitted {
            background: var(--vp-blue-lt);
            color: var(--vp-blue);
        }

        .vp-badge--revision {
            background: var(--vp-amber-lt);
            color: #b45309;
        }

        .vp-badge--verified {
            background: var(--vp-green-lt);
            color: var(--vp-green);
        }

        .vp-badge--in-progress {
            background: #e0f2fe;
            color: #0369a1;
        }

        .vp-progress-note {
            margin-top: .45rem;
            width: 126px;
        }

        .vp-progress-text {
            display: flex;
            justify-content: space-between;
            margin-bottom: .2rem;
            font-size: .68rem;
            font-weight: 700;
            color: var(--vp-muted);
        }

        .vp-progress-track {
            height: 5px;
            overflow: hidden;
            border-radius: 999px;
            background: #e5e7eb;
        }

        .vp-progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--vp-blue), #0ea5e9);
        }

        /* ── HK Chip ────────────────────────────────── */
        .vp-hk {
            display: inline-block;
            border-radius: 5px;
            padding: .2rem .5rem;
            font-size: .7rem;
            font-weight: 700;
            margin-top: .25rem;
        }

        .vp-hk--ok {
            background: var(--vp-blue-lt);
            color: var(--vp-blue);
        }

        .vp-hk--warn {
            background: var(--vp-amber-lt);
            color: #b45309;
        }

        .vp-hk--overdue {
            background: var(--vp-red-lt);
            color: var(--vp-red);
        }

        .vp-hk--done {
            background: var(--vp-green-lt);
            color: var(--vp-green);
        }

        /* ── Detail Button ──────────────────────────── */
        .vp-btn-detail {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .85rem;
            border-radius: 8px;
            font-size: .78rem;
            font-weight: 700;
            color: var(--vp-blue);
            background: var(--vp-blue-lt);
            border: 1.5px solid #c2d9f5;
            text-decoration: none;
            white-space: nowrap;
            transition: background .15s, box-shadow .15s;
        }

        .vp-btn-detail:hover {
            background: #d4e8fb;
            box-shadow: 0 2px 8px rgba(0, 93, 182, .18);
            text-decoration: none;
            color: var(--vp-blue);
        }

        /* ── DataTables override ────────────────────── */
        div.dataTables_wrapper div.dataTables_length select,
        div.dataTables_wrapper div.dataTables_filter input {
            border-radius: 8px;
            border: 1.5px solid var(--vp-border);
            font-size: .82rem;
            padding: .25rem .5rem;
            color: var(--vp-ink);
        }

        div.dataTables_wrapper div.dataTables_filter input:focus {
            border-color: var(--vp-blue);
            box-shadow: 0 0 0 3px rgba(0, 93, 182, .12);
            outline: none;
        }

        div.dataTables_wrapper div.dataTables_info {
            font-size: .78rem;
            color: var(--vp-muted);
            padding-top: .7rem;
        }

        div.dataTables_wrapper div.dataTables_paginate {
            padding-top: .4rem;
        }

        div.dataTables_wrapper div.dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            font-size: .78rem;
            font-weight: 600;
        }

        div.dataTables_wrapper div.dataTables_paginate .paginate_button.current,
        div.dataTables_wrapper div.dataTables_paginate .paginate_button.current:hover {
            background: var(--vp-blue) !important;
            border-color: var(--vp-blue) !important;
            color: #fff !important;
        }

        div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover {
            background: var(--vp-blue-lt) !important;
            border-color: var(--vp-border) !important;
            color: var(--vp-blue) !important;
        }

        /* ── Empty State ────────────────────────────── */
        .vp-empty {
            text-align: center;
            padding: 3.5rem 1rem;
            color: var(--vp-muted);
        }

        .vp-empty-icon {
            font-size: 2.5rem;
            margin-bottom: .75rem;
            opacity: .35;
        }

        .vp-empty-title {
            font-size: .95rem;
            font-weight: 700;
            margin-bottom: .25rem;
            color: var(--vp-ink);
        }

        .vp-empty-sub {
            font-size: .8rem;
        }
    </style>
@endpush

@section('content')
    @php
        $statusOptions = [
            'all' => 'Semua Status',
            'submitted' => 'Submitted',
            'need_revision' => 'Need Revision',
            'verified' => 'Verified',
        ];

        $addBusinessDays = function (\Carbon\Carbon $startDate, int $days) {
            $date = $startDate->copy();
            $count = 0;
            while ($count < $days) {
                $date->addDay();
                if (!$date->isWeekend()) {
                    $count++;
                }
            }
            return $date;
        };

        $remainingBusinessDays = function (?\Carbon\Carbon $deadline) {
            if (!$deadline) {
                return null;
            }
            $now = \Carbon\Carbon::today();
            $days = 0;
            $step = $deadline->gt($now) ? 1 : -1;
            $cur = $now->copy();
            while (!$cur->isSameDay($deadline)) {
                $cur->addDays($step);
                if (!$cur->isWeekend()) {
                    $days += $step;
                }
            }
            return $days;
        };
    @endphp

    {{-- ══════════════════════════════
         Stat Cards
    ══════════════════════════════ --}}
    <div class="row mb-6" style="gap:0;">
        <div class="col-12 col-sm-4 mb-4 mb-sm-0 pr-sm-3">
            <div class="vp-stat vp-stat--total">
                <div class="vp-stat-icon">
                    <i class="flaticon2-layers-1 text-white"></i>
                </div>
                <div>
                    <div class="vp-stat-num">{{ $applications->total() }}</div>
                    <div class="vp-stat-lbl">Total Permohonan</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4 mb-4 mb-sm-0 px-sm-2">
            <div class="vp-stat vp-stat--pending">
                <div class="vp-stat-icon">
                    <i class="flaticon2-hourglass text-white"></i>
                </div>
                <div>
                    <div class="vp-stat-num">{{ $totalSubmittedAll ?? '—' }}</div>
                    <div class="vp-stat-lbl">Menunggu Verifikasi</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4 pl-sm-3">
            <div class="vp-stat vp-stat--done">
                <div class="vp-stat-icon">
                    <i class="flaticon2-check-mark text-white"></i>
                </div>
                <div>
                    <div class="vp-stat-num">{{ $totalVerifiedAll ?? '—' }}</div>
                    <div class="vp-stat-lbl">Terverifikasi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════
         Main Card
    ══════════════════════════════ --}}
    <div class="vp-card">

        {{-- Card Head: judul + filter --}}
        <div class="vp-card-head">
            <div class="vp-card-title">
                <span class="vp-card-title-dot"></span>
                Daftar Permohonan
            </div>

            <form method="GET" action="{{ route('pengadaan.permohonan.index') }}" class="vp-filter" id="filterForm">
                {{-- Search --}}
                <div class="input-group" style="width:210px;">
                    <input type="text" name="q" id="filterQ" value="{{ $search }}"
                        class="form-control form-control-sm" placeholder="Nomor, vendor, NPWP…">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="flaticon-search" style="font-size:.7rem;"></i>
                        </span>
                    </div>
                </div>

                {{-- Status --}}
                <select name="status" id="filterStatus" class="form-control form-control-sm" style="width:160px;">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                {{-- Aksi --}}
                <button type="submit" class="vp-btn-filter">
                    <i class="flaticon-search" style="font-size:.7rem;"></i> Filter
                </button>
                <a href="{{ route('pengadaan.permohonan.index') }}" class="vp-btn-reset">
                    Reset
                </a>
            </form>
        </div>

        {{-- Table --}}
        <div class="card-body p-0 px-4 pt-5 pb-6">
            <table id="tbl-permohonan" class="table table-borderless" style="width:100%">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Vendor</th>
                        <th>Perusahaan</th>
                        <th>Status</th>
                        <th>Tgl Submit</th>
                        <th>Deadline 10 HK</th>
                        <th class="text-right no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        @php
                            $verificationTotal = (int) ($application->verification_items_count ?? 0);
                            $verificationApproved = (int) ($application->verification_approved_count ?? 0);
                            $verificationRejected = (int) ($application->verification_rejected_count ?? 0);
                            $verificationPending = (int) ($application->verification_pending_count ?? 0);
                            $verificationProcessed = $verificationApproved + $verificationRejected;
                            $isVerificationInProgress =
                                $verificationTotal > 0 && $verificationProcessed > 0 && $verificationPending > 0;
                            $verificationProgressPct =
                                $verificationTotal > 0 ? round(($verificationProcessed / $verificationTotal) * 100) : 0;
                            $isRevisionResubmitted =
                                $application->status === 'submitted' && !empty($application->revision_submitted_at);

                            $statusCls =
                                [
                                    'submitted' => 'vp-badge--submitted',
                                    'need_revision' => 'vp-badge--revision',
                                    'verified' => 'vp-badge--verified',
                                ][$application->status] ?? '';

                            $statusLabel =
                                [
                                    'submitted' => 'Submitted',
                                    'need_revision' => 'Need Revision',
                                    'verified' => 'Verified',
                                ][$application->status] ?? ucwords(str_replace('_', ' ', $application->status));

                            $statusIcon =
                                [
                                    'submitted' => 'flaticon2-hourglass',
                                    'need_revision' => 'flaticon-warning',
                                    'verified' => 'flaticon2-check-mark',
                                ][$application->status] ?? 'flaticon2-information';

                            if ($isVerificationInProgress) {
                                $statusCls = 'vp-badge--in-progress';
                                $statusLabel = 'Dalam Verifikasi';
                                $statusIcon = 'flaticon2-writing';
                            } elseif ($isRevisionResubmitted) {
                                $statusCls = 'vp-badge--revision';
                                $statusLabel = 'Revisi Dikirim';
                                $statusIcon = 'flaticon2-refresh';
                            }

                            $deadline = $application->submitted_at
                                ? $addBusinessDays($application->submitted_at, 10)
                                : null;

                            $hkLeft = $remainingBusinessDays($deadline);

                            if ($hkLeft === null) {
                                $hkCls = '';
                                $hkText = null;
                            } elseif ($application->status !== 'submitted') {
                                $hkCls = 'vp-hk--done';
                                $hkText = 'Selesai';
                            } elseif ($hkLeft < 0) {
                                $hkCls = 'vp-hk--overdue';
                                $hkText = abs($hkLeft) . ' HK terlambat';
                            } elseif ($hkLeft <= 3) {
                                $hkCls = 'vp-hk--warn';
                                $hkText = $hkLeft . ' HK tersisa';
                            } else {
                                $hkCls = 'vp-hk--ok';
                                $hkText = $hkLeft . ' HK tersisa';
                            }

                            $initials = strtoupper(substr(optional($application->user)->name ?? 'V', 0, 1));
                        @endphp
                        <tr>
                            {{-- Nomor --}}
                            <td>
                                <span class="vp-appnum">
                                    {{ $application->application_number ?? '-' }}
                                </span>
                            </td>

                            {{-- Vendor --}}
                            <td>
                                <div class="d-flex align-items-center" style="gap:.65rem;">
                                    <div class="vp-avatar">{{ $initials }}</div>
                                    <div>
                                        <div class="font-weight-bold text-dark" style="font-size:.84rem;">
                                            {{ optional($application->user)->name ?? '-' }}
                                        </div>
                                        <div style="font-size:.72rem; color:var(--vp-muted);">
                                            {{ optional($application->user)->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Perusahaan --}}
                            <td>
                                <div class="font-weight-bold" style="font-size:.84rem; color:var(--vp-ink);">
                                    {{ optional($application->general)->nama_perusahaan ?? '-' }}
                                </div>
                                <div style="font-size:.72rem; color:var(--vp-muted);">
                                    NPWP: {{ optional($application->general)->npwp ?? '-' }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="vp-badge {{ $statusCls }}">
                                    <i class="{{ $statusIcon }}"></i>
                                    {{ $statusLabel }}
                                </span>
                                @if ($isVerificationInProgress)
                                    <div class="vp-progress-note">
                                        <div class="vp-progress-text">
                                            <span>{{ $verificationProcessed }}/{{ $verificationTotal }} selesai</span>
                                            <span>{{ $verificationPending }} sisa</span>
                                        </div>
                                        <div class="vp-progress-track">
                                            <div class="vp-progress-fill"
                                                style="width: {{ min(100, max(0, $verificationProgressPct)) }}%;"></div>
                                        </div>
                                    </div>
                                @elseif ($isRevisionResubmitted)
                                    <div class="vp-progress-note">
                                        <div class="vp-progress-text">
                                            <span>Revisi ke-{{ (int) $application->revision_count }}</span>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            {{-- Tgl Submit --}}
                            <td>
                                @if ($application->submitted_at)
                                    <div style="font-size:.83rem; font-weight:600; color:var(--vp-ink);">
                                        {{ $application->submitted_at->format('d/m/Y') }}
                                    </div>
                                    <div style="font-size:.72rem; color:var(--vp-muted);">
                                        {{ $application->submitted_at->format('H:i') }}
                                    </div>
                                    @if ($isRevisionResubmitted && $application->revision_submitted_at)
                                        <div class="mt-1" style="font-size:.7rem; color:#b45309; font-weight:700;">
                                            Revisi:
                                            {{ $application->revision_submitted_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                @else
                                    <span style="color:var(--vp-muted);">—</span>
                                @endif
                            </td>

                            {{-- Deadline --}}
                            <td>
                                @if ($deadline)
                                    <div style="font-size:.83rem; font-weight:600; color:var(--vp-ink);">
                                        {{ $deadline->format('d/m/Y') }}
                                    </div>
                                    @if ($hkText)
                                        <span class="vp-hk {{ $hkCls }}">{{ $hkText }}</span>
                                    @endif
                                @else
                                    <span style="color:var(--vp-muted);">—</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-right">
                                <a href="{{ route('pengadaan.permohonan.show', $application) }}" class="vp-btn-detail">
                                    <i class="flaticon-eye" style="font-size:.75rem;"></i>
                                    {{ $isVerificationInProgress || $isRevisionResubmitted ? 'Lanjutkan' : 'Detail' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="vp-empty">
                                    <div class="vp-empty-icon">
                                        <i class="flaticon2-search-1"></i>
                                    </div>
                                    <div class="vp-empty-title">Tidak ada permohonan</div>
                                    <div class="vp-empty-sub">Coba ubah filter pencarian.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function() {
            var table = $('#tbl-permohonan').DataTable({
                pageLength: 15,
                lengthMenu: [10, 15, 25, 50],
                order: [
                    [4, 'desc']
                ], // default sort: tgl submit terbaru
                language: {
                    search: '',
                    searchPlaceholder: 'Cari di tabel…',
                    lengthMenu: 'Tampilkan _MENU_ baris',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total)',
                    zeroRecords: 'Tidak ada hasil yang cocok',
                    paginate: {
                        first: '«',
                        last: '»',
                        next: '›',
                        previous: '‹',
                    }
                },
                columnDefs: [{
                        orderable: false,
                        targets: -1
                    }, // kolom Aksi tidak bisa sort
                ],
                // Sembunyikan built-in search DataTables (kita sudah ada filter sendiri di header)
                dom: '<"d-flex justify-content-between align-items-center mb-4"lf>rtip',
                initComplete: function() {
                    // Pindah built-in search DT ke toolbar kita (opsional — bisa dihapus)
                    // Jika ingin pakai DT search terpisah, uncomment baris bawah:
                    // $('#tbl-permohonan_filter').prependTo('.vp-filter');
                }
            });

            // ── Sinkronisasi filter server-side (q & status) dengan DT search ──
            // Ketik di filter header → filter DT juga (untuk filter di halaman saat ini)
            $('#filterQ').on('keyup', function() {
                table.search($(this).val()).draw();
            });
            $('#filterStatus').on('change', function() {
                var val = $(this).val();
                if (val === 'all') {
                    table.column(3).search('').draw();
                } else {
                    // Search kolom Status berdasar text badge
                    var labelMap = {
                        'submitted': 'Submitted',
                        'need_revision': 'Need Revision',
                        'verified': 'Verified',
                    };
                    table.column(3).search(labelMap[val] || '').draw();
                }
            });
        });
    </script>
@endpush
