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

    {{-- Modal Alasan Rekualifikasi --}}
    <div class="modal fade" id="modalTriggerRekualifikasi" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="modalTriggerRekualifikasiTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius:12px;">
                <form id="formTriggerRekualifikasi" action="" method="POST">
                    @csrf
                    <div class="modal-header border-bottom py-4 px-6">
                        <h5 class="modal-title font-weight-bolder text-dark" id="modalTriggerRekualifikasiTitle">
                            <i class="fas fa-redo text-warning mr-2" style="font-size:1rem;"></i> Picu Rekualifikasi Vendor
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body p-6">
                        <div class="alert alert-custom alert-light-warning fade show mb-4 p-4" role="alert">
                            <div class="alert-icon"><i class="fas fa-exclamation-triangle text-warning"></i></div>
                            <div class="alert-text font-size-sm">
                                Anda akan memicu permohonan rekualifikasi manual untuk <strong
                                    id="modalVendorNameTarget">-</strong>.
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bolder text-dark">Alasan / Pemicu Rekualifikasi: <span
                                    class="text-danger">*</span></label>
                            <select name="reason" id="select_rekualifikasi_reason" class="form-control selectpicker"
                                required>
                                <option value="qa_trigger">Permintaan Tim Pengadaan / QA (QA Trigger)</option>
                                <option value="expired_period">Masa Berlaku Kadaluarsa (&le; 60 Hari)</option>
                                <option value="cdob_expiry">Masa Berlaku Sertifikat CDOB Kadaluarsa</option>
                                <option value="eval_score_drop">Penurunan Skor Evaluasi Kinerja</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-3 px-6">
                        <button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal"><i
                                class="fas fa-times mr-1"></i> Batal</button>
                        <button type="submit" class="btn btn-warning font-weight-bold px-6">
                            <i class="fas fa-paper-plane mr-1"></i> Picu Rekualifikasi
                        </button>
                    </div>
                </form>
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

            /* Modal Alasan Rekualifikasi Handler */
            $(document).on('click', '.btn-trigger-rekualifikasi', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var actionUrl = $btn.data('url');
                var vendorName = $btn.data('vendor-name') || 'vendor ini';

                $('#modalVendorNameTarget').text(vendorName);
                $('#formTriggerRekualifikasi').attr('action', actionUrl);
                $('#modalTriggerRekualifikasi').modal('show');
            });
        });
    </script>
@endpush
