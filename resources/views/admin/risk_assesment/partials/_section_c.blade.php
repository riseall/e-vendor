{{-- Section C — Detectability — partial --}}
<div class="ra-section-head">
    <div class="ra-section-letter ra-section-letter--c">{{ $section['letter'] }}</div>
    <div>
        <div class="ra-section-title">{{ $section['title'] }}</div>
        <div class="ra-section-sub">{{ $section['sub'] }}</div>
    </div>
    <div class="ra-section-score-pill">
        Skor {{ $section['letter'] }}: <span id="display_score_c" class="ra-section-score-num">0</span>
    </div>
</div>
<div class="ra-section-body">
    <div class="ra-field">
        <label class="ra-field-label" for="score_detectability_country">Country / Regulatory Risk <span
                class="ra-required">*</span></label>
        <select name="score_detectability_country" id="score_detectability_country"
            class="ra-select calc-trigger select2 @error('score_detectability_country') is-invalid @enderror" required>
            <option value="">&mdash; Pilih risiko negara &mdash;</option>
            <option value="4" {{ (string) $selectedCountry === '4' ? 'selected' : '' }}>Negara High Risk (Regulasi
                Lemah)</option>
            <option value="3" {{ (string) $selectedCountry === '3' ? 'selected' : '' }}>Negara Berkembang (Kontrol
                Terbatas)</option>
            <option value="2" {{ (string) $selectedCountry === '2' ? 'selected' : '' }}>Negara dengan Regulasi
                Menengah</option>
            <option value="1" {{ (string) $selectedCountry === '1' ? 'selected' : '' }}>Negara dengan Otoritas Kuat
                (FDA)</option>
        </select>
        <div class="ra-hint">Otoritas kuat (FDA) : 1 &middot; Regulasi menengah: 2 &middot; Berkembang (Kontrol
            terbatas): 3 &middot; High Risk (Regulasi lemah): 4</div>
        @error('score_detectability_country')
            <div class="ra-invalid">{{ $message }}</div>
        @enderror
    </div>

    <div class="ra-field">
        <label class="ra-field-label">Warning Letter / Hasil Audit <span class="ra-required">*</span></label>
        <div class="ra-radio-group">
            @foreach ($radioWarning as $opt)
                <label class="ra-radio-card {{ (string) $selectedWarning === $opt['val'] ? $opt['selected'] : '' }}"
                    data-group="score_detectability_warning" data-val="{{ $opt['val'] }}"
                    data-risk-class="{{ $opt['selected'] }}">
                    <input type="radio" name="score_detectability_warning" value="{{ $opt['val'] }}"
                        class="calc-trigger" {{ (string) $selectedWarning === $opt['val'] ? 'checked' : '' }} required>
                    <div class="ra-radio-card-dot"></div>
                    <span class="ra-radio-card-label">{{ $opt['label'] }}</span>
                    <span class="ra-radio-card-score">{{ $opt['val'] }}</span>
                </label>
            @endforeach
        </div>
        @error('score_detectability_warning')
            <div class="ra-invalid">{{ $message }}</div>
        @enderror
    </div>
</div>
