<div class="card card-custom border border-info mb-5 product-card" id="product-card-{{ $id }}">
    <div class="card-header min-h-40px px-5 py-3 bg-light-info">
        <div class="card-title m-0">
            <h6 class="card-label font-weight-bolder text-info m-0">{{ $name }}</h6>
            <input type="hidden" name="products[{{ $id }}][erp_product_id]" value="{{ $id }}">
            <input type="hidden" name="products[{{ $id }}][product_name]" value="{{ $name }}">
        </div>

        @if (!$isReadOnly)
            <div class="card-toolbar m-0">
                <button type="button" class="btn btn-icon btn-sm btn-danger btn-hapus-produk"
                    data-id="{{ $id }}">
                    <i class="flaticon2-trash"></i>
                </button>
            </div>
        @endif
    </div>

    <div class="card-body px-5 py-4">
        <div class="row">
            {{-- Manufaktur --}}
            <div class="col-md-4">
                <x-vendor-input name="products[{{ $id }}][manufaktur]" label="Manufaktur / Asal"
                    placeholder="Pabrik pembuat..." :value="$data['manufaktur'] ?? ''" :readonly="$isReadOnly" required wrapperClass="mb-4" />
            </div>

            {{-- Rantai Pasok --}}
            <div class="col-md-4">
                <x-vendor-select name="products[{{ $id }}][rantai_pasok]" label="Rantai Pasok"
                    :options="[
                        'Manufaktur' => 'Manufaktur',
                        'Distributor Pertama' => 'Distributor Pertama',
                        'Distributor Kedua' => 'Distributor Kedua',
                        'Repacker' => 'Repacker',
                        'Trader' => 'Trader',
                    ]" :selected="$data['rantai_pasok'] ?? ''" :readonly="$isReadOnly" required wrapperClass="mb-4" />
            </div>

            {{-- Surat Keagenan --}}
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label class="mb-2">Surat Keagenan @if (!$isReadOnly)
                            <span class="text-danger">*</span>
                        @endif
                    </label>

                    @php $filePath = $data['file_surat_path'] ?? null; @endphp

                    @if ($filePath)
                        <input type="hidden" name="products[{{ $id }}][existing_file_surat]"
                            value="{{ $filePath }}">
                    @endif

                    @if ($isReadOnly)
                        <div class="mt-2">
                            @if ($filePath)
                                <button type="button" class="btn btn-icon btn-xs btn-success btn-preview-doc"
                                    data-url="{{ asset('storage/' . $filePath) }}" data-title="Preview Surat Keagenan">
                                    <i class="flaticon-eye icon-sm"></i>
                                </button>
                            @else
                                <span class="text-muted italic">Tidak ada dokumen</span>
                            @endif
                        </div>
                    @else
                        <div class="custom-file">
                            <input type="file" class="custom-file-input product-file-input"
                                name="products[{{ $id }}][file_surat]" id="file_{{ $id }}"
                                accept=".pdf,.jpg,.jpeg,.png" {{ $filePath ? '' : 'required' }}>

                            <label class="custom-file-label text-truncate" for="file_{{ $id }}">
                                {{ $filePath ? 'Ganti file...' : 'Pilih File' }}
                            </label>
                        </div>

                        @if ($filePath)
                            <div class="mt-2">
                                <div class="d-flex align-items-center px-2 py-1 bg-light-success rounded">
                                    <div class="d-flex flex-column flex-grow-1 mr-2 overflow-hidden">
                                        <span class="text-success font-weight-bolder font-size-xs text-truncate">
                                            Dokumen Terlampir
                                        </span>
                                    </div>

                                    <button type="button" class="btn btn-icon btn-xs btn-success btn-preview-doc"
                                        data-url="{{ asset('storage/' . $filePath) }}"
                                        data-title="Preview Surat Keagenan">
                                        <i class="flaticon-eye icon-sm"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="col-md-12">
                <hr class="my-2">
            </div>

            {{-- Sertifikat TKDN --}}
            <div class="col-md-4 mt-4">
                <x-vendor-select name="products[{{ $id }}][has_tkdn]" label="Sertifikat TKDN?"
                    :options="['no' => 'Tidak', 'yes' => 'Ya']" :selected="$data['has_tkdn'] ?? 'no'" :readonly="$isReadOnly" wrapperClass="mb-2 toggle-select"
                    data-target=".detail-tkdn-{{ $id }}" />

                <div class="detail-tkdn-{{ $id }}"
                    style="{{ ($data['has_tkdn'] ?? 'no') === 'yes' ? '' : 'display:none' }}">
                    <x-vendor-input name="products[{{ $id }}][tkdn_value]" placeholder="Nilai TKDN (%)"
                        :value="$data['tkdn_value'] ?? ''" :readonly="$isReadOnly" wrapperClass="mb-0" />
                </div>
            </div>

            {{-- Sertifikat SNI --}}
            <div class="col-md-4 mt-4">
                <x-vendor-select name="products[{{ $id }}][has_sni]" label="Sertifikat SNI?"
                    :options="['no' => 'Tidak', 'yes' => 'Ya']" :selected="$data['has_sni'] ?? 'no'" :readonly="$isReadOnly" wrapperClass="mb-2 toggle-select"
                    data-target=".detail-sni-{{ $id }}" />

                <div class="detail-sni-{{ $id }}"
                    style="{{ ($data['has_sni'] ?? 'no') === 'yes' ? '' : 'display:none' }}">
                    <x-vendor-input name="products[{{ $id }}][sni_number]" placeholder="Nomor SNI"
                        :value="$data['sni_number'] ?? ''" :readonly="$isReadOnly" wrapperClass="mb-0" />
                </div>
            </div>

            {{-- Sertifikat Halal --}}
            <div class="col-md-4 mt-4">
                <x-vendor-select name="products[{{ $id }}][has_halal]" label="Sertifikat Halal Produk?"
                    :options="['no' => 'Tidak', 'yes' => 'Ya']" :selected="$data['has_halal'] ?? 'no'" :readonly="$isReadOnly" wrapperClass="mb-2 toggle-select"
                    data-target=".detail-halal-{{ $id }}" />

                <div class="detail-halal-{{ $id }}"
                    style="{{ ($data['has_halal'] ?? 'no') === 'yes' ? '' : 'display:none' }}">
                    <x-vendor-input name="products[{{ $id }}][halal_number]"
                        placeholder="Nomor Sertifikat Halal" :value="$data['halal_number'] ?? ''" :readonly="$isReadOnly"
                        wrapperClass="mb-0" />
                </div>
            </div>

        </div>
    </div>
</div>
