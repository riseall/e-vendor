@extends('user.layout.home', [
    'title' => 'Panduan Pengguna',
    'subtitle' => 'Panduan praktis pendaftaran akun, kelengkapan profil, dan penggunaan aplikasi E-Vendor PT Phapros Tbk.'
])

@section('content')
    {{-- Section Title --}}
    <div class="row justify-content-center">
        <div class="col-12 text-center">
            <div class="section-title mb-4 pb-2">
                <span class="badge badge-pill badge-primary mb-2">Pusat Panduan &amp; Bantuan</span>
                <h4 class="title mb-3">Panduan Penggunaan E-Vendor</h4>
                <p class="text-muted para-desc mx-auto mb-0">
                    Pelajari tata cara pendaftaran akun, pengunggahan berkas legalitas perusahaan, serta kemudahan pengoperasian aplikasi E-Vendor PT Phapros Tbk.
                </p>
            </div>
        </div>
    </div>

    {{-- Two Main Media Guides: Video & Manual Book --}}
    <div class="row align-items-stretch mt-4">
        {{-- Video Guide --}}
        <div class="col-lg-7 col-md-6 col-12">
            <div class="card border-0 rounded shadow h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="mb-3">
                        <span class="badge badge-pill badge-primary mb-2"><i class="fas fa-play-circle mr-1"></i> Panduan Video</span>
                        <h5 class="title font-weight-bold mb-2">Video Tutorial Sistem</h5>
                        <p class="text-muted mb-0">
                            Simak alur kerja pendaftaran rekanan dan pengoperasian dashboard E-Vendor secara visual melalui video panduan berikut.
                        </p>
                    </div>
                    <div class="embed-responsive embed-responsive-16by9 rounded overflow-hidden mt-auto shadow-sm">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/TA4CC9v4DUE" title="Video Panduan E-Vendor" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>

        {{-- PDF Manual Book Guide --}}
        <div class="col-lg-5 col-md-6 col-12 mt-4 mt-md-0">
            <div class="card border-0 rounded shadow h-100 text-center">
                <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                    <div class="features feature-primary feature-clean mb-3">
                        <div class="icons text-center mx-auto">
                            <i class="fas fa-book-open rounded h3 mb-0"></i>
                        </div>
                    </div>
                    <h5 class="title font-weight-bold mb-2">Buku Manual Pengguna</h5>
                    <p class="text-muted mb-3">
                        Dokumen panduan resmi berformat PDF yang memuat petunjuk langkah demi langkah pendaftaran mandiri, kelengkapan berkas legalitas, dan panduan fitur aplikasi.
                    </p>
                    <div class="d-flex align-items-center justify-content-center mb-4 text-muted font-size-sm">
                        <span class="mr-3"><i class="fas fa-file-pdf text-danger mr-1"></i> Format PDF</span>
                        <span><i class="fas fa-check-circle text-success mr-1"></i> Panduan Lengkap</span>
                    </div>
                    <div class="w-100 mt-auto" style="max-width: 280px;">
                        <a href="{{ asset('files/manual-book.pdf') }}" target="_blank" class="btn btn-primary btn-block font-weight-bold mb-2">
                            <i class="fas fa-eye mr-2"></i> Baca Online
                        </a>
                        <a href="{{ asset('files/manual-book.pdf') }}" download class="btn btn-outline-primary btn-block font-weight-bold">
                            <i class="fas fa-download mr-2"></i> Unduh PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
