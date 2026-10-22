@extends('layouts.app', ['title' => ($activeTab ?? 'new') === 'existing' ? 'Risk Assessment Vendor Eksisting' : 'Risk Assessment'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Risk Assessment')
@section('page_title',
    ($activeTab ?? 'new') === 'existing'
    ? 'Risk Assessment Vendor Eksisting'
    : 'Risk Assessment
    Vendor Baru')
@section('page_desc',
    ($activeTab ?? 'new') === 'existing'
    ? 'Daftar vendor eksisting kategori Bahan Baku & Bahan Kemas
    untuk penilaian risiko berkala.'
    : 'Daftar vendor yang sudah diverifikasi pengadaan dan perlu penilaian risiko QA.')

    @php
        $activeTab = $activeTab ?? 'new';
        $countTotal = $applications->total();
        $countLow = $countByLevel['low'] ?? 0;
        $countMedium = $countByLevel['medium'] ?? 0;
        $countHigh = $countByLevel['high'] ?? 0;
        $countVeryHigh = $countByLevel['very_high'] ?? 0;

        $riskMap = [
            'low' => ['class' => 'vnd-status--success', 'icon' => 'flaticon2-check-mark text-success'],
            'medium' => ['class' => 'vnd-status--revision', 'icon' => 'flaticon-warning text-warning'],
            'high' => ['class' => 'vnd-status--rejected', 'icon' => 'flaticon-danger text-danger'],
            'very_high' => [
                'class' => 'vnd-status--info font-weight-bolder',
                'icon' => 'fas fa-exclamation-circle text-info',
            ],
        ];
    @endphp

@section('content')
    {{-- Flash (ditampilkan via SweetAlert di @push('scripts')) --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- ══════ Sub-Tab Navigasi (Metronic) ══════ --}}
    <ul class="nav nav-tabs nav-tabs-line nav-bold nav-tabs-line-2x nav-tabs-primary mb-6" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'new' ? 'active' : '' }}"
                href="{{ route('qa.risk-assessment.index', ['tab' => 'new']) }}">
                <i class="fas fa-user-plus mr-2"></i> Vendor Baru
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'existing' ? 'active' : '' }}"
                href="{{ route('qa.risk-assessment.index', ['tab' => 'existing']) }}">
                <i class="fas fa-boxes mr-2"></i> Vendor Eksisting
            </a>
        </li>
    </ul>

    {{-- ══════ Stat Cards ══════ --}}
    @if ($activeTab === 'existing')
        <div class="row mb-6">
            <x-dash-card :value="$countTotal" label="Total Vendor" icon="flaticon2-layers-1" type="primary" />
            <x-dash-card :value="$countLow" label="Low" icon="flaticon2-check-mark" type="success" />
            <x-dash-card :value="$countMedium" label="Medium" icon="flaticon-warning" type="warning" />
            <x-dash-card :value="$countHigh" label="High" icon="flaticon-danger" type="danger" />
            <x-dash-card :value="$countVeryHigh" label="Very High" icon="flaticon2-warning" type="info" />
        </div>
    @else
        <div class="row mb-6">
            <x-dash-card :value="$countTotal" label="Total Vendor" icon="flaticon2-layers-1" type="primary" />
            <x-dash-card :value="$countLow" label="Low Risk" icon="flaticon2-check-mark" type="success" />
            <x-dash-card :value="$countMedium" label="Medium Risk" icon="flaticon-warning" type="warning" />
            <x-dash-card :value="$countHigh" label="High Risk" icon="flaticon-danger" type="danger" />
        </div>
    @endif

    {{-- ══════ Main Card ══════ --}}
    <div class="vnd-card">

        {{-- Head: judul + threshold info + filter --}}
        <div class="vnd-card-head">
            <div>
                <div class="vnd-card-title">
                    <span class="vnd-card-title-dot"></span>
                    {{ $activeTab === 'existing' ? 'Daftar Risk Assessment Vendor Eksisting (BB/BK)' : 'Risk Assessment Vendor Baru' }}
                </div>
                <div class="vnd-threshold-tag mt-1">
                    Threshold:
                    @if ($activeTab === 'existing')
                        <span class="vnd-tag vnd-tag--low">Low: 42&ndash;84</span>
                        <span class="vnd-tag vnd-tag--medium">Medium: 85&ndash;168</span>
                        <span class="vnd-tag vnd-tag--high">High: 169&ndash;336</span>
                        <span class="vnd-tag vnd-tag--on-desk">Very High: 337&ndash;672</span>
                    @else
                        <span class="vnd-tag vnd-tag--low">Low &le; {{ $lowThreshold }}</span>
                        <span class="vnd-tag vnd-tag--medium">Medium
                            {{ $lowThreshold + 1 }}&ndash;{{ $highThreshold }}</span>
                        <span class="vnd-tag vnd-tag--high">High &gt; {{ $highThreshold }}</span>
                    @endif
                </div>
            </div>

            <form method="GET" action="{{ route('qa.risk-assessment.index') }}" class="vnd-filter">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <select name="status" class="selectpicker" data-width="160px">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="vnd-btn-filter">
                    <i class="fas fa-filter mr-1" style="font-size:.7rem;"></i> Filter
                </button>
                <a href="{{ route('qa.risk-assessment.index', ['tab' => $activeTab]) }}" class="vnd-btn-reset">Reset</a>
            </form>
        </div>

        {{-- Table --}}
        <div class="card-body p-0 px-4 pt-5 pb-6">
            <table id="tbl-risk" class="table tbl-vendor table-borderless" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Permohonan</th>
                        <th>Vendor</th>
                        <th>Kategori</th>
                        <th>{{ $activeTab === 'existing' ? 'Status Vendor' : 'Status' }}</th>
                        <th>Total Nilai</th>
                        <th>Risk Level</th>
                        <th class="text-right no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        @php
                            $qualification = $application->qualification;
                            $isExistingTab = $activeTab === 'existing';
                            $riskLevel = $qualification ? $qualification->risk_level : null;
                            $risk = $riskMap[$riskLevel] ?? [
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
                                ->map(function ($id) {
                                    return \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id;
                                })
                                ->filter()
                                ->values();
                            $initial = strtoupper(substr(strip_tags($vendorName), 0, 1));
                            $isAssessed =
                                (bool) $qualification &&
                                (!$isExistingTab || $qualification->assessment_type === 'existing');
                            $activeAudit = $application->audits
                                ->whereNotIn('status', [
                                    \App\Models\VendorAudit::STATUS_COMPLETED,
                                    \App\Models\VendorAudit::STATUS_REJECTED,
                                ])
                                ->first();
                            $hasActiveAudit = !is_null($activeAudit);

                            if ($isExistingTab) {
                                $canCreateAudit =
                                    $isAssessed &&
                                    !$hasActiveAudit &&
                                    in_array(
                                        $application->status,
                                        [
                                            \App\Models\VendorApplication::STATUS_AUDIT_REQUIRED,
                                            \App\Models\VendorApplication::STATUS_RISK_ASSESSED,
                                        ],
                                        true,
                                    );
                            } else {
                                $canCreateAudit =
                                    $isAssessed &&
                                    in_array($application->risk_level, ['medium', 'high'], true) &&
                                    !$hasActiveAudit &&
                                    in_array(
                                        $application->status,
                                        [
                                            \App\Models\VendorApplication::STATUS_AUDIT_REQUIRED,
                                            \App\Models\VendorApplication::STATUS_RISK_ASSESSED,
                                        ],
                                        true,
                                    );
                            }
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
                                @php
                                    $statusClassMap = [
                                        'verified' => 'vnd-status--primary',
                                        'approved' => 'vnd-status--success',
                                    ];
                                    $stClass =
                                        $statusClassMap[$application->status] ??
                                        'vnd-status--' . str_replace('_', '-', strtolower($application->status));
                                @endphp
                                <span class="vnd-status {{ $stClass }}">
                                    {{ str_replace('_', ' ', strtoupper($application->status)) }}
                                </span>
                            </td>
                            <td>
                                @if ($isAssessed)
                                    <span
                                        class="vnd-score">{{ number_format($qualification->total_score, 0, ',', '.') }}</span>
                                @else
                                    <span class="vnd-score-empty">Belum dinilai</span>
                                @endif
                            </td>
                            <td>
                                @if ($isAssessed)
                                    <span class="vnd-status {{ $risk['class'] }}">
                                        <i class="{{ $risk['icon'] }}" style="font-size:.55rem;"></i>
                                        {{ strtoupper(str_replace('_', ' ', (string) $riskLevel)) }}
                                        @if ($isExistingTab && $qualification->re_audit_period_years)
                                            ({{ $qualification->re_audit_period_years }} Thn)
                                        @endif
                                    </span>
                                @else
                                    <span class="vnd-status vnd-status--none">—</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="d-inline-flex flex-wrap justify-content-end" style="gap:.35rem;">
                                    @if ($isExistingTab)
                                        {{-- Aksi Tab Vendor Eksisting --}}
                                        @if ($canCreateAudit)
                                            @canany(['audit-create', 'qa-risk-create', 'qa-risk-store'])
                                                <a href="{{ route('qa.audit.create', $application->id) }}"
                                                    class="vnd-btn-detail vnd-btn-detail--primary">
                                                    <i class="flaticon2-add-1 icon-sm" style="font-size:.7rem;"></i> Buat Audit
                                                </a>
                                            @endcanany
                                        @elseif ($hasActiveAudit && $activeAudit)
                                            <a href="{{ route('qa.audit.show', $activeAudit->id) }}"
                                                class="vnd-btn-detail">
                                                <i class="flaticon-eye icon-sm text-primary" style="font-size:.7rem;"></i> Buka Audit
                                            </a>
                                        @endif

                                        @if ($isAssessed)
                                            <a href="{{ route('qa.risk-assessment.create-existing', $application->id) }}"
                                                class="vnd-btn-detail">
                                                <i class="flaticon-eye icon-sm text-primary" style="font-size:.7rem;"></i>
                                                Detail / Nilai Ulang
                                            </a>
                                        @else
                                            @can('qa-risk-create')
                                                <a href="{{ route('qa.risk-assessment.create-existing', $application->id) }}"
                                                    class="vnd-btn-detail vnd-btn-detail--warning">
                                                    <i class="fas fa-clipboard-check mr-1" style="font-size:.75rem;"></i>
                                                    Penilaian Risiko
                                                </a>
                                            @endcan
                                        @endif
                                    @else
                                        {{-- Aksi Tab Vendor Baru --}}
                                        @if ($canCreateAudit)
                                            @canany(['audit-create', 'qa-risk-create', 'qa-risk-store'])
                                                <a href="{{ route('qa.audit.create', $application->id) }}"
                                                    class="vnd-btn-detail vnd-btn-detail--primary">
                                                    <i class="flaticon2-add-1 icon-sm" style="font-size:.7rem;"></i> Buat
                                                    Audit
                                                </a>
                                            @endcanany
                                        @endif
                                        @if ($isAssessed)
                                            <a href="{{ route('qa.risk-assessment.create', $application->id) }}"
                                                class="vnd-btn-detail">
                                                <i class="flaticon-eye icon-sm text-primary" style="font-size:.7rem;"></i>
                                                Detail
                                            </a>
                                        @else
                                            @can('qa-risk-create')
                                                <a href="{{ route('qa.risk-assessment.create', $application->id) }}"
                                                    class="vnd-btn-detail vnd-btn-detail--primary">
                                                    <i class="flaticon2-add-1 icon-sm" style="font-size:.7rem;"></i> Mulai
                                                    Assessment
                                                </a>
                                            @endcan
                                        @endif
                                    @endif
                                </div>
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
                scrollY: '65vh',
                scrollCollapse: true,
                scrollX: true,
                paging: true,
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
