<div class="form-section-title mt-10">{{ __('contractor_vendor_header') }}</div>

<div class="specific-container">
    <div class="row">
        {{-- Tenaga Profesional --}}
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">{{ __('q_professional_staff_handling') }}</label>
                <x-vendor-radio name="k1_pro_staff" :options="[
                    ['value' => 'yes', 'label' => __('yes_attach_certificate')],
                    ['value' => 'no', 'label' => __('no')],
                ]" :selected="$draft['k1_pro_staff'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_k1" :readonly="$isReadOnly" />

                <div id="wrap_k1"
                    class="toggle-content mt-3 {{ ($draft['k1_pro_staff'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <x-vendor-input 
                        type="file" 
                        name="k1_cert_file" 
                        :label="__('select_certificate')"
                        :value="$draft['k1_cert_file'] ?? null" 
                        :readonly="$isReadOnly" 
                        :placeholder="__('choose_certificate_placeholder')" 
                    />
                </div>
            </div>
        </div>

        {{-- Komitmen K3 --}}
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">{{ __('q_k3_safety_security_commitment') }}</label>
                <div class="text-muted font-size-xs mb-3">{{ __('k3_safety_security_contractor_examples') }}</div>
                <x-vendor-radio name="k2_safety_commitment" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['k2_safety_commitment'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    <div class="row mt-6">
        {{-- BPJS Ketenagakerjaan --}}
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_bpjs_ketenagakerjaan_active') }}</label>
                <x-vendor-radio name="k3_bpjs" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['k3_bpjs'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>

        {{-- APD --}}
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_equipped_with_ppe') }}</label>
                <x-vendor-radio name="k4_apd" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['k4_apd'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    {{-- Keanggotaan Asosiasi --}}
    <div class="row mt-6">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('association_membership_mention') }}</label>
                <input type="text" name="k5_association" class="form-control" placeholder="{{ __('association_name_status_placeholder') }}"
                    value="{{ $draft['k5_association'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                <x-revision-note name="k5_association" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    @include('admin.registrasi.category.partials.equipment-table', [
        'name' => 'k6_equipments',
        'items' => $draft['k6_equipments'] ?? [],
        'isReadOnly' => $isReadOnly,
        'tbodyId' => 'mesinKontraktorBody',
        'templateId' => 'k6_equipment',
        'revisionNotes' => $revisionNotes ?? []
    ])
</div>