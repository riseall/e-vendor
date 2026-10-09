{{-- ponytail: Simplified read-only product display into 4 columns without horizontal scroll. Upgrade path: add inline certification quick-filter if product rows exceed 100 --}}
@if ($isReadOnly)
    <tr class="product-row" id="product-row-{{ $id }}" data-product-id="{{ $id }}">
        {{-- Kolom: Nama Produk --}}
        <td class="align-middle" style="min-width: 200px;">
            <div class="font-weight-bold text-dark product-name" style="font-size: 0.85rem; line-height: 1.4;">
                {{ $name }}
            </div>
            <x-revision-note name="products.{{ $id }}" :notes="$revisionNotes ?? []" />
            <input type="hidden" name="products[{{ $id }}][erp_product_id]" value="{{ $id }}">
            <input type="hidden" name="products[{{ $id }}][product_name]" value="{{ $name }}">
        </td>

        {{-- Kolom: Manufaktur / Asal --}}
        <td class="align-middle" style="min-width: 170px;">
            <div class="font-weight-bold text-dark" style="font-size: 0.85rem;">
                {{ old('products.' . $id . '.manufaktur', $data['manufaktur'] ?? '-') }}
            </div>
            @php
                $negara = old('products.' . $id . '.negara', $data['negara'] ?? '');
            @endphp
            @if ($negara)
                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                    <i class="fas fa-map-marker-alt text-muted mr-1" style="font-size: 0.7rem;"></i>{{ $negara }}
                </div>
            @endif
        </td>

        {{-- Kolom: Rantai Pasok --}}
        <td class="align-middle" style="min-width: 150px;">
            @php
                $rantaiPasok = old('products.' . $id . '.rantai_pasok', $data['rantai_pasok'] ?? '');
                $rantaiPasokLabels = [
                    'Manufaktur' => __('manufacturer'),
                    'Distributor Pertama' => __('first_distributor'),
                    'Distributor Kedua' => __('second_distributor'),
                    'Trader' => __('trader'),
                    'Repacker' => __('repacker'),
                ];
                $rantaiPasokText = $rantaiPasokLabels[$rantaiPasok] ?? ($rantaiPasok ?: '-');
            @endphp
            <div class="font-weight-bold text-dark" style="font-size: 0.85rem;">
                {{ $rantaiPasokText }}
            </div>
        </td>

        {{-- Kolom: Dokumen & Sertifikasi --}}
        <td class="align-middle" style="min-width: 220px;">
            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                {{-- Surat Keagenan --}}
                @if (!empty($data['file_surat_path']))
                    <button type="button"
                        data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $data['file_surat_path']) }}"
                        data-title="{{ __('agency_letter') }} - {{ $name }}"
                        class="btn btn-xs btn-light-primary btn-preview-doc font-weight-bolder py-1 px-2"
                        title="{{ __('agency_letter') }}">
                        <i class="fas fa-file-contract mr-1"></i> {{ __('agency_letter') }}
                    </button>
                @endif

                {{-- GMP --}}
                @if (!empty($data['gmp_file_path']))
                    <button type="button"
                        data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $data['gmp_file_path']) }}"
                        data-title="GMP - {{ $name }}"
                        class="btn btn-xs btn-light-info btn-preview-doc font-weight-bolder py-1 px-2"
                        title="GMP">
                        <i class="fas fa-shield-alt mr-1"></i> GMP
                    </button>
                @endif

                {{-- TKDN --}}
                @if (($data['has_tkdn'] ?? '') === 'yes' && !empty($data['tkdn_file_path']))
                    <button type="button"
                        data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $data['tkdn_file_path']) }}"
                        data-title="TKDN - {{ $name }}"
                        class="btn btn-xs btn-light-success btn-preview-doc font-weight-bolder py-1 px-2"
                        title="TKDN">
                        <i class="fas fa-award mr-1"></i> TKDN
                    </button>
                @endif

                {{-- SNI --}}
                @if (($data['has_sni'] ?? '') === 'yes' && !empty($data['sni_file_path']))
                    <button type="button"
                        data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $data['sni_file_path']) }}"
                        data-title="SNI - {{ $name }}"
                        class="btn btn-xs btn-light-warning btn-preview-doc font-weight-bolder py-1 px-2"
                        title="SNI">
                        <i class="fas fa-check-circle mr-1"></i> SNI
                    </button>
                @endif

                {{-- Halal --}}
                @if (($data['has_halal'] ?? '') === 'yes' && !empty($data['halal_file_path']))
                    <button type="button"
                        data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $data['halal_file_path']) }}"
                        data-title="Halal - {{ $name }}"
                        class="btn btn-xs btn-light-dark btn-preview-doc font-weight-bolder py-1 px-2"
                        title="Halal">
                        <i class="fas fa-certificate mr-1"></i> Halal
                    </button>
                @endif

                {{-- BSE/TSE --}}
                @if (($data['has_bse_tse'] ?? '') === 'yes' && !empty($data['bse_tse_file_path']))
                    <button type="button"
                        data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $data['bse_tse_file_path']) }}"
                        data-title="BSE/TSE - {{ $name }}"
                        class="btn btn-xs btn-light-danger btn-preview-doc font-weight-bolder py-1 px-2"
                        title="BSE/TSE">
                        <i class="fas fa-file-alt mr-1"></i> BSE/TSE
                    </button>
                @endif

                @if (empty($data['file_surat_path']) && empty($data['gmp_file_path']) && empty($data['tkdn_file_path']) && empty($data['sni_file_path']) && empty($data['halal_file_path']) && empty($data['bse_tse_file_path']))
                    <span class="text-muted font-size-xs">-</span>
                @endif
            </div>
        </td>
    </tr>
@else
    {{-- Edit Mode --}}
    <tr class="product-row" id="product-row-{{ $id }}" data-product-id="{{ $id }}">

        {{-- Kolom: Nama Produk --}}
        <td class="align-top" style="min-width:200px; padding:12px 10px;">
            <div class="font-weight-bold text-dark product-name" style="font-size:0.85rem; line-height:1.4;">
                {{ $name }}
            </div>
            <x-revision-note name="products.{{ $id }}" :notes="$revisionNotes ?? []" />
            <input type="hidden" name="products[{{ $id }}][erp_product_id]" value="{{ $id }}">
            <input type="hidden" name="products[{{ $id }}][product_name]" value="{{ $name }}">
        </td>

        {{-- Kolom: Manufaktur / Asal --}}
        <td class="align-top" style="min-width:200px; padding:12px 10px;">
            <x-vendor-input name="products[{{ $id }}][manufaktur]" :value="old('products.' . $id . '.manufaktur', $data['manufaktur'] ?? '')"
                :placeholder="__('manufacturer_origin')" :readonly="false" required />

            <label class="font-weight-bold text-dark d-block mt-2" style="font-size:0.75rem;">GMP</label>
            <x-vendor-input type="file" name="products[{{ $id }}][gmp_file]"
                existingName="products[{{ $id }}][existing_gmp_file]" :value="$data['gmp_file_path'] ?? null" :application="$application"
                :readonly="false" required />
        </td>

        {{-- Kolom: Negara --}}
        <td class="align-top" style="min-width:160px; padding:12px 10px;">
            <x-vendor-select name="products[{{ $id }}][negara]" :options="[
                'Indonesia' => __('indonesia'),
                'China' => __('china'),
                'Japan' => __('japan'),
                'Korea' => __('korea'),
                'Malaysia' => __('malaysia'),
                'Thailand' => __('thailand'),
                'Vietnam' => __('vietnam'),
            ]" :selected="old('products.' . $id . '.negara', $data['negara'] ?? '')"
                :readonly="false" required wrapperClass="mb-0" />
        </td>

        {{-- Kolom: Rantai Pasok --}}
        <td class="align-top" style="min-width:160px; padding:12px 10px;">
            <x-vendor-select name="products[{{ $id }}][rantai_pasok]" :options="[
                'Manufaktur' => __('manufacturer'),
                'Distributor Pertama' => __('first_distributor'),
                'Distributor Kedua' => __('second_distributor'),
                'Trader' => __('trader'),
                'Repacker' => __('repacker'),
            ]" :selected="old('products.' . $id . '.rantai_pasok', $data['rantai_pasok'] ?? '')"
                :readonly="false" required wrapperClass="mb-0" />
        </td>

        {{-- Kolom: Surat Keagenan --}}
        <td class="align-top" style="min-width:170px; padding:12px 10px;">
            <x-vendor-input type="file" name="products[{{ $id }}][file_surat]"
                existingName="products[{{ $id }}][existing_file_surat]" :value="$data['file_surat_path'] ?? null" :application="$application"
                :readonly="false" required />
        </td>

        {{-- Kolom: TKDN --}}
        @php $hasTkdn = old('products.' . $id . '.has_tkdn', $data['has_tkdn'] ?? 'no'); @endphp
        <td class="align-top" style="min-width:160px; padding:12px 10px;">
            <x-vendor-select name="products[{{ $id }}][has_tkdn]" :options="['no' => __('no'), 'yes' => __('yes')]" :selected="$hasTkdn"
                :readonly="false" wrapperClass="mb-0" class="product-cert-toggle"
                data-target=".detail-tkdn-{{ $id }}" />

            <div class="detail-tkdn-{{ $id }} mt-2" style="{{ $hasTkdn === 'yes' ? '' : 'display:none' }}">
                <x-vendor-input type="file" name="products[{{ $id }}][tkdn_file]"
                    existingName="products[{{ $id }}][existing_tkdn_file]" :value="$data['tkdn_file_path'] ?? null"
                    :application="$application" :readonly="false" />
            </div>
        </td>

        {{-- Kolom: SNI --}}
        @php $hasSni = old('products.' . $id . '.has_sni', $data['has_sni'] ?? 'no'); @endphp
        <td class="align-top" style="min-width:160px; padding:12px 10px;">
            <x-vendor-select name="products[{{ $id }}][has_sni]" :options="['no' => __('no'), 'yes' => __('yes')]" :selected="$hasSni"
                :readonly="false" wrapperClass="mb-0" class="product-cert-toggle"
                data-target=".detail-sni-{{ $id }}" />

            <div class="detail-sni-{{ $id }} mt-2" style="{{ $hasSni === 'yes' ? '' : 'display:none' }}">
                <x-vendor-input type="file" name="products[{{ $id }}][sni_file]"
                    existingName="products[{{ $id }}][existing_sni_file]" :value="$data['sni_file_path'] ?? null"
                    :application="$application" :readonly="false" />
            </div>
        </td>

        {{-- Kolom: Halal --}}
        @php $hasHalal = old('products.' . $id . '.has_halal', $data['has_halal'] ?? 'no'); @endphp
        <td class="align-top" style="min-width:160px; padding:12px 10px;">
            <x-vendor-select name="products[{{ $id }}][has_halal]" :options="['no' => __('no'), 'yes' => __('yes')]" :selected="$hasHalal"
                :readonly="false" wrapperClass="mb-0" class="product-cert-toggle"
                data-target=".detail-halal-{{ $id }}" />

            <div class="detail-halal-{{ $id }} mt-2"
                style="{{ $hasHalal === 'yes' ? '' : 'display:none' }}">
                <x-vendor-input type="file" name="products[{{ $id }}][halal_file]"
                    existingName="products[{{ $id }}][existing_halal_file]" :value="$data['halal_file_path'] ?? null"
                    :application="$application" :readonly="false" />
            </div>
        </td>

        {{-- Kolom: BSE/TSE --}}
        @php $hasBseTse = old('products.' . $id . '.has_bse_tse', $data['has_bse_tse'] ?? 'no'); @endphp
        <td class="align-top" style="min-width:160px; padding:12px 10px;">
            <x-vendor-select name="products[{{ $id }}][has_bse_tse]" :options="['no' => __('no'), 'yes' => __('yes')]" :selected="$hasBseTse"
                :readonly="false" wrapperClass="mb-0" class="product-cert-toggle"
                data-target=".detail-bse-tse-{{ $id }}" />

            <div class="detail-bse-tse-{{ $id }} mt-2"
                style="{{ $hasBseTse === 'yes' ? '' : 'display:none' }}">
                <x-vendor-input type="file" name="products[{{ $id }}][bse_tse_file]"
                    existingName="products[{{ $id }}][existing_bse_tse_file]" :value="$data['bse_tse_file_path'] ?? null"
                    :application="$application" :readonly="false" />
            </div>
        </td>

        {{-- Kolom: Aksi --}}
        <td class="align-top text-center" style="min-width:56px; padding:12px 8px;">
            <button type="button" class="btn btn-icon btn-sm btn-danger btn-hapus-produk"
                data-id="{{ $id }}" title="{{ __('delete_product') }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>

    </tr>
@endif
