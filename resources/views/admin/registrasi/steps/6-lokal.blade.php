<div class="form-section-title">Isian Khusus Pemasok Lokal</div>

<div class="row">
    <div class="col-md-4">
        <div class="question-wrapper">
            {{-- Status Perusahaan --}}
            <label class="question-label">Status Perusahaan @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <x-vendor-radio name="status_perusahaan" label="" :options="[['value' => 'perorangan', 'label' => 'Perorangan'], ['value' => 'badan', 'label' => 'Badan']]" :selected="$draft['general']->status_perusahaan ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="question-wrapper">
            {{-- Status Pajak --}}
            <label class="question-label">Status Pajak @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <x-vendor-radio name="status_pajak" label="" :options="[['value' => 'pkp', 'label' => 'PKP'], ['value' => 'non_pkp', 'label' => 'Non PKP']]" :selected="$draft['general']->status_pajak ?? ''" :readonly="$isReadOnly" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="question-wrapper">
            {{-- Jenis Penanaman Modal --}}
            <label class="question-label">Jenis Penanaman Modal @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <x-vendor-radio name="jenis_modal" label="" :options="[['value' => 'pmdn', 'label' => 'PMDN'], ['value' => 'pma', 'label' => 'PMA']]" :selected="$draft['general']->jenis_modal ?? ''" :readonly="$isReadOnly" />
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="question-wrapper">
            {{-- Skala Perusahaan menggunakan Select Component --}}
            <label class="question-label">Skala Perusahaan (Berdasarkan NIB) @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <x-vendor-select name="skala_perusahaan" label="" :options="[
                'mikro' => 'Usaha Mikro',
                'kecil' => 'Usaha Kecil',
                'menengah' => 'Usaha Menengah',
                'besar' => 'Usaha Besar (Non UMKM)',
                'koperasi' => 'Koperasi',
            ]" :selected="$draft['general']->skala_perusahaan ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
    <div class="col-md-6">
        <div class="question-wrapper">
            {{-- KBLI --}}
            <label class="question-label">KBLI yang Dimiliki @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <x-vendor-input name="kbli" label="" required placeholder="Contoh: 12345, 67890" :value="$draft['general']->kbli ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
</div>
