@extends('layouts.app', ['title' => 'Daftar Supplier Terekomendasi'])

@section('breadcrumb', 'Pengadaan / QA')
@section('step', 'Supplier Terekomendasi')
@section('page_title', 'Daftar Supplier Terekomendasi')
@section('page_desc', 'Direktori seluruh vendor/supplier yang telah disetujui (Approved) & aktif di sistem e-Vendor.')

@section('content')
    {{-- Flash container untuk SweetAlert --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- Stat Cards Partial --}}
    @include('admin.supplier.partials.stats')

    {{-- Main Card --}}
    <div class="vnd-card">
        {{-- Card Head --}}
        <div class="vnd-card-head flex-wrap">
            <div class="mb-2 mb-md-0">
                <div class="vnd-card-title">
                    <span class="vnd-card-title-dot"></span>
                    Direktori Supplier Terekomendasi
                </div>
                <div class="text-muted font-size-sm mt-1">
                    Daftar vendor aktif berstatus Approved, status validitas sertifikat, dan pemicuan Rekualifikasi QA.
                </div>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="card-body p-0 px-4 pt-5 pb-6">
            <div class="table-responsive">
                <table id="tbl_approved_suppliers" class="table tbl-vendor table-borderless" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:40px;">No</th>
                            <th style="min-width:240px;">Vendor & Legalitas</th>
                            <th style="min-width:160px;">Kategori Komoditas</th>
                            <th style="min-width:140px;">Kualifikasi & Risk</th>
                            <th style="min-width:170px;">Masa Berlaku & Status</th>
                            <th class="text-right no-sort" style="min-width:215px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $app)
                            @include('admin.supplier.partials.supplier-row', ['app' => $app])
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="vnd-empty">
                                        <div class="vnd-empty-icon">
                                            <i class="flaticon2-search-1"></i>
                                        </div>
                                        <div class="vnd-empty-title">Belum Ada Supplier Terekomendasi</div>
                                        <div class="vnd-empty-sub">Tidak ditemukan vendor yang telah disetujui (Approved)
                                            sesuai kriteria pencarian.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            /* Inisialisasi DataTable untuk Supplier Terekomendasi */
            var table = $('#tbl_approved_suppliers').DataTable({
                scrollY: '60vh',
                scrollCollapse: true,
                scrollX: true
            });

            /* Sinkron filter status & kategori ke server */
            $('select[name="validity"], select[name="category"]').on('change', function() {
                $(this).closest('form').submit();
            });

            /* Tampilkan flash session via SweetAlert Toast */
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

            /* Konfirmasi Picu Rekualifikasi via SweetAlert Modal */
            $(document).on('click', '.btn-trigger-rekualifikasi', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var form = $btn.closest('form');
                var vendorName = $btn.data('vendor-name') || 'vendor ini';

                if (!form || !form.length) {
                    console.error('Form trigger rekualifikasi tidak ditemukan.');
                    return;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Picu Rekualifikasi?',
                        text: 'Apakah Anda yakin ingin memicu permohonan rekualifikasi manual untuk ' +
                            vendorName + '?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#b45309',
                        cancelButtonColor: '#6b7a96',
                        confirmButtonText: 'Ya, Picu Rekualifikasi',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-warning font-weight-bold mr-2',
                            cancelButton: 'btn btn-secondary font-weight-bold'
                        }
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.get(0).submit();
                        }
                    });
                } else {
                    if (confirm('Picu rekualifikasi manual untuk ' + vendorName + '?')) {
                        form.get(0).submit();
                    }
                }
            });
        });
    </script>
@endpush
