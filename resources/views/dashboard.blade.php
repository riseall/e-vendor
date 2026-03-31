@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    {{-- card welcome --}}
    <div class="card welcome-card p-6 py-8 shadow-sm">
        <p class="mb-1">{{ Auth::user()->roles->pluck('name')->implode(', ') }}</p>
        <h2 class="mb-1">{{ __('welcome') }}, <b>{{ Auth::user()->name }}!</b></h2>
        <p class="mb-0"><i class="far fa-calendar-alt mr-2 text-white"></i> {{ date('l') }}, {{ date('d F Y') }}</p>
    </div>

    {{-- card total --}}
    <div class="row mt-8">
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card card-custom bgi-no-repeat card-stretch gutter-b">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 d-block">300</span>
                        <i class="fas fa-users text-dark icon-lg"></i>
                    </div>
                    <span class="font-weight-bold text-muted font-size-sm">Total User</span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card card-custom bgi-no-repeat card-stretch gutter-b">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 d-block">209</span>
                        <i class="fas fa-building text-primary icon-lg"></i>
                    </div>
                    <span class="font-weight-bold text-muted font-size-sm">Total Supplier</span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card card-custom bgi-no-repeat card-stretch gutter-b">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 d-block">180</span>
                        <i class="fas fa-check-circle text-success icon-lg"></i>
                    </div>
                    <span class="font-weight-bold text-muted font-size-sm">Approved Supplier</span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card card-custom bgi-no-repeat card-stretch gutter-b">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 d-block">20</span>
                        <i class="fas fa-hourglass-half text-warning icon-lg"></i>
                    </div>
                    <span class="font-weight-bold text-muted font-size-sm">Supplier Under Qualification</span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card card-custom bgi-no-repeat card-stretch gutter-b">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 d-block">5</span>
                        <i class="fas fa-ban text-danger icon-lg"></i>
                    </div>
                    <span class="font-weight-bold text-muted font-size-sm">Suspended Supplier</span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card card-custom bgi-no-repeat card-stretch gutter-b">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 d-block">4</span>
                        <i class="fas fa-sync-alt text-info icon-lg"></i>
                    </div>
                    <span class="font-weight-bold text-muted font-size-sm">Supplier Requalification Due</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="row">
        <div class="col-lg-6">
            <!--begin::Card-->
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">Donut Chart</h3>
                    </div>
                </div>
                <div class="card-body">
                    <!--begin::Chart-->
                    <div id="chart_11" class="d-flex justify-content-center"></div>
                    <!--end::Chart-->
                </div>
            </div>
            <!--end::Card-->
        </div>
        <div class="col-lg-6">
            <!--begin::Card-->
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">Pie Chart</h3>
                    </div>
                </div>
                <div class="card-body">
                    <!--begin::Chart-->
                    <div id="chart_12" class="d-flex justify-content-center"></div>
                    <!--end::Chart-->
                </div>
            </div>
            <!--end::Card-->
        </div>
    </div>
@endSection

@push('scripts')
    <script src="{{ asset('js/charts/apexcharts.js') }}"></script>
@endpush
