@extends('layouts.app', ['title' => 'Detail Verifikasi'])

@php
    $isManagerStage = in_array(
        $application->status,
        [\App\Models\VendorApplication::STATUS_VERIFIED_ADMIN, \App\Models\VendorApplication::STATUS_VERIFIED],
        true,
    );
@endphp

@section('breadcrumb', 'Pengadaan')
@section('step', 'Verifikasi')
@section('page_title', 'Verifikasi Data Calon Penyedia')
@section('page_desc', $isManagerStage ? 'Review resume data calon penyedia dan berikan persetujuan akhir sebelum
    permohonan diteruskan ke QA Risk Assessment.' : 'Verifikasi setiap bagian data vendor melalui tab dan card berkas
    berikut sebelum diteruskan ke Manager Pengadaan.')

    @push('style')
        @include('admin.verifikasi.partials.show.styles')
    @endpush

@section('content')

    @include('admin.verifikasi.partials.show.header')

    {{-- Pemisahan Tampilan: Verifikasi Per Card (Level 1) vs Verifikasi Manager (Level 2) --}}
    @if ($isManagerStage)
        {{-- Tampilan Verifikasi Manager: Aktif setelah verifikasi per card selesai --}}
        <div class="mb-6">
            @include('admin.verifikasi.partials.show.resume')
        </div>
    @else
        {{-- Tampilan Verifikasi Per Card: Aktif saat masih proses verifikasi berkas per bagian --}}
        <div class="mb-6">
            @include('admin.verifikasi.partials.show.verification-card')
        </div>
    @endif

    @include('admin.verifikasi.partials.show.history')

    <x-document-preview />

    @include('admin.verifikasi.partials.show.modals')

@endsection

@push('scripts')
    @include('admin.verifikasi.partials.show.scripts')
@endpush
