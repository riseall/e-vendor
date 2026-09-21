@extends('layouts.app', ['title' => 'Evaluasi Vendor'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Evaluasi Kinerja')
@section('page_title', 'Evaluasi Kinerja Vendor')
@section('page_desc', 'Manajemen Progress Berjalan & Pengesahan Evaluasi Tahunan Vendor.')

@section('content')
    {{-- Flash container untuk SweetAlert Toast --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    @php
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    @endphp

    {{-- Executive Summary Stat Cards --}}
    @include('admin.evaluasi.partials.stats')

    {{-- Main Card Container --}}
    <div class="card card-custom shadow-sm border-0 mb-8" style="border-radius: 12px; overflow: hidden;">
        {{-- Card Header & Filter Bar --}}
        <div class="card-header border-0 pt-6 pb-6 bg-white d-flex align-items-center justify-content-between flex-wrap"
            style="min-height: auto;">
            <div class="d-flex align-items-center flex-wrap mr-2 mb-2 mb-md-0">
                <h3 class="card-title align-items-start flex-column mb-0 mr-8">
                    <span class="card-label font-weight-bolder text-dark" style="font-size:1.3rem;">Evaluasi Kinerja
                        Vendor</span>
                </h3>
                <ul class="nav nav-pills nav-light-primary nav-bold" role="tablist">
                    <li class="nav-item mr-2">
                        <a class="nav-link px-4 {{ $activeTab == 'monthly' ? 'active' : '' }}"
                            href="{{ route('admin.evaluasi.index', ['year' => $year, 'month' => $month, 'tab' => 'monthly']) }}"
                            style="border-radius: 8px;">
                            <span class="nav-icon"><i class="fas fa-calendar-check mr-2"></i></span>
                            <span class="nav-text">Progress Berjalan</span>
                            <span
                                class="badge badge-sm {{ $activeTab == 'monthly' ? 'badge-white text-primary' : 'badge-light-primary text-primary' }} ml-2">{{ $monthlyEvaluations->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 {{ $activeTab == 'annual' ? 'active' : '' }}"
                            href="{{ route('admin.evaluasi.index', ['year' => $year, 'month' => $month, 'tab' => 'annual']) }}"
                            style="border-radius: 8px;">
                            <span class="nav-icon"><i class="fas fa-award mr-2"></i></span>
                            <span class="nav-text">Evaluasi Tahunan</span>
                            <span
                                class="badge badge-sm {{ $activeTab == 'annual' ? 'badge-white text-primary' : 'badge-light-primary text-primary' }} ml-2">{{ $annualEvaluations->count() }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="d-flex align-items-center flex-wrap py-1">
                <form method="GET" action="{{ route('admin.evaluasi.index') }}"
                    class="d-flex align-items-center mr-2 mb-1 mb-md-0">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">

                    <div class="d-flex align-items-center mr-2">
                        <select name="year" class="form-control border-0 selectpicker" onchange="this.form.submit()">
                            @for ($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun
                                    {{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    @if ($activeTab == 'monthly')
                        <div class="d-flex align-items-center mr-2">
                            <select name="month" class="form-control font-weight-bolder border-0 selectpicker"
                                onchange="this.form.submit()">
                                @foreach ($months as $num => $name)
                                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </form>

                <div class="d-flex align-items-center pl-2 border-left" style="gap: 0.5rem; height: 36px;">
                    @can('evaluasi-settings')
                        <a href="{{ route('admin.evaluasi.settings') }}" class="btn btn-sm btn-icon btn-facebook"
                            style="width: 36px; height: 36px; border-radius: 8px;" title="Pengaturan Bobot & Threshold">
                            <i class="fas fa-cog fa-spin" style="animation-duration: 6s;"></i>
                        </a>
                    @endcan

                    @if ($activeTab == 'annual')
                        <form action="{{ route('admin.evaluasi.annual.generate') }}" method="POST" class="d-inline mb-0">
                            @csrf
                            <input type="hidden" name="year" value="{{ $year }}">
                            <button type="submit"
                                class="btn btn-sm btn-light-success font-weight-bold d-flex align-items-center"
                                style="height: 36px; border-radius: 8px;"
                                onclick="return confirm('Generate / kalkulasi evaluasi tahunan {{ $year }} untuk semua vendor?')">
                                <i class="fas fa-sync-alt mr-2"></i> Generate Evaluasi {{ $year }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Informational Banner: Threshold & Aspect Weight Info Bar --}}
        <div class="px-6 py-4 bg-light d-flex align-items-center justify-content-between flex-wrap"
            style="border-top: 1px dashed #e4e6ef; border-bottom: 1px dashed #e4e6ef;">
            {{-- <div class="d-flex align-items-center flex-wrap mr-4">
                <span class="font-weight-bolder text-dark-75 mr-3"><i class="fas fa-balance-scale text-primary mr-2"></i>Bobot Aspek:</span>
                <span class="label label-light-primary label-inline font-weight-bold mr-2 px-3 py-1">QAD ({{ number_format($settings->weight_delivery + $settings->weight_quality + $settings->weight_quantity, 0) }}%)</span>
                <span class="label label-light-warning label-inline font-weight-bold px-3 py-1">QA Manual ({{ number_format($settings->weight_complain + $settings->weight_incoming_material + $settings->weight_safety_environment, 0) }}%)</span>
            </div> --}}
            <div class="d-flex align-items-center flex-wrap mt-2 mt-md-0">
                <span class="font-weight-bolder text-dark-75 mr-3">Threshold Kategori:</span>
                <span class="label label-success label-inline font-weight-bold mr-2 px-3 py-1">BAIK &ge;
                    {{ number_format($settings->threshold_baik, 0) }}</span>
                <span class="label label-warning label-inline font-weight-bold mr-2 px-3 py-1 text-white">CUKUP &ge;
                    {{ number_format($settings->threshold_cukup, 0) }}</span>
                <span class="label label-danger label-inline font-weight-bold px-3 py-1">KURANG &lt;
                    {{ number_format($settings->threshold_cukup, 0) }}</span>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="card-body p-4 p-lg-6">
            @if ($activeTab == 'monthly')
                @include('admin.evaluasi.partials.monthly-tab')
            @else
                @include('admin.evaluasi.partials.annual-tab')
            @endif
        </div>
    </div>

    {{-- Reusable Single Action Alert Modal & Import QA Modal --}}
    @include('admin.evaluasi.partials.modal-action')
    @include('admin.evaluasi.partials.modal-import-qa')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Helper: Toast alert matching admin/audit (vnd-swal-toast style)
            function showToast(icon, title, message) {
                if (typeof Swal === 'undefined') return;

                var iconMap = {
                    'success': {
                        class: 'btn-outline-success',
                        iconClass: 'flaticon2-check-mark'
                    },
                    'error': {
                        class: 'btn-outline-danger',
                        iconClass: 'flaticon2-cross'
                    },
                    'warning': {
                        class: 'btn-outline-warning',
                        iconClass: 'flaticon-warning-1'
                    },
                    'info': {
                        class: 'btn-outline-info',
                        iconClass: 'flaticon-information'
                    }
                };
                var iconCfg = iconMap[icon] || iconMap['info'];

                Swal.fire({
                    html: '<div class="vnd-swal-toast-body">' +
                        '<div class="btn btn-icon ' + iconCfg.class + ' btn-circle btn-sm m-0">' +
                        '<i class="' + iconCfg.iconClass + '" style="font-size:1rem;"></i>' +
                        '</div>' +
                        '<div class="vnd-swal-toast-content">' +
                        '<div class="vnd-swal-toast__title">' + (title || 'Notifikasi') + '</div>' +
                        '<div class="vnd-swal-toast__text">' + message + '</div>' +
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

            // Expose showToast ke window agar dapat dipanggil dari partials
            window.showToast = showToast;

            // Flash toast matching admin/audit
            (function() {
                var $el = $('#vnd-flash');
                if (!$el.length || typeof Swal === 'undefined') return;
                var msgs = [{
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
                msgs.forEach(function(m) {
                    var v = $el.data(m.key);
                    if (v) {
                        showToast(m.icon, m.title, v);
                    }
                });
            })();
        });
    </script>
@endpush
