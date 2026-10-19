@extends('layouts.app', ['title' => 'Detail Supplier'])

@section('breadcrumb', 'Pengadaan')
@section('step', 'Supplier Terekomendasi')
@section('page_title', 'Profil & Riwayat Supplier')
@section('page_desc', 'Rangkuman terpadu profil legalitas, hasil verifikasi berkas, audit kualifikasi, hingga evaluasi kinerja.')

@push('style')
    @include('admin.verifikasi.partials.show.styles')
    <style>
        .supplier-tabs {
            border-bottom: none !important;
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }

        .supplier-tabs::-webkit-scrollbar {
            display: none; /* Chrome/Safari */
        }

        .supplier-tabs .nav-link {
            font-size: 0.925rem;
            font-weight: 600;
            color: #7e8299;
            padding: 1rem 1.4rem;
            border: none !important;
            border-bottom: 3px solid transparent !important;
            background: transparent !important;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .supplier-tabs .nav-link:hover {
            color: var(--brand-primary, #4f46e5);
        }

        .supplier-tabs .nav-link.active {
            color: var(--brand-primary, #4f46e5) !important;
            border-bottom-color: var(--brand-primary, #4f46e5) !important;
            font-weight: 700;
        }

        .table-detail-keyval td {
            padding: 0.65rem 0.6rem;
            border-top: none;
            border-bottom: 1px dashed #eef2f7;
            vertical-align: middle;
        }

        .table-detail-keyval tr:last-child td {
            border-bottom: none;
        }

        .table-custom-clean th {
            background-color: #f8fafc !important;
            color: #64748b !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            border-top: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 0.85rem 1rem !important;
        }

        .table-custom-clean td {
            padding: 0.85rem 1rem !important;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .border-left-md {
            border-left: 1px solid var(--border-light);
        }

        @media (max-width: 767.98px) {
            .supplier-tabs .nav-link {
                padding: 0.85rem 1rem;
                font-size: 0.825rem;
            }

            .border-left-md {
                border-left: none;
                border-top: 1px solid var(--border-light);
                padding-top: 1rem;
                margin-top: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $application = $app;
        view()->share('application', $app);
    @endphp

    {{-- Flash container untuk SweetAlert --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- 1. Header Ringkasan Vendor & KPI Box --}}
    @include('admin.supplier.partials.show.header')

    {{-- ponytail: Clean decoupled tab navigation bar without Metronic card-header scroll arrows --}}
    {{-- 2. Nav Tabs Header --}}
    <div class="card card-custom shadow-sm border-0 mb-6" style="border-radius: 12px;">
        <div class="card-body py-0 px-5 bg-white" style="border-radius: 12px;">
            <ul class="nav nav-tabs supplier-tabs flex-nowrap" role="tablist">
                <li class="nav-item mr-3">
                    <a class="nav-link active d-flex align-items-center" data-toggle="tab" href="#tab-registration" role="tab">
                        <span class="nav-icon mr-2"><i class="fas fa-building text-primary font-size-base"></i></span>
                        <span class="nav-text">Profil & Legalitas</span>
                    </a>
                </li>
                <li class="nav-item mr-3">
                    <a class="nav-link d-flex align-items-center" data-toggle="tab" href="#tab-verification-qa" role="tab">
                        <span class="nav-icon mr-2"><i class="fas fa-clipboard-check text-primary font-size-base"></i></span>
                        <span class="nav-text">Verifikasi & Kualifikasi QA</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" data-toggle="tab" href="#tab-evaluation" role="tab">
                        <span class="nav-icon mr-2"><i class="fas fa-chart-line text-primary font-size-base"></i></span>
                        <span class="nav-text">Evaluasi Kinerja</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- 3. Tab Content (Clean Canvas tanpa Card-in-Card Nesting) --}}
    <div class="tab-content">
        {{-- Tab 1: Profil & Legalitas --}}
        <div class="tab-pane fade show active" id="tab-registration" role="tabpanel">
            @include('admin.supplier.partials.show.tab-registration')
        </div>

        {{-- Tab 2: Verifikasi & Kualifikasi QA --}}
        <div class="tab-pane fade" id="tab-verification-qa" role="tabpanel">
            @include('admin.supplier.partials.show.tab-verification-qa')
        </div>

        {{-- Tab 3: Evaluasi Kinerja --}}
        <div class="tab-pane fade" id="tab-evaluation" role="tabpanel">
            @include('admin.supplier.partials.show.tab-evaluation')
        </div>
    </div>

    {{-- Modal Preview Dokumen Terpadu --}}
    <x-document-preview />

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Aktifkan tab berdasarkan hash URL jika tersedia (misal: #tab-evaluation)
            var hash = window.location.hash;
            if (hash) {
                $('.supplier-tabs a[href="' + hash + '"]').tab('show');
            }

            $('.supplier-tabs a').on('shown.bs.tab', function(e) {
                if (history.pushState) {
                    history.pushState(null, null, e.target.hash);
                } else {
                    window.location.hash = e.target.hash;
                }
                if (window.jQuery && $.fn.DataTable) {
                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
                }
            });
        });
    </script>
@endpush
