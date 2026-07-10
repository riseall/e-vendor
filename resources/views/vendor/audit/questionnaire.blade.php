@extends('layouts.app', ['title' => 'Questionnaire Audit'])

@section('breadcrumb', 'Vendor')
@section('step', 'Audit On Desk')
@section('page_title', 'Questionnaire Audit')
@section('page_desc', 'Jawab pertanyaan audit dari tim Quality Assurance Phapros. Anda dapat menyimpan draft dan
    melanjutkan nanti.')

    @push('style')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="{{ asset('css/admin/audit.css') }}">
    @endpush

    @php
        use App\Models\VendorAudit;
        use App\Models\VendorAuditQuestionTemplate;

        $statusLabels = [
            VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS => 'Sedang Diisi',
            VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED => 'Sudah Submit',
            VendorAudit::STATUS_NEED_REVISION => 'Perlu Revisi',
            VendorAudit::STATUS_COMPLETED => 'Selesai',
            VendorAudit::STATUS_REJECTED => 'Ditolak',
        ];

        $readOnly = in_array(
            $audit->status,
            [VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED, VendorAudit::STATUS_COMPLETED, VendorAudit::STATUS_REJECTED],
            true,
        );
    @endphp

@section('content')
    <div id="au-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    <div class="au-card">
        <div class="au-card-head">
            <div>
                <div class="au-card-title">
                    <span class="au-card-title-dot"></span>
                    Questionnaire Audit
                    <span class="au-tag au-tag--on-desk ml-2">ON DESK</span>
                </div>
                <div class="text-muted small mt-1">
                    No Permohonan: <strong>{{ $application->application_number }}</strong> ·
                    Status: <strong>{{ $statusLabels[$audit->status] ?? strtoupper($audit->status) }}</strong>
                </div>
            </div>
            @if (!$readOnly)
                <div class="text-muted small" id="au-saved-at">
                    @if ($audit->questionnaire_payload)
                        Draft terakhir disimpan: {{ optional($audit->updated_at)->diffForHumans() }}
                    @else
                        Belum ada draft
                    @endif
                </div>
            @endif
        </div>

        @if ($audit->status === VendorAudit::STATUS_NEED_REVISION && $audit->questionnaire_revision_notes)
            <div class="card-body px-4 pb-0">
                <div class="alert alert-warning">
                    <strong>QA meminta revisi:</strong>
                    <ul class="mt-2 mb-0">
                        @foreach ($audit->questionnaire_revision_notes as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="card-body px-4 pb-5">
            @if ($questions->isEmpty())
                <p class="text-muted">Belum ada template pertanyaan untuk kategori Anda. Hubungi QA.</p>
            @else
                <form id="au-qform" method="POST" action="{{ route('vendor.audit.questionnaire.submit', $audit->id) }}">
                    @csrf

                    @php $section = null; @endphp
                    @foreach ($questions as $q)
                        @if ($section !== $q->section)
                            @php $section = $q->section; @endphp
                            <h6 class="au-section-title">{{ $section }}</h6>
                        @endif

                        @php
                            $val = $payload[$q->id] ?? null;
                        @endphp
                        <div class="form-group">
                            <label class="au-form-label">
                                {{ $loop->iteration }}. {{ $q->question }}
                                <span
                                    class="text-muted small">({{ ucfirst(str_replace('_', '-', $q->answer_type)) }})</span>
                            </label>

                            @if ($q->answer_type === VendorAuditQuestionTemplate::TYPE_YES_NO)
                                <select name="answers[{{ $q->id }}]" class="form-control"
                                    {{ $readOnly ? 'disabled' : '' }} required>
                                    <option value="">— Pilih —</option>
                                    <option value="yes" {{ $val === 'yes' ? 'selected' : '' }}>Ya</option>
                                    <option value="no" {{ $val === 'no' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            @elseif ($q->answer_type === VendorAuditQuestionTemplate::TYPE_MULTIPLE_CHOICE)
                                <select name="answers[{{ $q->id }}]" class="form-control"
                                    {{ $readOnly ? 'disabled' : '' }} required>
                                    <option value="">— Pilih —</option>
                                    @foreach ($q->options ?? [] as $opt)
                                        <option value="{{ $opt }}" {{ $val === $opt ? 'selected' : '' }}>
                                            {{ $opt }}</option>
                                    @endforeach
                                </select>
                            @elseif ($q->answer_type === VendorAuditQuestionTemplate::TYPE_TEXT)
                                <textarea name="answers[{{ $q->id }}]" class="form-control" rows="3" {{ $readOnly ? 'disabled' : '' }}
                                    required>{{ $val }}</textarea>
                            @elseif ($q->answer_type === VendorAuditQuestionTemplate::TYPE_DOCUMENT)
                                <input type="text" name="answers[{{ $q->id }}]" class="form-control"
                                    placeholder="Nama / path dokumen (upload via menu Dokumen)"
                                    value="{{ is_string($val) ? $val : '' }}" {{ $readOnly ? 'disabled' : '' }} required>
                            @endif
                        </div>
                    @endforeach

                    @if (!$readOnly)
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-light" id="au-save-draft">
                                <i class="flaticon2-save"></i> Simpan Draft
                            </button>
                            <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Submit questionnaire ke QA? Anda tidak dapat mengubah setelah submit.');">
                                <i class="flaticon2-send-1"></i> Submit ke QA
                            </button>
                        </div>
                    @else
                        <div class="alert alert-info mt-3">
                            Questionnaire ini sudah disubmit dan sedang/telah diverifikasi oleh QA.
                        </div>
                    @endif
                </form>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        $(function() {
            @if (!$readOnly)
                $('#au-save-draft').on('click', function() {
                    var $btn = $(this);
                    var data = $('#au-qform').serialize();
                    $btn.prop('disabled', true).html('<i class="flaticon2-loader"></i> Menyimpan...');
                    $.ajax({
                        url: '{{ route('vendor.audit.questionnaire.save', $audit->id) }}',
                        method: 'POST',
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(r) {
                            $('#au-saved-at').text('Draft tersimpan ' + new Date()
                                .toLocaleTimeString());
                            Swal.fire({
                                icon: 'success',
                                title: 'Draft tersimpan',
                                toast: true,
                                position: 'top-end',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal menyimpan draft'
                            });
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html(
                                '<i class="flaticon2-save"></i> Simpan Draft');
                        }
                    });
                });
            @endif
        });
    </script>
@endpush
