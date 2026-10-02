<div class="form-section-title mt-10">{{ __('facility_vendor_header') }}</div>

<div class="specific-container">
    <div class="row">
        {{-- Keanggotaan Asosiasi --}}
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">{{ __('association_membership_mention') }}</label>
                <input type="text" name="f1_association" class="form-control" placeholder="{{ __('association_name_status_placeholder') }}"
                    value="{{ $draft['f1_association'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                <x-revision-note name="f1_association" :notes="$revisionNotes ?? []" />
            </div>
        </div>

        {{-- BPJS --}}
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">{{ __('q_bpjs_ketenagakerjaan_active') }}</label>
                <x-vendor-radio name="f2_bpjs" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['f2_bpjs'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    <div class="row mt-6">
        {{-- Ijin Permenaker --}}
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_permenaker_license_hygiene') }}</label>
                <x-vendor-input name="f3_permenaker_ijin" :placeholder="__('enter_license_number_placeholder')" :value="$draft['f3_permenaker_ijin'] ?? ''"
                    :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    {{-- Tabel Sertifikat Keterampilan Khusus (Q4) --}}
    <div class="row mt-6">
        <div class="col-12">
            <div class="question-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="question-label font-weight-bolder">{{ __('special_skills_certificates_facility') }}</label>
                    @if (!$isReadOnly)
                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold btn-add-repeater"
                            data-target-tbody="#certBody" data-template="#tpl_cert">
                            <i class="flaticon2-plus icon-sm"></i> {{ __('add_certificate') }}
                        </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered bg-light-only">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('skill_type') }}</th>
                                <th>{{ __('holder_name') }}</th>
                                <th width="200px">{{ __('certificate_date') }}</th>
                                @if (!$isReadOnly)
                                    <th width="50px"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="certBody">
                            @php $certs = $draft['f4_certs'] ?? []; @endphp
                            @foreach ($certs as $index => $item)
                                <tr>
                                    <td><input type="text" name="f4_certs[{{ $index }}][type]"
                                            class="form-control form-control-sm" value="{{ $item['type'] ?? '' }}"
                                            {{ $isReadOnly ? 'readonly' : '' }} required></td>
                                    <td><input type="text" name="f4_certs[{{ $index }}][name]"
                                            class="form-control form-control-sm" value="{{ $item['name'] ?? '' }}"
                                            {{ $isReadOnly ? 'readonly' : '' }} required></td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="f4_certs[{{ $index }}][date]"
                                                class="form-control datepicker" autocomplete="off" value="{{ $item['date'] ?? '' }}"
                                                {{ $isReadOnly ? 'readonly disabled' : '' }} required>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </td>
                                    @if (!$isReadOnly)
                                        <td class="text-center align-middle"><button type="button"
                                                class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater"><i
                                                    class="fas fa-trash-alt"></i></button></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-revision-note name="f4_certs" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    <div class="row mt-6">
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">{{ __('q_hygiene_boga_guarantee') }}</label>
                <x-vendor-radio name="f5_hygiene_guarantee" :options="[['value' => 'yes', 'label' => __('available')], ['value' => 'no', 'label' => __('not_available')]]" :selected="$draft['f5_hygiene_guarantee'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">{{ __('q_sanitation_food_handler_cert') }}</label>
                <x-vendor-radio name="f6_sanitation_cert" :options="[['value' => 'yes', 'label' => __('available_attach')], ['value' => 'no', 'label' => __('not_available')]]" :selected="$draft['f6_sanitation_cert'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_f6" :readonly="$isReadOnly" />

                <div id="wrap_f6" class="mt-3 {{ ($draft['f6_sanitation_cert'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <div class="form-group">
                        <label>{{ __('upload_sanitation_cert') }}</label>
                        <x-vendor-input type="file" name="f6_file" :value="$draft['f6_file'] ?? null" :readonly="$isReadOnly"
                            :placeholder="__('upload_sanitation_cert_placeholder')" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-6">
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('kitchen_facility') }}</label>
                <x-vendor-radio name="f7_kitchen_facility" :options="[
                    ['value' => 'sendiri', 'label' => __('owned_by_company')],
                    ['value' => 'subkontrak', 'label' => __('subcontract')],
                ]" :selected="$draft['f7_kitchen_facility'] ?? ''" :readonly="$isReadOnly" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('food_transport_facility') }}</label>
                <x-vendor-radio name="f8_transport_facility" :options="[
                    ['value' => 'sendiri', 'label' => __('owned_by_company')],
                    ['value' => 'subkontrak', 'label' => __('subcontract')],
                ]" :selected="$draft['f8_transport_facility'] ?? ''" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>
</div>

{{-- Template untuk Repeater Sertifikat --}}
@if (!$isReadOnly)
    <script type="text/template" id="tpl_cert">
    <tr>
        <td><input type="text" name="f4_certs[__INDEX__][type]" class="form-control form-control-sm" required></td>
        <td><input type="text" name="f4_certs[__INDEX__][name]" class="form-control form-control-sm" required></td>
        <td>
            <div class="input-group input-group-sm">
                <input type="text" name="f4_certs[__INDEX__][date]" class="form-control datepicker" autocomplete="off" required>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                </div>
            </div>
        </td>
        <td class="text-center align-middle"><button type="button" class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater"><i class="fas fa-trash-alt"></i></button></td>
    </tr>
</script>
@endif