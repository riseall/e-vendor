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
        $appsColl = collect($applications ?? []);
        $totalCount = $appsColl->count();
        $submittedCount = $appsColl
            ->filter(function ($a) {
                return in_array($a->status, ['submitted', 'verified', 'in_progress']);
            })
            ->count();
        $revisionCount = $appsColl
            ->filter(function ($a) {
                return in_array($a->status, ['need_revision', 'draft']);
            })
            ->count();
        $approvedCount = $appsColl
            ->filter(function ($a) {
                return $a->status === 'approved';
            })
            ->count();
    @endphp

    <div class="row mb-6">
        <x-dash-card :value="$totalCount" label="Total Rekualifikasi" icon="fas fa-redo" type="primary" />
        <x-dash-card :value="$submittedCount" label="Menunggu / Proses" icon="fas fa-hourglass-half" type="info" />
        <x-dash-card :value="$revisionCount" label="Draft / Perlu Revisi" icon="fas fa-exclamation-triangle" type="warning" />
        <x-dash-card :value="$approvedCount" label="Disetujui (Approved)" icon="fas fa-check-circle" type="success" />
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
                            <th style="width: 40px;" class="no-sort text-center">No</th>
                            <th style="min-width: 130px;">No Permohonan</th>
                            <th style="min-width: 200px;">Vendor / Perusahaan</th>
                            <th style="min-width: 170px;">Pemicu Rekualifikasi</th>
                            <th style="min-width: 130px;">Status</th>
                            <th style="min-width: 140px;">Tgl Dibuat</th>
                            <th class="text-right no-sort" style="min-width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $app)
                            @include('admin.rekualifikasi.partials.rekualifikasi-row', ['app' => $app])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Detail Rekualifikasi Vendor --}}
    @include('admin.rekualifikasi.partials.detail-modal')
@endsection

@push('scripts')
    <script>
        $(function() {
            /* Inisialisasi DataTable untuk Rekualifikasi */
            var table = $('#tbl_rekualifikasi').DataTable({
                scrollY: '60vh',
                scrollCollapse: true,
                scrollX: true,
                paging: true,
                // language: {
                //     search: "_INPUT_",
                //     searchPlaceholder: "Cari nomor, vendor, pemicu...",
                //     lengthMenu: "Tampilkan _MENU_ data",
                //     info: "Menampilkan _START_ &ndash; _END_ dari _TOTAL_ permohonan",
                //     infoEmpty: "Menampilkan 0 data",
                //     infoFiltered: "(disaring dari _MAX_ total permohonan)",
                //     zeroRecords: "Tidak ditemukan data rekualifikasi yang sesuai",
                //     emptyTable: "Belum ada data rekualifikasi vendor",
                //     paginate: {
                //         previous: '<i class="fas fa-chevron-left font-size-xs"></i>',
                //         next: '<i class="fas fa-chevron-right font-size-xs"></i>'
                //     }
                // }
            });

            /* Auto Update Nomor Urut Kolom No saat sorting / searching */
            table.on('draw.dt', function() {
                var info = table.page.info();
                table.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = info.start + i + 1;
                });
            });

            /* Event Delegation: Buka Modal Detail Rekualifikasi */
            $(document).on('click', '.btn-show-rekualifikasi-detail', function(e) {
                e.preventDefault();
                var btn = $(this);

                var appNum = btn.data('appnum') || 'Draft';
                var appId = btn.data('id') || '-';
                var vendorName = btn.data('vendor-name') || '-';
                var vendorEmail = btn.data('vendor-email') || '-';
                var picName = btn.data('pic-name') || '-';
                var phone = btn.data('phone') || '-';
                var reasonLabel = btn.data('reason-label') || '-';
                var reasonClass = btn.data('reason-class') || 'vnd-tag--muted';
                var statusLabel = btn.data('status-label') || '-';
                var statusClass = btn.data('status-class') || 'vnd-status--info';
                var statusIcon = btn.data('status-icon') || 'fas fa-info-circle';
                var createdAt = btn.data('created-at') || '-';
                var updatedAt = btn.data('updated-at') || '-';
                var parentAppNum = btn.data('parent-appnum') || '-';
                var adminNote = btn.data('admin-note') || '';
                var trackingUrl = btn.data('tracking-url') || '#';
                var verificationUrl = btn.data('verification-url') || '#';

                $('#dtl_appnum').text(appNum);
                // $('#dtl_app_id').text('ID: ' + appId);
                $('#dtl_vendor_name').text(vendorName);
                $('#dtl_vendor_email').text(vendorEmail);
                $('#dtl_pic_name').text(picName);
                $('#dtl_phone').text(phone);
                $('#dtl_reason_label').text(reasonLabel);
                $('#dtl_parent_appnum').text(parentAppNum);
                $('#dtl_created_at').text(createdAt);
                $('#dtl_updated_at').text(updatedAt);

                // Update Reason badge
                $('#dtl_reason_badge')
                    .attr('class', 'vnd-tag ' + reasonClass)
                    .text(reasonLabel);

                // Update Status badge
                $('#dtl_status_badge')
                    .attr('class', 'vnd-status ' + statusClass);
                $('#dtl_status_icon').attr('class', statusIcon + ' mr-1');
                $('#dtl_status_text').text(statusLabel);

                // Admin Note
                if (adminNote && adminNote.trim() !== '') {
                    $('#dtl_admin_note').text(adminNote);
                    $('#dtl_admin_note_section').show();
                } else {
                    $('#dtl_admin_note_section').hide();
                }

                // Action links
                $('#dtl_btn_tracking').attr('href', trackingUrl);
                if ($('#dtl_btn_verify').length) {
                    $('#dtl_btn_verify').attr('href', verificationUrl);
                }

                $('#modalDetailRekualifikasi').modal('show');
            });

            /* Flash Notifications via SweetAlert Toast */
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
                                '<i class="fas fa-check" style="font-size:1rem;"></i>' +
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
