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
            <span class="label label-light-{{ $statusPresentation['class'] }} label-inline font-weight-bold">
                {{ $statusPresentation['label'] }}
            </span>
            <div class="text-muted font-size-sm mt-2">{{ $statusPresentation['description'] }}</div>
            <a href="{{ route('registrasi.tracking', $application->application_number) }}"
                class="btn btn-light-primary btn-sm font-weight-bold mt-3">
                <i class="flaticon2-search-1"></i> Lihat Tracking
            </a>
        </div>
    </div>

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
                <button type="button" id="btn-first-revision"
                    class="btn btn-sm btn-warning font-weight-bold mt-3">
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

            <form id="kt_form" method="POST" enctype="multipart/form-data">
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
                            <div class="text-muted font-size-sm">Pastikan seluruh catatan revisi sudah ditindaklanjuti.</div>
                        </div>
                        <button type="button" id="btn-submit-revision" class="btn btn-primary font-weight-bold">
                            <i class="flaticon2-paper-plane"></i> Kirim Ulang Revisi
                        </button>
                    </div>
                @endif
            </form>
        </main>
    </div>

    <div class="modal fade" id="documentPreviewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="documentPreviewFrame" title="Preview dokumen" class="w-100 border-0"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/vendor-form.js') }}"></script>
    @include('admin.registrasi.partials.profile.scripts')
@endpush
