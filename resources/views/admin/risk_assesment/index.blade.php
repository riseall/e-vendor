@extends('layouts.app', ['title' => 'Risk Assessment'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Risk Assessment')
@section('page_title', 'Risk Assessment Vendor Baru')
@section('page_desc', 'Daftar vendor yang sudah diverifikasi pengadaan dan perlu penilaian risiko QA.')

@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/vms.css') }}">
@endpush

@php
    // Stat-card counts come from controller ($countByLevel is total across pages).
    $countTotal = $applications->total();
    $countLow = $countByLevel['low'] ?? 0;
    $countMedium = $countByLevel['medium'] ?? 0;
    $countHigh = $countByLevel['high'] ?? 0;

    // Risk level → css class & icon, single source of truth.
    $riskMap = [
        'low' => ['class' => 'vnd-status--submitted', 'icon' => 'flaticon2-check-mark text-success'],
        'medium' => ['class' => 'vnd-status--revision', 'icon' => 'flaticon-warning text-warning'],
        'high' => ['class' => 'vnd-status--rejected', 'icon' => 'flaticon-danger text-danger'],
    ];
@endphp

@section('content')
    {{-- Flash (ditampilkan via SweetAlert di @push('scripts')) --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- ══════ Stat Cards ══════ --}}
    <div class="row mb-6">
        @foreach ([['key' => 'primary', 'num' => $countTotal, 'lbl' => 'Total Vendor', 'icon' => 'flaticon2-layers-1 text-white'], ['key' => 'success', 'num' => $countLow, 'lbl' => 'Low Risk', 'icon' => 'flaticon2-check-mark text-white'], ['key' => 'warning', 'num' => $countMedium, 'lbl' => 'Medium Risk', 'icon' => 'flaticon-warning text-white'], ['key' => 'danger', 'num' => $countHigh, 'lbl' => 'High Risk', 'icon' => 'flaticon-danger text-white']] as $stat)
            <div class="col-6 col-sm-3 mb-3 mb-sm-0">
                <div class="vnd-stat vnd-stat--{{ $stat['key'] }}">
                    <div class="vnd-stat-icon"><i class="{{ $stat['icon'] }}"></i></div>
                    <div>
                        <div class="vnd-stat-num">{{ $stat['num'] }}</div>
                        <div class="vnd-stat-lbl">{{ $stat['lbl'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ══════ Main Card ══════ --}}
    <div class="vnd-card">

        {{-- Head: judul + threshold info + filter --}}
        <div class="vnd-card-head">
            <div>
                <div class="vnd-card-title">
                    <span class="vnd-card-title-dot"></span>
                    Risk Assessment Vendor
                </div>
                <div class="vnd-threshold-tag mt-1">
                    Threshold:
                    <span class="vnd-tag vnd-tag--low">Low &le; {{ $lowThreshold }}</span>
                    <span class="vnd-tag vnd-tag--medium">Medium {{ $lowThreshold + 1 }}&ndash;{{ $highThreshold }}</span>
                    <span class="vnd-tag vnd-tag--high">High &gt; {{ $highThreshold }}</span>
                </div>
            </div>

            <form method="GET" action="{{ route('qa.risk-assessment.index') }}" class="vnd-filter">
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
                <button type="submit" class="vnd-btn-filter">
                    <i class="flaticon-search" style="font-size:.65rem;"></i> Filter
                </button>
                <a href="{{ route('qa.risk-assessment.index') }}" class="vnd-btn-reset">Reset</a>
            </form>
        </div>

        {{-- Table --}}
        <div class="card-body p-0 px-4 pt-5 pb-6">
            <table id="tbl-vendor" class="table tbl-vendor table-borderless" style="width:100%">
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
                                'class' => 'vnd-status--none',
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
                            <td class="vnd-cell-muted">
                                {{ $applications->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <span class="vnd-appnum">{{ $application->application_number ?? '—' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center" style="gap:.65rem;">
                                    <div class="vnd-avatar">{{ $initial }}</div>
                                    <div>
                                        <div class="vnd-vendor-name">{!! $vendorName !!}</div>
                                        <div class="vnd-vendor-email">{!! $vendorEmail !!}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @forelse ($cats as $cat)
                                    <span class="vnd-cat-chip">{{ $cat }}</span>
                                @empty
                                    <span class="vnd-cell-muted">&mdash;</span>
                                @endforelse
                            </td>
                            <td>
                                <span class="vnd-status vnd-status--in-progress">
                                    {{ str_replace('_', ' ', strtoupper($application->status)) }}
                                </span>
                            </td>
                            <td>
                                @if ($qualification)
                                    <span
                                        class="vnd-score">{{ number_format($qualification->total_score, 0, ',', '.') }}</span>
                                @else
                                    <span class="vnd-score-empty">Belum dinilai</span>
                                @endif
                            </td>
                            <td>
                                <span class="vnd-status {{ $risk['class'] }}">
                                    <i class="{{ $risk['icon'] }}" style="font-size:.55rem;"></i>
                                    {{ $application->risk_level ? strtoupper($application->risk_level) : '—' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('qa.risk-assessment.create', $application->id) }}"
                                    class="vnd-btn-detail {{ $isAssessed ? 'vnd-btn-detail' : 'vnd-btn-detail--primary' }}">
                                    @if ($isAssessed)
                                        <i class="flaticon-eye icon-sm text-primary" style="font-size:.7rem;"></i> Detail
                                    @else
                                        <i class="flaticon2-add-1 icon-sm" style="font-size:.7rem;"></i> Mulai Assessment
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="vnd-empty">
                                    <i class="flaticon2-search-1"></i>
                                    <div class="vnd-empty-title">Tidak ada vendor</div>
                                    <div class="vnd-empty-sub">Coba ubah filter pencarian.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>{{-- /vnd-card --}}
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function() {
            /* Tampilkan flash session sebagai SweetAlert */
            (function() {
                var $el = $('#vnd-flash');
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
                            html: '<div class="vnd-swal-toast-body">' +
                                '<div class="btn btn-icon btn-outline-success btn-circle btn-sm m-0">' +
                                '<i class="flaticon2-check-mark" style="font-size:1rem;"></i>' +
                                '</div>' +
                                '<div class="vnd-swal-toast-content">' +
                                '<div class="vnd-swal-toast__title">' + m.title + '</div>' +
                                '<div class="vnd-swal-toast__text">' + msg + '</div>' +
                                '</div>' +
                                '</div>',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            showCloseButton: true,
                            timer: 3500,
                            timerProgressBar: true,
                            width: 360,
                            padding: '0',
                            customClass: {
                                popup: 'vnd-swal-toast shadow-sm',
                                closeButton: 'vnd-swal-toast__close',
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
