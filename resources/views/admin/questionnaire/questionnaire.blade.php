@extends('layouts.app', ['title' => 'Questionnaire Audit'])

@section('breadcrumb', 'Vendor')
@section('step', 'Audit On Desk')
@section('page_title', 'Questionnaire Audit')
@section('page_desc',
    'Jawab pertanyaan audit dari tim Quality Assurance Phapros. Anda dapat menyimpan draft dan
    melanjutkan nanti.')

    @php
        use App\Models\VendorAudit;
        use App\Models\VendorAuditQuestionTemplate;

        // Single source of truth for status label + badge color (avoids two maps going out of sync)
        $statusMeta = [
            VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS => ['label' => 'Sedang Diisi', 'color' => 'warning'],
            VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED => ['label' => 'Sudah Submit', 'color' => 'primary'],
            VendorAudit::STATUS_NEED_REVISION => ['label' => 'Perlu Revisi', 'color' => 'danger'],
            VendorAudit::STATUS_COMPLETED => ['label' => 'Selesai', 'color' => 'success'],
            VendorAudit::STATUS_REJECTED => ['label' => 'Ditolak', 'color' => 'danger'],
        ];

        $currentStatus = $statusMeta[$audit->status] ?? ['label' => strtoupper($audit->status), 'color' => 'primary'];

        $readOnly = in_array(
            $audit->status,
            [VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED, VendorAudit::STATUS_COMPLETED, VendorAudit::STATUS_REJECTED],
            true,
        );

        // Group questions by section
        $groupedQuestions = $questions->groupBy('section');

        // Previously saved answers (was referenced below but never defined -> undefined variable error)
        $payload = $audit->questionnaire_payload ?? [];
    @endphp

    @push('style')
        <link href="{{ asset('css/wizard-4.css') }}" rel="stylesheet" type="text/css" />
        <style>
            .wizard-content {
                animation: fadeIn 0.4s ease;
                display: none;
            }

            .wizard-content.active {
                display: block;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(8px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .cursor-pointer {
                cursor: pointer;
            }
        </style>
    @endpush

@section('content')
    <div id="au-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    <div class="card card-custom mb-8">
        <div class="card-header border-0 pt-6 pb-0">
            <div class="card-title align-items-start flex-column">
                <div class="d-flex align-items-center">
                    <span class="card-icon"><i class="fas fa-clipboard-list text-primary"></i></span>
                    <h3 class="card-label m-0">
                        Questionnaire Audit
                    </h3>
                </div>
                <div class="mt-2">
                    <span class="label label-light-info label-inline font-weight-bolder">ON DESK</span>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex flex-column text-right">
                    <span class="font-weight-bold text-dark mb-1">No Permohonan:
                        {{ $application->application_number }}</span>
                    <span class="text-muted font-size-sm">Status: <span
                            class="label label-inline font-weight-bolder label-light-{{ $currentStatus['color'] }}">{{ $currentStatus['label'] }}</span></span>
                </div>
            </div>
        </div>

        <div class="card-body py-4">
            @if (!$readOnly)
                <div class="text-muted small text-right mb-2" id="au-saved-at">
                    @if ($audit->questionnaire_payload)
                        <i class="flaticon2-check-mark text-success mr-1"></i>Draft terakhir disimpan:
                        {{ optional($audit->updated_at)->diffForHumans() }}
                    @else
                        Belum ada draft
                    @endif
                </div>
            @endif


            @if ($audit->status === VendorAudit::STATUS_NEED_REVISION && $audit->questionnaire_revision_notes)
                <div class="px-8 pb-4">
                    <div class="alert alert-custom alert-light-warning mb-0" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning text-warning"></i></div>
                        <div class="alert-text">
                            <div class="font-weight-bold mb-2">QA meminta revisi:</div>
                            <ul class="mb-0 pl-5">
                                @foreach ($audit->questionnaire_revision_notes as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if ($questions->isEmpty())
                <div class="alert alert-custom alert-light-info" role="alert">
                    <div class="alert-icon"><i class="flaticon-info"></i></div>
                    <div class="alert-text">
                        <h4 class="font-weight-bolder">Belum ada template pertanyaan</h4>
                        <p class="mb-0">Belum ada template pertanyaan untuk kategori Anda. Hubungi QA.</p>
                    </div>
                </div>
            @endif
        </div>

        @if (!$questions->isEmpty())
            <!-- Wizard Navigation -->
            <div class="wizard wizard-4 mb-8" id="kt_wizard">
                <div class="wz-nav-wrapper">
                    <div class="wz-nav" id="wzNav">
                        @foreach ($groupedQuestions as $sectionName => $sectionQuestions)
                            <div class="wz-nav-item {{ $loop->first ? 'active' : '' }}"
                                data-step-nav="{{ $loop->index }}" data-clickable="true"
                                title="{{ $sectionName ?: 'General' }}">
                                <div class="wz-nav-step">
                                    <div class="wz-nav-icon"><i class="fas fa-clipboard-list"></i></div>
                                    <div class="wz-nav-label text-truncate text-center font-weight-bold"
                                        style="max-width: 130px;">
                                        {{ $sectionName ?: 'General' }}
                                    </div>
                                </div>
                            </div>
                            @if (!$loop->last)
                                <div class="wz-nav-line" data-step-line="{{ $loop->index }}"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card-body py-4">
                <div class="px-8 pb-8">
                    <form id="au-qform" method="POST"
                        action="{{ route('vendor.audit.questionnaire.submit', $audit->id) }}"
                        enctype="multipart/form-data">
                        @csrf

                        @php $stepIndex = 0; @endphp
                        @foreach ($groupedQuestions as $sectionName => $sectionQuestions)
                            <div class="wizard-content {{ $stepIndex === 0 ? 'active' : '' }}"
                                data-step="{{ $stepIndex }}">
                                <div class="mb-8">
                                    <h4 class="font-weight-bolder text-dark mb-1">
                                        <i class="fas fa-clipboard-list text-primary mr-2"></i>
                                        {{ $sectionName ?: 'General' }}
                                    </h4>
                                    <span class="text-muted font-size-sm font-weight-bold">{{ $sectionQuestions->count() }}
                                        pertanyaan</span>
                                    <div class="separator separator-dashed separator-border-2 mt-4"></div>
                                </div>

                                @foreach ($sectionQuestions as $q)
                                    @php
                                        $val = $payload[$q->id] ?? null;
                                        $inputName = "answers[{$q->id}]";
                                        $inputLabel = "<span class=\"font-weight-bolder font-size-h6 text-dark\">{$loop->iteration}. {$q->question}</span>";
                                    @endphp

                                    <div class="mb-8 bg-light p-6 rounded" style="border-left: 4px solid var(--primary);">
                                        @if ($q->answer_type === VendorAuditQuestionTemplate::TYPE_YES_NO)
                                            <x-vendor-radio :name="$inputName" :label="$inputLabel" :options="[
                                                ['value' => 'yes', 'label' => 'Ya'],
                                                ['value' => 'no', 'label' => 'Tidak'],
                                            ]"
                                                :selected="$val" :readonly="$readOnly" :required="$q->is_required" />
                                        @elseif ($q->answer_type === VendorAuditQuestionTemplate::TYPE_MULTIPLE_CHOICE)
                                            @php
                                                $opts = array_combine($q->options ?? [], $q->options ?? []);
                                            @endphp
                                            <x-vendor-select :name="$inputName" :label="$inputLabel" :options="$opts"
                                                :selected="$val" :readonly="$readOnly" :required="$q->is_required" />
                                        @elseif ($q->answer_type === VendorAuditQuestionTemplate::TYPE_TEXT)
                                            <x-vendor-input :name="$inputName" :label="$inputLabel" type="textarea"
                                                :value="is_string($val) ? $val : ''" :readonly="$readOnly" :required="$q->is_required"
                                                :application="$application" />
                                        @elseif ($q->answer_type === VendorAuditQuestionTemplate::TYPE_DOCUMENT)
                                            <x-vendor-input :name="$inputName" :label="$inputLabel" type="file"
                                                :value="is_string($val) ? $val : ''" :readonly="$readOnly" :required="$q->is_required && !$val"
                                                :application="$application" />
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @php $stepIndex++; @endphp
                        @endforeach

                        <!-- Control Buttons -->
                        <div class="d-flex justify-content-between border-top pt-8 mt-10">
                            <div>
                                <button type="button"
                                    class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4" id="btn-prev"
                                    style="display: none;">
                                    <i class="la la-angle-left"></i> Sebelumnya
                                </button>
                            </div>
                            <div class="d-flex align-items-center">
                                @if (!$readOnly)
                                    <button type="button"
                                        class="btn btn-outline-success font-weight-bolder text-uppercase px-9 py-4 mr-3"
                                        id="au-save-draft">
                                        <i class="flaticon2-save"></i> Simpan Draft
                                    </button>
                                @endif

                                <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4"
                                    id="btn-next">
                                    Selanjutnya <i class="la la-angle-right"></i>
                                </button>

                                @if (!$readOnly)
                                    <button type="submit"
                                        class="btn btn-success font-weight-bolder text-uppercase px-9 py-4" id="btn-submit"
                                        style="display: none;">
                                        <i class="flaticon2-send-1"></i> Submit ke QA
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if ($readOnly)
                            <div class="alert alert-custom alert-light-info mt-8 mb-0" role="alert">
                                <div class="alert-icon"><i class="flaticon-info"></i></div>
                                <div class="alert-text font-weight-bold">
                                    Questionnaire ini sudah disubmit dan sedang/telah diverifikasi oleh QA. Anda
                                    hanya dapat
                                    melihatnya.
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        @endif
    </div>

    <x-document-preview />
@endsection

@push('scripts')
    <script>
        $(function() {
            var currentStep = 0;
            var totalSteps = {{ count($groupedQuestions) }};

            function updateWizardUI() {
                // Show/hide steps
                $('.wizard-content').removeClass('active');
                $('.wizard-content[data-step="' + currentStep + '"]').addClass('active');

                // Update nav tracker
                $('.wz-nav-item').removeClass('active done');
                $('.wz-nav-line').removeClass('done');

                $('.wz-nav-item').each(function() {
                    var idx = parseInt($(this).data('step-nav'));
                    var $icon = $(this).find('.wz-nav-icon i');
                    if (idx === currentStep) {
                        $(this).addClass('active');
                        $icon.attr('class', 'fas fa-clipboard-list');
                    } else if (idx < currentStep) {
                        $(this).addClass('done');
                        $icon.attr('class', 'fas fa-check');
                    } else {
                        $icon.attr('class', 'fas fa-clipboard-list');
                    }
                });

                $('.wz-nav-line').each(function() {
                    var idx = parseInt($(this).data('step-line'));
                    if (idx < currentStep) {
                        $(this).addClass('done');
                    }
                });



                // Show/hide buttons
                if (currentStep === 0) {
                    $('#btn-prev').hide();
                } else {
                    $('#btn-prev').show();
                }

                if (currentStep === totalSteps - 1) {
                    $('#btn-next').hide();
                    $('#btn-submit').show();
                } else {
                    $('#btn-next').show();
                    $('#btn-submit').hide();
                }
            }

            // Next Step click
            $('#btn-next').on('click', function() {
                // Validate current step fields natively using HTML5 validation API
                var activeStep = $('.wizard-content[data-step="' + currentStep + '"]');
                var isValid = true;

                var invalid = activeStep[0].querySelector(':invalid');
                if (invalid) {
                    invalid.reportValidity();
                    isValid = false;
                }

                if (!isValid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Harap lengkapi semua pertanyaan yang wajib diisi pada bagian ini.'
                    });
                    return;
                }

                if (currentStep < totalSteps - 1) {
                    currentStep++;
                    updateWizardUI();

                    $('html, body').animate({
                        scrollTop: $("#kt_wizard").offset().top - 30
                    }, 500);
                }
            });

            // Prev Step click
            $('#btn-prev').on('click', function() {
                if (currentStep > 0) {
                    currentStep--;
                    updateWizardUI();
                }
            });

            // Handle direct nav click on completed or active step
            $('.wz-nav-item').on('click', function() {
                var targetStep = parseInt($(this).data('step-nav'));
                if (isNaN(targetStep)) return;

                // Only allow jumping back to already visited/completed steps, or going forward if current validates
                if (targetStep < currentStep) {
                    currentStep = targetStep;
                    updateWizardUI();
                } else if (targetStep > currentStep) {
                    // To go forward, we must validate the current step
                    var activeStep = $('.wizard-content[data-step="' + currentStep + '"]');
                    var isValid = true;
                    var invalid = activeStep[0].querySelector(':invalid');
                    if (invalid) {
                        invalid.reportValidity();
                        isValid = false;
                    }

                    if (!isValid) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Harap lengkapi semua pertanyaan yang wajib diisi pada bagian ini.'
                        });
                    } else {
                        currentStep = targetStep;
                        updateWizardUI();

                        $('html, body').animate({
                            scrollTop: $("#kt_wizard").offset().top - 30
                        }, 500);
                    }
                }
            });

            // Initialize
            updateWizardUI();

            // Handle Flash Messages
            var flash = $('#au-flash');
            if (flash.length) {
                var success = flash.data('success');
                var error = flash.data('error');
                var warning = flash.data('warning');
                var info = flash.data('info');

                if (success) Swal.fire('Berhasil', success, 'success');
                if (error) Swal.fire('Gagal', error, 'error');
                if (warning) Swal.fire('Peringatan', warning, 'warning');
                if (info) Swal.fire('Info', info, 'info');
            }

            @if (!$readOnly)
                $('#au-save-draft').on('click', function() {
                    var $btn = $(this);
                    var data = new FormData($('#au-qform')[0]);
                    $btn.prop('disabled', true).html('<i class="flaticon2-loader"></i> Menyimpan...');
                    $.ajax({
                        url: '{{ route('vendor.audit.questionnaire.save', $audit->id) }}',
                        method: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(r) {
                            $('#au-saved-at').html('Draft terakhir disimpan: baru saja');
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

            // Submit with SweetAlert (intercepts HTML5 validated form submission)
            $('#au-qform').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Kirim Questionnaire?',
                    text: 'Submit questionnaire ke QA? Anda tidak dapat mengubah data setelah disubmit.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Submit!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-light-primary'
                    }
                }).then((result) => {
                    // Compatible with SweetAlert2 v7 (boolean), v8 ({value}), and v9+ ({isConfirmed})
                    var confirmed = result === true || result?.value === true || result
                        ?.isConfirmed === true;

                    if (confirmed) {
                        Swal.fire({
                            title: 'Mohon tunggu...',
                            text: 'Sedang memproses data',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Bypass all jQuery handlers and potential name="submit" collisions
                        var formObj = document.getElementById('au-qform');
                        HTMLFormElement.prototype.submit.call(formObj);
                    }
                });
            });
        });
    </script>
@endpush
