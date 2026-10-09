@extends('layouts.app', ['title' => __('vendor_registration_profile')])

@push('style')
    <link href="{{ asset('css/wizard-4.css') }}" rel="stylesheet" type="text/css" />
    @include('admin.registrasi.partials.profile.styles')
@endpush

@section('breadcrumb', __('registration'))
@section('step', __('vendor_profile'))
@section('page_title', __('vendor_registration_profile'))
@section('page_desc', __('vendor_reg_profile_desc'))

@section('content')
    <div class="vendor-profile-header">
        <div>
            <div class="vendor-profile-eyebrow">{{ __('application_number') }}</div>
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
                    class="btn btn-primary btn-sm font-weight-bold">
                    <i class="flaticon2-search-1"></i> {{ __('view_tracking') }}
                </a>

                @if ($application->status === \App\Models\VendorApplication::STATUS_APPROVED)
                    @if (!empty($isEditMode))
                        <a href="{{ route('registrasi.index') }}" class="btn btn-danger btn-sm font-weight-bold">
                            <i class="fas fa-angle-left"></i> {{ __('back') }}
                        </a>
                    @else
                        <a href="{{ route('registrasi.index', ['edit' => 1]) }}"
                            class="btn btn-warning btn-sm font-weight-bold">
                            <i class="fas fa-edit"></i> {{ __('edit_data') }}
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
            $reasonKey = 'reason_' . $application->requalification_reason . '_label';
            $reasonTrans = __($reasonKey);
            $reasonText =
                $reasonTrans !== $reasonKey
                    ? $reasonTrans
                    : \App\Models\VendorApplication::REASON_LABELS[$application->requalification_reason] ??
                        $application->requalification_reason;
        @endphp
        <div class="alert alert-custom alert-light-warning mb-6 shadow-sm" role="alert"
            style="border-left: 4px solid #f59e0b;">
            <div class="alert-icon"><i class="fas fa-exclamation-triangle text-warning" style="font-size:1.5rem;"></i></div>
            <div class="alert-text">
                <div class="font-weight-bolder text-dark font-size-h6 mb-1">
                    <i class="fas fa-redo text-warning mr-1"></i> {{ __('data_update_request') }}
                </div>
                <div class="text-dark-75 mb-2">
                    {{ __('data_update_request_desc') }}
                    <strong class="text-warning font-weight-bold">{{ $reasonText }}</strong>.
                </div>
                <a href="{{ route('registrasi.index', ['edit' => 1]) }}"
                    class="btn btn-warning font-weight-bold btn-sm px-4">
                    <i class="fas fa-edit mr-1"></i> {{ __('start_update_data') }}
                </a>
            </div>
        </div>
    @endif

    @if (!empty($isEditMode))
        <div class="alert alert-custom alert-light-primary mb-6" role="alert">
            <div class="alert-icon"><i class="fas fa-edit text-primary"></i></div>
            <div class="alert-text">
                <div class="font-weight-bold text-dark mb-1">{{ __('edit_profile_mode_active') }}</div>
                {!! __('edit_profile_mode_desc') !!}
            </div>
        </div>
    @endif

    @if ($isRevisionMode)
        <div class="alert alert-custom alert-light-warning mb-6" role="alert">
            <div class="alert-icon"><i class="flaticon-warning text-warning"></i></div>
            <div class="alert-text">
                <div class="font-weight-bold text-dark mb-1">{{ __('application_needs_correction') }}</div>
                {!! __('revision_notes_count_desc', ['count' => count($revisionNotes)]) !!}
                @if (!empty($draft['admin_note']))
                    <div class="mt-2"><strong>{{ __('general_note_colon') }}</strong> {{ $draft['admin_note'] }}</div>
                @endif
                <button type="button" id="btn-first-revision" class="btn btn-sm btn-warning font-weight-bold mt-3">
                    <i class="flaticon2-arrow-down"></i> {{ __('view_first_revision') }}
                </button>
            </div>
        </div>
    @endif

    <div class="vendor-profile-layout">
        <aside class="vendor-profile-nav">
            <div class="vendor-profile-nav-title">{{ __('registration_data') }}</div>
            <a href="#profil-perusahaan">{{ __('company_profile') }}</a>
            <a href="#pembayaran">{{ __('payment') }}</a>
            <a href="#komitmen">{{ __('commitment_and_certification') }}</a>
            <a href="#informasi-lain">{{ __('other_info') }}</a>
            <a href="#vendor-lokal">{{ __('local_vendor_data') }}</a>
            <a href="#produk">{{ __('product_list') }}</a>
            <a href="#dokumen">{{ __('document') }}</a>
            @foreach ($selectedCategorySections as $categorySection)
                <a href="#kategori-{{ $categorySection['id'] }}">
                    {{ \Illuminate\Support\Str::limit($categorySection['label'], 32) }}
                </a>
            @endforeach
        </aside>

        <main class="vendor-profile-content">
            <div class="vendor-profile-categories">
                <span class="font-weight-bold text-dark mr-2">{{ __('registered_categories_colon') }}</span>
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
                            <div class="font-weight-bold text-dark">{{ __('finished_correcting_data') }}</div>
                            <div class="text-muted font-size-sm">{{ __('ensure_all_revisions_addressed') }}</div>
                        </div>
                        <button type="button" id="btn-submit-revision" class="btn btn-primary font-weight-bold">
                            <i class="flaticon2-paper-plane"></i> {{ __('resubmit_revision') }}
                        </button>
                    </div>
                @elseif (!empty($isEditMode))
                    <div class="vendor-profile-submit">
                        <div class="flex-grow-1">
                            <div class="font-weight-bold text-dark">{{ __('finished_updating_data') }}</div>
                            <div class="text-muted font-size-sm">{{ __('click_save_to_update_data') }}</div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <a href="{{ route('registrasi.index') }}" class="btn btn-sm btn-danger font-weight-bold">
                                <i class="fas fa-angle-left"></i> {{ __('back') }}
                            </a>
                            <button type="button" id="btn-submit-rekualifikasi"
                                class="btn btn-sm btn-primary font-weight-bold">
                                <i class="flaticon2-paper-plane"></i> {{ __('save_changes') }}
                            </button>
                        </div>
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
