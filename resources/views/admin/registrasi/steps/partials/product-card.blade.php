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
    @php $gmpFilePath = $data['gmp_file_path'] ?? null; @endphp
    <td class="align-top" style="min-width:160px; padding:14px 12px;">
        <input type="text" name="products[{{ $id }}][manufaktur]" class="form-control form-control-sm"
            value="{{ old('products.' . $id . '.manufaktur', $data['manufaktur'] ?? '') }}"
            placeholder="Manufaktur / Asal" style="border-radius:6px;" {{ $isReadOnly ? 'readonly disabled' : '' }}
            required>

        <label class="font-weight-bold text-dark d-block mt-2" style="font-size:0.75rem;">GMP</label>
        @if ($gmpFilePath)
            <input type="hidden" name="products[{{ $id }}][existing_gmp_file]" value="{{ $gmpFilePath }}">
        @endif

        @if ($isReadOnly)
            @if ($gmpFilePath)
                <button type="button"
                    class="btn btn-sm btn-light-primary mt-2 btn-preview-doc w-100"
                    style="gap:4px; border-radius:5px; font-size:0.78rem;"
                    data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $gmpFilePath) }}"
                    data-title="Preview Sertifikat GMP">
                    <i class="flaticon-eye icon-sm"></i> Lihat
                </button>
            @else
                <span class="text-muted" style="font-size:0.78rem;">—</span>
            @endif
        @elseif ($gmpFilePath)
            <input type="file" class="product-file-input d-none ajax-file-upload"
                data-field="products[{{ $id }}][gmp_file]" id="gmp_file_{{ $id }}"
                accept=".pdf,.jpg,.jpeg,.png">
            <span class="upload-status" style="display:none;"></span>

            <div class="d-flex align-items-center px-2 py-1 bg-light-success rounded"
                style="gap:6px; border-radius:6px;">
                <i class="flaticon2-check-mark text-success" style="font-size:0.75rem;"></i>
                <span class="text-success font-weight-bold flex-grow-1"
                    style="font-size:0.75rem; white-space:nowrap;">Terlampir</span>
                <button type="button" class="btn btn-icon btn-xs btn-success btn-preview-doc"
                    style="width:22px; height:22px; border-radius:4px; padding:0;"
                    data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $gmpFilePath) }}"
                    data-title="Preview Sertifikat GMP" title="Preview">
                    <i class="flaticon-eye" style="font-size:0.7rem;"></i>
                </button>
            </div>
            <a href="#" class="d-block mt-1 text-muted cert-change-trigger" style="font-size:0.72rem;"
                data-target="#gmp_file_{{ $id }}">
                Ganti file...
            </a>
        @else
            <div class="custom-file custom-file-sm">
                <input type="file" class="custom-file-input product-file-input ajax-file-upload"
                    data-field="products[{{ $id }}][gmp_file]" id="gmp_file_{{ $id }}"
                    accept=".pdf,.jpg,.jpeg,.png" {{ $gmpFilePath ? '' : 'required' }}>
                <label class="custom-file-label text-truncate" for="gmp_file_{{ $id }}"
                    style="border-radius:6px; font-size:0.8rem;">
                    Pilih File
                </label>
                <span class="upload-status" style="display:none;"></span>
            </div>
        @endif
    </td>

    {{-- Kolom: Negara --}}
    <td class="align-top" style="min-width:175px; padding:14px 12px;">
        <select name="products[{{ $id }}][negara]" class="form-control form-control-sm custom-select"
            style="border-radius:6px;" {{ $isReadOnly ? 'disabled' : '' }} required>
            <option value="">Pilih...</option>
            @foreach ([
        'Indonesia' => 'Indonesia',
        'China' => 'China',
        'Japan' => 'Japan',
        'Korea' => 'Korea',
        'Malaysia' => 'Malaysia',
        'Thailand' => 'Thailand',
        'Vietnam' => 'Vietnam',
    ] as $val => $txt)
                <option value="{{ $val }}"
                    {{ old('products.' . $id . '.negara', $data['negara'] ?? '') == $val ? 'selected' : '' }}>
                    {{ $txt }}
                </option>
            @endforeach
        </select>
    </td>

    {{-- Kolom: Rantai Pasok --}}
    <td class="align-top" style="min-width:175px; padding:14px 12px;">
        <select name="products[{{ $id }}][rantai_pasok]" class="form-control form-control-sm custom-select"
            style="border-radius:6px;" {{ $isReadOnly ? 'disabled' : '' }} required>
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

    {{-- Kolom: Surat Keagenan --}}
    @php $suratPath = $data['file_surat_path'] ?? null; @endphp
    <td class="align-top" style="min-width:155px; padding:14px 12px;">
        @if ($suratPath)
            <input type="hidden" name="products[{{ $id }}][existing_file_surat]"
                value="{{ $suratPath }}">
        @endif

        @if ($isReadOnly)
            @if ($suratPath)
                <button type="button"
                    class="btn btn-sm btn-light-primary mt-2 btn-preview-doc w-100"
                    style="gap:4px; border-radius:5px; font-size:0.78rem;"
                    data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $suratPath) }}"
                    data-title="Preview Surat Keagenan">
                    <i class="flaticon-eye icon-sm"></i> Lihat
                </button>
            @else
                <span class="text-muted" style="font-size:0.78rem;">—</span>
            @endif
        @elseif ($suratPath)
            <div>
                <input type="file" class="product-file-input d-none ajax-file-upload"
                    data-field="products[{{ $id }}][file_surat]" id="file_{{ $id }}"
                    accept=".pdf,.jpg,.jpeg,.png">
                <span class="upload-status" style="display:none;"></span>
            </div>

            <div class="d-flex align-items-center px-2 py-1 bg-light-success rounded"
                style="gap:6px; border-radius:6px;">
                <i class="flaticon2-check-mark text-success" style="font-size:0.75rem;"></i>
                <span class="text-success font-weight-bold flex-grow-1"
                    style="font-size:0.75rem; white-space:nowrap;">Terlampir</span>
                <button type="button" class="btn btn-icon btn-xs btn-success btn-preview-doc"
                    style="width:22px; height:22px; border-radius:4px; padding:0;"
                    data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $suratPath) }}"
                    data-title="Preview Surat Keagenan" title="Preview">
                    <i class="flaticon-eye" style="font-size:0.7rem;"></i>
                </button>
            </div>
            <a href="#" class="d-block mt-1 text-muted cert-change-trigger" style="font-size:0.72rem;"
                data-target="#file_{{ $id }}">
                Ganti file...
            </a>
        @else
            {{-- Belum ada file --}}
            <div class="custom-file custom-file-sm">
                <input type="file" class="custom-file-input product-file-input ajax-file-upload"
                    data-field="products[{{ $id }}][file_surat]" id="file_{{ $id }}"
                    accept=".pdf,.jpg,.jpeg,.png" required>
                <label class="custom-file-label text-truncate" for="file_{{ $id }}"
                    style="border-radius:6px; font-size:0.8rem;">
                    Pilih File
                </label>
                <span class="upload-status" style="display:none;"></span>
            </div>
        @endif
    </td>

    {{-- Kolom: TKDN --}}
    @php
        $hasTkdn = old('products.' . $id . '.has_tkdn', $data['has_tkdn'] ?? 'no');
        $tkdnFilePath = $data['tkdn_file_path'] ?? null;
    @endphp
    <td class="align-top" style="min-width:155px; padding:14px 12px;">
        @if ($tkdnFilePath)
            <input type="hidden" name="products[{{ $id }}][existing_tkdn_file]"
                value="{{ $tkdnFilePath }}">
        @endif
        <select name="products[{{ $id }}][has_tkdn]"
            class="form-control form-control-sm custom-select product-cert-toggle"
            data-target=".detail-tkdn-{{ $id }}" style="border-radius:6px;"
            {{ $isReadOnly ? 'disabled' : '' }}>
            <option value="no" {{ $hasTkdn === 'no' ? 'selected' : '' }}>Tidak</option>
            <option value="yes" {{ $hasTkdn === 'yes' ? 'selected' : '' }}>Ya</option>
        </select>
        <div class="detail-tkdn-{{ $id }} mt-2" style="{{ $hasTkdn === 'yes' ? '' : 'display:none' }}">
            @include('admin.registrasi.steps.partials._cert-field', [
                'isReadOnly' => $isReadOnly,
                'filePath' => $tkdnFilePath,
                'fieldName' => "products[{$id}][tkdn_file]",
                'fieldId' => "tkdn_file_{$id}",
                'previewTitle' => 'Preview Sertifikat TKDN',
                'required' => $hasTkdn === 'yes' && !$tkdnFilePath,
            ])
        </div>
    </td>

    {{-- Kolom: SNI --}}
    @php
        $hasSni = old('products.' . $id . '.has_sni', $data['has_sni'] ?? 'no');
        $sniFilePath = $data['sni_file_path'] ?? null;
    @endphp
    <td class="align-top" style="min-width:155px; padding:14px 12px;">
        @if ($sniFilePath)
            <input type="hidden" name="products[{{ $id }}][existing_sni_file]"
                value="{{ $sniFilePath }}">
        @endif
        <select name="products[{{ $id }}][has_sni]"
            class="form-control form-control-sm custom-select product-cert-toggle"
            data-target=".detail-sni-{{ $id }}" style="border-radius:6px;"
            {{ $isReadOnly ? 'disabled' : '' }}>
            <option value="no" {{ $hasSni === 'no' ? 'selected' : '' }}>Tidak</option>
            <option value="yes" {{ $hasSni === 'yes' ? 'selected' : '' }}>Ya</option>
        </select>
        <div class="detail-sni-{{ $id }} mt-2" style="{{ $hasSni === 'yes' ? '' : 'display:none' }}">
            @include('admin.registrasi.steps.partials._cert-field', [
                'isReadOnly' => $isReadOnly,
                'filePath' => $sniFilePath,
                'fieldName' => "products[{$id}][sni_file]",
                'fieldId' => "sni_file_{$id}",
                'previewTitle' => 'Preview Sertifikat SNI',
                'required' => $hasSni === 'yes' && !$sniFilePath,
            ])
        </div>
    </td>

    {{-- Kolom: Halal --}}
    @php
        $hasHalal = old('products.' . $id . '.has_halal', $data['has_halal'] ?? 'no');
        $halalFilePath = $data['halal_file_path'] ?? null;
    @endphp
    <td class="align-top" style="min-width:155px; padding:14px 12px;">
        @if ($halalFilePath)
            <input type="hidden" name="products[{{ $id }}][existing_halal_file]"
                value="{{ $halalFilePath }}">
        @endif
        <select name="products[{{ $id }}][has_halal]"
            class="form-control form-control-sm custom-select product-cert-toggle"
            data-target=".detail-halal-{{ $id }}" style="border-radius:6px;"
            {{ $isReadOnly ? 'disabled' : '' }}>
            <option value="no" {{ $hasHalal === 'no' ? 'selected' : '' }}>Tidak</option>
            <option value="yes" {{ $hasHalal === 'yes' ? 'selected' : '' }}>Ya</option>
        </select>
        <div class="detail-halal-{{ $id }} mt-2" style="{{ $hasHalal === 'yes' ? '' : 'display:none' }}">
            @include('admin.registrasi.steps.partials._cert-field', [
                'isReadOnly' => $isReadOnly,
                'filePath' => $halalFilePath,
                'fieldName' => "products[{$id}][halal_file]",
                'fieldId' => "halal_file_{$id}",
                'previewTitle' => 'Preview Sertifikat Halal',
                'required' => $hasHalal === 'yes' && !$halalFilePath,
            ])
        </div>
    </td>

    {{-- Kolom: BSE/TSE --}}
    @php
        $hasBseTse = old('products.' . $id . '.has_bse_tse', $data['has_bse_tse'] ?? 'no');
        $bseTseFilePath = $data['bse_tse_file_path'] ?? null;
    @endphp
    <td class="align-top" style="min-width:155px; padding:14px 12px;">
        @if ($bseTseFilePath)
            <input type="hidden" name="products[{{ $id }}][existing_bse_tse_file]"
                value="{{ $bseTseFilePath }}">
        @endif
        <select name="products[{{ $id }}][has_bse_tse]"
            class="form-control form-control-sm custom-select product-cert-toggle"
            data-target=".detail-bse-tse-{{ $id }}" style="border-radius:6px;"
            {{ $isReadOnly ? 'disabled' : '' }}>
            <option value="no" {{ $hasBseTse === 'no' ? 'selected' : '' }}>Tidak</option>
            <option value="yes" {{ $hasBseTse === 'yes' ? 'selected' : '' }}>Ya</option>
        </select>
        <div class="detail-bse-tse-{{ $id }} mt-2"
            style="{{ $hasBseTse === 'yes' ? '' : 'display:none' }}">
            @include('admin.registrasi.steps.partials._cert-field', [
                'isReadOnly' => $isReadOnly,
                'filePath' => $bseTseFilePath,
                'fieldName' => "products[{$id}][bse_tse_file]",
                'fieldId' => "bse_tse_file_{$id}",
                'previewTitle' => 'Preview Sertifikat BSE/TSE',
                'required' => $hasBseTse === 'yes' && !$bseTseFilePath,
            ])
        </div>
    </td>

    {{-- Kolom: Aksi --}}
    <td class="align-top text-center" style="min-width:56px; padding:14px 8px;">
        @if (!$isReadOnly)
            <button type="button" class="btn btn-icon btn-sm btn-danger btn-hapus-produk"
                style="border-radius:6px; width:32px; height:32px;" data-id="{{ $id }}"
                title="Hapus produk">
                <i class="flaticon2-trash" style="font-size:0.85rem;"></i>
            </button>
        @endif
    </td>

</tr>
