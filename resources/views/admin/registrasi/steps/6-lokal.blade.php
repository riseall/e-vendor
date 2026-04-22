<div class="form-section-title">Isian Khusus Pemasok Lokal</div>

<div class="row">
    <div class="col-md-4">
        <div class="question-wrapper">
            <x-vendor-radio name="status_perusahaan" label="Status Perusahaan" :options="[['value' => 'perorangan', 'label' => 'Perorangan'], ['value' => 'badan', 'label' => 'Badan']]" :selected="$draft['general']->status_perusahaan ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="question-wrapper">
            <x-vendor-radio name="status_pajak" label="Status Pajak" :options="[['value' => 'pkp', 'label' => 'PKP'], ['value' => 'non_pkp', 'label' => 'Non PKP']]" :selected="$draft['general']->status_pajak ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="question-wrapper">
            <x-vendor-radio name="jenis_modal" label="Jenis Penanaman Modal" :options="[['value' => 'pmdn', 'label' => 'PMDN'], ['value' => 'pma', 'label' => 'PMA']]" :selected="$draft['general']->jenis_modal ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="question-wrapper">
            <x-vendor-select name="skala_perusahaan" label="Skala Perusahaan (NIB)" :options="[
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
            <x-vendor-input name="kbli" label="KBLI yang Dimiliki" required placeholder="Contoh: 12345, 67890"
                :value="$draft['general']->kbli ?? ''" :readonly="$isReadOnly" />
        </div>
    </div>
</div>
