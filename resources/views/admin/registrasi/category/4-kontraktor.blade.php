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
                    <div class="form-group">
                        <label>{{ __('select_certificate') }}</label>
                        <x-vendor-input 
                            type="file" 
                            name="k1_cert_file" 
                            :value="$draft['k1_cert_file'] ?? null" 
                            :readonly="$isReadOnly" 
                            :placeholder="__('choose_certificate_placeholder')" 
                        />
                    </div>
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

    {{-- Daftar Peralatan --}}
    <div class="row mt-8">
        <div class="col-12">
            <div class="question-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="question-label font-weight-bolder">{{ __('inspection_equipment_and_machinery_list') }}</label>
                    @if (!$isReadOnly)
                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold btn-add-repeater"
                            data-target-tbody="#mesinKontraktorBody" data-template="#k6_equipment">
                            <i class="flaticon2-plus icon-sm"></i> {{ __('add_equipment') }}
                        </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th width="50px" class="text-center">{{ __('no_short') }}</th>
                                <th>{{ __('equipment_type') }}</th>
                                <th width="100px">{{ __('qty') }}</th>
                                <th>{{ __('capacity_output') }}</th>
                                <th>{{ __('brand_type') }}</th>
                                <th width="120px">{{ __('year') }}</th>
                                @if (!$isReadOnly)
                                    <th width="50px"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="mesinKontraktorBody">
                            @php $equipments = $draft['k6_equipments'] ?? []; @endphp

                            @foreach ($equipments as $index => $item)
                                <tr>
                                    <td class="text-center align-middle row-number">{{ $loop->iteration }}</td>
                                    <td><input type="text" class="form-control form-control-sm"
                                            name="k6_equipments[{{ $index }}][jenis]"
                                            value="{{ $item['jenis'] ?? '' }}" required
                                            {{ $isReadOnly ? 'readonly' : '' }}></td>
                                    <td><input type="number" class="form-control form-control-sm"
                                            name="k6_equipments[{{ $index }}][jml]"
                                            value="{{ $item['jml'] ?? '' }}" required
                                            {{ $isReadOnly ? 'readonly' : '' }}></td>
                                    <td><input type="text" class="form-control form-control-sm"
                                            name="k6_equipments[{{ $index }}][kapasitas]"
                                            value="{{ $item['kapasitas'] ?? '' }}"
                                            {{ $isReadOnly ? 'readonly' : '' }}></td>
                                    <td><input type="text" class="form-control form-control-sm"
                                            name="k6_equipments[{{ $index }}][merk]"
                                            value="{{ $item['merk'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                                    </td>
                                    <td><input type="number" class="form-control form-control-sm"
                                            name="k6_equipments[{{ $index }}][tahun]"
                                            value="{{ $item['tahun'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}
                                            placeholder="YYYY"></td>
                                    @if (!$isReadOnly)
                                        <td class="text-center align-middle">
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
                <x-revision-note name="k6_equipments" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    {{-- Template Hidden untuk JS Repeater --}}
    @if (!$isReadOnly)
        <script type="text/template" id="k6_equipment">
            <tr>
                <td class="text-center align-middle row-number"></td>
                <td><input type="text" class="form-control form-control-sm" name="k6_equipments[__INDEX__][jenis]" required></td>
                <td><input type="number" class="form-control form-control-sm" name="k6_equipments[__INDEX__][jml]" required></td>
                <td><input type="text" class="form-control form-control-sm" name="k6_equipments[__INDEX__][kapasitas]"></td>
                <td><input type="text" class="form-control form-control-sm" name="k6_equipments[__INDEX__][merk]"></td>
                <td><input type="number" class="form-control form-control-sm" name="k6_equipments[__INDEX__][tahun]" placeholder="YYYY"></td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        </script>
    @endif
</div>