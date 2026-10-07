@extends('layouts.app', ['title' => __('questionnaire_audit_title')])

@section('breadcrumb', __('vendor'))
@section('step', __('audit_on_desk'))
@section('page_title', __('vendor_audit_questionnaire_title'))
@section('page_desc', __('vendor_audit_questionnaire_desc'))

    @php
        use App\Models\VendorAudit;
        use App\Models\VendorAuditQuestionTemplate;

        $statusMeta = [
            VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS => [
                'label' => __('status_filling_in_progress'),
                'class' => 'vnd-status--revision',
                'icon' => 'flaticon2-hourglass text-warning',
            ],
            VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED => [
                'label' => __('status_submitted'),
                'class' => 'vnd-status--info',
                'icon' => 'flaticon2-check-mark text-info',
            ],
            VendorAudit::STATUS_NEED_REVISION => [
                'label' => __('status_short_need_revision'),
                'class' => 'vnd-status--revision',
                'icon' => 'flaticon-warning text-warning',
            ],
            VendorAudit::STATUS_COMPLETED => [
                'label' => __('status_completed'),
                'class' => 'vnd-status--approved',
                'icon' => 'flaticon2-check-mark text-success',
            ],
            VendorAudit::STATUS_REJECTED => [
                'label' => __('status_short_rejected'),
                'class' => 'vnd-status--rejected',
                'icon' => 'flaticon-danger text-danger',
            ],
        ];

        $currentStatus = $statusMeta[$audit->status] ?? [
            'label' => strtoupper($audit->status),
            'class' => 'vnd-status--info',
            'icon' => 'flaticon2-information',
        ];

        $readOnly = in_array(
            $audit->status,
            [VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED, VendorAudit::STATUS_COMPLETED, VendorAudit::STATUS_REJECTED],
            true,
        );

        $groupedQuestions = $questions->groupBy('section');
        $payload = $audit->questionnaire_payload ?? [];
    @endphp

    @push('style')
        <link href="{{ asset('css/wizard-4.css') }}" rel="stylesheet" type="text/css" />
        <style>
            .wizard-content {
                animation: fadeIn 0.35s ease;
                display: none;
            }

            .wizard-content.active {
                display: block;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(6px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .vnd-question-block {
                background: #f8fafc;
                border: 1px solid var(--vnd-border, #e2e8f0);
                border-left: 4px solid var(--vnd-blue, #005db6);
                border-radius: 8px;
                padding: 1.25rem;
                margin-bottom: 1.25rem;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
            }

            .vnd-question-block:hover {
                border-color: #cbd5e1;
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.03);
            }
        </style>
    @endpush

@section('content')
    {{-- Hidden Flash Container untuk SweetAlert --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- Main Container Card --}}
    <div class="vnd-card mb-8">
        {{-- Card Head --}}
        <div class="vnd-card-head">
            <div>
                <div class="vnd-card-title">
                    <span class="vnd-card-title-dot"></span>
                    {{ __('vendor_audit_questionnaire_title') }}
                </div>
                <div class="d-flex align-items-center mt-1" style="gap:.5rem;">
                    <span class="vnd-tag vnd-tag--on-desk">{{ __('on_desk_audit_caps') }}</span>
                    <span class="text-muted font-size-sm">{{ __('application_number_colon') }} <strong>{{ $application->application_number }}</strong></span>
                </div>
            </div>

            <div class="d-flex align-items-center" style="gap:.75rem;">
                <span class="vnd-status {{ $currentStatus['class'] }}">
                    <i class="{{ $currentStatus['icon'] }}" style="font-size:.65rem;"></i>
                    {{ $currentStatus['label'] }}
                </span>
            </div>
        </div>

        {{-- Body Info & Alerts --}}
        <div class="card-body py-4 px-6">
            @if (!$readOnly)
                <div class="text-muted small text-right mb-3" id="au-saved-at">
                    @if ($audit->questionnaire_payload)
                        <i class="flaticon2-check-mark text-success mr-1"></i>{{ __('last_draft_saved_colon') }}
                        {{ optional($audit->updated_at)->diffForHumans() }}
                    @else
                        {{ __('no_draft_saved_yet') }}
                    @endif
                </div>
            @endif

            {{-- Alert Revisi QA --}}
            @if ($audit->status === VendorAudit::STATUS_NEED_REVISION && $audit->questionnaire_revision_notes)
                <div class="alert alert-custom alert-light-warning mb-6" role="alert">
                    <div class="alert-icon"><i class="flaticon-warning text-warning"></i></div>
                    <div class="alert-text">
                        <div class="font-weight-bold mb-2">{{ __('qa_revision_notes_colon') }}</div>
                        <ul class="mb-0 pl-5">
                            @foreach ($audit->questionnaire_revision_notes as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Empty Template Info --}}
            @if ($questions->isEmpty())
                <div class="vnd-empty py-8">
                    <div class="vnd-empty-icon">
                        <i class="flaticon-info text-info"></i>
                    </div>
                    <div class="vnd-empty-title">{{ __('no_audit_questions_title') }}</div>
                    <div class="vnd-empty-sub">{{ __('no_audit_questions_desc') }}</div>
                </div>
            @endif
        </div>

        @if (!$questions->isEmpty())
            {{-- Wizard Step Tracker --}}
            <div class="wizard wizard-4 mb-6" id="kt_wizard">
                <div class="wz-nav-wrapper px-4">
                    <div class="wz-nav" id="wzNav">
                        @foreach ($groupedQuestions as $sectionName => $sectionQuestions)
                            <div class="wz-nav-item {{ $loop->first ? 'active' : '' }}"
                                data-step-nav="{{ $loop->index }}" data-clickable="true"
                                title="{{ $sectionName ?: __('general') }}">
                                <div class="wz-nav-step">
                                    <div class="wz-nav-icon"><i class="fas fa-clipboard-list"></i></div>
                                    <div class="wz-nav-label text-truncate text-center font-weight-bold"
                                        style="max-width: 130px;">
                                        {{ $sectionName ?: __('general') }}
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

            {{-- Form Questions Content --}}
            <div class="card-body py-4 px-6">
                <form id="au-qform" method="POST"
                    action="{{ route('vendor.audit.questionnaire.submit', $audit->id) }}"
                    enctype="multipart/form-data">
                    @csrf

                    @php $stepIndex = 0; @endphp
                    @foreach ($groupedQuestions as $sectionName => $sectionQuestions)
                        <div class="wizard-content {{ $stepIndex === 0 ? 'active' : '' }}"
                            data-step="{{ $stepIndex }}">
                            <div class="mb-6">
                                <h4 class="font-weight-bolder text-dark mb-1 d-flex align-items-center">
                                    <i class="flaticon2-list-1 text-primary mr-2" style="font-size:1.1rem;"></i>
                                    {{ $sectionName ?: __('general') }}
                                </h4>
                                <span class="text-muted font-size-sm font-weight-bold">
                                    {{ __('questions_count_in_section', ['count' => $sectionQuestions->count()]) }}
                                </span>
                                <div class="separator separator-dashed separator-border-2 mt-3"></div>
                            </div>

                            @foreach ($sectionQuestions as $q)
                                @php
                                    $val = $payload[$q->id] ?? null;
                                    $inputName = "answers[{$q->id}]";
                                    $inputLabel = "<span class=\"font-weight-bolder font-size-h6 text-dark\">{$loop->iteration}. {$q->question}</span>";
                                @endphp

                                <div class="vnd-question-block">
                                    @if ($q->answer_type === VendorAuditQuestionTemplate::TYPE_YES_NO)
                                        <x-vendor-radio :name="$inputName" :label="$inputLabel" :options="[
                                            ['value' => 'yes', 'label' => __('yes')],
                                            ['value' => 'no', 'label' => __('no')],
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

                    {{-- Navigation & Form Control Buttons --}}
                    <div class="d-flex justify-content-between border-top pt-6 mt-8">
                        <div>
                            <button type="button"
                                class="btn btn-light-primary font-weight-bolder text-uppercase px-7 py-3" id="btn-prev"
                                style="display: none;">
                                <i class="la la-angle-left"></i> {{ __('previous') }}
                            </button>
                        </div>
                        <div class="d-flex align-items-center" style="gap:.5rem;">
                            @if (!$readOnly)
                                <button type="button"
                                    class="btn btn-outline-success font-weight-bolder text-uppercase px-6 py-3"
                                    id="au-save-draft">
                                    <i class="flaticon2-save"></i> {{ __('save_draft') }}
                                </button>
                            @endif

                            <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-7 py-3"
                                id="btn-next">
                                {{ __('next') }} <i class="la la-angle-right"></i>
                            </button>

                            @if (!$readOnly)
                                <button type="submit"
                                    class="btn btn-success font-weight-bolder text-uppercase px-7 py-3" id="btn-submit"
                                    style="display: none;">
                                    <i class="flaticon2-send-1"></i> {{ __('submit_to_qa') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    @if ($readOnly)
                        <div class="alert alert-custom alert-light-info mt-6 mb-0" role="alert">
                            <div class="alert-icon"><i class="flaticon-info"></i></div>
                            <div class="alert-text font-weight-bold">
                                {!! __('questionnaire_readonly_notice') !!}
                            </div>
                        </div>
                    @endif
                </form>
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
                $('.wizard-content').removeClass('active');
                $('.wizard-content[data-step="' + currentStep + '"]').addClass('active');

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

            $('#btn-next').on('click', function() {
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
                        title: @json(__('warning')),
                        text: @json(__('complete_all_required_questions'))
                    });
                    return;
                }

                if (currentStep < totalSteps - 1) {
                    currentStep++;
                    updateWizardUI();

                    $('html, body').animate({
                        scrollTop: $("#kt_wizard").offset().top - 30
                    }, 400);
                }
            });

            $('#btn-prev').on('click', function() {
                if (currentStep > 0) {
                    currentStep--;
                    updateWizardUI();
                }
            });

            $('.wz-nav-item').on('click', function() {
                var targetStep = parseInt($(this).data('step-nav'));
                if (isNaN(targetStep)) return;

                if (targetStep < currentStep) {
                    currentStep = targetStep;
                    updateWizardUI();
                } else if (targetStep > currentStep) {
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
                            title: @json(__('warning')),
                            text: @json(__('complete_all_required_questions'))
                        });
                    } else {
                        currentStep = targetStep;
                        updateWizardUI();

                        $('html, body').animate({
                            scrollTop: $("#kt_wizard").offset().top - 30
                        }, 400);
                    }
                }
            });

            updateWizardUI();

            /* Flash Notifications via SweetAlert Toast */
            (function() {
                var $el = $('#vnd-flash');
                if (!$el.length || typeof Swal === 'undefined') return;

                var messages = [
                    { key: 'success', icon: 'success', title: @json(__('success')) },
                    { key: 'error', icon: 'error', title: @json(__('failed')) },
                    { key: 'warning', icon: 'warning', title: @json(__('warning')) },
                    { key: 'info', icon: 'info', title: @json(__('information')) },
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

            @if (!$readOnly)
                $('#au-save-draft').on('click', function() {
                    var $btn = $(this);
                    var data = new FormData($('#au-qform')[0]);
                    $btn.prop('disabled', true).html('<i class="flaticon2-loader"></i> ' + @json(__('saving')));

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
                            $('#au-saved-at').html('<i class="flaticon2-check-mark text-success mr-1"></i>' + @json(__('last_draft_saved_just_now')));
                            Swal.fire({
                                icon: 'success',
                                title: @json(__('draft_saved')),
                                toast: true,
                                position: 'top-end',
                                timer: 2500,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: @json(__('failed_to_save_draft')),
                                text: @json(__('error_saving_draft_try_again'))
                            });
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html(
                                '<i class="flaticon2-save"></i> ' + @json(__('save_draft')));
                        }
                    });
                });
            @endif

            /* Confirm Submit Questionnaire via SweetAlert */
            $('#au-qform').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: @json(__('submit_questionnaire_question')),
                    text: @json(__('submit_questionnaire_confirm_desc')),
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: @json(__('yes_submit_exclamation')),
                    cancelButtonText: @json(__('cancel')),
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-success font-weight-bold mr-2',
                        cancelButton: 'btn btn-secondary font-weight-bold'
                    }
                }).then(function(result) {
                    var confirmed = result === true || result?.value === true || result?.isConfirmed === true;

                    if (confirmed) {
                        Swal.fire({
                            title: @json(__('please_wait_dots')),
                            text: @json(__('processing_and_sending_data')),
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: function() {
                                Swal.showLoading();
                            }
                        });
                        var formObj = document.getElementById('au-qform');
                        HTMLFormElement.prototype.submit.call(formObj);
                    }
                });
            });
        });
    </script>
@endpush
