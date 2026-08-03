@extends('layouts.app', ['title' => 'Kelola Pertanyaan Kuesioner'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Kelola Pertanyaan')
@section('page_title', 'Kelola Pertanyaan Kuesioner')
@section('page_desc', 'Daftar pertanyaan kuesioner audit untuk ' . $form->name)

@section('content')
    {{-- Flash container untuk SweetAlert Toast --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- Stat Cards Summary --}}
    @php
        $questionsColl = collect($questions ?? []);
        $totalQuestions = $questionsColl->count();
        $requiredCount = $questionsColl->filter(fn($q) => (bool) $q->is_required)->count();
        $sectionsCount = $questionsColl->pluck('section')->unique()->filter()->count();
        $totalWeight = $questionsColl->sum('weight');
    @endphp

    <div class="row mb-6">
        <x-dash-card :value="$totalQuestions" label="Total Pertanyaan" icon="flaticon2-list-1" type="primary" />
        <x-dash-card :value="$requiredCount" label="Wajib Diisi" icon="flaticon2-check-mark" type="success" />
        <x-dash-card :value="$sectionsCount" label="Bagian / Bab" icon="flaticon2-folder" type="info" />
        <x-dash-card :value="$totalWeight" label="Total Bobot" icon="flaticon-shapes" type="warning" />
    </div>

    {{-- Main Container Card --}}
    <div class="vnd-card">
        {{-- Card Head --}}
        <div class="vnd-card-head">
            <div class="d-flex align-items-center" style="gap:.75rem;">
                <a href="{{ route('questionnaire-form.index') }}"
                    class="btn btn-sm btn-icon btn-light-primary rounded-circle" title="Kembali ke Daftar Form">
                    <i class="flaticon2-back" style="font-size:.8rem;"></i>
                </a>
                <div>
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        {{ $form->name }}
                    </div>
                    @if ($form->document_number)
                        <div class="text-muted font-size-sm mt-1">
                            No Dokumen: <code>{{ $form->document_number }}</code>
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center" style="gap:.5rem;">
                <button type="button" class="btn btn-sm btn-success font-weight-bolder px-4" data-toggle="modal"
                    data-target="#modalImport">
                    <i class="fas fa-file-excel icon-sm" style="font-size:.75rem;"></i> Import Excel
                </button>
                <button type="button" class="btn btn-sm btn-primary font-weight-bolder px-4" data-toggle="modal"
                    data-target="#modalQuestion" onclick="openCreateModal()">
                    <i class="fas fa-plus-circle icon-sm" style="font-size:.75rem;"></i> Tambah Pertanyaan
                </button>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="card-body p-0 px-4 pt-5 pb-6">
            <div class="table-responsive">
                <table class="table tbl-vendor table-borderless" id="questions_table" style="width:100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 70px;">No Urut</th>
                            <th style="min-width: 250px;">Pertanyaan</th>
                            <th style="min-width: 150px;">Tipe Jawaban</th>
                            <th class="text-center" style="width: 90px;">Bobot</th>
                            <th class="text-center" style="width: 90px;">Wajib</th>
                            <th class="text-center" style="width: 100px;">Status</th>
                            <th class="text-right no-sort" style="min-width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentSection = null; @endphp
                        @forelse ($questions as $q)
                            @include('admin.questionnaire.partials.question-row', [
                                'q' => $q,
                                'currentSection' => $currentSection,
                                'form' => $form,
                            ])
                            @php $currentSection = $q->section; @endphp
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="vnd-empty py-8">
                                        <div class="vnd-empty-icon">
                                            <i class="flaticon2-list-1"></i>
                                        </div>
                                        <div class="vnd-empty-title">Belum Ada Pertanyaan</div>
                                        <div class="vnd-empty-sub">Klik "Tambah Pertanyaan" atau "Import Excel" untuk
                                            membuat pertanyaan kuesioner.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Question Modal Partial --}}
    @include('admin.questionnaire.partials.question-modal')

    {{-- Import Modal Partial --}}
    @include('admin.questionnaire.partials.import-modal')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
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

            @if ($errors->any())
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Validasi',
                        html: '{!! implode('<br>', array_map('e', $errors->all())) !!}'
                    });
                }
            @endif

            /* Confirm Delete Question via SweetAlert */
            $(document).on('click', '.btn-delete-question', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var questionText = $(this).data('question') || 'pertanyaan ini';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Pertanyaan?',
                        text: 'Pertanyaan "' + questionText + '" akan dihapus permanen!',
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
                    if (confirm('Hapus pertanyaan ini?')) {
                        form.submit();
                    }
                }
            });

            /* Update Custom File Input Label on file selection */
            $(document).on('change', '.custom-file-input', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName ||
                    'Pilih file excel...');
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
            $('#q_is_required').prop('checked', Boolean(q.is_required));
            $('#q_is_active').prop('checked', Boolean(q.is_active));
            toggleOptionsField();
            $('#modalQuestion').modal('show');
        }
    </script>
@endpush
