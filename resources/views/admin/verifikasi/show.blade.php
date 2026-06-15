@extends('layouts.app', ['title' => 'Detail Verifikasi'])

@section('breadcrumb', 'Pengadaan')
@section('step', 'Verifikasi')
@section('page_title', 'Verifikasi Data Calon Penyedia')
@section('page_desc', 'Verifikasi setiap bagian data vendor melalui tab berikut sebelum permohonan diteruskan.')

@push('style')
    @include('admin.verifikasi.partials.show.styles')
@endpush

@section('content')


    @include('admin.verifikasi.partials.show.header')

    @include('admin.verifikasi.partials.show.verification-card')

    @include('admin.verifikasi.partials.show.history')

    <x-document-preview />

    @include('admin.verifikasi.partials.show.modals')

@endsection

@push('scripts')
    @include('admin.verifikasi.partials.show.scripts')
@endpush
