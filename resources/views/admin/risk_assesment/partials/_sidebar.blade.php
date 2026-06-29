{{-- Sticky live-score sidebar — partial --}}
@php
    $auditType = optional($qualification)->audit_type;
    $actionLabel =
        $auditType === 'on_site'
            ? 'Audit On Site'
            : ($auditType === 'on_desk'
                ? 'Desk Evaluation / Document / Questionnaire'
                : 'Qualified');
@endphp
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
                <div class="ra-total-num" id="display_total_score">{{ optional($qualification)->total_score ?? 0 }}
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
                <span id="action_text">{{ $actionLabel }}</span>
            </div>

            {{-- Submit button --}}
            <div class="ra-sidebar-actions">
                <button type="submit" class="ra-btn-submit" id="btn-submit" disabled>
                    <i class="flaticon2-check-mark" style="font-size:.75rem;"></i>
                    Simpan Penilaian
                </button>
                <a href="{{ route('qa.risk-assessment.index') }}" class="ra-btn-cancel">Batal</a>
            </div>

        </div>
    </div>
</div>
