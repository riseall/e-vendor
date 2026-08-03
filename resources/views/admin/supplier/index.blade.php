@extends('layouts.app', ['title' => 'Daftar Supplier Terekomendasi'])

@section('breadcrumb', 'Pengadaan / QA')
@section('step', 'Supplier Terekomendasi')
@section('page_title', 'Daftar Supplier Terekomendasi')
@section('page_desc', 'Daftar seluruh vendor/supplier yang telah disetujui (Approved) & aktif di sistem.')

@section('content')
    {{-- Flash container untuk SweetAlert --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- Stat Cards Partial --}}
    @include('admin.supplier.partials.stats')

    {{-- Main Card --}}
    <div class="vnd-card">
        {{-- Card Head --}}
        <div class="vnd-card-head">
            <div>
                <div class="vnd-card-title">
                    <span class="vnd-card-title-dot"></span>
                    Supplier Terekomendasi
                </div>
                <div class="text-muted font-size-sm mt-1">
                    Daftar vendor aktif berstatus Approved dengan opsi pemicuan Rekualifikasi QA.
                </div>
            </div>

            <form method="GET" action="{{ route('pengadaan.supplier.index') }}" class="vnd-filter">
                <div class="form-input" style="width:240px;">
                    <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm"
                        placeholder="Cari nama, email, no. permohonan...">
                </div>
                <button type="submit" class="vnd-btn-filter">
                    <i class="flaticon-search" style="font-size:.65rem;"></i> Filter
                </button>
                @if ($search !== '')
                    <a href="{{ route('pengadaan.supplier.index') }}" class="vnd-btn-reset">Reset</a>
                @endif
            </form>
        </div>

        {{-- Table Content --}}
        <div class="card-body p-0 px-4 pt-5 pb-6">
            <div class="table-responsive">
                <table id="tbl_approved_suppliers" class="table tbl-vendor table-borderless" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:40px;">No</th>
                            <th style="min-width:140px;">No Permohonan</th>
                            <th style="min-width:200px;">Vendor / Perusahaan</th>
                            <th style="min-width:130px;">Kategori</th>
                            <th style="min-width:130px;">Risk Level</th>
                            <th style="min-width:140px;">Tgl Approved & Validitas</th>
                            <th class="text-right no-sort" style="min-width:170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $app)
                            @include('admin.supplier.partials.supplier-row', ['app' => $app])
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="vnd-empty">
                                        <div class="vnd-empty-icon">
                                            <i class="flaticon2-search-1"></i>
                                        </div>
                                        <div class="vnd-empty-title">Belum Ada Supplier Terekomendasi</div>
                                        <div class="vnd-empty-sub">Tidak ditemukan vendor yang telah disetujui (Approved) sesuai kriteria pencarian.</div>
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
                    Menampilkan <strong>{{ $suppliers->firstItem() ?? 0 }}</strong> &ndash; <strong>{{ $suppliers->lastItem() ?? 0 }}</strong> dari <strong>{{ $suppliers->total() }}</strong> supplier
                </div>
                <div>
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            /* Tampilkan flash session via SweetAlert Toast */
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

            /* Konfirmasi Picu Rekualifikasi via SweetAlert Modal */
            $(document).on('click', '.btn-trigger-rekualifikasi', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var vendorName = $(this).data('vendor-name') || 'vendor ini';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Picu Rekualifikasi?',
                        text: 'Apakah Anda yakin ingin memicu permohonan rekualifikasi manual untuk ' + vendorName + '?',
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
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('Picu rekualifikasi manual untuk ' + vendorName + '?')) {
                        form.submit();
                    }
                }
            });
        });
    </script>
@endpush
