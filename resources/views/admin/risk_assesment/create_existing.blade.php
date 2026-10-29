@extends('layouts.app', ['title' => 'Risk Assessment Vendor Eksisting'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Risk Assessment')
@section('page_title', 'Risk Assessment Vendor Eksisting')
@section('page_desc', 'Formulir penilaian risiko vendor eksisting bahan baku / bahan kemas.')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/admin/risk-assessment-form.css') }}">
    <style>
        .ra-risk-result--very-high {
            background: #fee2e2 !important;
            color: #991b1b !important;
            border: 1px solid #f87171 !important;
        }

        .ra-radio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: .65rem;
        }
    </style>
@endpush

@php
    $vendorName = optional($application->general)->nama_perusahaan ?? $application->user->name;
    $vendorEmail = optional($application->general)->email_perusahaan ?? $application->user->email;
    $categoryLabels = $application->categories
        ->pluck('category_id')
        ->map(function ($id) {
            return \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id;
        })
        ->implode(', ');

    $selected = [
        'score_safety_efficacy_doc' => old(
            'score_safety_efficacy_doc',
            optional($qualification)->score_safety_efficacy_doc ?? $autoScores['doc_score'],
        ),
        'score_complaint_side_effect' => old(
            'score_complaint_side_effect',
            optional($qualification)->score_complaint_side_effect,
        ),
        'score_safety_efficacy_attr' => old(
            'score_safety_efficacy_attr',
            optional($qualification)->score_safety_efficacy_attr,
        ),

        'score_pareto' => old('score_pareto', optional($qualification)->score_pareto),
        'score_purchase_volume' => old('score_purchase_volume', optional($qualification)->score_purchase_volume),
        'score_availability_type' => old(
            'score_availability_type',
            optional($qualification)->score_availability_type ?? $autoScores['supplier_type_score'],
        ),
        'score_nie_status' => old('score_nie_status', optional($qualification)->score_nie_status),

        'score_audit_year' => old('score_audit_year', optional($qualification)->score_audit_year),
        'score_audit_result' => old('score_audit_result', optional($qualification)->score_audit_result),
        'score_audit_response' => old('score_audit_response', optional($qualification)->score_audit_response),
        'score_complaint_count' => old('score_complaint_count', optional($qualification)->score_complaint_count),

        'score_material_grade' => old('score_material_grade', optional($qualification)->score_material_grade),
        'score_facility_variation' => old(
            'score_facility_variation',
            optional($qualification)->score_facility_variation,
        ),
        'score_material_purpose' => old('score_material_purpose', optional($qualification)->score_material_purpose),
    ];

    $sections = $sections ?? config('risk_assessment.existing_sections', []);
@endphp

@section('content')
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
                <a href="{{ route('qa.risk-assessment.index', ['tab' => 'existing']) }}"
                    class="btn btn-icon btn-sm btn-light-primary mr-5 mt-1" title="Kembali ke Daftar">
                    <i class="ki ki-arrow-back" style="font-size:.8rem;"></i>
                </a>
                <div>
                    <div class="ra-header-appnum">Nomor Permohonan / Vendor Eksisting</div>
                    <h4 class="ra-header-appnum-val">{{ $application->application_number ?? '-' }}</h4>
                    <div class="ra-vendor-sub font-weight-bolder text-dark">{{ $vendorName }}</div>
                    <div class="ra-vendor-sub">{{ $vendorEmail }}</div>
                </div>
            </div>
            <div class="ra-header-right">
                <div class="ra-header-form-lbl">Formulir Khusus</div>
                <div class="ra-header-form-val">Risk Assessment Eksisting BB/BK</div>
            </div>
        </div>
        <div class="ra-header-meta">
            @if ($categoryLabels)
                <span class="ra-meta-chip">
                    <span class="ra-meta-chip-lbl">Kategori</span>{{ $categoryLabels }}
                </span>
            @endif
            <span class="ra-meta-chip">
                <span class="ra-meta-chip-lbl">Status</span>
                <span class="badge badge-success font-size-xs px-2 py-1">APPROVED</span>
            </span>
        </div>
    </div>

    {{-- ════ FORMULA BANNER ════ --}}
    <div class="ra-formula-banner">
        <i class="flaticon-info ra-formula-banner-icon"></i>
        <div class="ra-formula-banner-text">
            <strong>Rumus Total Nilai:</strong>
            <span class="ra-formula-tag">(A + B) × (C + D)</span>
            = (Safety Efficacy + Availability) × (Detectability + Probability)
            <div class="ra-threshold-row">
                <span class="ra-thr ra-thr--low">Low: 42&ndash;84</span>
                <span class="ra-thr ra-thr--medium">Medium: 85&ndash;168</span>
                <span class="ra-thr ra-thr--high">High: 169&ndash;336</span>
                <span class="ra-thr text-white" style="background:#b91c1c;">Very High: 337&ndash;672</span>
            </div>
        </div>
    </div>

    <form action="{{ route('qa.risk-assessment.store-existing', $application->id) }}" method="POST"
        id="form-risk-assessment-existing">
        @csrf
        <input type="hidden" name="vendor_application_id" value="{{ $application->id }}">

        <div class="ra-layout">

            {{-- ════ MAIN FORM COLUMN (DRY Loop) ════ --}}
            <div>
                <div class="ra-form-card">
                    @foreach ($sections as $secKey => $sec)
                        <div class="ra-section-head">
                            <div class="ra-section-letter ra-section-letter--{{ $secKey }}">{{ $sec['letter'] }}
                            </div>
                            <div>
                                <div class="ra-section-title">{{ $sec['title'] }}</div>
                                <div class="ra-section-sub">{{ $sec['sub'] }}</div>
                            </div>
                            <div class="ra-section-score-pill">
                                Skor {{ $sec['letter'] }}: <span id="display_score_{{ $secKey }}"
                                    class="ra-section-score-num">0</span>
                            </div>
                        </div>

                        <div class="ra-section-body">
                            @foreach ($sec['fields'] as $f)
                                <div class="ra-field">
                                    <label class="ra-field-label">
                                        {{ $f['label'] }}
                                        <span class="ra-required">*</span>
                                    </label>
                                    <div class="ra-radio-grid">
                                        @foreach ($f['options'] as $opt)
                                            @php
                                                $val = (string) $opt['val'];
                                                $isChecked = (string) ($selected[$f['name']] ?? '') === $val;
                                                $rClass =
                                                    $opt['risk_class'] ??
                                                    ($val >= 3
                                                        ? 'selected-risk'
                                                        : ($val == 2
                                                            ? 'selected-neutral'
                                                            : 'selected-safe'));
                                            @endphp
                                            <label class="ra-radio-card {{ $isChecked ? $rClass : '' }}"
                                                data-group="{{ $f['name'] }}" data-val="{{ $val }}"
                                                data-risk-class="{{ $rClass }}">
                                                <input type="radio" name="{{ $f['name'] }}"
                                                    value="{{ $val }}" data-section="{{ $secKey }}"
                                                    class="calc-trigger" {{ $isChecked ? 'checked' : '' }} required>
                                                <div class="ra-radio-card-dot"></div>
                                                <div>
                                                    <div class="ra-radio-card-label">{{ $opt['label'] }}</div>
                                                    @if (!empty($opt['desc']))
                                                        <div class="font-size-xs text-muted">{{ $opt['desc'] }}</div>
                                                    @endif
                                                </div>
                                                <span class="ra-radio-card-score">{{ $val }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error($f['name'])
                                        <div class="ra-invalid">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="ra-section-divider"></div>
                    @endforeach

                    {{-- ── SECTION E: Keterangan QA ─────────────────────── --}}
                    <div class="ra-section-head">
                        <div class="ra-section-letter ra-section-letter--e">+</div>
                        <div>
                            <div class="ra-section-title">Keterangan / Catatan QA</div>
                            <div class="ra-section-sub">Opsional — catatan hasil pertimbangan khusus</div>
                        </div>
                    </div>
                    <div class="ra-section-body">
                        <div class="ra-field">
                            <label class="ra-field-label" for="notes">Catatan QA</label>
                            <textarea name="notes" id="notes" rows="3" class="ra-textarea @error('notes') is-invalid @enderror"
                                placeholder="Masukkan catatan hasil penilaian jika ada&hellip;">{{ old('notes', optional($qualification)->notes) }}</textarea>
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
                            Skor Risiko Eksisting
                        </div>
                    </div>
                    <div class="ra-score-body">

                        {{-- Progress kelengkapan --}}
                        <div class="ra-form-progress">
                            <div class="ra-form-progress-lbl">
                                <span>Kelengkapan Form</span>
                                <span id="progress_text">0/14</span>
                            </div>
                            <div class="ra-form-progress-bar-wrap">
                                <div class="ra-form-progress-bar" id="progress_bar" style="width:0%"></div>
                            </div>
                        </div>

                        {{-- Total Nilai --}}
                        <div class="ra-total-wrap">
                            <div class="ra-total-num" id="display_total_score">
                                {{ optional($qualification)->total_score ?? 0 }}</div>
                            <div class="ra-total-lbl">Total Nilai Penilaian</div>
                        </div>

                        {{-- Breakdown formula --}}
                        <div class="ra-formula-breakdown">
                            (<span class="ra-formula-val" id="bd_a">0</span> + <span class="ra-formula-val"
                                id="bd_b">0</span>)
                            &times;
                            (<span class="ra-formula-val" id="bd_c">0</span> + <span class="ra-formula-val"
                                id="bd_d">0</span>)
                            <br>
                            <span class="ra-formula-note">(A + B) &times; (C + D)</span>
                        </div>

                        {{-- Sub scores --}}
                        <div class="ra-sub-rows">
                            @foreach ($sections as $key => $s)
                                <div class="ra-sub-row">
                                    <div class="ra-sub-row-lbl">
                                        <span class="ra-sub-row-letter"
                                            style="background:{{ $s['color'] }}">{{ $s['letter'] }}</span>
                                        {{ $s['title'] }}
                                    </div>
                                    <span class="ra-sub-row-val" id="sb_{{ $key }}">0</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Risk badge --}}
                        <div id="display_risk_badge" class="ra-risk-result ra-risk-result--none">
                            <i class="flaticon2-information" style="font-size:.8rem;"></i>
                            <span id="risk_text">Belum Dihitung</span>
                        </div>

                        {{-- Action badge --}}
                        <div id="display_action_badge" class="ra-action-result">
                            <i class="flaticon2-hourglass ra-action-result-icon" style="font-size:.8rem;"></i>
                            <span id="action_text">Lengkapi form terlebih dahulu</span>
                        </div>

                        {{-- Submit button --}}
                        <div class="ra-sidebar-actions">
                            

                            @can('qa-risk-store')
                                <button type="submit" class="ra-btn-submit" id="btn-submit" disabled>
                                    <i class="flaticon2-check-mark text-white" style="font-size:.75rem;"></i>
                                    Simpan Penilaian
                                </button>
                            @endcan
                            <a href="{{ route('qa.risk-assessment.index', ['tab' => 'existing']) }}"
                                class="ra-btn-cancel">Batal</a>
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
            var lowThreshold = {{ (int) config('risk_assessment.existing.low_threshold', 84) }};
            var mediumThreshold = {{ (int) config('risk_assessment.existing.medium_threshold', 168) }};
            var highThreshold = {{ (int) config('risk_assessment.existing.high_threshold', 336) }};
            var totalQuestions = 14;

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

            /* ── Dynamic Calc ──────────────────────────────── */
            $('.calc-trigger').on('change', calc);
            calc();

            function calc() {
                var subScore = {
                    a: 0,
                    b: 0,
                    c: 0,
                    d: 0
                };
                var filled = 0;

                $('.calc-trigger:checked').each(function() {
                    var sec = $(this).data('section');
                    var val = parseInt($(this).val(), 10) || 0;
                    if (sec && subScore[sec] !== undefined && val > 0) {
                        subScore[sec] += val;
                        filled++;
                    }
                });

                var isOk = (filled === totalQuestions);

                /* Progress bar */
                $('#progress_text').text(filled + '/' + totalQuestions);
                $('#progress_bar').css('width', (filled / totalQuestions * 100) + '%');

                var total = isOk ? (subScore.a + subScore.b) * (subScore.c + subScore.d) : 0;

                ['a', 'b', 'c', 'd'].forEach(function(k) {
                    var val = (subScore[k] > 0) ? subScore[k] : 0;
                    $('#display_score_' + k + ', #sb_' + k + ', #bd_' + k).text(val);
                });

                $('#display_total_score').text(total);
                $('#btn-submit').prop('disabled', !isOk);

                /* Badges */
                var $risk = $('#display_risk_badge');
                var $action = $('#display_action_badge');
                var riskClasses =
                    'ra-risk-result--none ra-risk-result--low ra-risk-result--medium ra-risk-result--high ra-risk-result--very-high';
                var iconClasses =
                    'flaticon2-information flaticon2-check-mark flaticon-warning flaticon-danger flaticon2-warning';

                $risk.removeClass(riskClasses).find('i').removeClass(iconClasses);

                if (!isOk) {
                    $risk.addClass('ra-risk-result--none').find('i').addClass('flaticon2-information');
                    $('#risk_text').text('Belum Dihitung');
                    $('#action_text').text('Lengkapi ' + (totalQuestions - filled) + ' item form tersisa');
                    $('#display_total_score').css('color', 'var(--ra-blue)');
                } else if (total <= lowThreshold) {
                    $risk.addClass('ra-risk-result--low').find('i').addClass('flaticon2-check-mark');
                    $('#risk_text').text('LOW RISK');
                    $('#action_text').html(
                        '<strong>Re-audit: 7 Tahun</strong><br><span class="text-muted font-size-xs">Audit by Questionnaire</span>'
                    );
                    $('#display_total_score').css('color', 'var(--ra-green)');
                } else if (total <= mediumThreshold) {
                    $risk.addClass('ra-risk-result--medium').find('i').addClass('flaticon-warning');
                    $('#risk_text').text('MEDIUM RISK');
                    $('#action_text').html(
                        '<strong>Re-audit: 5 Tahun</strong><br><span class="text-muted font-size-xs">Audit by Questionnaire</span>'
                    );
                    $('#display_total_score').css('color', 'var(--ra-amber)');
                } else if (total <= highThreshold) {
                    $risk.addClass('ra-risk-result--high').find('i').addClass('flaticon-danger');
                    $('#risk_text').text('HIGH RISK');
                    $('#action_text').html(
                        '<strong>Re-audit: 3 Tahun</strong><br><span class="text-muted font-size-xs">Audit On Site</span>'
                    );
                    $('#display_total_score').css('color', 'var(--ra-red)');
                } else {
                    $risk.addClass('ra-risk-result--very-high').find('i').addClass('flaticon2-warning');
                    $('#risk_text').text('VERY HIGH RISK');
                    $('#action_text').html(
                        '<strong>Re-audit: 2 Tahun</strong><br><span class="text-danger font-weight-bold font-size-xs">Audit On Site & Quality Agreement</span>'
                    );
                    $('#display_total_score').css('color', '#991b1b');
                }
            }
        });
    </script>
@endpush
