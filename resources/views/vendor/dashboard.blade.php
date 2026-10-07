@extends('layouts.app', ['title' => __('vendor_dashboard')])

@push('style')
<style>
    /* ── Stepper Styles ── */
    .vnd-stepper {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 1.5rem 0 0.5rem;
    }
    .vnd-stepper::before {
        content: '';
        position: absolute;
        top: 22px;
        left: 5%;
        right: 5%;
        height: 3px;
        background: #e2e8f0;
        z-index: 1;
    }
    .vnd-step-item {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
        padding: 0 0.5rem;
    }
    .vnd-step-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        margin: 0 auto 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .vnd-step-circle--completed {
        background: #10b981;
        color: #ffffff;
        border: 3px solid #d1fae5;
    }
    .vnd-step-circle--current {
        background: #005db6;
        color: #ffffff;
        border: 3px solid #c2d9f5;
        box-shadow: 0 0 0 4px rgba(0, 93, 182, 0.15);
    }
    .vnd-step-circle--danger {
        background: #ef4444;
        color: #ffffff;
        border: 3px solid #fee2e2;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
    }
    .vnd-step-circle--pending {
        background: #f1f5f9;
        color: #94a3b8;
        border: 2px solid #e2e8f0;
    }
    .vnd-step-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.2rem;
    }
    .vnd-step-desc {
        font-size: 0.73rem;
        color: #64748b;
        line-height: 1.25;
    }
    .vnd-step-date {
        font-size: 0.7rem;
        color: #005db6;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    /* ── Banner Notification Styles ── */
    .vnd-alert-banner {
        border-radius: 12px;
        padding: 1.2rem 1.4rem;
        border-left: 5px solid;
        display: flex;
        align-items: flex-start;
        gap: 1.1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .vnd-alert-banner--danger {
        background-color: #fef2f2;
        border-color: #ef4444;
        color: #991b1b;
    }
    .vnd-alert-banner--warning {
        background-color: #fffbeb;
        border-color: #f59e0b;
        color: #92400e;
    }
    .vnd-alert-banner--primary {
        background-color: #eff6ff;
        border-color: #005db6;
        color: #1e40af;
    }
    .vnd-alert-banner--success {
        background-color: #f0fdf4;
        border-color: #10b981;
        color: #065f46;
    }

    /* ── Metric Bar Styles ── */
    .metric-row {
        margin-bottom: 0.85rem;
    }
    .metric-row:last-child {
        margin-bottom: 0;
    }
    .metric-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.35rem;
    }

    /* ── Category Pill Styling ── */
    .vnd-category-pill {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        color: #0369a1;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.35rem 0.85rem;
        border-radius: 50rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        margin-right: 0.5rem;
        margin-bottom: 0.35rem;
    }
    .vnd-category-pill:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        color: #0284c7;
    }
    .vnd-category-pill i {
        font-size: 0.72rem;
        margin-right: 0.4rem;
        color: #0284c7;
    }

    @media (max-width: 767.98px) {
        .vnd-stepper {
            flex-direction: column;
            gap: 1.25rem;
        }
        .vnd-stepper::before {
            display: none;
        }
        .vnd-step-item {
            display: flex;
            align-items: center;
            text-align: left;
            gap: 1rem;
        }
        .vnd-step-circle {
            margin: 0;
            flex-shrink: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    {{-- ═══════════════════════════════════════════════════════════════════
         1. WELCOME CARD
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="card welcome-card p-6 py-7 shadow-sm mb-6 position-relative overflow-hidden">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div class="mb-3 mb-md-0">
                <h2 class="mb-1 text-white font-weight-bolder">
                    {{ __('welcome') }}, <b>{{ $companyName }}!</b>
                </h2>
                <p class="mb-0 text-white-75 small">
                    <i class="far fa-calendar-alt mr-2 text-white-50"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>

                @if($categories->isNotEmpty())
                    <div class="d-flex align-items-center flex-wrap mt-3">
                        @foreach($categories as $cat)
                            <span class="vnd-category-pill">
                                <i class="fas fa-tag"></i> {{ $cat }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="text-md-right my-auto">
                <span class="label label-xl {{ $statusInfo['badge_class'] }} label-inline font-weight-bolder py-4 px-5 text-uppercase shadow-xs">
                    @if($status === \App\Models\VendorApplication::STATUS_APPROVED)
                        <i class="fas fa-check-circle mr-2" style="color: inherit;"></i>
                    @elseif($status === \App\Models\VendorApplication::STATUS_NEED_REVISION)
                        <i class="fas fa-exclamation-circle mr-2" style="color: inherit;"></i>
                    @elseif($status === \App\Models\VendorApplication::STATUS_SUBMITTED)
                        <i class="fas fa-hourglass-half mr-2" style="color: inherit;"></i>
                    @else
                        <i class="fas fa-info-circle mr-2" style="color: inherit;"></i>
                    @endif
                    {{ $statusInfo['label'] }}
                </span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         2. NOTIFICATION BANNER (Dynamic based on Status & Action Required)
         ═══════════════════════════════════════════════════════════════════ --}}
    @if($status === \App\Models\VendorApplication::STATUS_NEED_REVISION)
        {{-- Banner: Perlu Revisi Dokumen --}}
        <div class="vnd-alert-banner vnd-alert-banner--danger">
            <div class="mt-1">
                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bolder mb-1 text-danger">
                    {{ __('banner_revision_title') }}
                </h5>
                <p class="mb-2 small" style="color: #7f1d1d;">
                    {{ __('banner_revision_desc') }}
                </p>

                @if(!empty($revisionNotes))
                    <div class="bg-white rounded p-3 mb-3 border border-danger-light" style="border: 1px solid #fecaca;">
                        <ul class="mb-0 pl-4 small text-dark">
                            @foreach($revisionNotes as $rev)
                                <li class="mb-1">
                                    <strong class="text-danger">{{ $rev['field'] }}:</strong> {{ $rev['note'] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <a href="{{ route('registrasi.index') }}" class="btn btn-danger btn-sm font-weight-bolder px-4 shadow-sm">
                    <i class="fas fa-edit mr-1"></i> {{ __('btn_open_form_and_revise') }}
                </a>
            </div>
        </div>

    @elseif($activeAudit && $activeAudit->can_fill_questionnaire)
        {{-- Banner: Kuesioner Audit Perlu Diisi --}}
        <div class="vnd-alert-banner vnd-alert-banner--primary">
            <div class="mt-1">
                <i class="fas fa-clipboard-list fa-2x text-primary"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bolder mb-1 text-primary">
                    {{ __('banner_audit_title') }}
                </h5>
                <p class="mb-3 small" style="color: #1e3a8a;">
                    {{ __('banner_audit_desc') }}
                </p>
                <a href="{{ route('vendor.audit.questionnaire', $activeAudit->id) }}" class="btn btn-primary btn-sm font-weight-bolder px-4 shadow-sm">
                    <i class="fas fa-pen mr-1"></i> {{ __('btn_fill_audit_now') }}
                </a>
            </div>
        </div>

    @elseif($status === \App\Models\VendorApplication::STATUS_APPROVED && $validityStatus === 'expiring_soon')
        {{-- Banner: Masa Berlaku Kemitraan Mendekati Kadaluarsa --}}
        <div class="vnd-alert-banner vnd-alert-banner--warning">
            <div class="mt-1">
                <i class="fas fa-hourglass-end fa-2x text-warning"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bolder mb-1 text-warning">
                    {{ __('banner_expiring_soon_title', ['days' => $daysUntilExpiry]) }}
                </h5>
                <p class="mb-3 small" style="color: #78350f;">
                    {!! __('banner_expiring_soon_desc', ['date' => $validUntilDisplay]) !!}
                </p>
                <form action="{{ route('rekualifikasi.initiate') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm font-weight-bolder px-4 shadow-sm">
                        <i class="fas fa-sync-alt mr-1"></i> {{ __('btn_apply_requalification') }}
                    </button>
                </form>
            </div>
        </div>

    @elseif($status === \App\Models\VendorApplication::STATUS_SUBMITTED)
        {{-- Banner: Menunggu Verifikasi --}}
        <div class="vnd-alert-banner vnd-alert-banner--primary">
            <div class="mt-1">
                <i class="fas fa-info-circle fa-2x text-primary"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bolder mb-1 text-primary">
                    {{ __('banner_submitted_title') }}
                </h5>
                <p class="mb-0 small" style="color: #1e3a8a;">
                    {{ __('banner_submitted_desc', ['date' => $application->submitted_at ? $application->submitted_at->format('d M Y H:i') : '']) }}
                </p>
            </div>
        </div>

    @elseif(!$application || $status === \App\Models\VendorApplication::STATUS_DRAFT)
        {{-- Banner: Draf Formulir Belum Dikirim --}}
        <div class="vnd-alert-banner vnd-alert-banner--warning">
            <div class="mt-1">
                <i class="fas fa-clipboard-check fa-2x text-warning"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bolder mb-1 text-warning">
                    {{ __('banner_draft_title') }}
                </h5>
                <p class="mb-3 small" style="color: #78350f;">
                    {{ __('banner_draft_desc') }}
                </p>
                <a href="{{ route('registrasi.index') }}" class="btn btn-warning btn-sm font-weight-bolder px-4 shadow-sm">
                    <i class="fas fa-arrow-right mr-1"></i> {{ __('btn_continue_filling_form') }}
                </a>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════
         3. STEPPER TAHAPAN KUALIFIKASI VENDOR
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="vnd-card mb-6">
        <div class="vnd-card-head">
            <div class="vnd-card-title">
                <span class="vnd-card-title-dot"></span>
                <i class="fas fa-route text-primary mr-1"></i>
                {{ __('qualification_steps_and_partnership_status') }}
            </div>
            <div>
                <a href="{{ route('registrasi.index') }}" class="btn btn-sm btn-light-primary font-weight-bolder">
                    <i class="fas fa-external-link-alt mr-1"></i> {{ __('form_details') }}
                </a>
            </div>
        </div>
        <div class="card-body py-6 px-4 px-md-8">
            <div class="vnd-stepper">
                @foreach($pipelineSteps as $step)
                    <div class="vnd-step-item">
                        <div class="vnd-step-circle vnd-step-circle--{{ $step['status'] }}">
                            @if($step['status'] === 'completed')
                                <i class="fas fa-check text-white"></i>
                            @elseif($step['status'] === 'danger')
                                <i class="fas fa-exclamation text-white"></i>
                            @elseif($step['status'] === 'current')
                                <i class="{{ $step['icon'] }} text-white"></i>
                            @else
                                <span>{{ $step['step'] }}</span>
                            @endif
                        </div>
                        <div class="vnd-step-title">{{ $step['title'] }}</div>
                        <div class="vnd-step-desc">{{ $step['desc'] }}</div>
                        @if(!empty($step['date']))
                            <div class="vnd-step-date">
                                <i class="far fa-calendar-check mr-1"></i> {{ $step['date'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-5 p-3 rounded bg-light border text-muted small d-flex align-items-center">
                <i class="fas fa-info-circle text-primary mr-2 fa-lg"></i>
                <div>
                    <b>{{ __('current_status_info_colon') }}</b> {{ $statusInfo['desc'] }}
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         4. KONTEN DASHBOARD (Dokumen, Audit, Profil, Evaluasi & Bantuan)
         ═══════════════════════════════════════════════════════════════════ --}}

    {{-- BARIS 1: MONITORING OPERASIONAL (Dokumen Legalitas & Riwayat Audit) --}}
    <div class="row mb-6">
        {{-- Card Monitoring Dokumen --}}
        <div class="col-lg-6 mb-4 mb-lg-0 d-flex flex-column">
            <div class="vnd-card h-100 d-flex flex-column">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        <i class="fas fa-file-contract text-primary mr-1"></i>
                        {{ __('legal_docs_and_validity') }}
                    </div>
                    <div>
                        <span class="badge badge-light-primary font-weight-bolder">
                            {{ count($monitoredDocuments) }} {{ __('documents_count_unit') }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0 flex-grow-1 d-flex flex-column">
                    <div class="table-responsive flex-grow-1">
                        <table class="table tbl-vendor table-borderless table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('document_name') }}</th>
                                    <th>{{ __('issue_date') }}</th>
                                    <th>{{ __('validity_period') }}</th>
                                    <th>{{ __('status') }}</th>
                                    <th class="text-right">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monitoredDocuments as $doc)
                                    <tr>
                                        <td>
                                            <div class="vnd-vendor-name">{{ $doc->name }}</div>
                                            <div class="vnd-vendor-email">{{ $doc->doc_type }}</div>
                                        </td>
                                        <td>
                                            <span class="vnd-cell-muted">{{ $doc->issue_date }}</span>
                                        </td>
                                        <td>
                                            @if($doc->expiry_date !== '-')
                                                <span class="font-weight-bolder text-dark">{{ $doc->expiry_date }}</span>
                                                @if($doc->days_left !== null)
                                                    <span class="small d-block {{ $doc->days_left < 0 ? 'text-danger' : ($doc->days_left <= 60 ? 'text-warning' : 'text-muted') }}">
                                                        {{ $doc->days_left < 0 ? __('expired') : __('days_remaining', ['days' => $doc->days_left]) }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="vnd-cell-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($doc->status === 'valid')
                                                <span class="vnd-tag vnd-tag--verified">{{ __('status_valid') }}</span>
                                            @elseif($doc->status === 'expiring_soon')
                                                <span class="vnd-tag vnd-tag--medium">{{ __('status_expiring_soon') }}</span>
                                            @elseif($doc->status === 'expired')
                                                <span class="vnd-tag vnd-tag--high">{{ __('status_expired') }}</span>
                                            @elseif($doc->status === 'unuploaded')
                                                <span class="vnd-tag vnd-tag--muted">{{ __('status_not_uploaded') }}</span>
                                            @else
                                                <span class="vnd-tag vnd-tag--muted">{{ __('status_uploaded') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if($doc->file_url)
                                                <x-preview-doc-button
                                                    :url="$doc->file_url"
                                                    :title="$doc->name"
                                                    compact="true"
                                                />
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="vnd-empty py-5">
                                                <div class="vnd-empty-icon"><i class="fas fa-folder-open"></i></div>
                                                <div class="vnd-empty-title">{{ __('no_documents_uploaded_title') }}</div>
                                                <div class="vnd-empty-sub">{{ __('no_documents_uploaded_desc') }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Riwayat Audit --}}
        <div class="col-lg-6 d-flex flex-column">
            <div class="vnd-card h-100 d-flex flex-column">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        <i class="fas fa-clipboard-check text-primary mr-1"></i>
                        {{ __('audit_history_and_schedule') }}
                    </div>
                    @if($audits->isNotEmpty())
                        <a href="{{ route('vendor.audit.results') }}" class="vnd-btn-detail vnd-btn-detail--secondary">
                            {{ __('all_results') }} <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @endif
                </div>
                <div class="card-body p-0 flex-grow-1 d-flex flex-column">
                    @forelse($audits as $audit)
                        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
                            <div>
                                <div class="font-weight-bolder text-dark">
                                    {{ __('audit_label_with_type', ['type' => $audit->audit_type_label]) }}
                                    <span class="badge {{ $audit->status_cls }} ml-2">{{ $audit->status_label }}</span>
                                </div>
                                <div class="text-muted small mt-1">
                                    <i class="far fa-calendar-alt mr-1"></i> {{ $audit->schedule_date }}
                                </div>
                            </div>
                            <div>
                                @if($audit->can_fill_questionnaire)
                                    <a href="{{ route('vendor.audit.questionnaire', $audit->id) }}" class="btn btn-xs btn-primary font-weight-bold">
                                        {{ __('fill_questionnaire') }}
                                    </a>
                                @else
                                    <a href="{{ route('vendor.audit.results') }}" class="btn btn-xs btn-light font-weight-bold">
                                        {{ __('view') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="vnd-empty py-5 flex-grow-1 d-flex flex-column justify-content-center">
                            <div class="vnd-empty-icon"><i class="fas fa-calendar-times"></i></div>
                            <div class="vnd-empty-title">{{ __('no_audit_history_title') }}</div>
                            <div class="vnd-empty-sub">{{ __('no_audit_history_desc') }}</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 2: PROFIL PERUSAHAAN & EVALUASI KINERJA --}}
    <div class="row mb-6">
        {{-- Card Detail Profil Perusahaan --}}
        <div class="col-lg-6 mb-4 mb-lg-0 d-flex flex-column">
            <div class="vnd-card h-100 d-flex flex-column">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        <i class="fas fa-building text-primary mr-1"></i>
                        {{ __('company_brief_profile') }}
                    </div>
                    <a href="{{ route('registrasi.index') }}" class="vnd-btn-detail vnd-btn-detail--secondary">
                        {{ __('view_full_profile') }} <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-5 flex-grow-1">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">{{ __('official_company_name') }}</label>
                            <div class="font-weight-bolder text-dark">{{ $companyName }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">{{ __('business_entity_form') }}</label>
                            <div class="font-weight-bolder text-dark">{{ ($general && $general->badan_usaha) ? $general->badan_usaha : '—' }}</div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small mb-1">{{ __('office_factory_address') }}</label>
                            <div class="font-weight-normal text-dark">{{ ($general && $general->alamat_perusahaan) ? $general->alamat_perusahaan : '—' }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">{{ __('company_email') }}</label>
                            <div class="font-weight-normal text-dark">{{ ($general && $general->email_perusahaan) ? $general->email_perusahaan : $user->email }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">{{ __('office_phone') }}</label>
                            <div class="font-weight-normal text-dark">{{ ($general && $general->telepon_perusahaan) ? $general->telepon_perusahaan : '—' }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">{{ __('contact_person_pic') }}</label>
                            <div class="font-weight-bolder text-dark">
                                {{ ($general && $general->pic_nama) ? $general->pic_nama : '—' }}
                                @if($general && $general->pic_jabatan)
                                    <span class="text-muted font-weight-normal small">({{ $general->pic_jabatan }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">{{ __('pic_contact') }}</label>
                            <div class="font-weight-normal text-dark">
                                {{ ($general && $general->pic_telepon) ? $general->pic_telepon : '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card {{ __('vendor_performance_evaluation') }} --}}
        <div class="col-lg-6 d-flex flex-column">
            <div class="vnd-card h-100 d-flex flex-column">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        <i class="fas fa-award text-primary mr-1"></i>
                        {{ __('vendor_performance_evaluation') }}
                    </div>
                    @if($latestEvaluation)
                        <a href="{{ route('vendor.evaluasi.index') }}" class="vnd-btn-detail vnd-btn-detail--secondary">
                            {{ __('details') }} <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @endif
                </div>
                <div class="card-body p-5 flex-grow-1 d-flex flex-column justify-content-between">
                    @if($latestEvaluation)
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                                <div>
                                    <div class="text-muted small">{{ __('evaluation_for_year') }}</div>
                                    <div class="font-weight-bolder text-dark h4 mb-0">{{ __('year_num', ['year' => $latestEvaluation->year]) }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="badge {{ $latestEvaluation->category === 'BAIK' ? 'badge-success' : ($latestEvaluation->category === 'CUKUP' ? 'badge-warning' : 'badge-danger') }} font-weight-bolder px-3 py-2 h6 mb-0">
                                        {{ $latestEvaluation->category }} ({{ number_format($latestEvaluation->final_score, 1) }})
                                    </span>
                                </div>
                            </div>

                            <div class="metric-row">
                                <div class="metric-label">
                                    <span>{{ __('eval_delivery_accuracy') }}</span>
                                    <b>{{ number_format($latestEvaluation->delivery_score_avg, 1) }}</b>
                                </div>
                                <div class="vnd-progress-track">
                                    <div class="vnd-progress-fill" style="width: {{ min(100, $latestEvaluation->delivery_score_avg) }}%;"></div>
                                </div>
                            </div>

                            <div class="metric-row">
                                <div class="metric-label">
                                    <span>{{ __('eval_product_quality') }}</span>
                                    <b>{{ number_format($latestEvaluation->quality_score_avg, 1) }}</b>
                                </div>
                                <div class="vnd-progress-track">
                                    <div class="vnd-progress-fill" style="width: {{ min(100, $latestEvaluation->quality_score_avg) }}%;"></div>
                                </div>
                            </div>

                            <div class="metric-row">
                                <div class="metric-label">
                                    <span>{{ __('eval_goods_quantity') }}</span>
                                    <b>{{ number_format($latestEvaluation->quantity_score_avg, 1) }}</b>
                                </div>
                                <div class="vnd-progress-track">
                                    <div class="vnd-progress-fill" style="width: {{ min(100, $latestEvaluation->quantity_score_avg) }}%;"></div>
                                </div>
                            </div>

                            <div class="metric-row">
                                <div class="metric-label">
                                    <span>{{ __('eval_complaint_handling') }}</span>
                                    <b>{{ number_format($latestEvaluation->complain_score_avg, 1) }}</b>
                                </div>
                                <div class="vnd-progress-track">
                                    <div class="vnd-progress-fill" style="width: {{ min(100, $latestEvaluation->complain_score_avg) }}%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-right">
                            <a href="{{ route('vendor.evaluasi.index', ['year' => $latestEvaluation->year]) }}" class="btn btn-sm btn-light-primary font-weight-bold">
                                <i class="fas fa-file-alt mr-1"></i> {{ __('open_full_evaluation_sheet') }}
                            </a>
                        </div>
                    @else
                        <div class="vnd-empty py-5 my-auto">
                            <div class="vnd-empty-icon"><i class="fas fa-chart-line"></i></div>
                            <div class="vnd-empty-title">{{ __('no_evaluation_report_title') }}</div>
                            <div class="vnd-empty-sub">{{ __('no_evaluation_report_desc') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 3: BANTUAN & KONTAK PENGADAAN --}}
    <div class="row">
        <div class="col-12 mb-6">
            <div class="vnd-card">
                <div class="card-body p-5 p-md-6">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                        <div class="d-flex align-items-center mb-4 mb-lg-0 mr-lg-4">
                            <div class="btn btn-icon btn-light-primary btn-circle mr-4 flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="fas fa-question-circle fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-1">
                                    {{ __('help_and_procurement_contact') }}
                                </h6>
                                <p class="text-muted small mb-0">
                                    {{ __('help_procurement_question_desc') }}
                                </p>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-between justify-content-lg-end" style="gap: 1.5rem;">
                            <div class="d-flex align-items-center">
                                <div class="btn btn-icon btn-xs btn-light-primary mr-3">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.72rem; font-weight: 600;">{{ __('procurement_email') }}</div>
                                    <a href="mailto:pengadaan@phapros.co.id" class="font-weight-bolder text-dark small">pengadaan@phapros.co.id</a>
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <div class="btn btn-icon btn-xs btn-light-success mr-3">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.72rem; font-weight: 600;">{{ __('head_office_phone') }}</div>
                                    <span class="font-weight-bolder text-dark small">(024) 7604616 ({{ __('ext_procurement') }})</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center" style="gap: 0.5rem;">
                                <a href="{{ route('tutorial') }}" class="btn btn-sm btn-light font-weight-bolder px-4">
                                    <i class="fas fa-book-open mr-1"></i> {{ __('user_guide') }}
                                </a>
                                <a href="{{ route('contact') }}" class="btn btn-sm btn-primary font-weight-bolder px-4">
                                    <i class="fas fa-headset mr-1"></i> {{ __('contact') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Preview Dokumen Reusable --}}
<x-document-preview />
@endsection
