<div class="form-section-title">{{ __('varia_vendor_header') }}</div>

<div class="specific-container">
    {{-- SUB-SECTION: VARIA TEKNIK & REAGEN --}}
    <div class="row">
        {{-- Q1: Agen Tunggal --}}
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_is_sole_agent') }} @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="v1_is_sole_agent" label="" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['v1_is_sole_agent'] ?? 'no'"
                    class="toggle-input" data-target="#wrap_v1" :readonly="$isReadOnly" />

                <div id="wrap_v1"
                    class="mt-3 p-4 bg-light rounded border-left border-primary {{ ($draft['v1_is_sole_agent'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <div class="form-group">
                        <label>{{ __('agency_authorization_letter') }}</label>
                        <x-vendor-input type="file" name="v1_auth_letter" :value="$draft['v1_auth_letter'] ?? null" :readonly="$isReadOnly"
                            :placeholder="__('upload_auth_letter_placeholder')" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Q2: Perijinan Khusus (B3 dll) --}}
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_special_license_goods_transport') }} @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="v2_has_special_license" label="" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['v2_has_special_license'] ?? 'no'"
                    class="toggle-input" data-target="#wrap_v2" :readonly="$isReadOnly" />

                <div id="wrap_v2"
                    class="mt-3 p-4 bg-light rounded border-left border-primary {{ ($draft['v2_has_special_license'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <div class="form-group">
                        <label>{{ __('permit_license_document') }}</label>
                        <x-vendor-input type="file" name="v2_license_file" :value="$draft['v2_license_file'] ?? null" :readonly="$isReadOnly"
                            :placeholder="__('upload_permit_document_placeholder')" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-8">

    {{-- SUB-SECTION: GAS & SOLAR --}}
    <h6 class="font-weight-bolder mb-4 text-primary">{{ __('special_for_gas_solar_supplier') }}</h6>

    <div class="row">
        {{-- Q3: Ketentuan ISO & B3 --}}
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_transport_compliant_iso_b3') }} @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="v3_iso_b3" label="" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['v3_iso_b3'] ?? 'no'"
                    :readonly="$isReadOnly" />
            </div>
        </div>

        {{-- Q4: Training Driver --}}
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_driver_has_training') }} @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="v4_driver_training" label="" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['v4_driver_training'] ?? 'no'"
                    :readonly="$isReadOnly" />
            </div>
        </div>

        {{-- Q5: Ijin Valid --}}
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_carry_valid_license_per_delivery') }} @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="v5_valid_license" label="" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['v5_valid_license'] ?? 'no'"
                    :readonly="$isReadOnly" />
            </div>
        </div>

        {{-- Q6: Surat KIR --}}
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_has_dishub_kir_letter') }} @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>

                <x-vendor-radio name="v6_kir" label="" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['v6_kir'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_v6" :readonly="$isReadOnly" />

                <div id="wrap_v6"
                    class="mt-3 p-4 bg-light rounded border-left border-primary {{ ($draft['v6_kir'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <div class="form-group">
                        <label>{{ __('kir_letter') }}</label>
                        <x-vendor-input type="file" name="v6_kir_file" :value="$draft['v6_kir_file'] ?? null" :readonly="$isReadOnly"
                            :placeholder="__('upload_kir_placeholder')" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
