<tr class="product-row" id="product-row-{{ $id }}" data-product-id="{{ $id }}">
    <td class="align-top min-w-220px">
        <div class="font-weight-bolder text-dark product-name">{{ $name }}</div>
        <input type="hidden" name="products[{{ $id }}][erp_product_id]" value="{{ $id }}">
        <input type="hidden" name="products[{{ $id }}][product_name]" value="{{ $name }}">
    </td>

    <td class="align-top min-w-180px">
        <input type="text" name="products[{{ $id }}][manufaktur]" class="form-control form-control-sm"
            value="{{ old('products.' . $id . '.manufaktur', $data['manufaktur'] ?? '') }}" placeholder="Manufaktur / Asal"
            {{ $isReadOnly ? 'readonly disabled' : '' }} required>
    </td>

    <td class="align-top min-w-190px">
        <select name="products[{{ $id }}][rantai_pasok]" class="form-control form-control-sm custom-select"
            {{ $isReadOnly ? 'disabled' : '' }} required>
            <option value="">Pilih...</option>
            @foreach ([
        'Manufaktur' => 'Manufaktur',
        'Distributor Pertama' => 'Distributor Pertama',
        'Distributor Kedua' => 'Distributor Kedua',
        'Repacker' => 'Repacker',
        'Trader' => 'Trader',
    ] as $val => $txt)
                <option value="{{ $val }}"
                    {{ old('products.' . $id . '.rantai_pasok', $data['rantai_pasok'] ?? '') == $val ? 'selected' : '' }}>
                    {{ $txt }}
                </option>
            @endforeach
        </select>
    </td>

    <td class="align-top min-w-230px">
        @php $filePath = $data['file_surat_path'] ?? null; @endphp

        @if ($filePath)
            <input type="hidden" name="products[{{ $id }}][existing_file_surat]" value="{{ $filePath }}">
        @endif

        @if ($isReadOnly)
            @if ($filePath)
                <button type="button" class="btn btn-icon btn-xs btn-success btn-preview-doc"
                    data-url="{{ asset('storage/' . $filePath) }}" data-title="Preview Surat Keagenan">
                    <i class="flaticon-eye icon-sm"></i>
                </button>
            @else
                <span class="text-muted">Tidak ada dokumen</span>
            @endif
        @else
            <div class="custom-file custom-file-sm">
                <input type="file" class="custom-file-input product-file-input"
                    name="products[{{ $id }}][file_surat]" id="file_{{ $id }}" accept=".pdf,.jpg,.jpeg,.png"
                    {{ $filePath ? '' : 'required' }}>
                <label class="custom-file-label text-truncate" for="file_{{ $id }}">
                    {{ $filePath ? 'Ganti file...' : 'Pilih File' }}
                </label>
            </div>

            @if ($filePath)
                <div class="d-flex align-items-center mt-2 px-2 py-1 bg-light-success rounded">
                    <span class="text-success font-weight-bolder font-size-xs flex-grow-1 text-truncate">
                        Dokumen Terlampir
                    </span>
                    <button type="button" class="btn btn-icon btn-xs btn-success btn-preview-doc"
                        data-url="{{ asset('storage/' . $filePath) }}" data-title="Preview Surat Keagenan">
                        <i class="flaticon-eye icon-sm"></i>
                    </button>
                </div>
            @endif
        @endif
    </td>

    <td class="align-top min-w-160px">
        @php $hasTkdn = old('products.' . $id . '.has_tkdn', $data['has_tkdn'] ?? 'no'); @endphp
        <select name="products[{{ $id }}][has_tkdn]"
            class="form-control form-control-sm custom-select product-cert-toggle" data-target=".detail-tkdn-{{ $id }}"
            {{ $isReadOnly ? 'disabled' : '' }}>
            <option value="no" {{ $hasTkdn === 'no' ? 'selected' : '' }}>Tidak</option>
            <option value="yes" {{ $hasTkdn === 'yes' ? 'selected' : '' }}>Ya</option>
        </select>
        <div class="detail-tkdn-{{ $id }} mt-2" style="{{ $hasTkdn === 'yes' ? '' : 'display:none' }}">
            <input type="number" name="products[{{ $id }}][tkdn_value]" class="form-control form-control-sm"
                value="{{ old('products.' . $id . '.tkdn_value', $data['tkdn_value'] ?? '') }}" placeholder="Nilai (%)"
                min="0" max="100" step="0.01" {{ $isReadOnly ? 'readonly disabled' : '' }}>
        </div>
    </td>

    <td class="align-top min-w-160px">
        @php $hasSni = old('products.' . $id . '.has_sni', $data['has_sni'] ?? 'no'); @endphp
        <select name="products[{{ $id }}][has_sni]"
            class="form-control form-control-sm custom-select product-cert-toggle" data-target=".detail-sni-{{ $id }}"
            {{ $isReadOnly ? 'disabled' : '' }}>
            <option value="no" {{ $hasSni === 'no' ? 'selected' : '' }}>Tidak</option>
            <option value="yes" {{ $hasSni === 'yes' ? 'selected' : '' }}>Ya</option>
        </select>
        <div class="detail-sni-{{ $id }} mt-2" style="{{ $hasSni === 'yes' ? '' : 'display:none' }}">
            <input type="text" name="products[{{ $id }}][sni_number]" class="form-control form-control-sm"
                value="{{ old('products.' . $id . '.sni_number', $data['sni_number'] ?? '') }}" placeholder="Nomor SNI"
                {{ $isReadOnly ? 'readonly disabled' : '' }}>
        </div>
    </td>

    <td class="align-top min-w-160px">
        @php $hasHalal = old('products.' . $id . '.has_halal', $data['has_halal'] ?? 'no'); @endphp
        <select name="products[{{ $id }}][has_halal]"
            class="form-control form-control-sm custom-select product-cert-toggle" data-target=".detail-halal-{{ $id }}"
            {{ $isReadOnly ? 'disabled' : '' }}>
            <option value="no" {{ $hasHalal === 'no' ? 'selected' : '' }}>Tidak</option>
            <option value="yes" {{ $hasHalal === 'yes' ? 'selected' : '' }}>Ya</option>
        </select>
        <div class="detail-halal-{{ $id }} mt-2" style="{{ $hasHalal === 'yes' ? '' : 'display:none' }}">
            <input type="text" name="products[{{ $id }}][halal_number]" class="form-control form-control-sm"
                value="{{ old('products.' . $id . '.halal_number', $data['halal_number'] ?? '') }}"
                placeholder="Nomor Halal" {{ $isReadOnly ? 'readonly disabled' : '' }}>
        </div>
    </td>

    <td class="align-top text-center min-w-80px">
        @if (!$isReadOnly)
            <button type="button" class="btn btn-icon btn-sm btn-danger btn-hapus-produk" data-id="{{ $id }}">
                <i class="flaticon2-trash"></i>
            </button>
        @endif
    </td>
</tr>
