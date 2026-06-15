@extends('layouts.app', ['title' => 'Vendor Registration'])

@push('style')
    <link href="{{ asset('css/wizard-4.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('breadcrumb', 'Registrasi')
@section('step', 'Pra Kualifikasi')
@section('page_title', 'Vendor Registration')
@section('page_desc',
    'Lengkapi profil perusahaan Anda untuk memulai proses kualifikasi vendor. Fase ini memastikan
    kepatuhan terhadap standar perusahaan kami.')

@section('content')

    @if (($applicationStatus ?? null) === 'submitted')
        <div class="alert alert-custom alert-light-primary fade show mb-5" role="alert">
            <div class="alert-icon"><i class="flaticon2-check-mark text-primary icon-md"></i></div>
            <div class="alert-text">
                <span class="font-weight-bold text-dark-75">Permohonan Anda sudah dikirim.</span>
                Form ini tampil dalam mode read-only sambil menunggu verifikasi.
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
                <span class="font-weight-bold text-dark-75">Permohonan perlu revisi.</span>
                Silakan perbaiki data sesuai catatan pengadaan lalu submit ulang.
                @if (!empty($draft['admin_note'] ?? null))
                    <div class="mt-2 text-dark-75">
                        <span class="font-weight-bold">Catatan:</span> {{ $draft['admin_note'] }}
                    </div>
                @endif
                @if (!empty($draft['revision_notes'] ?? null))
                    <div class="mt-2 text-dark-75">
                        @foreach ($draft['revision_notes'] as $revision)
                            <div>
                                <span class="font-weight-bold">{{ $revision['label'] ?? ($revision['field'] ?? 'Umum') }}:</span>
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
                <span class="font-weight-bold text-dark-75">Anda memiliki permohonan yang belum selesai.</span>
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
                                                <i class="ki ki-arrow-back mr-1"></i> Sebelumnya
                                            </button>
                                        </div>
                                        <div>
                                            @if (!$isReadOnly)
                                                <button type="button" id="btnSaveDraft"
                                                    class="btn btn-light-primary font-weight-bold text-uppercase px-9 py-4 mr-3">
                                                    <i class="flaticon2-fax mr-1"></i> Simpan Draft
                                                </button>
                                            @endif
                                            <button type="button" id="btnNext"
                                                class="btn btn-primary font-weight-bold text-uppercase px-9 py-4">
                                                Lanjutkan
                                                <i class="fas fa-arrow-up icon-md" style="transform: rotate(45deg);"></i>
                                            </button>
                                            @if (!$isReadOnly)
                                                <button type="submit" id="btnSubmit"
                                                    class="btn btn-primary font-weight-bold text-uppercase px-9 py-4"
                                                    style="display:none">
                                                    <i class="flaticon2-check-mark mr-1"></i> Submit
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
                let msg = "<ul>";
                $.each(errors, function(key, value) {
                    msg += "<li class='text-left'>" + value[0] + "</li>";
                    $('[name="' + key + '"]').addClass('is-invalid');
                });
                msg += "</ul>";

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: msg
                });
            }

            function syncCategoryDraftBeforeContinue(actionType, btnElement) {
                var selectedCategories = $('.category-checkbox:checked');

                if (selectedCategories.length === 0) {
                    Swal.fire('Pilih Kategori', 'Silakan pilih minimal satu kategori terlebih dahulu.', 'warning');
                    return;
                }

                var originalBtnHtml = btnElement.data('original-html') || btnElement.html();
                var formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                selectedCategories.each(function() {
                    formData.append('categories[]', $(this).val());
                });

                btnElement.attr('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Menyiapkan draft...');

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
                            btnElement.attr('disabled', false).html(originalBtnHtml);
                            sendForm(actionType, btnElement, true);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showValidationErrors(xhr.responseJSON.errors);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message || 'Gagal membuat draft kategori.'
                            });
                        }
                    },
                    complete: function() {
                        if (!$('#application_id').val()) {
                            btnElement.attr('disabled', false).html(originalBtnHtml);
                        }
                    }
                });
            }

            function finalizeSubmit(applicationId, btnElement) {
                if (!applicationId) {
                    Swal.fire('Error', 'Nomor draft permohonan tidak ditemukan.', 'error');
                    return;
                }

                var originalBtnHtml = btnElement.data('original-html') || btnElement.html();
                btnElement.attr('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Mengirim permohonan...');

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
                            title: 'Permohonan Terkirim',
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
                                title: 'Gagal',
                                text: xhr.responseJSON?.message || 'Gagal mengirim permohonan.'
                            });
                        }
                    },
                    complete: function() {
                        btnElement.attr('disabled', false).html(originalBtnHtml);
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
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Gagal menyimpan revisi sebelum submit.'
                        });
                    }
                    btnElement.attr('disabled', false).html(btnElement.data('original-html'));
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
                    Swal.fire('Error', 'Sistem tidak mengenali posisi form saat ini.', 'error');
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
                btnElement.attr('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Loading...');

                if (actionType === 'submit') {
                    saveBeforeFinalSubmit(formData, btnElement);
                    return;
                }

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
                            title: 'Berhasil',
                            text: res.message,
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showValidationErrors(xhr.responseJSON.errors);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON.message || 'Terjadi kesalahan sistem.'
                            });
                        }
                    },
                    complete: function() {
                        btnElement.attr('disabled', false).html(originalBtnHtml);
                    }
                });
            }

            // Bind ke tombol
            // Gunakan class atau ID, pastikan tombol di Blade Bos sesuai
            $('#btnSaveDraft').on('click', function() {
                sendForm('draft', $(this));
            });

            $('#btnSubmit').on('click', function(e) {
                e.preventDefault();
                sendForm('submit', $(this));
            });
        });
    </script>

@endpush
