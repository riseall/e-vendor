{{-- Section B — Availability — partial --}}
<div class="ra-section-head">
    <div class="ra-section-letter ra-section-letter--b">{{ $section['letter'] }}</div>
    <div>
        <div class="ra-section-title">{{ $section['title'] }}</div>
        <div class="ra-section-sub">{{ $section['sub'] }}</div>
    </div>
    <div class="ra-section-score-pill">
        Skor {{ $section['letter'] }}: <span id="display_score_b" class="ra-section-score-num">0</span>
    </div>
</div>
<div class="ra-section-body">
    <div class="ra-field">
        <label class="ra-field-label">Traceability Supply Chain <span class="ra-field-auto-tag">(otomatis)</span></label>
        <div class="ra-readonly">
            <span>{{ $autoScores['traceability_label'] }}</span>
            <span class="ra-readonly-score">Skor {{ $autoScores['traceability_score'] }}</span>
        </div>
        <div class="ra-hint">Lengkap: 1 &middot; Tidak lengkap: 4</div>
    </div>

    <div class="ra-field">
        <label class="ra-field-label">Jenis Pemasok <span class="ra-field-auto-tag">(otomatis)</span></label>
        <div class="ra-readonly">
            <span>{{ $supplierType }}</span>
            <span class="ra-readonly-score">Skor {{ $autoScores['supplier_type_score'] }}</span>
        </div>
        <div class="ra-hint">Manufaktur: 1 &middot; Distributor: 2 &middot; Repacker: 3 &middot; Trader: 4</div>
    </div>
</div>
