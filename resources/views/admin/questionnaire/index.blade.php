@extends('layouts.app', ['title' => 'Master Kuesioner Audit'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Master Kuesioner')
@section('page_title', 'Master Template Kuesioner Audit')
@section('page_desc', 'Kelola form template kuesioner dan daftar pertanyaan untuk audit vendor.')

@section('content')
    {{-- Flash notification container untuk SweetAlert --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- Stat Cards Partial --}}
    @include('admin.questionnaire.partials.stats')

    {{-- Main Card Container --}}
    <div class="vnd-card">
        {{-- Card Head --}}
        <div class="vnd-card-head">
            <div>
                <div class="vnd-card-title">
                    <span class="vnd-card-title-dot"></span>
                    Master Form Kuesioner
                </div>
                <div class="text-muted font-size-sm mt-1">
                    Daftar template kuesioner audit yang aktif di sistem.
                </div>
            </div>

            <div class="d-flex align-items-center" style="gap:.6rem;">
                <button type="button" class="vnd-btn-filter style-none btn btn-primary font-weight-bolder px-4 text-white"
                    data-toggle="modal" data-target="#modalFormQuestionnaire" onclick="openCreateModal()">
                    <i class="fas fa-plus-circle icon-sm text-white" style="font-size:.7rem;"></i> Tambah Form
                </button>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="card-body p-0 px-4 pt-5 pb-6">
            <div class="table-responsive">
                <table class="table tbl-vendor table-borderless" id="forms_datatable" style="width:100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th style="min-width: 140px;">Kode</th>
                            <th style="min-width: 220px;">Nama Form</th>
                            <th style="min-width: 140px;">Tipe Material</th>
                            <th style="min-width: 150px;">No. Dokumen</th>
                            <th style="min-width: 110px;">Status</th>
                            <th class="text-right no-sort" style="min-width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($forms as $form)
                            @include('admin.questionnaire.partials.form-row', ['form' => $form])
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="vnd-empty">
                                        <div class="vnd-empty-icon">
                                            <i class="flaticon2-document"></i>
                                        </div>
                                        <div class="vnd-empty-title">Belum Ada Form Kuesioner</div>
                                        <div class="vnd-empty-sub">Klik tombol "Tambah Form" untuk membuat template
                                            kuesioner baru.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Form Partial --}}
    @include('admin.questionnaire.partials.form-modal')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            /* Initialize DataTables jika ada data */
            if ($('#forms_datatable tbody tr').find('.vnd-empty').length === 0) {
                $('#forms_datatable').DataTable({
                    responsive: true,
                    order: [
                        [0, 'asc']
                    ],
                    dom: '<"d-flex justify-content-between align-items-center mb-4"lf>rtip',
                });
            }

            /* Flash Session via SweetAlert Toast */
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

            /* Confirm Delete Form via SweetAlert Modal */
            $(document).on('click', '.btn-delete-form', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var formName = $(this).data('form-name') || 'form ini';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Form Kuesioner?',
                        text: 'Menghapus "' + formName +
                            '" akan menghapus seluruh pertanyaan di dalamnya!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#c62828',
                        cancelButtonColor: '#6b7a96',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-danger font-weight-bold mr-2',
                            cancelButton: 'btn btn-secondary font-weight-bold'
                        }
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('Hapus form ' + formName + '? Seluruh pertanyaan akan ikut terhapus.')) {
                        form.submit();
                    }
                }
            });
        });

        function openCreateModal() {
            $('#modalTitle').text('Tambah Form Kuesioner');
            $('#questionnaireForm').attr('action', "{{ route('questionnaire-form.store') }}");
            $('#methodPlaceholder').html('');
            $('#form_code').val('').removeAttr('readonly');
            $('#form_name').val('');
            $('#form_material_type').val('bahan_baku');
            $('#form_document_number').val('');
            $('#form_order').val('0');
            $('#form_is_active').prop('checked', true);
        }

        function openEditModal(btn) {
            var form = $(btn).data('form');
            $('#modalTitle').text('Edit Form Kuesioner');
            var actionUrl = "{{ route('questionnaire-form.update', ':id') }}".replace(':id', form.id);
            $('#questionnaireForm').attr('action', actionUrl);
            $('#methodPlaceholder').html('<input type="hidden" name="_method" value="PUT">');
            $('#form_code').val(form.code);
            $('#form_name').val(form.name);
            $('#form_material_type').val(form.material_type);
            $('#form_document_number').val(form.document_number);
            $('#form_order').val(form.order);
            $('#form_is_active').prop('checked', Boolean(form.is_active));
            $('#modalFormQuestionnaire').modal('show');
        }
    </script>
@endpush
