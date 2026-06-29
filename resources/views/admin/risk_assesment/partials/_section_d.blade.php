{{-- Section D — Probability — partial --}}
<div class="ra-section-head">
    <div class="ra-section-letter ra-section-letter--d">{{ $section['letter'] }}</div>
    <div>
        <div class="ra-section-title">{{ $section['title'] }}</div>
        <div class="ra-section-sub">{{ $section['sub'] }}</div>
    </div>
    <div class="ra-section-score-pill">
        Skor {{ $section['letter'] }}: <span id="display_score_d" class="ra-section-score-num">0</span>
    </div>
</div>
<div class="ra-section-body">
    <div class="ra-field">
        <label class="ra-field-label" for="score_probability_function">Fungsi Bahan <span
                class="ra-required">*</span></label>
        <select name="score_probability_function" id="score_probability_function"
            class="ra-select calc-trigger select2 @error('score_probability_function') is-invalid @enderror" required>
            <option value="">&mdash; Pilih fungsi bahan &mdash;</option>
            <option value="4" {{ (string) $selectedFunction === '4' ? 'selected' : '' }}>API
            </option>
            <option value="3" {{ (string) $selectedFunction === '3' ? 'selected' : '' }}>Eksipien / Primary
                Packaging</option>
            <option value="2" {{ (string) $selectedFunction === '2' ? 'selected' : '' }}>Secondary Packaging
            </option>
            <option value="1" {{ (string) $selectedFunction === '1' ? 'selected' : '' }}>Non Kontak Produk</option>
        </select>
        <div class="ra-hint">Non Kontak Produk: 1 &middot; Secondary Packaging: 2 &middot; Eksipien / Primary Packaging:
            3 &middot; API: 4</div>
        @error('score_probability_function')
            <div class="ra-invalid">{{ $message }}</div>
        @enderror
    </div>
</div>
