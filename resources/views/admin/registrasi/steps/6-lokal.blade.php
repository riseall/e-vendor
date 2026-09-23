<div class="form-section-title">{{ __('local_vendor_only') }}</div>

<div class="row">
    <div class="col-md-4">
        <div class="question-wrapper">
            {{-- Status Perusahaan --}}
            <label class="question-label">{{ __('company_status') }} @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <x-vendor-radio name="status_perusahaan" label="" :options="[['value' => 'perorangan', 'label' => __('individual')], ['value' => 'badan', 'label' => __('corporate_entity')]]" :selected="$draft['general']->status_perusahaan ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="question-wrapper">
            {{-- Status Pajak --}}
            <label class="question-label">{{ __('tax_status') }} @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <x-vendor-radio name="status_pajak" label="" :options="[['value' => 'pkp', 'label' => __('taxable_enterprise')], ['value' => 'non_pkp', 'label' => __('non_taxable_enterprise')]]" :selected="$draft['general']->status_pajak ?? ''" :readonly="$isReadOnly" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="question-wrapper">
            {{-- Jenis Penanaman Modal --}}
            <label class="question-label">{{ __('investment_type') }} @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <x-vendor-radio name="jenis_modal" label="" :options="[['value' => 'pmdn', 'label' => __('domestic_investment')], ['value' => 'pma', 'label' => __('foreign_investment')]]" :selected="$draft['general']->jenis_modal ?? ''" :readonly="$isReadOnly" />
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="question-wrapper">
            {{-- Skala Perusahaan menggunakan Select Component --}}
            <label class="question-label">{{ __('company_scale_nib') }} @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <x-vendor-select name="skala_perusahaan" label="" :options="[
                'mikro' => __('micro_business'),
                'kecil' => __('small_business'),
                'menengah' => __('medium_business'),
                'besar' => __('large_business'),
                'koperasi' => __('cooperative'),
            ]" :selected="$draft['general']->skala_perusahaan ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
    <div class="col-md-6">
        <div class="question-wrapper">
            {{-- KBLI --}}
            <label class="question-label">{{ __('kbli_owned') }} @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <x-vendor-input name="kbli" label="" required :placeholder="__('kbli_placeholder')" :value="$draft['general']->kbli ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
</div>
