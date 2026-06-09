@extends('layouts.app', ['title' => 'Tracking Permohonan'])

@section('breadcrumb', 'Registrasi')
@section('step', 'Pra Kualifikasi')
@section('page_title', 'Tracking Permohonan')
@section('page_desc', 'Pantau status permohonan vendor Anda secara ringkas dan aman.')

@section('content')
    @php
        $statusLabel = [
            'draft' => ['text' => 'Draft', 'class' => 'label-light-warning'],
            'submitted' => ['text' => 'Submitted', 'class' => 'label-light-primary'],
            'need_revision' => ['text' => 'Need Revision', 'class' => 'label-light-warning'],
            'verified' => ['text' => 'Verified', 'class' => 'label-light-info'],
            'approved' => ['text' => 'Approved', 'class' => 'label-light-success'],
            'rejected' => ['text' => 'Rejected', 'class' => 'label-light-danger'],
        ][$application->status] ?? [
            'text' => ucwords(str_replace('_', ' ', $application->status)),
            'class' => 'label-light',
        ];

        $stateClass = [
            'done' => ['media' => 'bg-success', 'icon' => 'flaticon2-check-mark text-white'],
            'active' => ['media' => 'bg-primary', 'icon' => 'flaticon-search text-white'],
            'warning' => ['media' => 'bg-warning', 'icon' => 'flaticon-warning text-white'],
            'danger' => ['media' => 'bg-danger', 'icon' => 'flaticon2-cross text-white'],
            'pending' => ['media' => 'bg-light', 'icon' => 'flaticon2-hourglass text-muted'],
        ];
    @endphp

    <div class="card card-custom mb-6">
        <div class="card-body p-8">
            <div class="d-flex flex-wrap justify-content-between align-items-start">
                <div class="mb-5">
                    <div class="text-muted font-size-sm text-uppercase font-weight-bold mb-2">Nomor Permohonan</div>
                    <h2 class="font-weight-bolder text-primary mb-2">{{ $applicationNumber }}</h2>
                    <div class="text-dark-75 font-weight-bold">
                        {{ optional($application->general)->nama_perusahaan ?? '-' }}
                    </div>
                </div>
                <div class="text-left text-lg-right mb-5">
                    <span class="label label-lg {{ $statusLabel['class'] }} label-inline font-weight-bold mb-3">
                        {{ $statusLabel['text'] }}
                    </span>
                    <div class="text-muted font-size-sm">
                        Dikirim:
                        <span class="font-weight-bold text-dark">
                            {{ optional($application->submitted_at)->format('d/m/Y H:i') ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="separator separator-dashed my-5"></div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="bg-light rounded p-5 h-100">
                        <div class="text-muted font-size-sm mb-1">Pemohon</div>
                        <div class="font-weight-bold text-dark">
                            {{ optional($application->user)->name ?? auth()->user()->name }}</div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="bg-light rounded p-5 h-100">
                        <div class="text-muted font-size-sm mb-1">Kategori</div>
                        <div class="font-weight-bold text-dark">{{ optional($application->category)->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="bg-light rounded p-5 h-100">
                        <div class="text-muted font-size-sm mb-1">Update Terakhir</div>
                        <div class="font-weight-bold text-dark">
                            {{ optional($application->updated_at)->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label font-weight-bolder">Progress Permohonan</h3>
            </div>
        </div>
        <div class="card-body p-8">
            <div class="timeline timeline-3">
                <div class="timeline-items">
                    @foreach ($statusSteps as $step)
                        @php
                            $style = $stateClass[$step['state']] ?? $stateClass['pending'];
                        @endphp
                        <div class="timeline-item">
                            <div class="timeline-media {{ $style['media'] }}">
                                <i class="{{ $style['icon'] }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                    <div class="font-weight-bolder text-dark">{{ $step['title'] }}</div>
                                    <div class="text-muted font-size-sm">
                                        {{ optional($step['date'])->format('d/m/Y H:i') ?? '-' }}
                                    </div>
                                </div>
                                <div class="text-muted">{{ $step['description'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($application->status === \App\Models\VendorApplication::STATUS_NEED_REVISION && $application->admin_note)
                <div class="alert alert-custom alert-light-warning mt-8 mb-0">
                    <div class="alert-icon"><i class="flaticon-warning"></i></div>
                    <div class="alert-text">
                        <div class="font-weight-bolder mb-1">Catatan Revisi</div>
                        {{ $application->admin_note }}
                    </div>
                </div>
            @endif

            {{-- <div class="mt-8">
                <a href="{{ route('registrasi.index') }}" class="btn btn-light-primary font-weight-bold mr-2">
                    <i class="flaticon-eye"></i> Lihat Form
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary font-weight-bold">
                    <i class="flaticon2-dashboard"></i> Ke Dashboard
                </a>
            </div> --}}
        </div>
    </div>
@endsection
