@extends('layouts.app', ['title' => 'Master Questionnaire'])

@push('style')
    <style>
        .custom-badge {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.35em 0.75em;
        }
    </style>
@endpush

@section('content')
    <div id="flash-message" data-success="{{ session('success') }}" data-error="{{ session('error') }}" hidden></div>

    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">Master Questionnaire Forms</h3>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-primary font-weight-bolder" data-toggle="modal"
                    data-target="#modalFormQuestionnaire" onclick="openCreateModal()">
                    <i class="la la-plus-circle icon-xl"></i> New Form
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-checkable tbl-vendor" id="forms_datatable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Order</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Material Type</th>
                            <th>Document No</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 250px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($forms as $form)
                            <tr>
                                <td>{{ $form->order }}</td>
                                <td><code>{{ $form->code }}</code></td>
                                <td><strong>{{ $form->name }}</strong></td>
                                <td>
                                    @php
                                        $badgeColor = 'badge-primary';
                                        if ($form->material_type === 'bahan_kemas') {
                                            $badgeColor = 'badge-warning';
                                        }
                                        if ($form->material_type === 'bahan_baku') {
                                            $badgeColor = 'badge-success';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeColor }} font-weight-bold">
                                        {{ $form->materialTypeLabel() }}
                                    </span>
                                </td>
                                <td>{{ $form->document_number ?? '-' }}</td>
                                <td>
                                    @if ($form->is_active)
                                        <span class="label label-success label-dot mr-2"></span><span
                                            class="font-weight-bold text-success">Aktif</span>
                                    @else
                                        <span class="label label-danger label-dot mr-2"></span><span
                                            class="font-weight-bold text-danger">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('questionnaire-form.questions', $form->id) }}"
                                            class="btn btn-sm btn-light-primary font-weight-bolder mr-2"
                                            title="Manage Questions">
                                            <i class="la la-list-alt"></i> Questions
                                        </a>
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-warning mr-2"
                                            title="Edit Form" data-form="{{ json_encode($form) }}"
                                            onclick="openEditModal(this)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('questionnaire-form.destroy', $form->id) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                                title="Delete Form">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div class="modal fade" id="modalFormQuestionnaire" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Form Kuesioner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <form id="questionnaireForm" method="POST">
                    @csrf
                    <div id="methodPlaceholder"></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="code" id="form_code" required
                                        placeholder="Contoh: bahan_baku_pemasok">
                                    <small class="text-muted">Unique identifier for the form templates.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Material Type <span class="text-danger">*</span></label>
                                    <select class="form-control" name="material_type" id="form_material_type" required>
                                        <option value="bahan_baku">Bahan Baku</option>
                                        <option value="bahan_kemas">Bahan Kemas</option>
                                        {{-- <option value="produk_jadi">Produk Jadi</option>
                                <option value="alkes">Alkes</option> --}}
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Form Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="form_name" required
                                placeholder="Contoh: Daftar Periksa Pemasok Bahan Baku">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Document Number</label>
                            <input type="text" class="form-control" name="document_number" id="form_document_number"
                                placeholder="Contoh: QA-FM-I2.02-017">
                        </div>
                        {{-- <div class="form-group">
                            <label class="font-weight-bold">Description</label>
                            <textarea class="form-control" name="description" id="form_description" rows="3"
                                placeholder="Deskripsi form..."></textarea>
                        </div> --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Sort Order <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="order" id="form_order" value="0"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="checkbox checkbox-lg checkbox-outline checkbox-success font-weight-bold">
                                <input type="checkbox" name="is_active" id="form_is_active" value="1" checked />
                                <span></span>&nbsp; Aktifkan Form
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-primary font-weight-bold"
                            data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary font-weight-bold" id="btnSubmit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#forms_datatable').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ]
            });

            // Flash Message Alert
            var flashSuccess = $('#flash-message').data('success');
            var flashError = $('#flash-message').data('error');
            if (flashSuccess) {
                Swal.fire("Berhasil!", flashSuccess, "success");
            }
            if (flashError) {
                Swal.fire("Gagal!", flashError, "error");
            }

            // Confirm Delete
            $(document).on('click', '.btn-delete', function() {
                var form = $(this).closest('form');
                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Menghapus form akan menghapus semua pertanyaan di dalamnya!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
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
            $('#form_is_active').prop('checked', form.is_active);
            $('#modalFormQuestionnaire').modal('show');
        }
    </script>
@endpush
