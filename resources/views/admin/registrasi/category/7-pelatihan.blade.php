<div class="form-section-title mt-10">{{ __('training_consultant_vendor_header') }}</div>

<div class="specific-container">
    {{-- Umum: Keanggotaan Asosiasi --}}
    <div class="row">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('association_membership_mention') }}</label>
                <input type="text" name="g1_association" class="form-control" placeholder="{{ __('association_name_status_placeholder') }}"
                    value="{{ $draft['g1_association'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                <x-revision-note name="g1_association" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    {{-- SUB-SECTION: JASA PELATIHAN --}}
    <h6 class="font-weight-bolder mb-4 text-primary">{{ __('for_training_services') }}</h6>

    <div class="question-wrapper">
        <label class="question-label">{{ __('q_trainer_certification_guarantee') }}</label>
        <x-vendor-radio name="g2_trainer_cert" :options="[['value' => 'yes', 'label' => __('available')], ['value' => 'no', 'label' => __('not_available')]]" :selected="$draft['g2_trainer_cert'] ?? 'no'" class="toggle-input"
            data-target="#wrap_g2" :readonly="$isReadOnly" />

        <div id="wrap_g2" class="mt-4 {{ ($draft['g2_trainer_cert'] ?? '') === 'yes' ? '' : 'd-none' }}">
            <x-vendor-input name="g2_cert_source" :label="__('specify_issuing_institution')"
                :placeholder="__('issuing_institution_placeholder')" :value="$draft['g2_cert_source'] ?? ''" :readonly="$isReadOnly" />
        </div>
    </div>

    {{-- SUB-SECTION: JASA KONSULTAN DAN NOTARIS --}}
    <h6 class="font-weight-bolder mb-4 text-primary mt-6">{{ __('for_consultant_and_notary') }}</h6>

    <div class="row mt-6">
        <div class="col-12">
            <div class="question-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="question-label">{{ __('permit_number_and_validity') }}</label>
                    @if (!$isReadOnly)
                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold btn-add-repeater"
                            data-target-tbody="#notarisBody" data-template="#tpl_notaris">
                            <i class="flaticon2-plus icon-sm"></i> {{ __('add_permit') }}
                        </button>
                    @endif
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-borderless">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('permit_type_description') }}</th>
                                <th width="200px">{{ __('license_number') }}</th>
                                <th width="200px">{{ __('valid_until') }}</th>
                                @if (!$isReadOnly)
                                    <th width="50px"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="notarisBody">
                            @php $permits = $draft['g3_permits'] ?? []; @endphp
                            @foreach ($permits as $index => $item)
                                <tr>
                                    <td>
                                        <input type="text" name="g3_permits[{{ $index }}][desc]"
                                            class="form-control form-control-sm"
                                            placeholder="{{ __('permit_desc_placeholder') }}"
                                            value="{{ $item['desc'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}
                                            required>
                                    </td>
                                    <td>
                                        <input type="text" name="g3_permits[{{ $index }}][no]"
                                            class="form-control form-control-sm" placeholder="{{ __('license_number') }}"
                                            value="{{ $item['no'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}
                                            required>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="g3_permits[{{ $index }}][date]"
                                                class="form-control datepicker" autocomplete="off" value="{{ $item['date'] ?? '' }}"
                                                {{ $isReadOnly ? 'readonly disabled' : '' }} required>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </td>
                                    @if (!$isReadOnly)
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-revision-note name="g3_permits" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    {{-- SUB-SECTION: JASA ALIH DAYA TENAGA KERJA --}}
    <h6 class="font-weight-bolder mb-4 text-primary mt-6">{{ __('for_labor_outsourcing_services') }}</h6>

    <div class="row">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('operational_permit_kemnaker') }}</label>
                <x-vendor-input name="g4_labor_permit" :placeholder="__('enter_operational_permit_placeholder')"
                    :value="$draft['g4_labor_permit'] ?? ''" :readonly="$isReadOnly" />
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_bpjs_ketenagakerjaan_active_question') }}</label>
                <x-vendor-radio name="g5_bpjs" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['g5_bpjs'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>
</div>

{{-- Template untuk Repeater Notaris --}}
@if (!$isReadOnly)
    <script type="text/template" id="tpl_notaris">
    <tr>
        <td><input type="text" name="g3_permits[__INDEX__][desc]" class="form-control form-control-sm" placeholder="{{ __('permit_desc_placeholder') }}" required></td>
        <td><input type="text" name="g3_permits[__INDEX__][no]" class="form-control form-control-sm" placeholder="{{ __('license_number') }}" required></td>
        <td>
            <div class="input-group input-group-sm">
                <input type="text" name="g3_permits[__INDEX__][date]" class="form-control datepicker" autocomplete="off" required>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                </div>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
</script>
@endif