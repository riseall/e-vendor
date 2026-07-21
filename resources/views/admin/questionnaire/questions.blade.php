@extends('layouts.app', ['title' => 'Manage Questions'])

@push('style')
    <style>
        .section-header {
            background-color: #f3f6f9;
            font-weight: 700;
            color: #3f4254;
            padding: 10px 15px !important;
        }

        .question-text {
            font-size: 1.05rem;
            color: #181c32;
        }
    </style>
@endpush

@section('content')
    <div id="flash-message" data-success="{{ session('success') }}" data-error="{{ session('error') }}" hidden></div>

    <div class="card card-custom">
        <div class="card-header align-items-center">
            <div class="card-title d-flex align-items-center">
                <a href="{{ route('questionnaire-form.index') }}"
                    class="btn btn-sm btn-light-primary font-weight-bolder mr-4">
                    <i class="ki ki-arrow-back"></i>
                </a>
                <h3 class="card-label mb-0">
                    Pertanyaan: <span class="text-primary">{{ $form->name }}</span>
                    @if ($form->document_number)
                        <small class="text-muted ml-2 font-size-sm">({{ $form->document_number }})</small>
                    @endif
                </h3>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-success font-weight-bolder mr-2" data-toggle="modal"
                    data-target="#modalImport">
                    <i class="la la-file-excel icon-md"></i> Import Excel
                </button>
                <button type="button" class="btn btn-primary font-weight-bolder" data-toggle="modal"
                    data-target="#modalQuestion" onclick="openCreateModal()">
                    <i class="la la-plus icon-md"></i> New Question
                </button>
            </div>
        </div>

        <div class="card-body">
            @if ($questions->isEmpty())
                <div class="text-center p-10">
                    <img src="{{ asset('media/svg/illustrations/sad.svg') }}" alt="No data" style="height: 120px;"
                        class="mb-5">
                    <h5 class="text-muted">Belum ada pertanyaan pada form ini. Silakan tambahkan pertanyaan baru.</h5>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-vertical-center" id="questions_table">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 70px;" class="text-center">No Urut</th>
                                <th>Pertanyaan</th>
                                <th style="width: 150px;">Tipe Jawaban</th>
                                <th style="width: 100px;" class="text-center">Bobot</th>
                                <th style="width: 100px;" class="text-center">Wajib</th>
                                <th style="width: 100px;" class="text-center">Status</th>
                                <th style="width: 150px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $currentSection = null;
                            @endphp
                            @foreach ($questions as $q)
                                @if ($currentSection !== $q->section)
                                    @php $currentSection = $q->section; @endphp
                                    <tr>
                                        <td colspan="7" class="section-header">
                                            <i class="la la-folder-open text-primary mr-2 icon-lg"></i>
                                            {{ $currentSection }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="text-center font-weight-bold">{{ $q->order }}</td>
                                    <td>
                                        <div class="question-text font-weight-bold mb-1">{!! nl2br(e($q->question)) !!}</div>
                                        @if ($q->answer_type === 'multiple_choice' && is_array($q->options))
                                            <div class="mt-1">
                                                @foreach ($q->options as $opt)
                                                    <span
                                                        class="label label-inline label-light mr-1 mb-1">{{ $opt }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $types = [
                                                'yes_no' => ['label' => 'Yes / No', 'color' => 'primary'],
                                                'multiple_choice' => ['label' => 'Pilihan Ganda', 'color' => 'warning'],
                                                'text' => ['label' => 'Teks Bebas', 'color' => 'info'],
                                                'document' => ['label' => 'Unggah Dokumen', 'color' => 'success'],
                                            ];
                                            $typeInfo = $types[$q->answer_type] ?? [
                                                'label' => $q->answer_type,
                                                'color' => 'secondary',
                                            ];
                                        @endphp
                                        <span class="label label-dot label-{{ $typeInfo['color'] }} mr-2"></span>
                                        <span
                                            class="font-weight-bold text-{{ $typeInfo['color'] }}">{{ $typeInfo['label'] }}</span>
                                    </td>
                                    <td class="text-center font-weight-bolder">{{ $q->weight }}</td>
                                    <td class="text-center">
                                        @if ($q->is_required)
                                            <span class="label label-light-danger label-inline font-weight-bold">Ya</span>
                                        @else
                                            <span class="label label-light-secondary label-inline text-dark">Tidak</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($q->is_active)
                                            <span class="label label-success label-dot mr-2"></span><span
                                                class="font-weight-bold text-success">Aktif</span>
                                        @else
                                            <span class="label label-danger label-dot mr-2"></span><span
                                                class="font-weight-bold text-danger">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-warning mr-2"
                                                title="Edit Pertanyaan" data-form="{{ json_encode($q) }}"
                                                onclick="openEditModal(this)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form
                                                action="{{ route('questionnaire-form.questions.destroy', [$form->id, $q->id]) }}"
                                                method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                                    title="Hapus Pertanyaan">
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
            @endif
        </div>
    </div>

    <!-- Modal Question Form -->
    <div class="modal fade" id="modalQuestion" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Pertanyaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <form id="questionForm" method="POST">
                    @csrf
                    <div id="methodPlaceholder"></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Section / Bagian Kuesioner <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="section" id="q_section" required
                                        placeholder="Contoh: I. Sistem Manajemen Mutu">
                                    <span class="form-text text-muted">Mengelompokkan pertanyaan ke dalam bagian/bab
                                        tertentu.</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Tipe Jawaban <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="answer_type" id="q_answer_type" required
                                        onchange="toggleOptionsField()">
                                        <option value="yes_no">Yes / No</option>
                                        <option value="multiple_choice">Pilihan Ganda (Multiple Choice)</option>
                                        <option value="text">Teks Bebas</option>
                                        <option value="document">Unggah Dokumen Bukti</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Pertanyaan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="question" id="q_question" rows="4" required
                                placeholder="Tuliskan pertanyaan disini..."></textarea>
                        </div>

                        <div class="form-group" id="optionsGroup" style="display: none;">
                            <label class="font-weight-bold">Pilihan Jawaban (Satu baris satu pilihan) <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="options" id="q_options" rows="4"
                                placeholder="Ketik opsi jawaban&#10;Contoh:&#10;Pilihan A&#10;Pilihan B&#10;Pilihan C"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Bobot Nilai <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="weight" id="q_weight"
                                        value="1" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">No Urut Tampil <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="order" id="q_order"
                                        value="0" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="checkbox checkbox-lg checkbox-outline checkbox-danger font-weight-bold">
                                        <input type="checkbox" name="is_required" id="q_is_required" value="1" checked />
                                        <span></span>&nbsp; Wajib Diisi (Required)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="checkbox checkbox-lg checkbox-outline checkbox-success font-weight-bold">
                                        <input type="checkbox" name="is_active" id="q_is_active" value="1" checked />
                                        <span></span>&nbsp; Aktifkan Pertanyaan
                                    </label>
                                </div>
                            </div>
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

    <!-- Modal Import -->
    <div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Pertanyaan Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <form action="{{ route('questionnaire-form.questions.import', $form->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="custom-file custom-file-sm">
                            <input type="file" name="file" class="custom-file-input" accept=".xlsx, .xls, .csv"
                                required>
                            <label class="custom-file-label text-truncated">Upload File</label>
                            <span class="form-text text-muted">Pastikan format file excel sesuai dengan template yang
                                disediakan.</span>
                        </div>
                        <div class="mt-4">
                            <a href="{{ Storage::url('templates/template_questions.xlsx') }}"
                                class="btn btn-sm btn-light-info font-weight-bold">
                                <i class="la la-download"></i> Download Template
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-primary font-weight-bold"
                            data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success font-weight-bold">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Flash Message Alert
            var flashSuccess = $('#flash-message').data('success');
            var flashError = $('#flash-message').data('error');
            if (flashSuccess) {
                Swal.fire("Berhasil!", flashSuccess, "success");
            }
            if (flashError) {
                Swal.fire("Gagal!", flashError, "error");
            }

            @if ($errors->any())
                Swal.fire("Error Validasi!", "{!! implode('\n', $errors->all()) !!}", "error");
            @endif

            // Confirm Delete
            $(document).on('click', '.btn-delete', function() {
                var form = $(this).closest('form');
                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Pertanyaan ini akan dihapus permanen!",
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

        function toggleOptionsField() {
            var type = $('#q_answer_type').val();
            if (type === 'multiple_choice') {
                $('#optionsGroup').slideDown();
                $('#q_options').attr('required', true);
            } else {
                $('#optionsGroup').slideUp();
                $('#q_options').removeAttr('required').val('');
            }
        }

        function openCreateModal() {
            $('#modalTitle').text('Tambah Pertanyaan');
            $('#questionForm').attr('action', "{{ route('questionnaire-form.questions.store', $form->id) }}");
            $('#methodPlaceholder').html('');
            $('#q_section').val('');
            $('#q_question').val('');
            $('#q_answer_type').val('yes_no');
            $('#q_options').val('');
            $('#q_weight').val('1');
            $('#q_order').val('0');
            $('#q_is_required').prop('checked', true);
            $('#q_is_active').prop('checked', true);
            toggleOptionsField();
        }

        function openEditModal(btn) {
            var q = $(btn).data('form');
            $('#modalTitle').text('Edit Pertanyaan');
            var actionUrl = "{{ route('questionnaire-form.questions.update', [$form->id, ':id']) }}".replace(':id', q.id);
            $('#questionForm').attr('action', actionUrl);
            $('#methodPlaceholder').html('<input type="hidden" name="_method" value="PUT">');
            $('#q_section').val(q.section);
            $('#q_question').val(q.question);
            $('#q_answer_type').val(q.answer_type);

            if (q.answer_type === 'multiple_choice' && Array.isArray(q.options)) {
                $('#q_options').val(q.options.join('\n'));
            } else {
                $('#q_options').val('');
            }

            $('#q_weight').val(q.weight);
            $('#q_order').val(q.order);
            $('#q_is_required').prop('checked', q.is_required);
            $('#q_is_active').prop('checked', q.is_active);
            toggleOptionsField();
            $('#modalQuestion').modal('show');
        }
    </script>
@endpush
