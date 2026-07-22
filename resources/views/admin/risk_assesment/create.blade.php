@extends('layouts.app', ['title' => 'Risk Assessment Pemasok Baru'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Risk Assessment')
@section('page_title', 'Risk Assessment Pemasok Baru')
@section('page_desc', 'Formulir risk assessment pemasok baru.')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/admin/risk-assessment-form.css') }}">
@endpush

@php
    $vendorName = optional($application->general)->nama_perusahaan ?? $application->user->name;
    $vendorEmail = optional($application->general)->email_perusahaan ?? $application->user->email;
    $vendorAddress = optional($application->general)->alamat_perusahaan ?? '-';
    $categoryLabels = $application->categories
        ->pluck('category_id')
        ->map(fn($id) => \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id)
        ->implode(', ');
    $supplierType = $autoScores['supplier_type_label'];

    // Selected values: prefer old() (validation failure), fall back to saved qualification.
    $selected = [
        'score_safety_efficacy_attr' => old(
            'score_safety_efficacy_attr',
            optional($qualification)->score_safety_efficacy_attr,
        ),
        'score_detectability_country' => old(
            'score_detectability_country',
            optional($qualification)->score_detectability_country,
        ),
        'score_detectability_warning' => old(
            'score_detectability_warning',
            optional($qualification)->score_detectability_warning,
        ),
        'score_probability_function' => old(
            'score_probability_function',
            optional($qualification)->score_probability_function,
        ),
    ];

    // Map each user input → score source used by the JS calculator.
    //   'auto'   = hidden input id
    //   'radio'  = radio button group name
    //   'select' = <select id>
    $scoreSources = [
        'a_attr' => ['type' => 'radio', 'name' => 'score_safety_efficacy_attr'],
        'c_country' => ['type' => 'select', 'id' => 'score_detectability_country'],
        'c_warning' => ['type' => 'radio', 'name' => 'score_detectability_warning'],
        'd_function' => ['type' => 'select', 'id' => 'score_probability_function'],
    ];

    // Section definitions (single source of truth for header + body + sidebar letter).
    $sections = [
        'a' => [
            'letter' => 'A',
            'title' => 'Safety Efficacy',
            'sub' => 'Severity — Keamanan & kelengkapan dokumen',
            'color' => '#005db6',
        ],
        'b' => [
            'letter' => 'B',
            'title' => 'Availability',
            'sub' => 'Severity — Ketersediaan & traceability supply chain',
            'color' => '#0f766e',
        ],
        'c' => [
            'letter' => 'C',
            'title' => 'Detectability',
            'sub' => 'Risiko regulasi & riwayat audit',
            'color' => '#7c3aed',
        ],
        'd' => ['letter' => 'D', 'title' => 'Probability', 'sub' => 'Fungsi bahan dalam produk', 'color' => '#b45309'],
    ];

    // Pre-built radio card groups (label → [score, risk-class]).
    $radioAttr = [
        ['val' => '8', 'label' => 'Memiliki critical attribute', 'selected' => 'selected-risk'],
        ['val' => '1', 'label' => 'Tidak memiliki critical attribute', 'selected' => 'selected-safe'],
    ];
    $radioWarning = [
        ['val' => '4', 'label' => 'Ada warning letter / hasil audit buruk', 'selected' => 'selected-risk'],
        ['val' => '1', 'label' => 'Tidak ada warning letter', 'selected' => 'selected-safe'],
    ];
@endphp

@section('content')
    {{-- Flash errors --}}
    @if ($errors->any())
        <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
            <div class="alert-icon"><i class="flaticon-warning"></i></div>
            <div class="alert-text">{{ $errors->first() }}</div>
            <div class="alert-close"><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        </div>
    @endif

    {{-- ════ VENDOR HEADER ════ --}}
    <div class="ra-header">
        <div class="ra-header-top">
            <div class="d-flex align-items-start">
                <a href="{{ route('qa.risk-assessment.index') }}" class="btn btn-icon btn-sm btn-light-primary mr-5 mt-1"
                    title="Kembali">
                    <i class="ki ki-arrow-back" style="font-size:.8rem;"></i>
                </a>
                <div>
                    <div class="ra-header-appnum">Nomor Permohonan</div>
                    <h4 class="ra-header-appnum-val">{{ $application->application_number ?? '-' }}</h4>
                    <div class="ra-vendor-sub">{{ $vendorName }}</div>
                    <div class="ra-vendor-sub">{{ $vendorEmail }}</div>
                </div>
            </div>
            <div class="ra-header-right">
                <div class="ra-header-form-lbl">Formulir</div>
                <div class="ra-header-form-val">Risk Assessment QA 2026</div>
            </div>
        </div>
        <div class="ra-header-meta">
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
                <span class="ra-thr ra-thr--low">Low: 12&ndash;{{ $lowThreshold }}</span>
                <span class="ra-thr ra-thr--medium">Medium: {{ $lowThreshold + 1 }}&ndash;{{ $highThreshold }}</span>
                <span class="ra-thr ra-thr--high">High: &gt; {{ $highThreshold }}</span>
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
                    @include('admin.risk_assesment.partials._section_a', [
                        'section' => $sections['a'],
                        'selectedAttr' => $selected['score_safety_efficacy_attr'],
                        'autoScores' => $autoScores,
                        'radioAttr' => $radioAttr,
                    ])

                    <div class="ra-section-divider"></div>

                    {{-- ── SECTION B: Availability ─────────────────────── --}}
                    @include('admin.risk_assesment.partials._section_b', [
                        'section' => $sections['b'],
                        'autoScores' => $autoScores,
                        'supplierType' => $supplierType,
                    ])

                    <div class="ra-section-divider"></div>

                    {{-- ── SECTION C: Detectability ─────────────────────── --}}
                    @include('admin.risk_assesment.partials._section_c', [
                        'section' => $sections['c'],
                        'selectedCountry' => $selected['score_detectability_country'],
                        'selectedWarning' => $selected['score_detectability_warning'],
                        'radioWarning' => $radioWarning,
                    ])

                    <div class="ra-section-divider"></div>

                    {{-- ── SECTION D: Probability ───────────────────────── --}}
                    @include('admin.risk_assesment.partials._section_d', [
                        'section' => $sections['d'],
                        'selectedFunction' => $selected['score_probability_function'],
                    ])

                    <div class="ra-section-divider"></div>

                    {{-- ── SECTION E: Keterangan QA ─────────────────────── --}}
                    <div class="ra-section-head">
                        <div class="ra-section-letter ra-section-letter--e">+</div>
                        <div>
                            <div class="ra-section-title">Keterangan QA</div>
                            <div class="ra-section-sub">Opsional — catatan tambahan</div>
                        </div>
                    </div>
                    <div class="ra-section-body">
                        <div class="ra-field">
                            <label class="ra-field-label" for="notes">Keterangan / Catatan</label>
                            <textarea name="notes" id="notes" rows="4" class="ra-textarea @error('notes') is-invalid @enderror"
                                placeholder="Masukkan keterangan penilaian jika ada&hellip;">{{ old('notes', optional($qualification)->notes) }}</textarea>
                            @error('notes')
                                <div class="ra-invalid">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>{{-- /ra-form-card --}}
            </div>

            {{-- ════ SIDEBAR: LIVE SCORE ════ --}}
            @include('admin.risk_assesment.partials._sidebar', [
                'qualification' => $qualification,
                'sections' => $sections,
            ])



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
            var isMediumRisk = false;

            /* ── Radio card click ─────────────────────────── */
            $(document).on('click', '.ra-radio-card', function() {
                var $card = $(this);
                var group = $card.data('group');
                var rClass = $card.data('risk-class');

                $('[data-group="' + group + '"]').removeClass(
                    'selected-risk selected-safe selected-neutral');
                $card.addClass(rClass);
                $card.find('input[type="radio"]').prop('checked', true).trigger('change');
            });

            /* ── Calc ─────────────────────────────────────── */
            $('.calc-trigger').on('change', calc);
            calc();

            function numVal(src) {
                var v = parseInt($(src).val(), 10);
                return isNaN(v) ? 0 : v;
            }

            function radioVal(name) {
                var v = parseInt($('input[name="' + name + '"]:checked').val(), 10);
                return isNaN(v) ? 0 : v;
            }

            function calc() {
                var doc = numVal('#auto_score_doc');
                var trace = numVal('#auto_score_trace');
                var type = numVal('#auto_score_type');
                var attr = radioVal('score_safety_efficacy_attr');
                var country = numVal('#score_detectability_country');
                var warning = radioVal('score_detectability_warning');
                var func = numVal('#score_probability_function');

                var filled = (attr > 0 ? 1 : 0) + (country > 0 ? 1 : 0) + (warning > 0 ? 1 : 0) + (func > 0 ? 1 :
                    0);
                var isOk = filled === 4;

                /* progress bar */
                $('#progress_text').text(filled + '/4');
                $('#progress_bar').css('width', (filled / 4 * 100) + '%');

                var subScore = {
                    a: doc + attr,
                    b: trace + type,
                    c: country + warning,
                    d: func
                };
                var total = isOk ? (subScore.a + subScore.b) * (subScore.c + subScore.d) : 0;

                /* single loop drives both header pills and sidebar rows */
                ['a', 'b', 'c', 'd'].forEach(function(k) {
                    var val = isOk ? subScore[k] : 0;
                    $('#display_score_' + k + ', #sb_' + k + ', #bd_' + k).text(val);
                });

                $('#display_total_score').text(total);
                $('#btn-submit').prop('disabled', !isOk);

                /* badges */
                var $risk = $('#display_risk_badge');
                var $action = $('#display_action_badge');
                var riskClasses =
                    'ra-risk-result--none ra-risk-result--low ra-risk-result--medium ra-risk-result--high';
                var iconClasses = 'flaticon2-information flaticon2-check-mark flaticon-warning flaticon-danger';

                $risk.removeClass(riskClasses).find('i').removeClass(iconClasses);
                $action.removeClass('border-success border-warning border-danger');

                isMediumRisk = false;

                if (!isOk) {
                    isMediumRisk = false;
                    $risk.addClass('ra-risk-result--none').find('i').addClass('flaticon2-information');
                    $('#risk_text').text('Belum Dihitung');
                    $('#action_text').text('Lengkapi form terlebih dahulu');
                    $('#display_total_score').css('color', 'var(--ra-blue)');
                } else if (total <= lowThreshold) {
                    isMediumRisk = false;
                    $risk.addClass('ra-risk-result--low').find('i').addClass('flaticon2-check-mark');
                    $('#risk_text').text('LOW RISK');
                    $('#action_text').text('Qualified');
                    $('#display_total_score').css('color', 'var(--ra-green)');
                } else if (total <= highThreshold) {
                    isMediumRisk = true;
                    $risk.addClass('ra-risk-result--medium').find('i').addClass('flaticon-warning');
                    $('#risk_text').text('MEDIUM RISK');
                    $('#action_text').text('Desk Evaluation / Document / Questionnaire');
                    $('#display_total_score').css('color', 'var(--ra-amber)');
                } else {
                    isMediumRisk = false;
                    $risk.addClass('ra-risk-result--high').find('i').addClass('flaticon-danger');
                    $('#risk_text').text('HIGH RISK');
                    $('#action_text').text('Audit On Site');
                    $('#display_total_score').css('color', 'var(--ra-red)');
                }

            }
        });
    </script>
@endpush
