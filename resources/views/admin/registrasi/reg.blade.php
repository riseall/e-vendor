@extends('layouts.app', ['title' => __('vendor_registration')])

@push('style')
    <link href="{{ asset('css/wizard-4.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('breadcrumb', __('registration'))
@section('step', __('pre_qualification'))
@section('page_title', __('vendor_registration'))
@section('page_desc', __('reg_page_desc'))

@section('content')

    @if (($applicationStatus ?? null) === 'submitted')
        <div class="alert alert-custom alert-light-primary fade show mb-5" role="alert">
            <div class="alert-icon"><i class="flaticon2-check-mark text-primary icon-md"></i></div>
            <div class="alert-text">
                <span class="font-weight-bold text-dark-75">{{ __('app_already_submitted') }}</span>
                {{ __('form_readonly_verification') }}
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert">
                    <span><i class="ki ki-close text-dark-75"></i></span>
                </button>
            </div>
        </div>
    @elseif (($applicationStatus ?? null) === 'need_revision')
        <div class="alert alert-custom alert-light-warning fade show mb-5" role="alert">
            <div class="alert-icon"><i class="flaticon-warning text-warning icon-md"></i></div>
            <div class="alert-text">
                <span class="font-weight-bold text-dark-75">{{ __('app_need_revision') }}</span>
                {{ __('app_need_revision_desc') }}
                @if (!empty($draft['admin_note'] ?? null))
                    <div class="mt-2 text-dark-75">
                        <span class="font-weight-bold">{{ __('note') }}:</span> {{ $draft['admin_note'] }}
                    </div>
                @endif
                @if (!empty($draft['revision_notes'] ?? null))
                    <div class="mt-2 text-dark-75">
                        @foreach ($draft['revision_notes'] as $revision)
                            <div>
                                <span
                                    class="font-weight-bold">{{ $revision['label'] ?? ($revision['field'] ?? __('general')) }}:</span>
                                {{ $revision['note'] ?? '-' }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert">
                    <span><i class="ki ki-close text-dark-75"></i></span>
                </button>
            </div>
        </div>
    @elseif (isset($hasDraft) && $hasDraft)
        <div class="alert alert-custom alert-light-warning fade show mb-5" role="alert">
            <div class="alert-icon"><i class="fas fa-exclamation-triangle text-warning icon-md"></i></div>
            <div class="alert-text">
                <span class="font-weight-bold text-dark-75">{{ __('app_unfinished_draft') }}</span>
                {{-- Data tersimpan hingga step <strong>{{ $draftStep ?? 1 }} --}}</strong>
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert">
                    <span><i class="ki ki-close text-dark-75"></i></span>
                </button>
            </div>
        </div>
    @endif

    <div class="card card-custom card-transparent">
        <div class="card-body p-0">

            {{-- Wizard --}}
            <div class="wizard wizard-4" id="kt_wizard">

                {{-- Wizard Nav --}}
                <div class="wz-nav-wrapper">
                    <div class="wz-nav" id="wzNav">
                    </div>
                </div>
                {{-- End Wizard Nav --}}

                {{-- Wizard Body --}}
                <div class="card card-custom card-shadowless rounded-top-0">
                    <div class="card-body p-0">
                        <div class="row justify-content-center py-8 px-8 py-lg-10 px-lg-10">
                            <div class="col-xl-12">

                                <form class="form" id="kt_form" action="{{ route('registrasi.save-draft') }}"
                                    method="POST" enctype="multipart/form-data"
                                    data-max-file-kb="{{ \App\Services\VendorUploadPolicy::MAX_FILE_KB }}">
                                    @csrf
                                    <input type="hidden" name="application_id" id="application_id"
                                        value="{{ $applicationId ?? '' }}">
                                    <input type="hidden" name="current_step" id="current_step" value="1">

                                    {{-- [STEP 1: KATEGORI] --}}
                                    <div class="pb-5" data-step-id="step-1">
                                        @include('admin.registrasi.steps.1-kategori')
                                    </div>
                                    {{-- End Step 1 --}}

                                    {{-- Persyaratan Umum --}}
                                    {{-- [STEP 2: FORM UMUM] --}}
                                    <div class="pb-5" data-step-id="step-2">
                                        @include('admin.registrasi.steps.2-umum')
                                    </div>

                                    {{-- [STEP 3: INFO PEMBAYARAN] --}}
                                    <div class="pb-5" data-step-id="step-3">
                                        @include('admin.registrasi.steps.3-pembayaran')
                                    </div>

                                    {{-- [STEP 4: KOMITMEN] --}}
                                    <div class="pb-5" data-step-id="step-4">
                                        @include('admin.registrasi.steps.4-komitmen')
                                    </div>

                                    {{-- [STEP 5: INFO LAIN] --}}
                                    <div class="pb-5" data-step-id="step-5">
                                        @include('admin.registrasi.steps.5-info')
                                    </div>

                                    {{-- [STEP 6: ISIAN LOKAL] --}}
                                    <div class="pb-5" data-step-id="step-6">
                                        @include('admin.registrasi.steps.6-lokal')
                                    </div>

                                    {{-- [STEP 7: PRODUK --}}
                                    <div class="pb-5" data-step-id="step-7">
                                        @include('admin.registrasi.steps.7-produk')
                                    </div>

                                    {{-- [STEP 8: DOKUMEN --}}
                                    <div class="pb-5" data-step-id="step-8">
                                        @include('admin.registrasi.steps.8-dokumen')
                                    </div>

                                    {{-- Step 9: Persyaratan Khusus --}}
                                    {{-- Bahan Baku, Bahan Kemas, Produk Jadi & Alkes --}}
                                    <div class="pb-5" data-step-id="cat-1">
                                        @include('admin.registrasi.category.1-baku')
                                    </div>
                                    {{-- End --}}

                                    {{-- Varia Teknik dan Umum, Reagen, Barang Investasi --}}
                                    <div class="pb-5" data-step-id="cat-2">
                                        @include('admin.registrasi.category.2-varia')
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Transporter, Forwarder & PPJK --}}
                                    <div class="pb-5" data-step-id="cat-3">
                                        @include('admin.registrasi.category.3-trans')
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Kontraktor, Perbaikan & Pemeliharaan --}}
                                    <div class="pb-5" data-step-id="cat-4">
                                        @include('admin.registrasi.category.4-kontraktor')
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Pengujian Laboratorium, Kalibrasi, Radiasi & Sertifikasi --}}
                                    <div class="pb-5" data-step-id="cat-5">
                                        @include('admin.registrasi.category.5-pengujian')
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Facility Service, Sewa, Security, Katering & MCU --}}
                                    <div class="pb-5" data-step-id="cat-6">
                                        @include('admin.registrasi.category.6-facility')
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Pelatihan,Konsultan, Notaris & Alih Daya Tenaga Kerja --}}
                                    <div class="pb-5" data-step-id="cat-7">
                                        @include('admin.registrasi.category.7-pelatihan')
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Agency Advertising --}}
                                    <div class="pb-5" data-step-id="cat-8">
                                        @include('admin.registrasi.category.8-agency')
                                    </div>
                                    {{-- End --}}

                                    {{-- Review --}}
                                    {{-- <div class="pb-5" data-step-id="review">
                                        @include('admin.registrasi.review')
                                    </div> --}}
                                    {{-- End Review --}}

                                    {{-- Wizard Actions --}}
                                    <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                        <div class="mr-2">
                                            <button type="button" id="btnPrev"
                                                class="btn btn-light-primary font-weight-bold text-uppercase px-9 py-4"
                                                style="display:none">
                                                <i class="ki ki-arrow-back mr-1"></i> {{ __('previous') }}
                                            </button>
                                        </div>
                                        <div>
                                            @if (!$isReadOnly)
                                                <button type="button" id="btnSaveDraft"
                                                    class="btn btn-light-primary font-weight-bold text-uppercase px-9 py-4 mr-3">
                                                    <i class="flaticon2-fax mr-1"></i> {{ __('save_draft') }}
                                                </button>
                                            @endif
                                            <button type="button" id="btnNext"
                                                class="btn btn-primary font-weight-bold text-uppercase px-9 py-4">
                                                {{ __('continue') }}
                                                <i class="fas fa-arrow-up icon-md" style="transform: rotate(45deg);"></i>
                                            </button>
                                            @if (!$isReadOnly)
                                                <button type="submit" id="btnSubmit"
                                                    class="btn btn-primary font-weight-bold text-uppercase px-9 py-4"
                                                    style="display:none">
                                                    <i class="flaticon2-check-mark mr-1"></i> {{ __('submit') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- End Wizard Actions --}}

                                </form>

                            </div>
                        </div>
                    </div>
                </div>
                {{-- End Wizard Body --}}
            </div>
            {{-- End Wizard --}}
        </div>
    </div>

    <x-document-preview />
@endsection


@push('scripts')
    <script src="{{ asset('js/dashboard/wizard-nav.js') }}"></script>
    <script src="{{ asset('js/dashboard/vendor-form.js') }}"></script>
    <script>
        $(document).ready(function() {

            function showValidationErrors(errors) {
                // Bersihkan error lama
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback.dynamic-error').remove();

                var $firstError = null;
                var errorCount = 0;
                // group error per step: { 'step-2': 2, 'cat-3': 1, ... }
                var perStep = {};

                $.each(errors, function(key, value) {
                    // Laravel mereturn dot notation untuk array: 'products.1.manufaktur'
                    // Tapi di HTML atribut name menggunakan bracket: 'products[1][manufaktur]'
                    var arrayName = key;
                    if (key.indexOf('.') !== -1) {
                        var parts = key.split('.');
                        arrayName = parts[0];
                        for (var i = 1; i < parts.length; i++) {
                            arrayName += '[' + parts[i] + ']';
                        }
                    }

                    // Cari berdasarkan dot notation atau bracket notation
                    var $inputs = $('[name="' + key + '"], [name="' + arrayName + '"]');
                    
                    // Khusus untuk ajax-file-upload dan summernote atau custom component
                    // terkadang kita harus menarget fieldnya saja jika input aslinya dihidden
                    if ($inputs.length === 0) {
                        $inputs = $('[data-field="' + key + '"], [data-field="' + arrayName + '"]');
                    }

                    if ($inputs.length === 0) return;

                    // Deteksi radio/checkbox group (name sama, type radio/checkbox)
                    var isGroup = $inputs.length > 1 &&
                        $.inArray($inputs.attr('type'), ['radio', 'checkbox']) > -1;

                    $inputs.addClass('is-invalid');

                    if (isGroup) {
                        // Cari container pembungkus group (label / .radio-list / .form-group)
                        // lalu sisipkan pesan di BAWAH seluruh group, bukan di sela radio pertama
                        var $container = $inputs.first().closest(
                            '.radio-list, .checkbox-list, .form-group, .form-item, label');
                        if ($container.length === 0) $container = $inputs.first().parent();
                        $container.append(
                            '<div class="invalid-feedback dynamic-error d-block w-100 mt-2">' +
                            value[0] + '</div>'
                        );
                    } else {
                        var $input = $inputs.first();
                        var errorHtml = '<div class="invalid-feedback dynamic-error d-block" style="font-size: 0.85rem; line-height: 1.2; margin-top: 4px; word-break: break-word;">' + value[0] + '</div>';

                        if ($input.hasClass('custom-file-input')) {
                            // File input: letakkan di luar .custom-file
                            $input.closest('.custom-file').after(errorHtml);
                        } else if ($input.hasClass('selectpicker') || $input.next().hasClass('bootstrap-select')) {
                            // Selectpicker: letakkan setelah div .bootstrap-select
                            var $bsSelect = $input.next('.bootstrap-select');
                            if ($bsSelect.length) {
                                $bsSelect.after(errorHtml);
                            } else {
                                $input.after(errorHtml);
                            }
                        } else if ($input.closest('.input-group').length) {
                            $input.closest('.input-group').after(errorHtml);
                        } else {
                            // Default: tepat di bawah input
                            $input.after(errorHtml);
                        }
                    }

                    var $step = $inputs.first().closest('div[data-step-id]');
                    var stepId = $step.length ? $step.attr('data-step-id') : 'unknown';
                    perStep[stepId] = (perStep[stepId] || 0) + 1;

                    errorCount++;
                    if (!$firstError) $firstError = $inputs.first();
                });

                if (errorCount === 0) {
                    var genericErrors = '';
                    $.each(errors, function(key, value) {
                        genericErrors += '<li>' + value[0] + '</li>';
                    });
                    if (genericErrors) {
                        Swal.fire({
                            icon: 'error',
                            title: @json(__('validation_failed')),
                            html: '<ul class="text-left mb-0 text-danger" style="font-size:0.9rem;">' + genericErrors + '</ul>'
                        });
                    }
                    return;
                }

                // Auto-scroll ke error pertama supaya user langsung tahu letaknya
                if ($firstError && $firstError.length) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 100
                    }, 300);
                    $firstError.trigger('focus');
                }

                // Label step yang ramah dibaca user
                var stepLabels = {
                    'step-1': @json(__('category')),
                    'step-2': @json(__('general_info')),
                    'step-3': @json(__('payment')),
                    'step-4': @json(__('commitment')),
                    'step-5': @json(__('other_info')),
                    'step-6': @json(__('local_vendor_special')),
                    'step-7': @json(__('product')),
                    'step-8': @json(__('document')),
                    'cat-1': @json(__('raw_material')),
                    'cat-2': @json(__('technical_varia')),
                    'cat-3': @json(__('transporter')),
                    'cat-4': @json(__('contractor')),
                    'cat-5': @json(__('testing')),
                    'cat-6': @json(__('facility')),
                    'cat-7': @json(__('training')),
                    'cat-8': @json(__('advertising'))
                };

                // Ringkasan: nama step + jumlah error (bukan daftar pesan lengkap)
                var fieldsNeedCorrectionText = @json(__('fields_need_correction'));
                var errorsFoundText = @json(__('errors_found_in_steps'));

                var list = '<ul class="text-left mb-0">';
                Object.keys(perStep).forEach(function(stepId) {
                    var label = stepLabels[stepId] || stepId;
                    var itemText = fieldsNeedCorrectionText.replace(':count', perStep[stepId]);
                    list += '<li><b>' + label + '</b> &mdash; ' + itemText + '</li>';
                });
                list += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: @json(__('validation_failed')),
                    html: '<p class="mb-2">' + errorsFoundText.replace(':count', errorCount) + '</p>' + list
                });
            }

            function showSubmitLoading(title, text) {
                Swal.fire({
                    title: title || @json(__('processing_application')),
                    text: text || @json(__('processing_please_wait')),
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    onOpen: function() {
                        Swal.showLoading();
                    },
                    didOpen: function() {
                        Swal.showLoading();
                    }
                });
            }

            function syncCategoryDraftBeforeContinue(actionType, btnElement) {
                var selectedCategories = $('.category-checkbox:checked');

                if (selectedCategories.length === 0) {
                    Swal.fire(@json(__('select_category')), @json(__('select_at_least_one_category')), 'warning');
                    return;
                }

                var originalBtnHtml = btnElement.data('original-html') || btnElement.html();
                btnElement.data('original-html', originalBtnHtml);
                var formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                selectedCategories.each(function() {
                    formData.append('categories[]', $(this).val());
                });

                if (actionType === 'submit') {
                    showSubmitLoading(@json(__('preparing_application')), @json(__('preparing_application_desc')));
                    btnElement.attr('disabled', true);
                } else {
                    btnElement.attr('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm"></span> ' + @json(__('preparing_draft')));
                }

                $.ajax({
                    url: "{{ route('registrasi.save-draft') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.application_id) {
                            $('#application_id').val(res.application_id);
                            if (actionType !== 'submit') {
                                btnElement.attr('disabled', false).html(originalBtnHtml);
                            }
                            sendForm(actionType, btnElement, true);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showValidationErrors(xhr.responseJSON.errors);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: @json(__('failed')),
                                text: xhr.responseJSON?.message ||
                                    @json(__('failed_create_category_draft'))
                            });
                        }
                    },
                    complete: function() {
                        if (!$('#application_id').val() || actionType !== 'submit') {
                            btnElement.attr('disabled', false).html(originalBtnHtml);
                        }
                    }
                });
            }

            function finalizeSubmit(applicationId, btnElement) {
                if (!applicationId) {
                    Swal.fire(@json(__('error')), @json(__('draft_number_not_found')), 'error');
                    btnElement.attr('disabled', false).html(btnElement.data('original-html') || btnElement.html());
                    return;
                }

                showSubmitLoading(@json(__('submitting_application')), @json(__('submitting_application_desc')));
                btnElement.attr('disabled', true);

                $.ajax({
                    url: "{{ route('registrasi.submit') }}",
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        application_id: applicationId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: @json(__('application_submitted')),
                            text: res.message,
                        }).then(() => {
                            window.location.href = res.redirect;
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showValidationErrors(xhr.responseJSON.errors);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: @json(__('failed')),
                                text: xhr.responseJSON?.message || @json(__('failed_submit_application'))
                            });
                        }
                    },
                    complete: function() {
                        btnElement.attr('disabled', false).html(btnElement.data('original-html') || btnElement.html());
                    }
                });
            }

            function saveBeforeFinalSubmit(formData, btnElement) {
                $.ajax({
                    url: "{{ route('registrasi.save-umum') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).then(function(res) {
                    if (res.application_id) {
                        $('#application_id').val(res.application_id);
                        formData.set('application_id', res.application_id);
                    }

                    return $.ajax({
                        url: "{{ route('registrasi.save-specific') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                }).then(function(res) {
                    finalizeSubmit(res.application_id || $('#application_id').val(), btnElement);
                }).fail(function(xhr) {
                    if (xhr.status === 422) {
                        showValidationErrors(xhr.responseJSON.errors);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: @json(__('failed')),
                            text: xhr.responseJSON?.message ||
                                @json(__('failed_save_before_submit'))
                        });
                    }
                    btnElement.attr('disabled', false).html(btnElement.data('original-html') || btnElement.html());
                });
            }

            function sendForm(actionType, btnElement, skipCategorySync = false) {
                // 1. Deteksi Step Aktif
                var currentStepId = $('div[data-step-id]:visible').attr('data-step-id');
                var targetUrl = "";

                // 2. Mapping URL secara dinamis
                if (currentStepId === 'step-1') {
                    // Khusus Step 1: Inisiasi Draft & Kategori
                    targetUrl = "{{ route('registrasi.save-draft') }}";
                } else if (currentStepId && currentStepId.startsWith('step-')) {
                    // Mencakup Step-2 sampai Step-8 (Sesuai rute umum Bos)
                    targetUrl = "{{ route('registrasi.save-umum') }}";
                } else if (currentStepId && currentStepId.startsWith('cat-')) {
                    // Mencakup cat-1 sampai cat-8 (Rute Spesifik yang baru kita buat)
                    targetUrl = "{{ route('registrasi.save-specific') }}";
                }

                // Validasi jika step tidak terdeteksi (safety catch)
                if (!targetUrl) {
                    Swal.fire(@json(__('error')), @json(__('form_step_unrecognized')), 'error');
                    return;
                }

                var appId = $('#application_id').val();
                if (currentStepId !== 'step-1' && !skipCategorySync) {
                    syncCategoryDraftBeforeContinue(actionType, btnElement);
                    return;
                }

                if (typeof window.prepareProductRowsForSubmit === 'function') {
                    window.prepareProductRowsForSubmit();
                }

                // 3. Siapkan FormData (Support File Upload)
                var formData = new FormData($('#kt_form')[0]);
                if (typeof window.restoreProductRowsAfterSubmit === 'function') {
                    window.restoreProductRowsAfterSubmit();
                }
                formData.append('action', actionType); // 'draft' atau 'submit'

                if (appId) {
                    formData.append('application_id', appId);
                }

                // 4. Animasi Loading
                var originalBtnHtml = btnElement.html();
                btnElement.data('original-html', originalBtnHtml);

                if (actionType === 'submit') {
                    showSubmitLoading(@json(__('submitting_application')), @json(__('submitting_application_desc')));
                    btnElement.attr('disabled', true);
                    saveBeforeFinalSubmit(formData, btnElement);
                    return;
                }

                btnElement.attr('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> ' + @json(__('loading')));

                // 5. Eksekusi AJAX
                $.ajax({
                    url: targetUrl,
                    method: 'POST',
                    data: formData,
                    processData: false, // Penting untuk FormData
                    contentType: false, // Penting untuk FormData
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.application_id) {
                            $('#application_id').val(res.application_id);
                        }

                        Swal.fire({
                            icon: 'success',
                            title: @json(__('success')),
                            text: res.message,
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showValidationErrors(xhr.responseJSON.errors);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: @json(__('failed')),
                                text: xhr.responseJSON.message || @json(__('system_error'))
                            });
                        }
                    },
                    complete: function() {
                        btnElement.attr('disabled', false).html(originalBtnHtml);
                    }
                });
            }

            // Prevent default form submit
            $('#kt_form').on('submit', function(e) {
                e.preventDefault();
            });

            // Bind ke tombol
            // Gunakan class atau ID, pastikan tombol di Blade Bos sesuai
            $('#btnSaveDraft').on('click', function() {
                sendForm('draft', $(this));
            });

            $('#btnSubmit').on('click', function(e) {
                e.preventDefault();
                sendForm('submit', $(this));
            });

            // Hapus error secara otomatis saat user mulai memperbaiki input.
            // Untuk radio/checkbox group, hapus class di semua sibling + pesan terkait.
            $(document).on('input change', '.is-invalid', function() {
                var $changed = $(this);
                var name = $changed.attr('name');
                $('[name="' + name + '"]').removeClass('is-invalid');
                $changed.closest('div, td, label').find('.invalid-feedback.dynamic-error').remove();
                
                // Fallback untuk custom-file dan selectpicker yang meletakkan error di luar komponen
                if ($changed.hasClass('custom-file-input')) {
                    $changed.closest('.custom-file').next('.invalid-feedback.dynamic-error').remove();
                } else if ($changed.hasClass('selectpicker')) {
                    $changed.next('.bootstrap-select').next('.invalid-feedback.dynamic-error').remove();
                } else if ($changed.closest('.input-group').length) {
                    $changed.closest('.input-group').next('.invalid-feedback.dynamic-error').remove();
                }
                
                // fallback: kalau masih ada (struktur DOM unik), hapus satu pesan setelah input pertama
                $('[name="' + name + '"]').first().next('.invalid-feedback.dynamic-error').remove();
            });
        });
    </script>
@endpush
