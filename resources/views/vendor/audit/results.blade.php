@extends('layouts.app', ['title' => __('vendor_audit_results')])

@section('breadcrumb', __('quality_assurance'))
@section('step', __('audit_results'))
@section('page_title', __('vendor_audit_results'))
@section('page_desc', __('vendor_audit_results_desc'))

@section('content')
    <div class="d-flex flex-column gap-5 mb-5">
        @if ($audits->isEmpty())
            <div class="card card-custom border-0 shadow-sm" style="border-radius: var(--radius-lg);">
                <div class="card-body text-center py-12">
                    <img src="{{ asset('assets/media/svg/illustrations/sorry.svg') }}" alt="Empty" style="max-width: 170px;"
                        class="mb-4 opacity-75" />
                    <h5 class="text-dark font-weight-bolder">{{ __('no_audit_data_title') }}</h5>
                    <p class="text-muted font-size-sm mb-0">{{ __('no_audit_data_desc') }}</p>
                </div>
            </div>
        @else
            @foreach ($audits as $audit)
                @php
                    $cat = $audit->audit_result_category;
                    if (!$cat) {
                        if ($audit->status === \App\Models\VendorAudit::STATUS_COMPLETED) {
                            $cat = 'terekomendasi';
                        } elseif ($audit->status === \App\Models\VendorAudit::STATUS_REJECTED) {
                            $cat = 'tdk_rekomendasi';
                        }
                    }

                    if ($cat === 'terekomendasi') {
                        $bannerBg = 'linear-gradient(135deg, #10B981 0%, #059669 100%)';
                        $badgeClass = 'badge-light-success text-success';
                        $catLabel = __('recommended_approved');
                    } elseif ($cat === 'tdk_rekomendasi') {
                        $bannerBg = 'linear-gradient(135deg, #EF4444 0%, #DC2626 100%)';
                        $badgeClass = 'badge-light-danger text-danger';
                        $catLabel = __('not_recommended_rejected');
                    } elseif ($cat === 'on_hold') {
                        $bannerBg = 'linear-gradient(135deg, #F59E0B 0%, #D97706 100%)';
                        $badgeClass = 'badge-light-warning text-warning';
                        $catLabel = __('on_hold_pending');
                    } else {
                        $bannerBg = 'linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%)';
                        $badgeClass = 'badge-light-primary text-primary';
                        $catLabel = __('in_evaluation_process');
                    }

                    $filePath = $audit->audit_result_path;
                    $docUrl = $filePath ? asset('storage/' . $filePath) : null;
                    $extension = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : '';
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                    $isPdf = $extension === 'pdf';
                @endphp

                <div class="card card-custom border-0 mb-5 overflow-hidden"
                    style="border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
                    {{-- Status Banner Header --}}
                    <div class="px-6 py-4 text-white d-flex flex-wrap align-items-center justify-content-between"
                        style="background: {{ $bannerBg }};">
                        <div class="d-flex align-items-center gap-3">
                            <span class="svg-icon svg-icon-white svg-icon-2x mr-2">
                                <svg viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                            </span>
                            <div>
                                <h6 class="font-weight-bolder mb-0 text-white"
                                    style="font-size: 1.1rem; letter-spacing: 0.3px;">
                                    {{ $catLabel }}
                                </h6>
                                <span class="font-size-xs text-white-50 font-weight-bold">
                                    {{ $audit->audit_type === 'on_desk' ? __('on_desk_audit') : __('on_site_audit') }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 mt-sm-0">
                            <span class="badge badge-white text-dark font-weight-bolder px-3 py-2"
                                style="font-size: 0.8rem; border-radius: 20px;">
                                {{ __('application_number_colon') }} {{ $audit->application->application_number ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-6">
                        {{-- Meta Details Row --}}
                        <div class="row mb-5">
                            <div class="col-md-4 mb-3 mb-md-0 border-right">
                                <div class="text-muted font-weight-bold font-size-xs text-uppercase mb-1"
                                    style="letter-spacing: 0.5px;">{{ __('audit_evaluation_status') }}</div>
                                <div class="font-weight-bolder text-dark mb-3" style="font-size: 1rem;">
                                    <span class="badge badge-light-info font-weight-bolder px-3 py-2"
                                        style="border-radius: 6px;">
                                        {{ strtoupper(str_replace('_', ' ', $audit->status)) }}
                                    </span>
                                </div>

                                @if ($audit->confirmed_schedule_at)
                                    <div class="text-muted font-weight-bold font-size-xs text-uppercase mb-1"
                                        style="letter-spacing: 0.5px;">{{ __('audit_schedule') }}</div>
                                    <div class="font-weight-bolder text-dark mb-3" style="font-size: 0.9rem;">
                                        {{ \Carbon\Carbon::parse($audit->confirmed_schedule_at)->translatedFormat('d F Y H:i') }}
                                        WIB
                                    </div>
                                @endif

                                @if ($audit->completed_at)
                                    <div class="text-muted font-weight-bold font-size-xs text-uppercase mb-1"
                                        style="letter-spacing: 0.5px;">{{ __('completion_date') }}</div>
                                    <div class="font-weight-bolder text-dark" style="font-size: 0.9rem;">
                                        {{ \Carbon\Carbon::parse($audit->completed_at)->translatedFormat('d F Y') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-8 pl-md-5">
                                <div class="text-muted font-weight-bold font-size-xs text-uppercase mb-2"
                                    style="letter-spacing: 0.5px;">{{ __('qa_evaluation_summary') }}</div>
                                <div class="p-4 bg-light rounded text-dark font-weight-bold"
                                    style="font-size: 0.95rem; line-height: 1.6; min-height: 75px; border-left: 4px solid var(--brand-primary, #0284c7);">
                                    {!! nl2br(e($audit->summary ?? __('no_qa_evaluation_notes'))) !!}
                                </div>

                                @if ($audit->audit_type === 'on_desk' && $audit->status === \App\Models\VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS)
                                    <div class="mt-4">
                                        <a href="{{ route('vendor.audit.questionnaire', $audit->id) }}"
                                            class="btn btn-primary font-weight-bold">
                                            <i class="flaticon2-edit"></i> {{ __('fill_questionnaire_now') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Direct Document Viewer Section --}}
                        @if ($docUrl)
                            <div class="mt-6 pt-5 border-top">
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="flaticon2-file text-primary icon-lg mr-2"></i>
                                        <h6 class="font-weight-bolder text-dark mb-0">{{ __('official_audit_result_doc') }}</h6>
                                    </div>
                                    <div class="d-flex gap-2 mt-2 mt-sm-0">
                                        <a href="{{ $docUrl }}" target="_blank"
                                            class="btn btn-sm btn-light-primary font-weight-bold mr-2">
                                            <i class="flaticon2-open-text-book"></i> {{ __('open_in_new_tab') }}
                                        </a>
                                        <a href="{{ $docUrl }}" download
                                            class="btn btn-sm btn-primary font-weight-bold">
                                            <i class="flaticon2-download"></i> {{ __('download_document') }}
                                        </a>
                                    </div>
                                </div>

                                {{-- Inline Document Display Container --}}
                                <div class="rounded border p-2 bg-dark-subtle"
                                    style="border-radius: 12px; overflow: hidden; background: #f8fafc;">
                                    @if ($isPdf)
                                        <div style="height: 600px; width: 100%;">
                                            <iframe src="{{ $docUrl }}" width="100%" height="100%"
                                                style="border: none; border-radius: 8px;"></iframe>
                                        </div>
                                    @elseif ($isImage)
                                        <div class="text-center p-4">
                                            <img src="{{ $docUrl }}" alt="{{ __('audit_result_document') }}"
                                                class="img-fluid rounded shadow-sm"
                                                style="max-height: 600px; object-fit: contain;">
                                        </div>
                                    @else
                                        <div
                                            class="alert alert-custom alert-light-primary border-0 rounded p-4 mb-0 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="alert-icon mr-3"><i
                                                        class="flaticon-doc icon-2x text-primary"></i></div>
                                                <div>
                                                    <div class="font-weight-bold text-dark font-size-base">{{ __('audit_result_doc_ext', ['ext' => strtoupper($extension)]) }}</div>
                                                    <div class="text-muted font-size-sm">{{ __('unsupported_preview_format_desc') }}</div>
                                                </div>
                                            </div>
                                            <a href="{{ $docUrl }}" download
                                                class="btn btn-primary font-weight-bold btn-sm">
                                                <i class="flaticon2-download"></i> {{ __('download_file') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
