<tr class="product-row" id="product-row-{{ $id }}" data-product-id="{{ $id }}">

    {{-- Kolom: Nama Produk --}}
    <td class="align-top" style="min-width:200px; padding:14px 12px;">
        <div class="font-weight-bolder text-dark product-name" style="font-size:0.875rem; line-height:1.4;">
            {{ $name }}
        </div>
        <x-revision-note name="products.{{ $id }}" :notes="$revisionNotes ?? []" />
        <input type="hidden" name="products[{{ $id }}][erp_product_id]" value="{{ $id }}">
        <input type="hidden" name="products[{{ $id }}][product_name]" value="{{ $name }}">
    </td>

    {{-- Kolom: Manufaktur / Asal --}}
    <td class="align-top" style="min-width:160px; padding:14px 12px;">
        <x-vendor-input name="products[{{ $id }}][manufaktur]" :value="old('products.' . $id . '.manufaktur', $data['manufaktur'] ?? '')"
            placeholder="Manufaktur / Asal" :readonly="$isReadOnly" required />

        <label class="font-weight-bold text-dark d-block mt-2" style="font-size:0.75rem;">GMP</label>
        <x-vendor-input type="file" name="products[{{ $id }}][gmp_file]"
            existingName="products[{{ $id }}][existing_gmp_file]" :value="$data['gmp_file_path'] ?? null" :application="$application"
            :readonly="$isReadOnly" required />
    </td>

    {{-- Kolom: Negara --}}
    <td class="align-top" style="min-width:175px; padding:14px 12px;">
        <x-vendor-select name="products[{{ $id }}][negara]" :options="[
            'Indonesia' => 'Indonesia',
            'China' => 'China',
            'Japan' => 'Japan',
            'Korea' => 'Korea',
            'Malaysia' => 'Malaysia',
            'Thailand' => 'Thailand',
            'Vietnam' => 'Vietnam',
        ]" :selected="old('products.' . $id . '.negara', $data['negara'] ?? '')"
            :readonly="$isReadOnly" :isSimple="true" required wrapperClass="" />
    </td>

    {{-- Kolom: Rantai Pasok --}}
    <td class="align-top" style="min-width:175px; padding:14px 12px;">
        <x-vendor-select name="products[{{ $id }}][rantai_pasok]" :options="[
            'Manufaktur' => 'Manufaktur',
            'Distributor Pertama' => 'Distributor Pertama',
            'Distributor Kedua' => 'Distributor Kedua',
            'Repacker' => 'Repacker',
            'Trader' => 'Trader',
        ]" :selected="old('products.' . $id . '.rantai_pasok', $data['rantai_pasok'] ?? '')"
            :readonly="$isReadOnly" :isSimple="true" required wrapperClass="" />
    </td>

    {{-- Kolom: Surat Keagenan --}}
    <td class="align-top" style="min-width:155px; padding:14px 12px;">
        <x-vendor-input type="file" name="products[{{ $id }}][file_surat]"
            existingName="products[{{ $id }}][existing_file_surat]" :value="$data['file_surat_path'] ?? null" :application="$application"
            :readonly="$isReadOnly" required />
    </td>

    {{-- Kolom: TKDN --}}
    @php $hasTkdn = old('products.' . $id . '.has_tkdn', $data['has_tkdn'] ?? 'no'); @endphp
    <td class="align-top" style="min-width:155px; padding:14px 12px;">
        <x-vendor-select name="products[{{ $id }}][has_tkdn]" :options="['no' => 'Tidak', 'yes' => 'Ya']" :selected="$hasTkdn"
            :readonly="$isReadOnly" :isSimple="true" wrapperClass="" class="product-cert-toggle"
            data-target=".detail-tkdn-{{ $id }}" />

        <div class="detail-tkdn-{{ $id }} mt-2" style="{{ $hasTkdn === 'yes' ? '' : 'display:none' }}">
            <x-vendor-input type="file" name="products[{{ $id }}][tkdn_file]"
                existingName="products[{{ $id }}][existing_tkdn_file]" :value="$data['tkdn_file_path'] ?? null" :application="$application"
                :readonly="$isReadOnly" />
        </div>
    </td>

    {{-- Kolom: SNI --}}
    @php $hasSni = old('products.' . $id . '.has_sni', $data['has_sni'] ?? 'no'); @endphp
    <td class="align-top" style="min-width:155px; padding:14px 12px;">
        <x-vendor-select name="products[{{ $id }}][has_sni]" :options="['no' => 'Tidak', 'yes' => 'Ya']" :selected="$hasSni"
            :readonly="$isReadOnly" :isSimple="true" wrapperClass="" class="product-cert-toggle"
            data-target=".detail-sni-{{ $id }}" />

        <div class="detail-sni-{{ $id }} mt-2" style="{{ $hasSni === 'yes' ? '' : 'display:none' }}">
            <x-vendor-input type="file" name="products[{{ $id }}][sni_file]"
                existingName="products[{{ $id }}][existing_sni_file]" :value="$data['sni_file_path'] ?? null" :application="$application"
                :readonly="$isReadOnly" />
        </div>
    </td>

    {{-- Kolom: Halal --}}
    @php $hasHalal = old('products.' . $id . '.has_halal', $data['has_halal'] ?? 'no'); @endphp
    <td class="align-top" style="min-width:155px; padding:14px 12px;">
        <x-vendor-select name="products[{{ $id }}][has_halal]" :options="['no' => 'Tidak', 'yes' => 'Ya']" :selected="$hasHalal"
            :readonly="$isReadOnly" :isSimple="true" wrapperClass="" class="product-cert-toggle"
            data-target=".detail-halal-{{ $id }}" />

        <div class="detail-halal-{{ $id }} mt-2" style="{{ $hasHalal === 'yes' ? '' : 'display:none' }}">
            <x-vendor-input type="file" name="products[{{ $id }}][halal_file]"
                existingName="products[{{ $id }}][existing_halal_file]" :value="$data['halal_file_path'] ?? null" :application="$application"
                :readonly="$isReadOnly" />
        </div>
    </td>

    {{-- Kolom: BSE/TSE --}}
    @php $hasBseTse = old('products.' . $id . '.has_bse_tse', $data['has_bse_tse'] ?? 'no'); @endphp
    <td class="align-top" style="min-width:155px; padding:14px 12px;">
        <x-vendor-select name="products[{{ $id }}][has_bse_tse]" :options="['no' => 'Tidak', 'yes' => 'Ya']" :selected="$hasBseTse"
            :readonly="$isReadOnly" :isSimple="true" wrapperClass="" class="product-cert-toggle"
            data-target=".detail-bse-tse-{{ $id }}" />

        <div class="detail-bse-tse-{{ $id }} mt-2" style="{{ $hasBseTse === 'yes' ? '' : 'display:none' }}">
            <x-vendor-input type="file" name="products[{{ $id }}][bse_tse_file]"
                existingName="products[{{ $id }}][existing_bse_tse_file]" :value="$data['bse_tse_file_path'] ?? null"
                :application="$application" :readonly="$isReadOnly" />
        </div>
    </td>

    {{-- Kolom: Aksi --}}
    <td class="align-top text-center" style="min-width:56px; padding:14px 8px;">
        @if (!$isReadOnly)
            <button type="button" class="btn btn-icon btn-sm btn-danger btn-hapus-produk"
                style="border-radius:6px; width:32px; height:32px;" data-id="{{ $id }}" title="Hapus produk">
                <i class="flaticon2-trash" style="font-size:0.85rem;"></i>
            </button>
        @endif
    </td>

</tr>
