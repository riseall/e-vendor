@extends('layouts.app', ['title' => 'Permohonan Terkirim'])

@section('breadcrumb', 'Registrasi')
@section('step', 'Pra Kualifikasi')
@section('page_title', 'Permohonan Terkirim')
@section('page_desc', 'Permohonan vendor Anda sudah dikirim dan sedang menunggu proses verifikasi.')

@section('content')
    <div class="card card-custom">
        <div class="card-body p-8">
            <div class="d-flex align-items-center mb-8">
                <div class="symbol symbol-60 symbol-light-success mr-5">
                    <span class="symbol-label">
                        <i class="flaticon2-check-mark text-success icon-2x"></i>
                    </span>
                </div>
                <div>
                    <h3 class="font-weight-bolder text-dark mb-1">Permohonan berhasil dikirim</h3>
                    <div class="text-muted">Simpan nomor permohonan berikut untuk tracking proses registrasi.</div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-5 mb-6">
                    <div class="bg-light-primary rounded p-6 h-100">
                        <div class="text-muted font-size-sm text-uppercase font-weight-bold mb-2">Nomor Permohonan</div>
                        <div class="font-weight-bolder text-primary" style="font-size: 1.6rem;">
                            {{ $applicationNumber }}
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 mb-6">
                    <div class="bg-light rounded p-6 h-100">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Status</span>
                            <span class="label label-lg label-light-primary label-inline font-weight-bold">Submitted</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tanggal Submit</span>
                            <span class="font-weight-bold">
                                {{ optional($application->submitted_at)->format('d/m/Y H:i') ?? '-' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Nama Perusahaan</span>
                            <span class="font-weight-bold text-right">
                                {{ optional($application->general)->nama_perusahaan ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="separator separator-dashed my-6"></div>

            <div class="timeline timeline-3">
                <div class="timeline-items">
                    <div class="timeline-item">
                        <div class="timeline-media bg-success">
                            <i class="flaticon2-check-mark text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="font-weight-bolder text-dark">Permohonan dikirim</div>
                            <div class="text-muted">Data vendor sudah masuk ke sistem.</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-media bg-light-primary">
                            <i class="flaticon-search text-primary"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="font-weight-bolder text-dark">Menunggu verifikasi pengadaan</div>
                            <div class="text-muted">Tim pengadaan akan memeriksa kelengkapan data dan dokumen.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('registrasi.tracking', $applicationNumber) }}" class="btn btn-primary font-weight-bold mr-2">
                    <i class="flaticon-search"></i> Tracking Permohonan
                </a>
                <a href="{{ route('registrasi.index') }}" class="btn btn-light-primary font-weight-bold mr-2">
                    <i class="flaticon-eye"></i> Lihat Form
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-light font-weight-bold">
                    <i class="flaticon2-dashboard"></i> Ke Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection
