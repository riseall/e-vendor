@extends('layouts.app', ['title' => 'Risk Assessment'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Risk Assessment')
@section('page_title', 'Risk Assessment Vendor Baru')
@section('page_desc', 'Daftar vendor yang sudah diverifikasi pengadaan dan perlu penilaian risiko QA.')

@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/risk-assessment.css') }}">
    <style>
        /* Rapikan posisi & tipografi SweetAlert toast */
        .swal2-container.swal2-top-end {
            top: 1rem !important;
            right: 1rem;
            left: auto !important;
        }

        .swal2-popup.ra-swal-toast {
            border-radius: .5rem;
            align-items: flex-start;
            text-align: left;
        }

        .ra-swal-toast .swal2-icon {
            margin: 0 .75rem 0 0 !important;
            width: 1.75rem !important;
            height: 1.75rem !important;
            line-height: 1.75rem !important;
        }

        .ra-swal-toast__title {
            font-size: .95rem !important;
            font-weight: 600 !important;
            margin: 0 0 .15rem !important;
        }

        .ra-swal-toast__body {
            font-size: .8rem !important;
            color: #5a5a6b;
            margin: 0 !important;
        }

        .ra-swal-toast__close {
            color: #b5b5c3;
        }

        .ra-swal-toast__close:hover {
            color: #5a5a6b;
        }
    </style>
@endpush

@php
    // Stat-card counts come from controller ($countByLevel is total across pages).
    $countTotal = $applications->total();
    $countLow = $countByLevel['low'] ?? 0;
    $countMedium = $countByLevel['medium'] ?? 0;
    $countHigh = $countByLevel['high'] ?? 0;

    // Risk level → css class & icon, single source of truth.
    $riskMap = [
        'low' => ['class' => 'ra-risk--low', 'icon' => 'flaticon2-check-mark text-success'],
        'medium' => ['class' => 'ra-risk--medium', 'icon' => 'flaticon-warning text-warning'],
        'high' => ['class' => 'ra-risk--high', 'icon' => 'flaticon-danger text-danger'],
    ];
@endphp

@section('content')
    {{-- Flash (ditampilkan via SweetAlert di @push('scripts')) --}}
    <div id="ra-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- ══════ Stat Cards ══════ --}}
    <div class="row mb-6">
        @foreach ([['key' => 'total', 'num' => $countTotal, 'lbl' => 'Total Vendor', 'icon' => 'flaticon2-layers-1 text-white'], ['key' => 'low', 'num' => $countLow, 'lbl' => 'Low Risk', 'icon' => 'flaticon2-check-mark text-white'], ['key' => 'medium', 'num' => $countMedium, 'lbl' => 'Medium Risk', 'icon' => 'flaticon-warning text-white'], ['key' => 'high', 'num' => $countHigh, 'lbl' => 'High Risk', 'icon' => 'flaticon-danger text-white']] as $stat)
            <div class="col-6 col-sm-3 mb-3 mb-sm-0">
                <div class="ra-stat ra-stat--{{ $stat['key'] }}">
                    <div class="ra-stat-icon"><i class="{{ $stat['icon'] }}"></i></div>
                    <div>
                        <div class="ra-stat-num">{{ $stat['num'] }}</div>
                        <div class="ra-stat-lbl">{{ $stat['lbl'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
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
                    <span class="ra-tag ra-tag--low">Low &le; {{ $lowThreshold }}</span>
                    <span class="ra-tag ra-tag--medium">Medium {{ $lowThreshold + 1 }}&ndash;{{ $highThreshold }}</span>
                    <span class="ra-tag ra-tag--high">High &gt; {{ $highThreshold }}</span>
                </div>
            </div>

            <form method="GET" action="{{ route('qa.risk-assessment.index') }}" class="ra-filter">
                <div class="form-input" style="width:210px;">
                    <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm"
                        placeholder="Nomor, vendor, email&hellip;">
                </div>
                <select name="status" class="form-control form-control-sm" style="width:160px;">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
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
                            $risk = $riskMap[$application->risk_level] ?? [
                                'class' => 'ra-risk--none',
                                'icon' => 'flaticon2-information',
                            ];
                            $vendorName =
                                optional($application->general)->nama_perusahaan ??
                                (optional($application->user)->name ?? '—');
                            $vendorEmail =
                                optional($application->general)->email_perusahaan ??
                                (optional($application->user)->email ?? '—');
                            $cats = $application->categories
                                ->pluck('category_id')
                                ->map(fn($id) => \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id)
                                ->filter()
                                ->values();
                            $initial = strtoupper(substr(strip_tags($vendorName), 0, 1));
                            $isAssessed = (bool) $qualification;
                        @endphp
                        <tr>
                            <td class="ra-cell-muted">
                                {{ $applications->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <span class="ra-appnum">{{ $application->application_number ?? '—' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center" style="gap:.65rem;">
                                    <div class="ra-avatar">{{ $initial }}</div>
                                    <div>
                                        <div class="ra-vendor-name">{!! $vendorName !!}</div>
                                        <div class="ra-vendor-email">{!! $vendorEmail !!}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @forelse ($cats as $cat)
                                    <span class="ra-cat-chip">{{ $cat }}</span>
                                @empty
                                    <span class="ra-cell-muted">&mdash;</span>
                                @endforelse
                            </td>
                            <td>
                                <span class="ra-status">
                                    {{ str_replace('_', ' ', strtoupper($application->status)) }}
                                </span>
                            </td>
                            <td>
                                @if ($qualification)
                                    <span
                                        class="ra-score">{{ number_format($qualification->total_score, 0, ',', '.') }}</span>
                                @else
                                    <span class="ra-score-empty">Belum dinilai</span>
                                @endif
                            </td>
                            <td>
                                <span class="ra-risk {{ $risk['class'] }}">
                                    <i class="{{ $risk['icon'] }}" style="font-size:.55rem;"></i>
                                    {{ $application->risk_level ? strtoupper($application->risk_level) : '—' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('qa.risk-assessment.create', $application->id) }}"
                                    class="ra-btn-action {{ $isAssessed ? 'ra-btn-action--edit' : 'ra-btn-action--new' }}">
                                    @if ($isAssessed)
                                        <i class="flaticon-eye" style="font-size:.7rem;"></i> Lihat
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js">
        < /link> <
        script src = "https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js" >
    </script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function() {
            /* Tampilkan flash session sebagai SweetAlert */
            (function() {
                var $el = $('#ra-flash');
                if (!$el.length || typeof Swal === 'undefined') return;

                var messages = [{
                        key: 'success',
                        icon: 'success',
                        title: 'Sukses'
                    },
                    {
                        key: 'error',
                        icon: 'error',
                        title: 'Gagal'
                    },
                    {
                        key: 'warning',
                        icon: 'warning',
                        title: 'Peringatan'
                    },
                    {
                        key: 'info',
                        icon: 'info',
                        title: 'Informasi'
                    },
                ];

                messages.forEach(function(m) {
                    var msg = $el.data(m.key);
                    if (msg) {
                        Swal.fire({
                            icon: m.icon,
                            title: m.title,
                            text: msg,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            showCloseButton: true,
                            timer: 3500,
                            timerProgressBar: true,
                            width: 360,
                            padding: '1rem',
                            customClass: {
                                popup: 'ra-swal-toast shadow-sm',
                                title: 'ra-swal-toast__title',
                                htmlContainer: 'ra-swal-toast__body',
                                closeButton: 'ra-swal-toast__close',
                            },
                        });
                    }
                });
            })();

            var table = $('#tbl-risk').DataTable({
                pageLength: 15,
                lengthMenu: [10, 15, 25, 50],
                order: [
                    [1, 'asc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }],
                dom: '<"d-flex justify-content-between align-items-center mb-4"lf>rtip',
            });

            /* Sinkron filter status ke server (refresh halaman) */
            $('select[name="status"]').on('change', function() {
                $(this).closest('form').submit();
            });

            /* Filter search tambahan di client (opsional, tanpa reload) */
            $('input[name="q"]').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
@endpush
