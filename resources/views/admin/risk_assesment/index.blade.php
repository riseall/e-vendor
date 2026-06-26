@extends('layouts.app', ['title' => 'Risk Assessment'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Risk Assessment')
@section('page_title', 'Risk Assessment Vendor Baru')
@section('page_desc', 'Daftar vendor yang sudah diverifikasi pengadaan dan perlu penilaian risiko QA.')

@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        :root {
            --ra-blue: #005db6;
            --ra-blue-lt: #e8f1fb;
            --ra-blue-bd: #c2d9f5;
            --ra-green: #2e7d32;
            --ra-green-lt: #e8f5e9;
            --ra-green-bd: #a5d6a7;
            --ra-amber: #b45309;
            --ra-amber-lt: #fff8e7;
            --ra-amber-bd: #fcd97a;
            --ra-red: #c62828;
            --ra-red-lt: #fdecea;
            --ra-red-bd: #ef9a9a;
            --ra-ink: #1a2235;
            --ra-muted: #6b7a96;
            --ra-border: #e6eaf2;
            --ra-surface: #f5f7fb;
        }

        /* ── Stat Cards ───────────────────────────────── */
        .ra-stat {
            border-radius: 12px;
            padding: 1.1rem 1.3rem;
            display: flex;
            align-items: center;
            gap: .9rem;
            border: 1.5px solid transparent;
            transition: transform .15s, box-shadow .15s;
            background: #fff;
        }

        .ra-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, .07);
        }

        .ra-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.15rem;
            color: #fff;
        }

        .ra-stat-num {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1;
        }

        .ra-stat-lbl {
            font-size: .62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .55px;
            margin-top: .2rem;
        }

        .ra-stat--total {
            border-color: var(--ra-blue-bd);
            background: var(--ra-blue-lt);
        }

        .ra-stat--total .ra-stat-icon {
            background: var(--ra-blue);
        }

        .ra-stat--total .ra-stat-num {
            color: var(--ra-blue);
        }

        .ra-stat--total .ra-stat-lbl {
            color: #3d6fa8;
        }

        .ra-stat--low {
            border-color: var(--ra-green-bd);
            background: var(--ra-green-lt);
        }

        .ra-stat--low .ra-stat-icon {
            background: var(--ra-green);
        }

        .ra-stat--low .ra-stat-num {
            color: var(--ra-green);
        }

        .ra-stat--low .ra-stat-lbl {
            color: #2d6e30;
        }

        .ra-stat--medium {
            border-color: var(--ra-amber-bd);
            background: var(--ra-amber-lt);
        }

        .ra-stat--medium .ra-stat-icon {
            background: var(--ra-amber);
        }

        .ra-stat--medium .ra-stat-num {
            color: var(--ra-amber);
        }

        .ra-stat--medium .ra-stat-lbl {
            color: #8a4e08;
        }

        .ra-stat--high {
            border-color: var(--ra-red-bd);
            background: var(--ra-red-lt);
        }

        .ra-stat--high .ra-stat-icon {
            background: var(--ra-red);
        }

        .ra-stat--high .ra-stat-num {
            color: var(--ra-red);
        }

        .ra-stat--high .ra-stat-lbl {
            color: #9e2020;
        }

        /* ── Main Card ────────────────────────────────── */
        .ra-card {
            border: 1.5px solid var(--ra-border);
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
        }

        .ra-card-head {
            padding: 1rem 1.4rem;
            border-bottom: 1.5px solid var(--ra-border);
            background: var(--ra-surface);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .9rem;
        }

        .ra-card-title {
            font-size: .8rem;
            font-weight: 800;
            color: var(--ra-ink);
            text-transform: uppercase;
            letter-spacing: .7px;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .ra-card-title-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--ra-blue);
            flex-shrink: 0;
        }

        .ra-threshold-tag {
            font-size: .68rem;
            font-weight: 600;
            color: var(--ra-muted);
            display: flex;
            align-items: center;
            gap: .4rem;
            flex-wrap: wrap;
        }

        .ra-threshold-tag span {
            border-radius: 4px;
            padding: .15rem .5rem;
            font-weight: 700;
        }

        /* ── Filter Bar ───────────────────────────────── */
        .ra-filter {
            display: flex;
            align-items: center;
            gap: .55rem;
            flex-wrap: wrap;
        }

        .ra-filter .form-control {
            border-radius: 7px;
            border: 1.5px solid var(--ra-border);
            font-size: .78rem;
            height: 32px;
            color: var(--ra-ink);
        }

        .ra-filter .form-control:focus {
            border-color: var(--ra-blue);
            box-shadow: 0 0 0 3px rgba(0, 93, 182, .1);
        }

        .ra-filter .input-group-text {
            border: 1.5px solid var(--ra-border);
            border-left: 0;
            border-radius: 0 7px 7px 0;
            background: var(--ra-surface);
            color: var(--ra-muted);
            height: 32px;
        }

        .ra-filter .input-group input {
            border-right: 0;
            border-radius: 7px 0 0 7px;
        }

        .ra-btn-filter {
            height: 32px;
            padding: 0 .9rem;
            border-radius: 7px;
            font-size: .78rem;
            font-weight: 700;
            background: var(--ra-blue);
            color: #fff;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            transition: background .15s;
        }

        .ra-btn-filter:hover {
            background: #004f9e;
        }

        .ra-btn-reset {
            height: 32px;
            padding: 0 .8rem;
            border-radius: 7px;
            font-size: .78rem;
            font-weight: 700;
            border: 1.5px solid var(--ra-border);
            background: #fff;
            color: var(--ra-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: border-color .15s, color .15s;
        }

        .ra-btn-reset:hover {
            border-color: var(--ra-blue);
            color: var(--ra-blue);
            text-decoration: none;
        }

        /* ── Table ────────────────────────────────────── */
        #tbl-risk {
            width: 100% !important;
        }

        #tbl-risk thead th {
            background: var(--ra-surface);
            border-bottom: 2px solid var(--ra-border);
            color: var(--ra-muted);
            font-size: .65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .55px;
            padding: .8rem 1rem;
            white-space: nowrap;
        }

        #tbl-risk tbody td {
            padding: .85rem 1rem;
            border-bottom: 1px solid var(--ra-border);
            border-top: none;
            vertical-align: middle;
            font-size: .82rem;
            color: var(--ra-ink);
        }

        #tbl-risk tbody tr:hover td {
            background: #f0f5ff;
        }

        #tbl-risk tbody tr:last-child td {
            border-bottom: none;
        }

        /* ── App Number ───────────────────────────────── */
        .ra-appnum {
            font-family: 'SFMono-Regular', Consolas, monospace;
            font-size: .72rem;
            font-weight: 700;
            color: var(--ra-blue);
            background: var(--ra-blue-lt);
            border: 1.5px solid var(--ra-blue-bd);
            border-radius: 5px;
            padding: .22rem .55rem;
        }

        /* ── Avatar ───────────────────────────────────── */
        .ra-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #005db6, #3b82f6);
            color: #fff;
            font-size: .65rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        /* ── Category chips ───────────────────────────── */
        .ra-cat-chip {
            display: inline-block;
            border-radius: 4px;
            padding: .15rem .5rem;
            font-size: .65rem;
            font-weight: 700;
            background: var(--ra-surface);
            color: var(--ra-muted);
            border: 1px solid var(--ra-border);
            margin: 1px 2px 1px 0;
            white-space: nowrap;
        }

        /* ── Score pill ───────────────────────────────── */
        .ra-score {
            font-family: 'SFMono-Regular', Consolas, monospace;
            font-size: .78rem;
            font-weight: 800;
            color: var(--ra-ink);
        }

        .ra-score-empty {
            color: var(--ra-muted);
            font-style: italic;
            font-size: .76rem;
        }

        /* ── Risk level badge ─────────────────────────── */
        .ra-risk {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            border-radius: 20px;
            padding: .28rem .8rem;
            font-size: .68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            white-space: nowrap;
        }

        .ra-risk--low {
            background: var(--ra-green-lt);
            color: var(--ra-green);
            border: 1.5px solid var(--ra-green-bd);
        }

        .ra-risk--medium {
            background: var(--ra-amber-lt);
            color: var(--ra-amber);
            border: 1.5px solid var(--ra-amber-bd);
        }

        .ra-risk--high {
            background: var(--ra-red-lt);
            color: var(--ra-red);
            border: 1.5px solid var(--ra-red-bd);
        }

        .ra-risk--none {
            background: var(--ra-surface);
            color: var(--ra-muted);
            border: 1.5px solid var(--ra-border);
        }

        /* ── Status badge ─────────────────────────────── */
        .ra-status {
            display: inline-block;
            border-radius: 4px;
            padding: .2rem .55rem;
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            background: var(--ra-blue-lt);
            color: var(--ra-blue);
            border: 1px solid var(--ra-blue-bd);
        }

        /* ── Action button ────────────────────────────── */
        .ra-btn-action {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .32rem .85rem;
            border-radius: 7px;
            font-size: .74rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
            transition: background .15s, box-shadow .15s;
        }

        .ra-btn-action--new {
            background: var(--ra-blue);
            color: #fff;
        }

        .ra-btn-action--new:hover {
            background: #004f9e;
            box-shadow: 0 3px 8px rgba(0, 93, 182, .25);
            color: #fff;
            text-decoration: none;
        }

        .ra-btn-action--edit {
            background: var(--ra-surface);
            color: var(--ra-ink);
            border: 1.5px solid var(--ra-border) !important;
        }

        .ra-btn-action--edit:hover {
            background: #eef2ff;
            border-color: var(--ra-blue) !important;
            color: var(--ra-blue);
            text-decoration: none;
        }

        /* ── DataTable overrides ──────────────────────── */
        div.dataTables_wrapper div.dataTables_length select,
        div.dataTables_wrapper div.dataTables_filter input {
            border-radius: 7px;
            border: 1.5px solid var(--ra-border);
            font-size: .78rem;
            padding: .2rem .5rem;
            color: var(--ra-ink);
        }

        div.dataTables_wrapper div.dataTables_filter input:focus {
            border-color: var(--ra-blue);
            box-shadow: 0 0 0 3px rgba(0, 93, 182, .1);
            outline: none;
        }

        div.dataTables_wrapper div.dataTables_info {
            font-size: .74rem;
            color: var(--ra-muted);
            padding-top: .6rem;
        }

        div.dataTables_wrapper div.dataTables_paginate {
            padding-top: .3rem;
        }

        div.dataTables_wrapper div.dataTables_paginate .paginate_button {
            border-radius: 7px !important;
            font-size: .74rem;
            font-weight: 600;
        }

        div.dataTables_wrapper div.dataTables_paginate .paginate_button.current,
        div.dataTables_wrapper div.dataTables_paginate .paginate_button.current:hover {
            background: var(--ra-blue) !important;
            border-color: var(--ra-blue) !important;
            color: #fff !important;
        }

        div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover {
            background: var(--ra-blue-lt) !important;
            border-color: var(--ra-border) !important;
            color: var(--ra-blue) !important;
        }

        /* ── Empty state ──────────────────────────────── */
        .ra-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--ra-muted);
        }

        .ra-empty i {
            font-size: 2rem;
            opacity: .35;
            display: block;
            margin-bottom: .6rem;
        }

        .ra-empty-title {
            font-size: .88rem;
            font-weight: 700;
            color: var(--ra-ink);
        }

        .ra-empty-sub {
            font-size: .76rem;
            margin-top: .25rem;
        }
    </style>
@endpush

@section('content')
    @php
        /* Count per level untuk stat cards — hitung dari semua record (bukan hanya current page).
 Pastikan controller pass $countByLevel atau gunakan fallback dari collection page ini. */
        $countTotal = $applications->total();
        $countLow = $countByLevel['low'] ?? $applications->getCollection()->where('risk_level', 'low')->count();
        $countMedium =
            $countByLevel['medium'] ?? $applications->getCollection()->where('risk_level', 'medium')->count();
        $countHigh = $countByLevel['high'] ?? $applications->getCollection()->where('risk_level', 'high')->count();
    @endphp

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert alert-custom alert-light-success fade show mb-6" role="alert">
            <div class="alert-icon"><i class="flaticon2-check-mark"></i></div>
            <div class="alert-text">{{ session('success') }}</div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        </div>
    @endif

    {{-- ══════ Stat Cards ══════ --}}
    <div class="row mb-6">
        <div class="col-6 col-sm-3 mb-3 mb-sm-0">
            <div class="ra-stat ra-stat--total">
                <div class="ra-stat-icon"><i class="flaticon2-layers-1"></i></div>
                <div>
                    <div class="ra-stat-num">{{ $countTotal }}</div>
                    <div class="ra-stat-lbl">Total Vendor</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3 mb-3 mb-sm-0">
            <div class="ra-stat ra-stat--low">
                <div class="ra-stat-icon"><i class="flaticon2-check-mark"></i></div>
                <div>
                    <div class="ra-stat-num">{{ $countLow }}</div>
                    <div class="ra-stat-lbl">Low Risk</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3 mb-3 mb-sm-0 pr-sm-2">
            <div class="ra-stat ra-stat--medium">
                <div class="ra-stat-icon"><i class="flaticon-warning"></i></div>
                <div>
                    <div class="ra-stat-num">{{ $countMedium }}</div>
                    <div class="ra-stat-lbl">Medium Risk</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="ra-stat ra-stat--high">
                <div class="ra-stat-icon"><i class="flaticon-danger"></i></div>
                <div>
                    <div class="ra-stat-num">{{ $countHigh }}</div>
                    <div class="ra-stat-lbl">High Risk</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════ Main Card ══════ --}}
    <div class="ra-card">

        {{-- Head: judul + threshold info + filter --}}
        <div class="ra-card-head">
            <div>
                <div class="ra-card-title">
                    <span class="ra-card-title-dot"></span>
                    Risk Assessment Vendor
                </div>
                <div class="ra-threshold-tag mt-1">
                    Threshold:
                    <span style="background:var(--ra-green-lt); color:var(--ra-green);">
                        Low ≤ {{ $lowThreshold }}
                    </span>
                    <span style="background:var(--ra-amber-lt); color:var(--ra-amber);">
                        Medium {{ $lowThreshold + 1 }}–{{ $highThreshold }}
                    </span>
                    <span style="background:var(--ra-red-lt); color:var(--ra-red);">
                        High > {{ $highThreshold }}
                    </span>
                </div>
            </div>

            <form method="GET" action="{{ route('qa.risk-assessment.index') }}" class="ra-filter">
                <select name="status" class="form-control form-control-sm" style="width:160px;">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <div class="input-group" style="width:210px;">
                    <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm"
                        placeholder="Nomor, vendor, email…">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="flaticon-search" style="font-size:.65rem;"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="ra-btn-filter">
                    <i class="flaticon-search" style="font-size:.65rem;"></i> Filter
                </button>
                <a href="{{ route('qa.risk-assessment.index') }}" class="ra-btn-reset">Reset</a>
            </form>
        </div>

        {{-- Table --}}
        <div class="card-body p-0 px-4 pt-5 pb-6">
            <table id="tbl-risk" class="table table-borderless" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Permohonan</th>
                        <th>Vendor</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Total Nilai</th>
                        <th>Risk Level</th>
                        <th class="text-right no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        @php
                            $qualification = $application->qualification;

                            $riskLevel = $application->risk_level;
                            $riskCls =
                                [
                                    'low' => 'ra-risk--low',
                                    'medium' => 'ra-risk--medium',
                                    'high' => 'ra-risk--high',
                                ][$riskLevel] ?? 'ra-risk--none';
                            $riskIcon =
                                [
                                    'low' => 'flaticon2-check-mark',
                                    'medium' => 'flaticon-warning',
                                    'high' => 'flaticon-danger',
                                ][$riskLevel] ?? 'flaticon2-information';
                            $riskLabel = $riskLevel ? strtoupper($riskLevel) : '—';

                            $cats = $application->categories
                                ->pluck('category_id')
                                ->map(function ($id) {
                                    return \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id;
                                })
                                ->filter()
                                ->values();

                            $initial = strtoupper(
                                substr(
                                    optional($application->general)->nama_perusahaan ??
                                        (optional($application->user)->name ?? 'V'),
                                    0,
                                    1,
                                ),
                            );

                            $isAssessed = (bool) $qualification;
                        @endphp
                        <tr>
                            {{-- # --}}
                            <td style="color:var(--ra-muted); font-size:.78rem;">
                                {{ $applications->firstItem() + $loop->index }}
                            </td>

                            {{-- No Permohonan --}}
                            <td>
                                <span class="ra-appnum">{{ $application->application_number ?? '—' }}</span>
                            </td>

                            {{-- Vendor --}}
                            <td>
                                <div class="d-flex align-items-center" style="gap:.65rem;">
                                    <div class="ra-avatar">{{ $initial }}</div>
                                    <div>
                                        <div style="font-weight:700; font-size:.82rem; color:var(--ra-ink);">
                                            {{ optional($application->general)->nama_perusahaan ?? (optional($application->user)->name ?? '—') }}
                                        </div>
                                        <div style="font-size:.7rem; color:var(--ra-muted);">
                                            {{ optional($application->general)->email_perusahaan ?? (optional($application->user)->email ?? '—') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kategori --}}
                            <td>
                                @forelse ($cats as $cat)
                                    <span class="ra-cat-chip">{{ $cat }}</span>
                                @empty
                                    <span style="color:var(--ra-muted);">—</span>
                                @endforelse
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="ra-status">
                                    {{ str_replace('_', ' ', strtoupper($application->status)) }}
                                </span>
                            </td>

                            {{-- Total Nilai --}}
                            <td>
                                @if ($qualification)
                                    <span
                                        class="ra-score">{{ number_format($qualification->total_score, 0, ',', '.') }}</span>
                                @else
                                    <span class="ra-score-empty">Belum dinilai</span>
                                @endif
                            </td>

                            {{-- Risk Level --}}
                            <td>
                                <span class="ra-risk {{ $riskCls }}">
                                    <i class="{{ $riskIcon }}" style="font-size:.55rem;"></i>
                                    {{ $riskLabel }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="text-right">
                                <a href="{{ route('qa.risk-assessment.create', $application->id) }}"
                                    class="ra-btn-action {{ $isAssessed ? 'ra-btn-action--edit' : 'ra-btn-action--new' }}">
                                    @if ($isAssessed)
                                        <i class="flaticon-eye" style="font-size:.7rem;"></i> Lihat / Ubah
                                    @else
                                        <i class="flaticon2-add-1" style="font-size:.7rem;"></i> Mulai Assessment
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="ra-empty">
                                    <i class="flaticon2-search-1"></i>
                                    <div class="ra-empty-title">Tidak ada vendor</div>
                                    <div class="ra-empty-sub">Coba ubah filter pencarian.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>{{-- /ra-card --}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function() {
            var table = $('#tbl-risk').DataTable({
                pageLength: 15,
                lengthMenu: [10, 15, 25, 50],
                order: [
                    [1, 'asc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }, ],
                dom: '<"d-flex justify-content-between align-items-center mb-4"lf>rtip',
            });

            /* Sinkron filter server-side ke DT search */
            $('input[name="q"]').on('keyup', function() {
                table.search($(this).val()).draw();
            });
            $('select[name="status"]').on('change', function() {
                var v = $(this).val();
                table.column(4).search(v === 'all' ? '' : v.replace('_', ' ')).draw();
            });
        });
    </script>
@endpush
