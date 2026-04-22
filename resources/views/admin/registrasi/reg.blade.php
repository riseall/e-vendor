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

    @if (isset($hasDraft) && $hasDraft)
        <div class="alert alert-custom alert-light-warning fade show mb-5" role="alert">
            <div class="alert-icon"><i class="fas fa-exclamation-triangle text-warning icon-md"></i></div>
            <div class="alert-text">
                <span class="font-weight-bold">Anda memiliki permohonan yang belum selesai.</span>
                {{-- Data tersimpan hingga step <strong>{{ $draftStep ?? 1 }} --}}</strong>
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert">
                    <span><i class="ki ki-close"></i></span>
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
                                    method="POST" enctype="multipart/form-data">
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
                                    </div>
                                    {{-- End --}}

                                    {{-- Varia Teknik dan Umum, Reagen, Barang Investasi --}}
                                    <div class="pb-5" data-step-id="cat-2">
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Transporter, Forwarder & PPJK --}}
                                    <div class="pb-5" data-step-id="cat-3">
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Kontraktor, Perbaikan & Pemeliharaan --}}
                                    <div class="pb-5" data-step-id="cat-4">
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Pengujian Laboratorium, Kalibrasi, Radiasi & Sertifikasi --}}
                                    <div class="pb-5" data-step-id="cat-5">
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Facility Service, Sewa, Security, Katering & MCU --}}
                                    <div class="pb-5" data-step-id="cat-6">
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Pelatihan,Konsultan, Notaris & Alih Daya Tenaga Kerja --}}
                                    <div class="pb-5" data-step-id="cat-7">
                                    </div>
                                    {{-- End --}}

                                    {{-- Jasa Agency Advertising --}}
                                    <div class="pb-5" data-step-id="cat-8">
                                    </div>
                                    {{-- End --}}

                                    {{-- Review --}}
                                    <div class="pb-5" data-step-id="review">
                                        <h4 class="font-weight-bold text-dark mb-8">Review & Kirim Permohonan</h4>
                                        <p class="text-muted mb-5">Periksa kembali data Anda sebelum mengirim.</p>
                                        <div class="border-bottom mb-5 pb-3">
                                            <p class="font-weight-bold text-primary mb-3">Kategori Terpilih</p>
                                            <div id="reviewCategories"></div>
                                        </div>
                                        <div class="alert alert-custom alert-light-info fade show">
                                            <div class="alert-icon"><i class="flaticon2-bell text-info"></i></div>
                                            <div class="alert-text">
                                                Dengan menekan <strong>Kirim Permohonan</strong>, Anda menyatakan
                                                seluruh data dan dokumen yang diberikan adalah
                                                <strong>benar dan dapat dipertanggungjawabkan</strong>.
                                            </div>
                                        </div>
                                    </div>
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
                                            <button type="button" id="btnSaveDraft"
                                                class="btn btn-light-primary font-weight-bold text-uppercase px-9 py-4 mr-3">
                                                <i class="flaticon2-fax mr-1"></i> Simpan Draft
                                            </button>
                                            <button type="button" id="btnNext"
                                                class="btn btn-primary font-weight-bold text-uppercase px-9 py-4">
                                                Lanjutkan
                                                <i class="fas fa-arrow-up icon-md" style="transform: rotate(45deg);"></i>
                                            </button>
                                            <button type="submit" id="btnSubmit"
                                                class="btn btn-primary font-weight-bold text-uppercase px-9 py-4"
                                                style="display:none">
                                                <i class="flaticon2-check-mark mr-1"></i> Submit
                                            </button>
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

    <div class="modal fade" id="modalPreviewDoc" tabindex="-1" role="dialog" aria-labelledby="modalPreviewDocLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPreviewDocLabel">Preview Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body p-0 bg-light" style="height: 80vh;">
                    <div id="previewContainer" class="h-100 d-flex align-items-center justify-content-center">
                        <div class="spinner spinner-primary spinner-lg"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('js/dashboard/wizard-nav.js') }}"></script>
    <script>
        $(document).ready(function() {

            function sendForm(actionType, btnElement) {
                // 1. Deteksi Step Aktif berdasarkan elemen yang sedang VISIBLE
                // Mencari div yang memiliki atribut data-step-id dan sedang tampil
                var currentStepId = $('div[data-step-id]:visible').attr('data-step-id');

                var targetUrl = "";

                // 2. Tentukan URL berdasarkan Step ID yang terdeteksi
                if (currentStepId === 'step-1') {
                    targetUrl = "{{ route('registrasi.save-draft') }}";
                } else if (currentStepId === 'step-2') {
                    targetUrl = "{{ route('registrasi.save-umum') }}";
                } else {
                    // Default atau untuk step selanjutnya
                    targetUrl = "{{ route('registrasi.save-umum') }}";
                }

                // 3. Siapkan FormData
                var formData = new FormData($('#kt_form')[0]);
                formData.append('action', actionType);

                // Ambil application_id dari hidden input jika ada
                var appId = $('#application_id').val();
                if (appId) {
                    formData.append('application_id', appId);
                }

                // 4. Animasi Loading
                var originalBtnHtml = btnElement.html();
                btnElement.attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                // 5. Eksekusi AJAX
                $.ajax({
                    url: targetUrl,
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
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                        }).then(() => {
                            if (actionType === 'submit') {
                                // Jika kamu punya fungsi manual untuk pindah tab/step visual:
                                // moveToNextStep(); 
                            }
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let msg = Object.values(errors).map(e => e[0]).join("<br>");
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: msg
                            });
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
            $('#btnSaveDraft').on('click', function() {
                sendForm('draft', $(this));
            });

            $('#btnSubmitUmum').on('click', function() {
                sendForm('submit', $(this));
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '.btn-preview-doc', function(e) {
                e.preventDefault();

                let url = $(this).data('url');
                let title = $(this).data('title');
                let container = $('#previewContainer');
                let extension = url.split('.').pop().toLowerCase();

                // Update Judul & Link Download
                $('#modalPreviewDocLabel').text(title);
                $('#btnDownloadDoc').attr('href', url);

                // Reset Container
                container.html('<div class="spinner spinner-primary spinner-lg"></div>');

                // Tampilkan Modal
                $('#modalPreviewDoc').modal('show');

                // Logic Preview berdasarkan tipe file
                setTimeout(function() {
                    if (extension === 'pdf') {
                        container.html(
                            `<iframe src="${url}" frameborder="0" class="w-100 h-100"></iframe>`
                        );
                    } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
                        container.html(
                            `<img src="${url}" class="img-fluid shadow-sm rounded" style="max-height: 95%; object-fit: contain;">`
                        );
                    } else {
                        container.html(`
                        <div class="text-center">
                            <i class="flaticon-file-2 display-1 text-muted"></i>
                            <p class="mt-4">Format file tidak mendukung preview langsung.<br>Silakan klik tombol download di bawah.</p>
                        </div>
                    `);
                    }
                }, 500);
            });
        });
    </script>
@endpush
