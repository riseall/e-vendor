@extends('layouts.app', ['title' => 'Profil Registrasi Vendor'])

@push('style')
    <link href="{{ asset('css/wizard-4.css') }}" rel="stylesheet" type="text/css" />
    @include('admin.registrasi.partials.profile.styles')
@endpush

@section('breadcrumb', 'Registrasi')
@section('step', 'Profil Vendor')
@section('page_title', 'Profil Registrasi Vendor')
@section('page_desc', 'Data registrasi dan perkembangan verifikasi permohonan vendor.')

@section('content')
    <div class="vendor-profile-header">
        <div>
            <div class="vendor-profile-eyebrow">Nomor Permohonan</div>
            <div class="vendor-profile-number">{{ $application->application_number ?: '-' }}</div>
            <div class="vendor-profile-company">
                {{ optional($application->general)->nama_perusahaan ?: auth()->user()->name }}
            </div>
        </div>

        <div class="vendor-profile-status">
            <span class="label label-{{ $statusPresentation['class'] }} label-inline font-weight-bold">
                {{ $statusPresentation['label'] }}
            </span>
            <div class="text-muted font-size-sm mt-2">{{ $statusPresentation['description'] }}</div>
            <div class="d-flex align-items-center justify-content-end flex-wrap mt-3" style="gap: 8px;">
                <a href="{{ route('registrasi.tracking', $application->application_number) }}"
                    class="btn btn-light-primary btn-sm font-weight-bold">
                    <i class="flaticon2-search-1"></i> Lihat Tracking
                </a>

                @if ($application->status === \App\Models\VendorApplication::STATUS_APPROVED)
                    @if (!empty($isEditMode))
                        <a href="{{ route('registrasi.index') }}" class="btn btn-danger btn-sm font-weight-bold">
                            <i class="fas fa-angle-left"></i> Kembali
                        </a>
                    @else
                        <a href="{{ route('registrasi.index', ['edit' => 1]) }}"
                            class="btn btn-warning btn-sm font-weight-bold">
                            <i class="fas fa-edit"></i> Ubah Data
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>

    @if (
        $application->status === \App\Models\VendorApplication::STATUS_APPROVED &&
            $application->requalification_reason &&
            !$application->qualification &&
            empty($isEditMode))
        @php
            $reasonText =
                \App\Models\VendorApplication::REASON_LABELS[$application->requalification_reason] ??
                $application->requalification_reason;
        @endphp
        <div class="alert alert-custom alert-light-warning mb-6 shadow-sm" role="alert"
            style="border-left: 4px solid #f59e0b;">
            <div class="alert-icon"><i class="fas fa-exclamation-triangle text-warning" style="font-size:1.5rem;"></i></div>
            <div class="alert-text">
                <div class="font-weight-bolder text-dark font-size-h6 mb-1">
                    <i class="fas fa-redo text-warning mr-1"></i> Permintaan Pembaharuan Data
                </div>
                <div class="text-dark-75 mb-2">
                    Tim Phapros telah meminta Anda untuk melakukan pembaharuan data dengan alasan:
                    <strong class="text-warning font-weight-bold">{{ $reasonText }}</strong>.
                </div>
                <a href="{{ route('registrasi.index', ['edit' => 1]) }}"
                    class="btn btn-warning font-weight-bold btn-sm px-4">
                    <i class="fas fa-edit mr-1"></i> Mulai Perbarui Data
                </a>
            </div>
        </div>
    @endif

    @if (!empty($isEditMode))
        <div class="alert alert-custom alert-light-primary mb-6" role="alert">
            <div class="alert-icon"><i class="fas fa-edit text-primary"></i></div>
            <div class="alert-text">
                <div class="font-weight-bold text-dark mb-1">Mode Edit Profil Aktif</div>
                Seluruh data profil Anda terbuka untuk diubah. Silakan perbarui data atau dokumen yang
                diperlukan, lalu klik <strong>"Simpan Perubahan"</strong> di bagian bawah. Jika tidak ingin mengubah
                data, klik <strong>"Kembali"</strong> di bagian atas.
            </div>
        </div>
    @endif

    @if ($isRevisionMode)
        <div class="alert alert-custom alert-light-warning mb-6" role="alert">
            <div class="alert-icon"><i class="flaticon-warning text-warning"></i></div>
            <div class="alert-text">
                <div class="font-weight-bold text-dark mb-1">Permohonan perlu diperbaiki</div>
                Terdapat <strong>{{ count($revisionNotes) }} catatan revisi</strong>. Field terkait sudah ditandai
                dengan warna merah dan kuning.
                @if (!empty($draft['admin_note']))
                    <div class="mt-2"><strong>Catatan umum:</strong> {{ $draft['admin_note'] }}</div>
                @endif
                <button type="button" id="btn-first-revision" class="btn btn-sm btn-warning font-weight-bold mt-3">
                    <i class="flaticon2-arrow-down"></i> Lihat Revisi Pertama
                </button>
            </div>
        </div>
    @endif

    <div class="vendor-profile-layout">
        <aside class="vendor-profile-nav">
            <div class="vendor-profile-nav-title">Data Registrasi</div>
            <a href="#profil-perusahaan">Profil Perusahaan</a>
            <a href="#pembayaran">Pembayaran</a>
            <a href="#komitmen">Komitmen & Sertifikasi</a>
            <a href="#informasi-lain">Informasi Lain</a>
            <a href="#vendor-lokal">Data Vendor Lokal</a>
            <a href="#produk">Daftar Produk</a>
            <a href="#dokumen">Dokumen</a>
            @foreach ($selectedCategorySections as $categorySection)
                <a href="#kategori-{{ $categorySection['id'] }}">
                    {{ \Illuminate\Support\Str::limit($categorySection['label'], 32) }}
                </a>
            @endforeach
        </aside>

        <main class="vendor-profile-content">
            <div class="vendor-profile-categories">
                <span class="font-weight-bold text-dark mr-2">Kategori terdaftar:</span>
                @foreach ($selectedCategoryLabels as $categoryLabel)
                    <span class="label label-light-primary label-inline mb-1">{{ $categoryLabel }}</span>
                @endforeach
            </div>

            <form id="kt_form" method="POST" enctype="multipart/form-data"
                data-max-file-kb="{{ \App\Services\VendorUploadPolicy::MAX_FILE_KB }}">
                @csrf
                <input type="hidden" name="application_id" id="application_id" value="{{ $applicationId }}">

                <section id="profil-perusahaan" class="vendor-profile-section">
                    @include('admin.registrasi.steps.2-umum')
                </section>

                <section id="pembayaran" class="vendor-profile-section">
                    @include('admin.registrasi.steps.3-pembayaran')
                </section>

                <section id="komitmen" class="vendor-profile-section">
                    @include('admin.registrasi.steps.4-komitmen')
                </section>

                <section id="informasi-lain" class="vendor-profile-section">
                    @include('admin.registrasi.steps.5-info')
                </section>

                <section id="vendor-lokal" class="vendor-profile-section">
                    @include('admin.registrasi.steps.6-lokal')
                </section>

                <section id="produk" class="vendor-profile-section">
                    @include('admin.registrasi.steps.7-produk')
                </section>

                <section id="dokumen" class="vendor-profile-section">
                    @include('admin.registrasi.steps.8-dokumen')
                </section>

                @foreach ($selectedCategorySections as $categorySection)
                    <section id="kategori-{{ $categorySection['id'] }}" class="vendor-profile-section">
                        @include($categorySection['view'])
                    </section>
                @endforeach

                @if ($isRevisionMode)
                    <div class="vendor-profile-submit">
                        <div>
                            <div class="font-weight-bold text-dark">Selesai memperbaiki data?</div>
                            <div class="text-muted font-size-sm">Pastikan seluruh catatan revisi sudah ditindaklanjuti.
                            </div>
                        </div>
                        <button type="button" id="btn-submit-revision" class="btn btn-primary font-weight-bold">
                            <i class="flaticon2-paper-plane"></i> Kirim Ulang Revisi
                        </button>
                    </div>
                @elseif (!empty($isEditMode))
                    <div class="vendor-profile-submit">
                        <div class="flex-grow-1">
                            <div class="font-weight-bold text-dark">Selesai memperbarui data?</div>
                            <div class="text-muted font-size-sm">Klik Simpan Perubahan untuk menyimpan perubahan data.
                            </div>
                        </div>
                        <button type="button" id="btn-submit-rekualifikasi" class="btn btn-primary font-weight-bold">
                            <i class="flaticon2-paper-plane"></i> Simpan Perubahan
                        </button>
                    </div>
                @endif
            </form>
        </main>
    </div>

    <x-document-preview />
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/vendor-form.js') }}"></script>
    @include('admin.registrasi.partials.profile.scripts')
@endpush
