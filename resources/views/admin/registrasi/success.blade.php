@extends('layouts.app', ['title' => __('application_submitted')])

@section('breadcrumb', __('registration'))
@section('step', __('pre_qualification'))
@section('page_title', __('application_submitted'))
@section('page_desc', __('application_submitted_page_desc'))

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
                    <h3 class="font-weight-bolder text-dark mb-1">{{ __('application_successfully_submitted') }}</h3>
                    <div class="text-muted">{{ __('save_app_number_for_tracking') }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-5 mb-6">
                    <div class="bg-light-primary rounded p-6 h-100">
                        <div class="text-muted font-size-sm text-uppercase font-weight-bold mb-2">{{ __('application_number') }}</div>
                        <div class="font-weight-bolder text-primary" style="font-size: 1.6rem;">
                            {{ $applicationNumber }}
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 mb-6">
                    <div class="bg-light rounded p-6 h-100">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">{{ __('status') }}</span>
                            <span class="label label-lg label-light-primary label-inline font-weight-bold">{{ __('status_short_submitted') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">{{ __('submission_date') }}</span>
                            <span class="font-weight-bold">
                                {{ optional($application->submitted_at)->format('d/m/Y H:i') ?? '-' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('company_name') }}</span>
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
                            <div class="font-weight-bolder text-dark">{{ __('application_sent') }}</div>
                            <div class="text-muted">{{ __('vendor_data_entered_system') }}</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-media bg-light-primary">
                            <i class="flaticon-search text-primary"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="font-weight-bolder text-dark">{{ __('awaiting_procurement_verification') }}</div>
                            <div class="text-muted">{{ __('procurement_will_verify_data_docs') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('registrasi.tracking', $applicationNumber) }}" class="btn btn-primary font-weight-bold mr-2">
                    <i class="flaticon-search"></i> {{ __('track_application') }}
                </a>
                <a href="{{ route('registrasi.index') }}" class="btn btn-light-primary font-weight-bold mr-2">
                    <i class="flaticon-eye"></i> {{ __('view_form') }}
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-light font-weight-bold">
                    <i class="flaticon2-dashboard"></i> {{ __('to_dashboard') }}
                </a>
            </div>
        </div>
    </div>
@endsection
