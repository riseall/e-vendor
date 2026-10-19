{{-- ponytail: Product detail modal for Step 7. Replaces oversized 10-column horizontal scrolling with clean popup form. Ceiling: single product per modal. Upgrade path: add batch-fill if vendor supplies >50 items --}}
@php
    $isBahanKemas = strpos($id, 'KEMAS-') === 0;
    $initialType = old(
        'products.' . $id . '.product_type',
        $data['product_type'] ?? ($isBahanKemas ? 'kemas' : 'baku'),
    );
    $isBaku = $initialType === 'baku';
@endphp
<div class="modal fade modal-product-detail" id="modal-product-{{ $id }}" data-backdrop="static" tabindex="-1"
    role="dialog" aria-labelledby="modalLabel-{{ $id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-product-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header py-3 px-6 bg-light">
                <div>
                    <h5 class="modal-title font-weight-bolder text-dark mb-0" id="modalLabel-{{ $id }}">
                        <i class="fas fa-boxes text-primary mr-2"></i>{{ $name }}
                    </h5>
                    <span class="text-muted font-size-xs mt-1 d-block">
                        {{ __('fill_product_details_notice') }}
                    </span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>

            <div class="modal-body py-4 px-6">
                {{-- 1. Informasi Pabrik & Asal --}}
                <div class="d-flex align-items-center justify-content-between border-bottom pb-1 mb-3">
                    <span class="font-weight-bolder text-dark font-size-sm">
                        1. {{ __('origin_and_supply_chain') }}
                    </span>
                    <span class="text-danger font-size-xs font-weight-bold">* {{ __('required') }}</span>
                </div>

                <div class="row">
                    {{-- Kategori Produk (Bahan Baku vs Bahan Kemas) --}}
                    <div class="col-md-4 mb-2">
                        <label class="font-weight-bold font-size-xs d-block text-dark mb-2">
                            {{ __('product_category') }} <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex flex-column" style="gap: 8px;">
                            <label class="radio radio-primary mb-0 align-items-center">
                                <input type="radio" name="products[{{ $id }}][product_type]" value="baku"
                                    class="product-type-radio" data-id="{{ $id }}"
                                    {{ $isBaku ? 'checked' : '' }}>
                                <span></span>
                                <div class="d-inline-block font-size-xs">
                                    <strong class="text-dark">{{ __('raw_material') }}</strong>
                                    <small
                                        class="text-danger font-weight-bold ml-1">({{ __('gmp_mandatory') }})</small>
                                </div>
                            </label>
                            <label class="radio radio-primary mb-0 align-items-center">
                                <input type="radio" name="products[{{ $id }}][product_type]" value="kemas"
                                    class="product-type-radio" data-id="{{ $id }}"
                                    {{ !$isBaku ? 'checked' : '' }}>
                                <span></span>
                                <div class="d-inline-block font-size-xs">
                                    <strong class="text-dark">{{ __('packaging_material_or_other') }}</strong>
                                    <small class="text-muted ml-1">({{ __('gmp_optional') }})</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Manufaktur --}}
                    <div class="col-md-4 mb-2">
                        <x-vendor-input name="products[{{ $id }}][manufaktur]" :label="__('manufacturer_origin')"
                            labelClass="font-size-xs font-weight-bold text-dark d-block mb-1" :value="old('products.' . $id . '.manufaktur', $data['manufaktur'] ?? '')"
                            :placeholder="__('manufacturer_origin')" :readonly="false" required />
                    </div>

                    {{-- Negara Asal --}}
                    <div class="col-md-4 mb-2">
                        <x-vendor-select name="products[{{ $id }}][negara]" :label="__('country')"
                            labelClass="font-size-xs font-weight-bold text-dark d-block mb-1" :options="config('countries', [])"
                            data-live-search="true" :selected="old('products.' . $id . '.negara', $data['negara'] ?? 'Indonesia')" :readonly="false" required wrapperClass="mb-2" />
                    </div>
                </div>

                {{-- 2. Dokumen & Rantai Pasok --}}
                <div class="d-flex align-items-center justify-content-between border-bottom pb-1 mb-3 mt-3">
                    <span class="font-weight-bolder text-dark font-size-sm">
                        2. {{ __('product_documents') }}
                    </span>
                    <span class="font-size-xs text-muted">{{ __('agency_required_gmp_rule_notice') }}</span>
                </div>

                <div class="row">
                    {{-- Rantai Pasok --}}
                    <div class="col-md-4 mb-2">
                        <x-vendor-select name="products[{{ $id }}][rantai_pasok]" :label="__('supply_chain')"
                            labelClass="font-size-xs font-weight-bold text-dark d-block mb-1" :options="[
                                'Manufaktur' => __('manufacturer'),
                                'Distributor Pertama' => __('first_distributor'),
                                'Distributor Kedua' => __('second_distributor'),
                                'Trader' => __('trader'),
                                'Repacker' => __('repacker'),
                            ]"
                            :selected="old('products.' . $id . '.rantai_pasok', $data['rantai_pasok'] ?? '')" :readonly="false" required wrapperClass="mb-2" />
                    </div>

                    {{-- Surat Keagenan --}}
                    <div class="col-md-4 mb-2">
                        <x-vendor-input type="file" name="products[{{ $id }}][file_surat]"
                            :label="__('agency_letter')" labelClass="font-size-xs font-weight-bold text-dark d-block mb-1"
                            existingName="products[{{ $id }}][existing_file_surat]" :value="$data['file_surat_path'] ?? null"
                            :application="$application" :readonly="false" required />
                    </div>

                    {{-- Sertifikat GMP (Wajib untuk Bahan Baku, Opsional untuk Bahan Kemas) --}}
                    <div class="col-md-4 mb-2 gmp-col-{{ $id }}">
                        <x-vendor-input type="file" name="products[{{ $id }}][gmp_file]"
                            label="{{ __('gmp_certificate') }}"
                            labelClass="font-size-xs font-weight-bold text-dark d-block mb-1"
                            existingName="products[{{ $id }}][existing_gmp_file]" :value="$data['gmp_file_path'] ?? null"
                            :application="$application" :readonly="false" :required="$isBaku" />
                    </div>
                </div>

                {{-- 3. Sertifikasi Tambahan (Opsional) --}}
                <div class="d-flex align-items-center justify-content-between border-bottom pb-1 mb-3 mt-3">
                    <span class="font-weight-bolder text-dark font-size-sm">
                        3. {{ __('optional_certifications') }}
                    </span>
                    <span class="text-muted font-size-xs">{{ __('optional') }}</span>
                </div>

                @php
                    $certToggles = [
                        'tkdn' => [
                            'select' => 'has_tkdn',
                            'file' => 'tkdn_file',
                            'existing' => 'existing_tkdn_file',
                            'path' => 'tkdn_file_path',
                            'label' => __('cert_tkdn_label'),
                        ],
                        'sni' => [
                            'select' => 'has_sni',
                            'file' => 'sni_file',
                            'existing' => 'existing_sni_file',
                            'path' => 'sni_file_path',
                            'label' => __('cert_sni_label'),
                        ],
                        'halal' => [
                            'select' => 'has_halal',
                            'file' => 'halal_file',
                            'existing' => 'existing_halal_file',
                            'path' => 'halal_file_path',
                            'label' => __('cert_halal_label'),
                        ],
                        'bse_tse' => [
                            'select' => 'has_bse_tse',
                            'file' => 'bse_tse_file',
                            'existing' => 'existing_bse_tse_file',
                            'path' => 'bse_tse_file_path',
                            'label' => __('cert_bse_tse_label'),
                        ],
                    ];
                @endphp

                <div class="row">
                    @foreach ($certToggles as $key => $cfg)
                        @php $isSelected = old('products.' . $id . '.' . $cfg['select'], $data[$cfg['select']] ?? 'no'); @endphp
                        <div class="col-md-3 col-6 mb-2">
                            <div
                                class="border rounded p-2 bg-light-secondary h-100 d-flex flex-column justify-content-between cert-box">
                                <x-vendor-select name="products[{{ $id }}][{{ $cfg['select'] }}]"
                                    :label="$cfg['label']" labelClass="font-weight-bold text-dark d-block mb-1 cert-label"
                                    :options="['no' => __('no'), 'yes' => __('yes')]" :selected="$isSelected" :readonly="false" wrapperClass="mb-1"
                                    class="product-cert-toggle"
                                    data-target=".detail-{{ $key }}-{{ $id }}" />

                                <div class="detail-{{ $key }}-{{ $id }} mt-1"
                                    style="{{ $isSelected === 'yes' ? '' : 'display:none' }}">
                                    <x-vendor-input type="file"
                                        name="products[{{ $id }}][{{ $cfg['file'] }}]"
                                        existingName="products[{{ $id }}][{{ $cfg['existing'] }}]"
                                        :value="$data[$cfg['path']] ?? null" :application="$application" :readonly="false" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="modal-footer py-2 px-6 bg-light">
                <button type="button" class="btn btn-sm btn-light-danger font-weight-bold" data-dismiss="modal">
                    {{ __('close') }}
                </button>
                <button type="button" class="btn btn-sm btn-primary font-weight-bold btn-save-modal-product"
                    data-id="{{ $id }}">
                    <i class="fas fa-check mr-1"></i> {{ __('save_and_done') }}
                </button>
            </div>
        </div>
    </div>
</div>
