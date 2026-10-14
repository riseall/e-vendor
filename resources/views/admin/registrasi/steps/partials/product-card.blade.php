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
            @php
                $certButtons = [
                    ['path' => $data['file_surat_path'] ?? null, 'title' => __('agency_letter') . ' - ' . $name, 'label' => __('agency_letter'), 'class' => 'primary', 'icon' => 'file-contract'],
                    ['path' => $data['gmp_file_path'] ?? null, 'title' => 'GMP - ' . $name, 'label' => 'GMP', 'class' => 'info', 'icon' => 'shield-alt'],
                    ['path' => (($data['has_tkdn'] ?? '') === 'yes') ? ($data['tkdn_file_path'] ?? null) : null, 'title' => 'TKDN - ' . $name, 'label' => 'TKDN', 'class' => 'success', 'icon' => 'award'],
                    ['path' => (($data['has_sni'] ?? '') === 'yes') ? ($data['sni_file_path'] ?? null) : null, 'title' => 'SNI - ' . $name, 'label' => 'SNI', 'class' => 'warning', 'icon' => 'check-circle'],
                    ['path' => (($data['has_halal'] ?? '') === 'yes') ? ($data['halal_file_path'] ?? null) : null, 'title' => 'Halal - ' . $name, 'label' => 'Halal', 'class' => 'dark', 'icon' => 'certificate'],
                    ['path' => (($data['has_bse_tse'] ?? '') === 'yes') ? ($data['bse_tse_file_path'] ?? null) : null, 'title' => 'BSE/TSE - ' . $name, 'label' => 'BSE/TSE', 'class' => 'danger', 'icon' => 'file-alt'],
                ];
                $hasAnyCert = false;
            @endphp
            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                @foreach ($certButtons as $btn)
                    @if (!empty($btn['path']))
                        @php $hasAnyCert = true; @endphp
                        <button type="button"
                            data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $btn['path']) }}"
                            data-title="{{ $btn['title'] }}"
                            class="btn btn-xs btn-light-{{ $btn['class'] }} btn-preview-doc font-weight-bolder py-1 px-2"
                            title="{{ $btn['label'] }}">
                            <i class="fas fa-{{ $btn['icon'] }} mr-1"></i> {{ $btn['label'] }}
                        </button>
                    @endif
                @endforeach
                @if (!$hasAnyCert)
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

        @php
            $certToggles = [
                'tkdn' => ['select' => 'has_tkdn', 'file' => 'tkdn_file', 'existing' => 'existing_tkdn_file', 'path' => 'tkdn_file_path'],
                'sni' => ['select' => 'has_sni', 'file' => 'sni_file', 'existing' => 'existing_sni_file', 'path' => 'sni_file_path'],
                'halal' => ['select' => 'has_halal', 'file' => 'halal_file', 'existing' => 'existing_halal_file', 'path' => 'halal_file_path'],
                'bse-tse' => ['select' => 'has_bse_tse', 'file' => 'bse_tse_file', 'existing' => 'existing_bse_tse_file', 'path' => 'bse_tse_file_path'],
            ];
        @endphp
        @foreach ($certToggles as $key => $cfg)
            @php $isSelected = old('products.' . $id . '.' . $cfg['select'], $data[$cfg['select']] ?? 'no'); @endphp
            <td class="align-top" style="min-width:160px; padding:12px 10px;">
                <x-vendor-select name="products[{{ $id }}][{{ $cfg['select'] }}]" :options="['no' => __('no'), 'yes' => __('yes')]"
                    :selected="$isSelected" :readonly="false" wrapperClass="mb-0" class="product-cert-toggle"
                    data-target=".detail-{{ $key }}-{{ $id }}" />

                <div class="detail-{{ $key }}-{{ $id }} mt-2" style="{{ $isSelected === 'yes' ? '' : 'display:none' }}">
                    <x-vendor-input type="file" name="products[{{ $id }}][{{ $cfg['file'] }}]"
                        existingName="products[{{ $id }}][{{ $cfg['existing'] }}]" :value="$data[$cfg['path']] ?? null"
                        :application="$application" :readonly="false" />
                </div>
            </td>
        @endforeach

        {{-- Kolom: Aksi --}}
        <td class="align-top text-center" style="min-width:56px; padding:12px 8px;">
            <button type="button" class="btn btn-icon btn-sm btn-danger btn-hapus-produk"
                data-id="{{ $id }}" title="{{ __('delete_product') }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>

    </tr>
@endif
