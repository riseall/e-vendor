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
                    [
                        'path' => $data['file_surat_path'] ?? null,
                        'title' => __('agency_letter') . ' - ' . $name,
                        'label' => __('agency_letter'),
                        'class' => 'primary',
                        'icon' => 'file-contract',
                    ],
                    [
                        'path' => $data['gmp_file_path'] ?? null,
                        'title' => 'GMP - ' . $name,
                        'label' => 'GMP',
                        'class' => 'info',
                        'icon' => 'microscope',
                    ],
                    [
                        'path' => ($data['has_tkdn'] ?? '') === 'yes' ? $data['tkdn_file_path'] ?? null : null,
                        'title' => 'TKDN - ' . $name,
                        'label' => 'TKDN',
                        'class' => 'dark',
                        'icon' => 'cogs',
                    ],
                    [
                        'path' => ($data['has_sni'] ?? '') === 'yes' ? $data['sni_file_path'] ?? null : null,
                        'title' => 'SNI - ' . $name,
                        'label' => 'SNI',
                        'class' => 'warning',
                        'icon' => 'award',
                    ],
                    [
                        'path' => ($data['has_halal'] ?? '') === 'yes' ? $data['halal_file_path'] ?? null : null,
                        'title' => 'Halal - ' . $name,
                        'label' => 'Halal',
                        'class' => 'success',
                        'icon' => 'leaf',
                    ],
                    [
                        'path' => ($data['has_bse_tse'] ?? '') === 'yes' ? $data['bse_tse_file_path'] ?? null : null,
                        'title' => 'BSE/TSE - ' . $name,
                        'label' => 'BSE/TSE',
                        'class' => 'danger',
                        'icon' => 'heartbeat',
                    ],
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
                            <i class="fas fa-{{ $btn['icon'] }} icon-sm"></i> {{ $btn['label'] }}
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
    {{-- Edit Mode: Master-Table 5 Kolom Ringkas (Tanpa Horizontal Scroll) --}}
    <tr class="product-row" id="product-row-{{ $id }}" data-product-id="{{ $id }}">

        {{-- Kolom 1: Nama Produk --}}
        <td class="align-middle">
            <div class="font-weight-bolder text-dark product-name font-size-sm" style="line-height:1.4;">
                {{ $name }}
            </div>
            <x-revision-note name="products.{{ $id }}" :notes="$revisionNotes ?? []" />
            <input type="hidden" name="products[{{ $id }}][erp_product_id]" value="{{ $id }}">
            <input type="hidden" name="products[{{ $id }}][product_name]" value="{{ $name }}">
        </td>

        {{-- Kolom 2: Manufaktur & Negara --}}
        <td class="align-middle">
            @php
                $manufaktur = old('products.' . $id . '.manufaktur', $data['manufaktur'] ?? '');
                $negara = old('products.' . $id . '.negara', $data['negara'] ?? '');
            @endphp
            <div class="font-weight-bold text-dark prod-summary-manufaktur font-size-sm">
                {{ $manufaktur ?: '-' }}
            </div>
            <div class="text-muted font-size-xs prod-summary-negara mt-1">
                @if ($negara)
                    <i class="fas fa-map-marker-alt text-muted mr-1" style="font-size: 0.7rem;"></i>{{ $negara }}
                @endif
            </div>
        </td>

        {{-- Kolom 3: Rantai Pasok --}}
        <td class="align-middle">
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
            <div class="font-weight-bold text-dark prod-summary-rantai font-size-sm">
                {{ $rantaiPasokText }}
            </div>
        </td>

        {{-- Kolom 4: Dokumen & Sertifikasi --}}
        <td class="align-middle">
            @php
                $isBahanKemas = strpos($id, 'KEMAS-') === 0 || (($data['product_type'] ?? '') === 'kemas');
                $summaryDocs = array_filter([
                    !empty($data['file_surat_path']) ? [__('agency_letter'), 'primary', 'fas fa-file-alt text-primary'] : null,
                    (!$isBahanKemas && !empty($data['gmp_file_path'])) ? ['GMP', 'info', 'fas fa-microscope text-info'] : null,
                    (($data['has_tkdn'] ?? '') === 'yes' && !empty($data['tkdn_file_path'])) ? ['TKDN', 'dark', 'fas fa-cogs text-dark'] : null,
                    (($data['has_sni'] ?? '') === 'yes' && !empty($data['sni_file_path'])) ? ['SNI', 'warning', 'fas fa-award text-warning'] : null,
                    (($data['has_halal'] ?? '') === 'yes' && !empty($data['halal_file_path'])) ? ['Halal', 'success', 'fas fa-leaf text-success'] : null,
                    (($data['has_bse_tse'] ?? '') === 'yes' && !empty($data['bse_tse_file_path'])) ? ['BSE/TSE', 'danger', 'fas fa-heartbeat text-danger'] : null,
                ]);
            @endphp
            <div class="d-flex align-items-center flex-wrap prod-summary-docs" style="gap: 4px;">
                @forelse ($summaryDocs as $doc)
                    <span class="label label-light-{{ $doc[1] }} label-inline font-size-xs font-weight-bolder mr-1 mb-1">
                        @if (!empty($doc[2])) <i class="{{ $doc[2] }} mr-1"></i> @endif{{ $doc[0] }}
                    </span>
                @empty
                    <span class="label label-light-warning label-inline font-size-xs font-weight-bold">{{ __('not_filled') }}</span>
                @endforelse
            </div>
        </td>

        {{-- Kolom 5: Aksi --}}
        <td class="align-middle text-center">
            <div class="d-flex align-items-center justify-content-center">
                <button type="button"
                    class="btn btn-icon btn-sm btn-light-warning font-weight-bolder btn-kelola-produk"
                    data-toggle="modal" data-target="#modal-product-{{ $id }}" title="{{ __('edit') }}">
                    <i class="fas fa-edit icon-md"></i>
                </button>
                <button type="button" class="btn btn-icon btn-sm btn-light-danger btn-hapus-produk ml-2"
                    data-id="{{ $id }}" title="{{ __('delete_product') }}"
                    style="width: 28px; height: 28px;">
                    <i class="fas fa-trash-alt icon-md"></i>
                </button>
            </div>
        </td>

    </tr>
@endif
