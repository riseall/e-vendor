@extends('layouts.app', ['title' => 'Daftar Rekualifikasi Vendor'])

@section('breadcrumb', 'Vendor')
@section('step', 'Rekualifikasi')
@section('page_title', 'Daftar Rekualifikasi Vendor')
@section('page_desc', 'Daftar permohonan evaluasi ulang / rekualifikasi vendor mandiri dan pemicuan QA.')

@section('content')
    {{-- Flash container untuk SweetAlert Toast --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- Stat Cards Summary --}}
    @php
        $appsColl = collect($applications->items() ?? []);
        $totalCount = $applications->total();
        $submittedCount = $appsColl->filter(fn($a) => in_array($a->status, ['submitted', 'verified', 'in_progress']))->count();
        $revisionCount = $appsColl->filter(fn($a) => in_array($a->status, ['need_revision', 'draft']))->count();
        $approvedCount = $appsColl->filter(fn($a) => $a->status === 'approved')->count();
    @endphp

    <div class="row mb-6">
        <x-dash-card :value="$totalCount" label="Total Rekualifikasi" icon="flaticon2-reload" type="primary" />
        <x-dash-card :value="$submittedCount" label="Menunggu / Proses" icon="flaticon2-hourglass" type="info" />
        <x-dash-card :value="$revisionCount" label="Draft / Perlu Revisi" icon="flaticon-warning" type="warning" />
        <x-dash-card :value="$approvedCount" label="Disetujui (Approved)" icon="flaticon2-check-mark" type="success" />
    </div>

    {{-- Main Container Card --}}
    <div class="vnd-card">
        {{-- Card Head --}}
        <div class="vnd-card-head">
            <div>
                <div class="vnd-card-title">
                    <span class="vnd-card-title-dot"></span>
                    Riwayat & Manajemen Rekualifikasi
                </div>
                <div class="text-muted font-size-sm mt-1">
                    Daftar permohonan evaluasi ulang / rekualifikasi vendor mandiri dan pemicuan QA.
                </div>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="card-body p-0 px-4 pt-5 pb-6">
            <div class="table-responsive">
                <table class="table tbl-vendor table-borderless" id="tbl_rekualifikasi" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th style="min-width: 130px;">No Permohonan</th>
                            <th style="min-width: 200px;">Vendor / Perusahaan</th>
                            <th style="min-width: 170px;">Pemicu Rekualifikasi</th>
                            <th style="min-width: 130px;">Status</th>
                            <th style="min-width: 140px;">Tgl Dibuat</th>
                            <th class="text-right no-sort" style="min-width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $app)
                            @include('admin.rekualifikasi.partials.rekualifikasi-row', ['app' => $app])
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="vnd-empty py-8">
                                        <div class="vnd-empty-icon">
                                            <i class="flaticon2-reload"></i>
                                        </div>
                                        <div class="vnd-empty-title">Belum Ada Data Rekualifikasi</div>
                                        <div class="vnd-empty-sub">Permohonan rekualifikasi akan tampil di sini setelah dipicu oleh Vendor atau QA.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap px-2 py-3 border-top mt-3">
                <div class="text-muted font-size-sm mb-2 mb-sm-0">
                    Menampilkan <strong>{{ $applications->firstItem() ?? 0 }}</strong> &ndash; <strong>{{ $applications->lastItem() ?? 0 }}</strong> dari <strong>{{ $applications->total() }}</strong> permohonan
                </div>
                <div>
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            /* Flash Notifications via SweetAlert Toast */
            (function() {
                var $el = $('#vnd-flash');
                if (!$el.length || typeof Swal === 'undefined') return;

                var messages = [
                    { key: 'success', icon: 'success', title: 'Sukses' },
                    { key: 'error', icon: 'error', title: 'Gagal' },
                    { key: 'warning', icon: 'warning', title: 'Peringatan' },
                    { key: 'info', icon: 'info', title: 'Informasi' },
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
        });
    </script>
@endpush
