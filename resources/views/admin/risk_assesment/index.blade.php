@extends('layouts.app', ['title' => 'Risk Assessment'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Risk Assessment')
@section('page_title', 'Risk Assessment Vendor Baru')
@section('page_desc', 'Daftar vendor yang sudah diverifikasi pengadaan dan perlu penilaian risiko QA.')

@section('content')
    <div class="card card-custom gutter-b">
        <div class="card-header flex-wrap border-0 pt-6 pb-0">
            <div class="card-title">
                <h3 class="card-label">
                    Risk Assessment Vendor
                    <span class="d-block text-muted pt-2 font-size-sm">
                        Kategori: 12-{{ $lowThreshold }} Low, {{ $lowThreshold + 1 }}-{{ $highThreshold }} Medium, > {{ $highThreshold }} High
                    </span>
                </h3>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-custom alert-light-success fade show mb-6" role="alert">
                    <div class="alert-icon"><i class="flaticon2-check-mark"></i></div>
                    <div class="alert-text">{{ session('success') }}</div>
                    <div class="alert-close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="ki ki-close"></i></span>
                        </button>
                    </div>
                </div>
            @endif

            <form method="GET" action="{{ route('qa.risk-assessment.index') }}" class="mb-6">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label>Pencarian</label>
                        <input type="text" name="q" class="form-control" value="{{ $search }}"
                            placeholder="Nomor permohonan, nama vendor, email">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary font-weight-bold mr-2">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="{{ route('qa.risk-assessment.index') }}" class="btn btn-light-primary font-weight-bold">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Permohonan</th>
                            <th>Vendor</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Total Nilai</th>
                            <th>Level</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $application)
                            @php
                                $qualification = $application->qualification;
                                $riskClass = [
                                    'low' => 'label-light-success',
                                    'medium' => 'label-light-warning',
                                    'high' => 'label-light-danger',
                                ][$application->risk_level] ?? 'label-light-dark';
                            @endphp
                            <tr>
                                <td>{{ $applications->firstItem() + $loop->index }}</td>
                                <td>{{ $application->application_number ?? '-' }}</td>
                                <td>
                                    <div class="font-weight-bold">
                                        {{ optional($application->general)->nama_perusahaan ?? $application->user->name }}
                                    </div>
                                    <span class="text-muted">{{ optional($application->general)->email_perusahaan ?? $application->user->email }}</span>
                                </td>
                                <td>
                                    {{ $application->categories->pluck('category_id')->map(function ($id) {
                                        return \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id;
                                    })->implode(', ') ?: '-' }}
                                </td>
                                <td>
                                    <span class="label label-inline label-light-primary font-weight-bold">
                                        {{ str_replace('_', ' ', strtoupper($application->status)) }}
                                    </span>
                                </td>
                                <td>{{ $qualification ? number_format($qualification->total_score, 0, ',', '.') : '-' }}</td>
                                <td>
                                    <span class="label label-inline {{ $riskClass }} font-weight-bold">
                                        {{ $application->risk_level ? strtoupper($application->risk_level) : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('qa.risk-assessment.create', $application->id) }}"
                                        class="btn btn-sm btn-primary font-weight-bold">
                                        {{ $qualification ? 'Lihat / Ubah' : 'Assessment' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-10">
                                    Tidak ada vendor untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $applications->links() }}
        </div>
    </div>
@endsection
