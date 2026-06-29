{{-- Section A — Safety Efficacy — partial --}}
<div class="ra-section-head">
    <div class="ra-section-letter ra-section-letter--a">{{ $section['letter'] }}</div>
    <div>
        <div class="ra-section-title">{{ $section['title'] }}</div>
        <div class="ra-section-sub">{{ $section['sub'] }}</div>
    </div>
    <div class="ra-section-score-pill">
        Skor {{ $section['letter'] }}: <span id="display_score_a" class="ra-section-score-num">0</span>
    </div>
</div>

<div class="ra-section-body">

    {{-- ── Kelengkapan Dokumen ─────────────────────────── --}}
    <div class="ra-field">
        <label class="ra-field-label">
            Kelengkapan Dokumen
            <span class="ra-field-auto-tag">(otomatis)</span>
        </label>

        @php
            $checklist = $autoScores['doc_checklist'] ?? [];
            $totalDocs = count($checklist);
            $doneDocs = collect($checklist)->where('fulfilled', true)->count();
            $pct = $totalDocs > 0 ? round(($doneDocs / $totalDocs) * 100) : 0;
            $scoreDoc = $autoScores['doc_score'];
            $scoreCls =
                $scoreDoc <= 1 ? 'ra-doc-score--ok' : ($scoreDoc <= 3 ? 'ra-doc-score--warn' : 'ra-doc-score--danger');
        @endphp

        {{-- Summary header --}}
        <div class="ra-doc-summary">
            <div class="ra-doc-summary-left">
                <span class="ra-doc-summary-label">{{ $autoScores['doc_label'] }}</span>
                <span class="ra-doc-summary-count">{{ $doneDocs }}/{{ $totalDocs }} dokumen tersedia</span>
            </div>
            <span class="ra-doc-score-badge {{ $scoreCls }}">Skor {{ $scoreDoc }}</span>
        </div>

        {{-- Progress bar --}}
        <div class="ra-doc-progress-wrap">
            <div class="ra-doc-progress-bar {{ $scoreCls }}" style="width: {{ $pct }}%"></div>
        </div>

        {{-- Doc item list --}}
        @if (!empty($checklist))
            <div class="ra-doc-list">
                @foreach ($checklist as $doc)
                    @php
                        $isFulfilled = !empty($doc['fulfilled']);
                        $isOptional = ($doc['note'] ?? '') === 'Tidak wajib';
                        $itemCls = $isFulfilled ? 'is-complete' : ($isOptional ? 'is-optional' : 'is-missing');
                        $statusText = $isFulfilled
                            ? ($isOptional
                                ? 'Tidak wajib'
                                : 'Ada')
                            : ($isOptional
                                ? 'Tidak wajib'
                                : 'Belum ada');
                    @endphp
                    <div class="ra-doc-item {{ $itemCls }}">
                        <div class="ra-doc-item-icon ">
                            @if ($isFulfilled)
                                <i class="fas fa-check text-white"></i>
                            @elseif ($isOptional)
                                <i class="fas fa-info-circle text-white"></i>
                            @else
                                <i class="fas fa-times text-white"></i>
                            @endif
                        </div>
                        <div class="ra-doc-item-body">
                            <span class="ra-doc-item-label">{{ $doc['label'] }}</span>
                            @if (!empty($doc['note']) && !$isOptional)
                                <span class="ra-doc-item-note">{{ $doc['note'] }}</span>
                            @endif
                        </div>
                        <span class="ra-doc-item-status">{{ $statusText }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="ra-hint">
            Dokumen lengkap: <strong>1</strong> &middot;
            Kurang lengkap: <strong>3</strong> &middot;
            Tidak lengkap / N/A: <strong>4</strong>
        </div>
    </div>

    {{-- ── Critical Attribute ──────────────────────────── --}}
    <div class="ra-field">
        <label class="ra-field-label">
            Bahan memiliki critical attribute
            <span class="ra-required">*</span>
        </label>
        <div class="ra-radio-group">
            @foreach ($radioAttr as $opt)
                <label class="ra-radio-card {{ (string) $selectedAttr === $opt['val'] ? $opt['selected'] : '' }}"
                    data-group="score_safety_efficacy_attr" data-val="{{ $opt['val'] }}"
                    data-risk-class="{{ $opt['selected'] }}">
                    <input type="radio" name="score_safety_efficacy_attr" value="{{ $opt['val'] }}"
                        class="calc-trigger" {{ (string) $selectedAttr === $opt['val'] ? 'checked' : '' }} required>
                    <div class="ra-radio-card-dot"></div>
                    <span class="ra-radio-card-label">{{ $opt['label'] }}</span>
                    <span class="ra-radio-card-score">{{ $opt['val'] }}</span>
                </label>
            @endforeach
        </div>
        @error('score_safety_efficacy_attr')
            <div class="ra-invalid">{{ $message }}</div>
        @enderror
    </div>

</div>
