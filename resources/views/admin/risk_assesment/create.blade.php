@extends('layouts.app', ['title' => 'Risk Assessment Pemasok Baru'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Risk Assessment')
@section('page_title', 'Risk Assessment Pemasok Baru')
@section('page_desc', 'Formulir risk assessment supplier baru sesuai format QA 2026.')

@push('style')
    <style>
        /* ════════════════════════════════════════════════
           TOKENS
        ════════════════════════════════════════════════ */
        :root {
            --ra-blue: #005db6;
            --ra-blue-lt: #e8f1fb;
            --ra-blue-bd: #c2d9f5;
            --ra-blue-mid: #1a73e8;
            --ra-green: #2e7d32;
            --ra-green-lt: #e8f5e9;
            --ra-green-bd: #a5d6a7;
            --ra-amber: #b45309;
            --ra-amber-lt: #fff8e7;
            --ra-amber-bd: #fcd97a;
            --ra-red: #c62828;
            --ra-red-lt: #fdecea;
            --ra-red-bd: #ef9a9a;
            --ra-ink: #1a2235;
            --ra-ink2: #3d4a63;
            --ra-muted: #6b7a96;
            --ra-border: #e6eaf2;
            --ra-surface: #f5f7fb;
            --ra-white: #ffffff;
            --ra-radius: 10px;
            --ra-radius-sm: 6px;
        }

        /* ════════════════════════════════════════════════
           LAYOUT
        ════════════════════════════════════════════════ */
        .ra-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 1.25rem;
            align-items: start;
        }

        @media (max-width: 991.98px) {
            .ra-layout {
                grid-template-columns: 1fr;
            }
        }

        /* ════════════════════════════════════════════════
           HEADER CARD
        ════════════════════════════════════════════════ */
        .ra-header {
            border: 1.5px solid var(--ra-border);
            border-radius: 12px;
            background: var(--ra-white);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .ra-header-top {
            padding: 1.1rem 1.4rem;
            background: linear-gradient(135deg, #f8faff, var(--ra-white));
            border-bottom: 1.5px solid var(--ra-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .8rem;
        }

        .ra-vendor-name {
            font-size: 1rem;
            font-weight: 800;
            color: var(--ra-ink);
            margin-bottom: .2rem;
        }

        .ra-vendor-sub {
            font-size: .76rem;
            color: var(--ra-muted);
        }

        .ra-appnum {
            font-family: 'SFMono-Regular', Consolas, monospace;
            font-size: .68rem;
            font-weight: 700;
            color: var(--ra-blue);
            background: var(--ra-blue-lt);
            border: 1.5px solid var(--ra-blue-bd);
            border-radius: 5px;
            padding: .18rem .55rem;
            display: inline-block;
            margin-bottom: .35rem;
        }

        .ra-header-meta {
            padding: .65rem 1.4rem;
            background: var(--ra-surface);
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            align-items: center;
        }

        .ra-meta-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 5px;
            padding: .22rem .65rem;
            font-size: .68rem;
            font-weight: 700;
            background: var(--ra-white);
            border: 1.5px solid var(--ra-border);
            color: var(--ra-ink2);
        }

        .ra-meta-chip-lbl {
            color: var(--ra-muted);
            margin-right: .15rem;
        }

        .ra-back-btn {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .9rem;
            border-radius: var(--ra-radius-sm);
            font-size: .78rem;
            font-weight: 700;
            background: var(--ra-blue-lt);
            color: var(--ra-blue);
            border: 1.5px solid var(--ra-blue-bd);
            text-decoration: none;
            transition: background .15s;
        }

        .ra-back-btn:hover {
            background: #d4e8fb;
            text-decoration: none;
            color: var(--ra-blue);
        }

        /* ════════════════════════════════════════════════
           FORMULA INFO BANNER
        ════════════════════════════════════════════════ */
        .ra-formula-banner {
            border: 1.5px solid var(--ra-blue-bd);
            border-left: 4px solid var(--ra-blue);
            border-radius: var(--ra-radius-sm);
            padding: .85rem 1.1rem;
            background: var(--ra-blue-lt);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: .75rem;
        }

        .ra-formula-banner-icon {
            color: var(--ra-blue);
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: .1rem;
        }

        .ra-formula-banner-text {
            font-size: .8rem;
            color: var(--ra-ink);
            line-height: 1.55;
        }

        .ra-formula-tag {
            display: inline-flex;
            align-items: center;
            background: var(--ra-white);
            border: 1.5px solid var(--ra-blue-bd);
            border-radius: 4px;
            padding: .15rem .5rem;
            font-size: .75rem;
            font-weight: 800;
            color: var(--ra-blue);
            font-family: 'SFMono-Regular', Consolas, monospace;
            margin: 0 .15rem;
        }

        .ra-threshold-row {
            display: flex;
            gap: .4rem;
            flex-wrap: wrap;
            margin-top: .5rem;
        }

        .ra-thr {
            display: inline-block;
            border-radius: 4px;
            padding: .18rem .55rem;
            font-size: .68rem;
            font-weight: 700;
        }

        .ra-thr--low {
            background: var(--ra-green-lt);
            color: var(--ra-green);
            border: 1px solid var(--ra-green-bd);
        }

        .ra-thr--medium {
            background: var(--ra-amber-lt);
            color: var(--ra-amber);
            border: 1px solid var(--ra-amber-bd);
        }

        .ra-thr--high {
            background: var(--ra-red-lt);
            color: var(--ra-red);
            border: 1px solid var(--ra-red-bd);
        }

        /* ════════════════════════════════════════════════
           FORM CARD (MAIN)
        ════════════════════════════════════════════════ */
        .ra-form-card {
            border: 1.5px solid var(--ra-border);
            border-radius: 12px;
            background: var(--ra-white);
            overflow: hidden;
        }

        /* ── Section header ─────────────────────────── */
        .ra-section-head {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .8rem 1.25rem;
            background: var(--ra-surface);
            border-bottom: 1.5px solid var(--ra-border);
        }

        .ra-section-letter {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
            font-weight: 800;
            flex-shrink: 0;
            color: #fff;
        }

        .ra-section-letter--a {
            background: #005db6;
        }

        .ra-section-letter--b {
            background: #0f766e;
        }

        .ra-section-letter--c {
            background: #7c3aed;
        }

        .ra-section-letter--d {
            background: #b45309;
        }

        .ra-section-letter--e {
            background: #374151;
        }

        .ra-section-title {
            font-size: .86rem;
            font-weight: 800;
            color: var(--ra-ink);
        }

        .ra-section-sub {
            font-size: .7rem;
            color: var(--ra-muted);
            margin-top: .1rem;
        }

        .ra-section-score-pill {
            margin-left: auto;
            background: var(--ra-blue-lt);
            color: var(--ra-blue);
            border: 1.5px solid var(--ra-blue-bd);
            border-radius: 20px;
            padding: .2rem .75rem;
            font-size: .72rem;
            font-weight: 700;
            white-space: nowrap;
        }

        /* ── Section body ───────────────────────────── */
        .ra-section-body {
            padding: 1.1rem 1.25rem;
        }

        /* ── Field rows ─────────────────────────────── */
        .ra-field {
            margin-bottom: 1.1rem;
        }

        .ra-field:last-child {
            margin-bottom: 0;
        }

        .ra-field-label {
            font-size: .76rem;
            font-weight: 700;
            color: var(--ra-ink);
            margin-bottom: .35rem;
            display: block;
        }

        .ra-field-label .ra-required {
            color: var(--ra-red);
            margin-left: 2px;
        }

        /* readonly display */
        .ra-readonly {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .55rem .9rem;
            background: var(--ra-surface);
            border: 1.5px solid var(--ra-border);
            border-radius: var(--ra-radius-sm);
            font-size: .82rem;
            color: var(--ra-ink2);
            gap: .5rem;
        }

        .ra-readonly-score {
            flex-shrink: 0;
            background: var(--ra-blue-lt);
            color: var(--ra-blue);
            border: 1px solid var(--ra-blue-bd);
            border-radius: 4px;
            padding: .12rem .5rem;
            font-size: .68rem;
            font-weight: 800;
            font-family: 'SFMono-Regular', Consolas, monospace;
        }

        .ra-hint {
            font-size: .7rem;
            color: var(--ra-muted);
            margin-top: .28rem;
            line-height: 1.4;
        }

        /* select */
        .ra-select {
            width: 100%;
            height: 38px;
            border: 1.5px solid var(--ra-border);
            border-radius: var(--ra-radius-sm);
            font-size: .82rem;
            color: var(--ra-ink);
            padding: 0 .75rem;
            background: var(--ra-white);
            transition: border-color .15s, box-shadow .15s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7a96' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            padding-right: 2.2rem;
        }

        .ra-select:focus {
            outline: none;
            border-color: var(--ra-blue);
            box-shadow: 0 0 0 3px rgba(0, 93, 182, .1);
        }

        .ra-select.is-invalid {
            border-color: var(--ra-red);
        }

        /* textarea */
        .ra-textarea {
            width: 100%;
            border: 1.5px solid var(--ra-border);
            border-radius: var(--ra-radius-sm);
            font-size: .82rem;
            color: var(--ra-ink);
            padding: .6rem .9rem;
            resize: vertical;
            font-family: inherit;
            transition: border-color .15s, box-shadow .15s;
        }

        .ra-textarea:focus {
            outline: none;
            border-color: var(--ra-blue);
            box-shadow: 0 0 0 3px rgba(0, 93, 182, .1);
        }

        /* ── Radio cards ─────────────────────────────── */
        .ra-radio-group {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .ra-radio-card {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem .9rem;
            border: 1.5px solid var(--ra-border);
            border-radius: var(--ra-radius-sm);
            cursor: pointer;
            transition: border-color .15s, background .15s;
            user-select: none;
        }

        .ra-radio-card:hover {
            border-color: var(--ra-blue);
            background: var(--ra-blue-lt);
        }

        .ra-radio-card input[type="radio"] {
            display: none;
        }

        .ra-radio-card-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid var(--ra-border);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color .15s, background .15s;
            background: var(--ra-white);
        }

        .ra-radio-card-dot::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: transparent;
            transition: background .15s;
        }

        .ra-radio-card-label {
            font-size: .82rem;
            color: var(--ra-ink);
            flex: 1;
            line-height: 1.4;
        }

        .ra-radio-card-score {
            flex-shrink: 0;
            border-radius: 4px;
            padding: .12rem .45rem;
            font-size: .68rem;
            font-weight: 800;
            font-family: 'SFMono-Regular', Consolas, monospace;
        }

        /* selected state — set by JS */
        .ra-radio-card.selected-risk {
            border-color: var(--ra-red-bd);
            background: var(--ra-red-lt);
        }

        .ra-radio-card.selected-risk .ra-radio-card-dot {
            border-color: var(--ra-red);
        }

        .ra-radio-card.selected-risk .ra-radio-card-dot::after {
            background: var(--ra-red);
        }

        .ra-radio-card.selected-risk .ra-radio-card-score {
            background: var(--ra-red-lt);
            color: var(--ra-red);
            border: 1px solid var(--ra-red-bd);
        }

        .ra-radio-card.selected-safe {
            border-color: var(--ra-green-bd);
            background: var(--ra-green-lt);
        }

        .ra-radio-card.selected-safe .ra-radio-card-dot {
            border-color: var(--ra-green);
        }

        .ra-radio-card.selected-safe .ra-radio-card-dot::after {
            background: var(--ra-green);
        }

        .ra-radio-card.selected-safe .ra-radio-card-score {
            background: var(--ra-green-lt);
            color: var(--ra-green);
            border: 1px solid var(--ra-green-bd);
        }

        .ra-radio-card.selected-neutral {
            border-color: var(--ra-blue-bd);
            background: var(--ra-blue-lt);
        }

        .ra-radio-card.selected-neutral .ra-radio-card-dot {
            border-color: var(--ra-blue);
        }

        .ra-radio-card.selected-neutral .ra-radio-card-dot::after {
            background: var(--ra-blue);
        }

        .ra-radio-card.selected-neutral .ra-radio-card-score {
            background: var(--ra-blue-lt);
            color: var(--ra-blue);
            border: 1px solid var(--ra-blue-bd);
        }

        /* default score chip color */
        .ra-radio-card:not(.selected-risk):not(.selected-safe):not(.selected-neutral) .ra-radio-card-score {
            background: var(--ra-surface);
            color: var(--ra-muted);
            border: 1px solid var(--ra-border);
        }

        /* section divider */
        .ra-section-divider {
            height: 1.5px;
            background: var(--ra-border);
            margin: 0;
        }

        /* ════════════════════════════════════════════════
           STICKY SCORE SIDEBAR
        ════════════════════════════════════════════════ */
        .ra-sidebar {
            position: sticky;
            top: 80px;
        }

        .ra-score-card {
            border: 1.5px solid var(--ra-border);
            border-radius: 12px;
            background: var(--ra-white);
            overflow: hidden;
        }

        .ra-score-head {
            padding: .75rem 1.1rem;
            border-bottom: 1.5px solid var(--ra-border);
            background: var(--ra-surface);
        }

        .ra-score-head-title {
            font-size: .72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .55px;
            color: var(--ra-ink);
            display: flex;
            align-items: center;
            gap: .45rem;
        }

        .ra-score-head-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--ra-blue);
            flex-shrink: 0;
        }

        .ra-score-body {
            padding: 1rem 1.1rem;
        }

        /* big total number */
        .ra-total-wrap {
            text-align: center;
            padding: .85rem 0 1rem;
        }

        .ra-total-num {
            font-size: 3rem;
            font-weight: 900;
            line-height: 1;
            color: var(--ra-blue);
            font-family: 'SFMono-Regular', Consolas, monospace;
            transition: color .3s;
        }

        .ra-total-lbl {
            font-size: .68rem;
            font-weight: 700;
            color: var(--ra-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-top: .3rem;
        }

        /* formula breakdown */
        .ra-formula-breakdown {
            background: var(--ra-surface);
            border-radius: var(--ra-radius-sm);
            padding: .65rem .85rem;
            margin-bottom: .85rem;
            font-size: .74rem;
            color: var(--ra-muted);
            text-align: center;
            line-height: 1.8;
        }

        .ra-formula-val {
            display: inline-block;
            font-weight: 800;
            color: var(--ra-ink);
            font-family: 'SFMono-Regular', Consolas, monospace;
            min-width: 22px;
            text-align: center;
        }

        /* sub score rows */
        .ra-sub-rows {
            margin-bottom: .85rem;
        }

        .ra-sub-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .45rem 0;
            border-bottom: 1px dashed var(--ra-border);
        }

        .ra-sub-row:last-child {
            border-bottom: none;
        }

        .ra-sub-row-lbl {
            font-size: .72rem;
            color: var(--ra-muted);
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        .ra-sub-row-letter {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .6rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .ra-sub-row-val {
            font-size: .78rem;
            font-weight: 800;
            color: var(--ra-ink);
            font-family: 'SFMono-Regular', Consolas, monospace;
        }

        /* risk result badges */
        .ra-risk-result {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border-radius: var(--ra-radius-sm);
            padding: .6rem;
            font-size: .75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: .5rem;
            transition: all .3s;
        }

        .ra-risk-result--none {
            background: var(--ra-surface);
            color: var(--ra-muted);
        }

        .ra-risk-result--low {
            background: var(--ra-green-lt);
            color: var(--ra-green);
            border: 1.5px solid var(--ra-green-bd);
        }

        .ra-risk-result--medium {
            background: var(--ra-amber-lt);
            color: var(--ra-amber);
            border: 1.5px solid var(--ra-amber-bd);
        }

        .ra-risk-result--high {
            background: var(--ra-red-lt);
            color: var(--ra-red);
            border: 1.5px solid var(--ra-red-bd);
        }

        .ra-action-result {
            display: flex;
            align-items: flex-start;
            gap: .5rem;
            border-radius: var(--ra-radius-sm);
            padding: .55rem .75rem;
            font-size: .74rem;
            font-weight: 600;
            color: var(--ra-ink2);
            background: var(--ra-surface);
            border: 1.5px solid var(--ra-border);
            transition: all .3s;
            line-height: 1.4;
        }

        .ra-action-result-icon {
            flex-shrink: 0;
            margin-top: .1rem;
        }

        /* progress bar (visual indicator kelengkapan form) */
        .ra-form-progress {
            margin-bottom: .85rem;
        }

        .ra-form-progress-lbl {
            display: flex;
            justify-content: space-between;
            font-size: .68rem;
            color: var(--ra-muted);
            margin-bottom: .3rem;
        }

        .ra-form-progress-bar-wrap {
            height: 5px;
            background: var(--ra-border);
            border-radius: 99px;
            overflow: hidden;
        }

        .ra-form-progress-bar {
            height: 100%;
            background: var(--ra-blue);
            border-radius: 99px;
            transition: width .3s;
        }

        /* submit button */
        .ra-btn-submit {
            width: 100%;
            height: 42px;
            border-radius: var(--ra-radius-sm);
            border: none;
            font-size: .84rem;
            font-weight: 800;
            background: var(--ra-blue);
            color: #fff;
            cursor: pointer;
            transition: background .15s, box-shadow .15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
        }

        .ra-btn-submit:hover:not(:disabled) {
            background: #004f9e;
            box-shadow: 0 4px 12px rgba(0, 93, 182, .3);
        }

        .ra-btn-submit:disabled {
            background: #b0c8e8;
            cursor: not-allowed;
        }

        .ra-btn-cancel {
            width: 100%;
            height: 36px;
            border-radius: var(--ra-radius-sm);
            font-size: .78rem;
            font-weight: 700;
            background: var(--ra-white);
            color: var(--ra-muted);
            border: 1.5px solid var(--ra-border);
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: .5rem;
            transition: border-color .15s, color .15s;
        }

        .ra-btn-cancel:hover {
            border-color: var(--ra-blue);
            color: var(--ra-blue);
            text-decoration: none;
        }

        /* invalid feedback */
        .ra-invalid {
            font-size: .72rem;
            color: var(--ra-red);
            margin-top: .3rem;
        }
    </style>
@endpush

@section('content')
    @php
        $vendorName = optional($application->general)->nama_perusahaan ?? $application->user->name;
        $vendorEmail = optional($application->general)->email_perusahaan ?? $application->user->email;
        $vendorAddress = optional($application->general)->alamat_perusahaan ?? '-';
        $categoryLabels = $application->categories
            ->pluck('category_id')
            ->map(function ($id) {
                return \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id;
            })
            ->implode(', ');
        $supplierType = $autoScores['supplier_type_label'];
        $actionLabel =
            optional($qualification)->audit_type === 'on_site'
                ? 'Audit On Site'
                : (optional($qualification)->audit_type === 'on_desk'
                    ? 'Desk Evaluation / Document / Questionnaire'
                    : 'Qualified');

        $selAttr = old('score_safety_efficacy_attr', optional($qualification)->score_safety_efficacy_attr);
        $selCountry = old('score_detectability_country', optional($qualification)->score_detectability_country);
        $selWarning = old('score_detectability_warning', optional($qualification)->score_detectability_warning);
        $selFunction = old('score_probability_function', optional($qualification)->score_probability_function);
    @endphp

    {{-- Flash errors --}}
    @if ($errors->any())
        <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
            <div class="alert-icon"><i class="flaticon-warning"></i></div>
            <div class="alert-text">{{ $errors->first() }}</div>
            <div class="alert-close"><button type="button" class="close"
                    data-dismiss="alert"><span>&times;</span></button></div>
        </div>
    @endif

    {{-- ════ VENDOR HEADER ════ --}}
    <div class="ra-header">
        <div class="ra-header-top">
            <div class="d-flex align-items-start" style="gap:.85rem;">
                <a href="{{ route('qa.risk-assessment.index') }}" class="ra-back-btn" title="Kembali">
                    <i class="ki ki-arrow-back" style="font-size:.8rem;"></i> Kembali
                </a>
                <div>
                    <span class="ra-appnum">{{ $application->application_number ?? '-' }}</span>
                    <div class="ra-vendor-name">{{ $vendorName }}</div>
                    <div class="ra-vendor-sub">{{ $vendorEmail }}</div>
                </div>
            </div>
            <div style="text-align:right;">
                <div
                    style="font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--ra-muted); margin-bottom:.3rem;">
                    Formulir</div>
                <div style="font-size:.84rem; font-weight:800; color:var(--ra-ink);">Risk Assessment QA 2026</div>
            </div>
        </div>
        <div class="ra-header-meta">
            <span class="ra-meta-chip">
                <span class="ra-meta-chip-lbl">Alamat</span>{{ $vendorAddress }}
            </span>
            <span class="ra-meta-chip">
                <span class="ra-meta-chip-lbl">Jenis</span>
                {{ $supplierType }}
                <span
                    style="font-family:monospace; font-weight:800; color:var(--ra-blue);">({{ $autoScores['supplier_type_score'] }})</span>
            </span>
            @if ($categoryLabels)
                <span class="ra-meta-chip">
                    <span class="ra-meta-chip-lbl">Kategori</span>{{ $categoryLabels }}
                </span>
            @endif
        </div>
    </div>

    {{-- ════ FORMULA INFO ════ --}}
    <div class="ra-formula-banner">
        <i class="flaticon-info ra-formula-banner-icon"></i>
        <div class="ra-formula-banner-text">
            <strong>Rumus Total Nilai:</strong>
            <span class="ra-formula-tag">(A + B) × (C + D)</span>
            = (Safety Efficacy + Availability) × (Detectability + Probability)
            <div class="ra-threshold-row">
                <span class="ra-thr ra-thr--low">Low: 12–{{ $lowThreshold }}</span>
                <span class="ra-thr ra-thr--medium">Medium: {{ $lowThreshold + 1 }}–{{ $highThreshold }}</span>
                <span class="ra-thr ra-thr--high">High: > {{ $highThreshold }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('qa.risk-assessment.store', $application->id) }}" method="POST" id="form-risk-assessment">
        @csrf
        <input type="hidden" id="auto_score_doc" value="{{ $autoScores['doc_score'] }}">
        <input type="hidden" id="auto_score_trace" value="{{ $autoScores['traceability_score'] }}">
        <input type="hidden" id="auto_score_type" value="{{ $autoScores['supplier_type_score'] }}">
        <input type="hidden" name="vendor_application_id" value="{{ $application->id }}">

        <div class="ra-layout">

            {{-- ════ MAIN FORM COLUMN ════ --}}
            <div>
                <div class="ra-form-card">

                    {{-- ── SECTION A: Safety Efficacy ─────────────────── --}}
                    <div class="ra-section-head">
                        <div class="ra-section-letter ra-section-letter--a">A</div>
                        <div>
                            <div class="ra-section-title">Safety Efficacy</div>
                            <div class="ra-section-sub">Severity — Keamanan & kelengkapan dokumen</div>
                        </div>
                        <div class="ra-section-score-pill">Skor A: <span id="display_score_a"
                                style="font-family:monospace;font-weight:800;">0</span></div>
                    </div>
                    <div class="ra-section-body">

                        {{-- Kelengkapan Dokumen (auto) --}}
                        <div class="ra-field">
                            <label class="ra-field-label">Kelengkapan Dokumen <span
                                    style="font-size:.68rem;font-weight:600;color:var(--ra-muted);">(otomatis)</span></label>
                            <div class="ra-readonly">
                                <span>{{ $autoScores['doc_label'] }}</span>
                                <span class="ra-readonly-score">Skor {{ $autoScores['doc_score'] }}</span>
                            </div>
                            <div class="ra-hint">Dokumen lengkap: 1 · Kurang lengkap: 3 · Tidak lengkap / N/A: 4</div>
                        </div>

                        {{-- Critical Attribute (manual radio) --}}
                        <div class="ra-field">
                            <label class="ra-field-label">Bahan memiliki critical attribute <span
                                    class="ra-required">*</span></label>
                            <div class="ra-radio-group">
                                <label class="ra-radio-card {{ (string) $selAttr === '8' ? 'selected-risk' : '' }}"
                                    data-group="score_safety_efficacy_attr" data-val="8" data-risk-class="selected-risk">
                                    <input type="radio" name="score_safety_efficacy_attr" value="8"
                                        class="calc-trigger" {{ (string) $selAttr === '8' ? 'checked' : '' }} required>
                                    <div class="ra-radio-card-dot"></div>
                                    <span class="ra-radio-card-label">Memiliki critical attribute</span>
                                    <span class="ra-radio-card-score">8</span>
                                </label>
                                <label class="ra-radio-card {{ (string) $selAttr === '1' ? 'selected-safe' : '' }}"
                                    data-group="score_safety_efficacy_attr" data-val="1" data-risk-class="selected-safe">
                                    <input type="radio" name="score_safety_efficacy_attr" value="1"
                                        class="calc-trigger" {{ (string) $selAttr === '1' ? 'checked' : '' }}>
                                    <div class="ra-radio-card-dot"></div>
                                    <span class="ra-radio-card-label">Tidak memiliki critical attribute</span>
                                    <span class="ra-radio-card-score">1</span>
                                </label>
                            </div>
                            @error('score_safety_efficacy_attr')
                                <div class="ra-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="ra-section-divider"></div>

                    {{-- ── SECTION B: Availability ─────────────────────── --}}
                    <div class="ra-section-head">
                        <div class="ra-section-letter ra-section-letter--b">B</div>
                        <div>
                            <div class="ra-section-title">Availability</div>
                            <div class="ra-section-sub">Severity — Ketersediaan & traceability supply chain</div>
                        </div>
                        <div class="ra-section-score-pill">Skor B: <span id="display_score_b"
                                style="font-family:monospace;font-weight:800;">0</span></div>
                    </div>
                    <div class="ra-section-body">

                        {{-- Traceability (auto) --}}
                        <div class="ra-field">
                            <label class="ra-field-label">Traceability Supply Chain <span
                                    style="font-size:.68rem;font-weight:600;color:var(--ra-muted);">(otomatis)</span></label>
                            <div class="ra-readonly">
                                <span>{{ $autoScores['traceability_label'] }}</span>
                                <span class="ra-readonly-score">Skor {{ $autoScores['traceability_score'] }}</span>
                            </div>
                            <div class="ra-hint">Lengkap: 1 · Tidak lengkap: 4</div>
                        </div>

                        {{-- Jenis Pemasok (auto) --}}
                        <div class="ra-field">
                            <label class="ra-field-label">Jenis Pemasok <span
                                    style="font-size:.68rem;font-weight:600;color:var(--ra-muted);">(otomatis)</span></label>
                            <div class="ra-readonly">
                                <span>{{ $supplierType }}</span>
                                <span class="ra-readonly-score">Skor {{ $autoScores['supplier_type_score'] }}</span>
                            </div>
                            <div class="ra-hint">Manufaktur: 1 · Distributor: 2 · Repacker: 3 · Trader: 4</div>
                        </div>

                    </div>
                    <div class="ra-section-divider"></div>

                    {{-- ── SECTION C: Detectability ─────────────────────── --}}
                    <div class="ra-section-head">
                        <div class="ra-section-letter ra-section-letter--c">C</div>
                        <div>
                            <div class="ra-section-title">Detectability</div>
                            <div class="ra-section-sub">Risiko regulasi & riwayat audit</div>
                        </div>
                        <div class="ra-section-score-pill">Skor C: <span id="display_score_c"
                                style="font-family:monospace;font-weight:800;">0</span></div>
                    </div>
                    <div class="ra-section-body">

                        {{-- Country Risk --}}
                        <div class="ra-field">
                            <label class="ra-field-label" for="score_detectability_country">Country / Regulatory Risk
                                <span class="ra-required">*</span></label>
                            <select name="score_detectability_country" id="score_detectability_country"
                                class="ra-select calc-trigger select2 @error('score_detectability_country') is-invalid @enderror"
                                required>
                                <option value="">— Pilih risiko negara —</option>
                                <option value="4" {{ (string) $selCountry === '4' ? 'selected' : '' }}>Negara high
                                    risk / regulasi lemah</option>
                                <option value="3" {{ (string) $selCountry === '3' ? 'selected' : '' }}>Negara
                                    berkembang / kontrol terbatas</option>
                                <option value="2" {{ (string) $selCountry === '2' ? 'selected' : '' }}>Negara dengan
                                    regulasi menengah</option>
                                <option value="1" {{ (string) $selCountry === '1' ? 'selected' : '' }}>Negara dengan
                                    otoritas kuat / FDA</option>
                            </select>
                            @error('score_detectability_country')
                                <div class="ra-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Warning Letter --}}
                        <div class="ra-field">
                            <label class="ra-field-label">Warning Letter / Hasil Audit <span
                                    class="ra-required">*</span></label>
                            <div class="ra-radio-group">
                                <label class="ra-radio-card {{ (string) $selWarning === '4' ? 'selected-risk' : '' }}"
                                    data-group="score_detectability_warning" data-val="4"
                                    data-risk-class="selected-risk">
                                    <input type="radio" name="score_detectability_warning" value="4"
                                        class="calc-trigger" {{ (string) $selWarning === '4' ? 'checked' : '' }} required>
                                    <div class="ra-radio-card-dot"></div>
                                    <span class="ra-radio-card-label">Ada warning letter / hasil audit buruk</span>
                                    <span class="ra-radio-card-score">4</span>
                                </label>
                                <label class="ra-radio-card {{ (string) $selWarning === '1' ? 'selected-safe' : '' }}"
                                    data-group="score_detectability_warning" data-val="1"
                                    data-risk-class="selected-safe">
                                    <input type="radio" name="score_detectability_warning" value="1"
                                        class="calc-trigger" {{ (string) $selWarning === '1' ? 'checked' : '' }}>
                                    <div class="ra-radio-card-dot"></div>
                                    <span class="ra-radio-card-label">Tidak ada warning letter</span>
                                    <span class="ra-radio-card-score">1</span>
                                </label>
                            </div>
                            @error('score_detectability_warning')
                                <div class="ra-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="ra-section-divider"></div>

                    {{-- ── SECTION D: Probability ───────────────────────── --}}
                    <div class="ra-section-head">
                        <div class="ra-section-letter ra-section-letter--d">D</div>
                        <div>
                            <div class="ra-section-title">Probability</div>
                            <div class="ra-section-sub">Fungsi bahan dalam produk</div>
                        </div>
                        <div class="ra-section-score-pill">Skor D: <span id="display_score_d"
                                style="font-family:monospace;font-weight:800;">0</span></div>
                    </div>
                    <div class="ra-section-body">

                        <div class="ra-field">
                            <label class="ra-field-label" for="score_probability_function">Fungsi Bahan <span
                                    class="ra-required">*</span></label>
                            <select name="score_probability_function" id="score_probability_function"
                                class="ra-select calc-trigger select2 @error('score_probability_function') is-invalid @enderror"
                                required>
                                <option value="">— Pilih fungsi bahan —</option>
                                <option value="4" {{ (string) $selFunction === '4' ? 'selected' : '' }}>API (bahan
                                    aktif utama)</option>
                                <option value="3" {{ (string) $selFunction === '3' ? 'selected' : '' }}>Eksipien /
                                    Primary Packaging</option>
                                <option value="2" {{ (string) $selFunction === '2' ? 'selected' : '' }}>Secondary
                                    Packaging</option>
                                <option value="1" {{ (string) $selFunction === '1' ? 'selected' : '' }}>Non Kontak
                                    Produk</option>
                            </select>
                            @error('score_probability_function')
                                <div class="ra-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="ra-section-divider"></div>

                    {{-- ── SECTION E: Keterangan QA ─────────────────────── --}}
                    <div class="ra-section-head">
                        <div class="ra-section-letter ra-section-letter--e">+</div>
                        <div>
                            <div class="ra-section-title">Keterangan QA</div>
                            <div class="ra-section-sub">Opsional — Manager & catatan tambahan</div>
                        </div>
                    </div>
                    <div class="ra-section-body">

                        <div class="ra-field">
                            <label class="ra-field-label" for="qa_manager_id">Manager QA</label>
                            <select name="qa_manager_id" id="qa_manager_id"
                                class="ra-select select2 @error('qa_manager_id') is-invalid @enderror">
                                <option value="">— Pilih Manager QA jika diperlukan —</option>
                                @foreach ($qaManagers as $manager)
                                    <option value="{{ $manager->id }}"
                                        {{ (string) old('qa_manager_id', optional($qualification)->qa_manager_id) === (string) $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}{{ $manager->email ? ' — ' . $manager->email : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('qa_manager_id')
                                <div class="ra-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="ra-field">
                            <label class="ra-field-label" for="notes">Keterangan / Catatan</label>
                            <textarea name="notes" id="notes" rows="4" class="ra-textarea @error('notes') is-invalid @enderror"
                                placeholder="Masukkan keterangan penilaian jika ada…">{{ old('notes', optional($qualification)->notes) }}</textarea>
                            @error('notes')
                                <div class="ra-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                </div>{{-- /ra-form-card --}}
            </div>

            {{-- ════ SIDEBAR: LIVE SCORE ════ --}}
            <div class="ra-sidebar">
                <div class="ra-score-card">
                    <div class="ra-score-head">
                        <div class="ra-score-head-title">
                            <span class="ra-score-head-dot"></span>
                            Skor Risiko
                        </div>
                    </div>
                    <div class="ra-score-body">

                        {{-- Progress kelengkapan --}}
                        <div class="ra-form-progress">
                            <div class="ra-form-progress-lbl">
                                <span>Kelengkapan Form</span>
                                <span id="progress_text">0/4</span>
                            </div>
                            <div class="ra-form-progress-bar-wrap">
                                <div class="ra-form-progress-bar" id="progress_bar" style="width:0%"></div>
                            </div>
                        </div>

                        {{-- Total Nilai --}}
                        <div class="ra-total-wrap">
                            <div class="ra-total-num" id="display_total_score">
                                {{ optional($qualification)->total_score ?? 0 }}
                            </div>
                            <div class="ra-total-lbl">Total Nilai</div>
                        </div>

                        {{-- Breakdown formula --}}
                        <div class="ra-formula-breakdown">
                            (<span class="ra-formula-val" id="bd_a">0</span> + <span class="ra-formula-val"
                                id="bd_b">0</span>)
                            &times;
                            (<span class="ra-formula-val" id="bd_c">0</span> + <span class="ra-formula-val"
                                id="bd_d">0</span>)
                            <br>
                            <span style="font-size:.65rem; color:var(--ra-muted);">(A + B) × (C + D)</span>
                        </div>

                        {{-- Sub scores --}}
                        <div class="ra-sub-rows">
                            <div class="ra-sub-row">
                                <div class="ra-sub-row-lbl">
                                    <span class="ra-sub-row-letter" style="background:#005db6;">A</span>
                                    Safety Efficacy
                                </div>
                                <span class="ra-sub-row-val" id="sb_a">0</span>
                            </div>
                            <div class="ra-sub-row">
                                <div class="ra-sub-row-lbl">
                                    <span class="ra-sub-row-letter" style="background:#0f766e;">B</span>
                                    Availability
                                </div>
                                <span class="ra-sub-row-val" id="sb_b">0</span>
                            </div>
                            <div class="ra-sub-row">
                                <div class="ra-sub-row-lbl">
                                    <span class="ra-sub-row-letter" style="background:#7c3aed;">C</span>
                                    Detectability
                                </div>
                                <span class="ra-sub-row-val" id="sb_c">0</span>
                            </div>
                            <div class="ra-sub-row">
                                <div class="ra-sub-row-lbl">
                                    <span class="ra-sub-row-letter" style="background:#b45309;">D</span>
                                    Probability
                                </div>
                                <span class="ra-sub-row-val" id="sb_d">0</span>
                            </div>
                        </div>

                        {{-- Risk badge --}}
                        <div id="display_risk_badge" class="ra-risk-result ra-risk-result--none">
                            <i class="flaticon2-information" style="font-size:.8rem;"></i>
                            <span id="risk_text">Belum Dihitung</span>
                        </div>

                        {{-- Action badge --}}
                        <div id="display_action_badge" class="ra-action-result">
                            <i class="flaticon2-hourglass ra-action-result-icon" style="font-size:.8rem;"></i>
                            <span id="action_text">{{ $actionLabel }}</span>
                        </div>

                        {{-- Submit button --}}
                        <div style="margin-top:1rem;">
                            <button type="submit" class="ra-btn-submit" id="btn-submit" disabled>
                                <i class="flaticon2-check-mark" style="font-size:.75rem;"></i>
                                Simpan Penilaian
                            </button>
                            <a href="{{ route('qa.risk-assessment.index') }}" class="ra-btn-cancel">
                                Batal
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>{{-- /ra-layout --}}
    </form>

@endsection

@push('scripts')
    <script>
        $(function() {
            /* ── Select2 ───────────────────────────────────── */
            $('.select2').select2({
                width: '100%'
            });

            var lowThreshold = {{ $lowThreshold }};
            var highThreshold = {{ $highThreshold }};

            /* ── Radio card click ─────────────────────────── */
            $(document).on('click', '.ra-radio-card', function() {
                var $card = $(this);
                var group = $card.data('group');
                var rClass = $card.data('risk-class');

                /* deselect siblings */
                $('[data-group="' + group + '"]').removeClass(
                    'selected-risk selected-safe selected-neutral');
                /* select this */
                $card.addClass(rClass);
                /* check the hidden radio */
                $card.find('input[type="radio"]').prop('checked', true).trigger('change');
            });

            /* ── Calc ─────────────────────────────────────── */
            $('.calc-trigger').on('change', calc);
            calc();

            function intVal(selector) {
                var v = parseInt($(selector).val(), 10);
                return isNaN(v) ? 0 : v;
            }

            function radioVal(name) {
                var v = parseInt($('input[name="' + name + '"]:checked').val(), 10);
                return isNaN(v) ? 0 : v;
            }

            function calc() {
                var doc = intVal('#auto_score_doc');
                var trace = intVal('#auto_score_trace');
                var type = intVal('#auto_score_type');
                var attr = radioVal('score_safety_efficacy_attr');
                var country = intVal('#score_detectability_country');
                var warning = radioVal('score_detectability_warning');
                var func = intVal('#score_probability_function');

                var filled = (attr > 0 ? 1 : 0) + (country > 0 ? 1 : 0) + (warning > 0 ? 1 : 0) + (func > 0 ? 1 :
                    0);
                var isOk = filled === 4;

                /* progress bar */
                $('#progress_text').text(filled + '/4');
                $('#progress_bar').css('width', (filled / 4 * 100) + '%');

                var scoreA = doc + attr;
                var scoreB = trace + type;
                var scoreC = country + warning;
                var scoreD = func;
                var total = isOk ? (scoreA + scoreB) * (scoreC + scoreD) : 0;

                /* update section header pills */
                $('#display_score_a').text(isOk ? scoreA : 0);
                $('#display_score_b').text(isOk ? scoreB : 0);
                $('#display_score_c').text(isOk ? scoreC : 0);
                $('#display_score_d').text(isOk ? scoreD : 0);

                /* sidebar breakdown */
                $('#bd_a').text(isOk ? scoreA : 0);
                $('#bd_b').text(isOk ? scoreB : 0);
                $('#bd_c').text(isOk ? scoreC : 0);
                $('#bd_d').text(isOk ? scoreD : 0);
                $('#sb_a').text(isOk ? scoreA : 0);
                $('#sb_b').text(isOk ? scoreB : 0);
                $('#sb_c').text(isOk ? scoreC : 0);
                $('#sb_d').text(isOk ? scoreD : 0);

                $('#display_total_score').text(total);
                $('#btn-submit').prop('disabled', !isOk);

                /* badges */
                var $risk = $('#display_risk_badge');
                var $action = $('#display_action_badge');

                $risk.removeClass(
                    'ra-risk-result--none ra-risk-result--low ra-risk-result--medium ra-risk-result--high');
                $risk.find('i').removeClass(
                    'flaticon2-information flaticon2-check-mark flaticon-warning flaticon-danger');
                $action.removeClass('border-success border-warning border-danger');

                if (!isOk) {
                    $risk.addClass('ra-risk-result--none');
                    $risk.find('i').addClass('flaticon2-information');
                    $('#risk_text').text('Belum Dihitung');
                    $('#action_text').text('Lengkapi form terlebih dahulu');
                    $('#display_total_score').css('color', 'var(--ra-blue)');
                } else if (total <= lowThreshold) {
                    $risk.addClass('ra-risk-result--low');
                    $risk.find('i').addClass('flaticon2-check-mark');
                    $('#risk_text').text('LOW RISK');
                    $('#action_text').text('Qualified');
                    $('#display_total_score').css('color', 'var(--ra-green)');
                } else if (total <= highThreshold) {
                    $risk.addClass('ra-risk-result--medium');
                    $risk.find('i').addClass('flaticon-warning');
                    $('#risk_text').text('MEDIUM RISK');
                    $('#action_text').text('Desk Evaluation / Document / Questionnaire');
                    $('#display_total_score').css('color', 'var(--ra-amber)');
                } else {
                    $risk.addClass('ra-risk-result--high');
                    $risk.find('i').addClass('flaticon-danger');
                    $('#risk_text').text('HIGH RISK');
                    $('#action_text').text('Audit On Site');
                    $('#display_total_score').css('color', 'var(--ra-red)');
                }
            }
        });
    </script>
@endpush
