<div class="form-section-title">{{ __('raw_material_vendor_header') }}</div>

<div class="specific-container">
    {{-- Baris 1: Produsen & Agen Tunggal --}}
    <div class="row">
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_is_manufacturer') }} @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="q1_is_manufacturer" label="" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['q1_is_manufacturer'] ?? 'no'"
                    class="toggle-input" data-target="#wrap_q1" :readonly="$isReadOnly" />

                <div id="wrap_q1"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['q1_is_manufacturer'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <x-vendor-input name="q1_manufacturer_name" :label="__('mention_manufacturer_company_name')"
                        :value="$draft['q1_manufacturer_name'] ?? ''" :readonly="$isReadOnly" :placeholder="__('company_name_placeholder')" />
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_is_sole_agent') }} @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="q2_is_sole_agent" label="" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['q2_is_sole_agent'] ?? 'no'"
                    class="toggle-input" data-target="#wrap_q2" :readonly="$isReadOnly" />

                <div id="wrap_q2"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['q2_is_sole_agent'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    @php $q2File = $draft['q2_auth_letter'] ?? null; @endphp

                    <div class="form-group">
                        <label>{{ __('agency_authorization_letter') }}</label>
                        <x-vendor-input type="file" name="q2_auth_letter" :value="$draft['q2_auth_letter'] ?? null" :readonly="$isReadOnly"
                            :placeholder="__('upload_auth_letter_placeholder')" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Baris 2: Transportasi & Gudang --}}
    <div class="row">
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_delivery_transportation') }} @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="q3_transportation" label="" :options="[
                    ['value' => 'owned', 'label' => __('owned_by_company')],
                    ['value' => '3pl', 'label' => __('third_party_logistics')],
                ]" :selected="$draft['q3_transportation'] ?? 'owned'"
                    class="toggle-input" data-target="#wrap_q3" :readonly="$isReadOnly" />

                <div id="wrap_q3"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['q3_transportation'] ?? '') === '3pl' ? '' : 'd-none' }}">
                    <x-vendor-input name="q3_3pl_name" :label="__('mention_3pl_company_name')" :value="$draft['q3_3pl_name'] ?? ''"
                        :readonly="$isReadOnly" :placeholder="__('logistics_name_placeholder')" />
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_has_own_warehouse') }} @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="q4_has_warehouse" label="" :options="[['value' => 'yes', 'label' => __('yes')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['q4_has_warehouse'] ?? 'no'"
                    class="toggle-input" data-target="#wrap_q4" :readonly="$isReadOnly" />

                <div id="wrap_q4"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['q4_has_warehouse'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <x-vendor-input name="q4_warehouse_address" :label="__('warehouse_address')" :value="$draft['q4_warehouse_address'] ?? ''"
                        :readonly="$isReadOnly" />

                    <x-vendor-radio name="q4_warehouse_condition" :label="__('warehouse_condition')" :options="[
                        ['value' => 'cold', 'label' => __('cold_storage')],
                        ['value' => 'ac', 'label' => __('ac_room')],
                        ['value' => 'ambient', 'label' => __('ambient_temperature')],
                        ['value' => 'grey', 'label' => __('grey_area_gmp')],
                    ]"
                        :selected="$draft['q4_warehouse_condition'] ?? ''" :readonly="$isReadOnly" />
                </div>
            </div>
        </div>
    </div>

    {{-- Baris 3: Sertifikat & SIPA --}}
    <div class="row">
        <div class="col-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('cdob_gdp_certificate') }}</label>
                <div class="form-row">
                    <div class="col-md-4">
                        <x-vendor-input type="file" name="q5_document" :label="__('cdob_cert_document')"
                            :value="$draft['q5_document'] ?? null" :readonly="$isReadOnly" :placeholder="__('choose_cdob_file')" />
                    </div>
                    <div class="col-md-4">
                        <x-vendor-input name="q5_issue_date" :label="__('issue_date')" type="text" class="datepicker" autocomplete="off" rightIcon="far fa-calendar-alt" :value="$draft['q5_issue_date'] ?? ''"
                            :readonly="$isReadOnly" />
                    </div>
                    <div class="col-md-4">
                        <x-vendor-input name="q5_valid_until" :label="__('valid_until')" type="text" class="datepicker" autocomplete="off" rightIcon="far fa-calendar-alt" :value="$draft['q5_valid_until'] ?? ''"
                            :readonly="$isReadOnly" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h6 class="font-weight-bolder mb-4 text-primary">{{ __('special_for_local_raw_material') }}</h6>

    {{-- Surat Izin PBF --}}
    <div class="row">
        <div class="col-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('pbf_license') }}</label>
                <div class="form-row">
                    <div class="col-md-4">
                        <x-vendor-input type="file" name="pbf_document" :label="__('pbf_license_document')"
                            :value="$draft['pbf_document'] ?? null" :readonly="$isReadOnly" :placeholder="__('choose_pbf_file')" />
                    </div>
                    <div class="col-md-4">
                        <x-vendor-input name="pbf_num" :label="__('license_number')" :value="$draft['pbf_num'] ?? ''" :readonly="$isReadOnly"
                            :placeholder="__('pbf_num_placeholder')" />
                    </div>
                    <div class="col-md-4">
                        <x-vendor-input name="pbf_issue_date" :label="__('issue_date')" type="text" class="datepicker" autocomplete="off" rightIcon="far fa-calendar-alt" :value="$draft['pbf_issue_date'] ?? ''"
                            :readonly="$isReadOnly" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('sipa_apj') }}</label>
                <div class="form-row">
                    <div class="col-md-6">
                        <x-vendor-input type="file" name="q6_document" :label="__('sipa_apj_document')" :value="$draft['q6_document'] ?? null"
                            :readonly="$isReadOnly" :placeholder="__('choose_sipa_file')" />
                    </div>
                    <div class="col-md-6">
                        <x-vendor-input name="q6_name" :label="__('name')" :placeholder="__('name')" :value="$draft['q6_name'] ?? ''"
                            :readonly="$isReadOnly" />
                    </div>
                    <div class="col-md-4">
                        <x-vendor-input name="q6_num" :label="__('sipa_number')" :placeholder="__('sipa_number')" :value="$draft['q6_num'] ?? ''"
                            :readonly="$isReadOnly" />
                    </div>
                    <div class="col-md-4">
                        <x-vendor-input name="q6_issue_date" :label="__('issue_date')" type="text" class="datepicker" autocomplete="off" rightIcon="far fa-calendar-alt" :value="$draft['q6_issue_date'] ?? ''"
                            :readonly="$isReadOnly" />
                    </div>
                    <div class="col-md-4">
                        <x-vendor-input name="q6_valid_until" :label="__('valid_until')" type="text" class="datepicker" autocomplete="off" rightIcon="far fa-calendar-alt"
                            :value="$draft['q6_valid_until'] ?? ''" :readonly="$isReadOnly" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h6 class="font-weight-bolder mb-4 text-primary">{{ __('special_for_packaging_material') }}</h6>

    {{-- Baris 4: Peralatan (Repeater) --}}
    <div class="row">
        <div class="col-12">
            <div class="question-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="question-label font-weight-bolder">{{ __('equipment_machinery_data') }}</label>
                    @if (!$isReadOnly)
                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold btn-add-repeater"
                            data-target-tbody="#equipmentBody" data-template="#tpl_equipment">
                            <i class="flaticon2-plus icon-sm"></i> {{ __('add_equipment') }}
                        </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th width="50px">{{ __('no_short') }}</th>
                                <th>{{ __('equipment_type') }}</th>
                                <th width="100px">{{ __('qty') }}</th>
                                <th>{{ __('current_capacity_output') }}</th>
                                <th>{{ __('brand_type') }}</th>
                                <th width="120px">{{ __('year_of_manufacture') }}</th>
                                @if (!$isReadOnly)
                                    <th width="50px"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="equipmentBody">
                            @php $equipments = $draft['q7_equipments'] ?? []; @endphp
                            @foreach ($equipments as $index => $item)
                                <tr>
                                    <td class="text-center align-middle row-number">{{ $loop->iteration }}</td>
                                    <td><input type="text" class="form-control form-control-sm"
                                            name="q7_equipments[{{ $index }}][jenis]"
                                            value="{{ $item['jenis'] ?? '' }}" required
                                            {{ $isReadOnly ? 'readonly' : '' }}></td>
                                    <td><input type="number" class="form-control form-control-sm"
                                            name="q7_equipments[{{ $index }}][jml]"
                                            value="{{ $item['jml'] ?? '' }}" required
                                            {{ $isReadOnly ? 'readonly' : '' }}></td>
                                    <td><input type="text" class="form-control form-control-sm"
                                            name="q7_equipments[{{ $index }}][kapasitas]"
                                            value="{{ $item['kapasitas'] ?? '' }}"
                                            {{ $isReadOnly ? 'readonly' : '' }}></td>
                                    <td><input type="text" class="form-control form-control-sm"
                                            name="q7_equipments[{{ $index }}][merk]"
                                            value="{{ $item['merk'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                                    </td>
                                    <td><input type="number" class="form-control form-control-sm"
                                            name="q7_equipments[{{ $index }}][tahun]"
                                            value="{{ $item['tahun'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                                    </td>
                                    @if (!$isReadOnly)
                                        <td class="text-center"><button type="button"
                                                class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater"><i
                                                    class="fas fa-trash-alt"></i></button></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-revision-note name="q7_equipments" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    {{-- Baris 5: Material Impor --}}
    <div class="row">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_imported_material_for_production') }}</label>
                <x-vendor-radio name="q8_is_import" :options="[
                    ['value' => 'yes', 'label' => __('yes_from_which_country')],
                    ['value' => 'no', 'label' => __('no')],
                ]" :selected="$draft['q8_is_import'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_q8" :readonly="$isReadOnly" />

                <div id="wrap_q8"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['q8_is_import'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <x-vendor-input name="q8_country_name" :label="__('mention_country_name')" :value="$draft['q8_country_name'] ?? ''"
                        :readonly="$isReadOnly" :placeholder="__('import_origin_country_placeholder')" />
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Template untuk Repeater --}}
@if (!$isReadOnly)
    <script type="text/template" id="tpl_equipment">
    <tr>
        <td class="text-center align-middle row-number"></td>
        <td><input type="text" class="form-control form-control-sm" name="q7_equipments[__INDEX__][jenis]" required></td>
        <td><input type="number" class="form-control form-control-sm" name="q7_equipments[__INDEX__][jml]" required></td>
        <td><input type="text" class="form-control form-control-sm" name="q7_equipments[__INDEX__][kapasitas]"></td>
        <td><input type="text" class="form-control form-control-sm" name="q7_equipments[__INDEX__][merk]"></td>
        <td><input type="number" class="form-control form-control-sm" name="q7_equipments[__INDEX__][tahun]"></td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater"><i class="fas fa-trash-alt"></i></button>
        </td>
    </tr>
</script>
@endif
