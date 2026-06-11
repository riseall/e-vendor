<div class="form-section-title">Pemasok Bahan Baku, Bahan Kemas, Produk Jadi Farmasi & Alkes</div>

<div class="specific-container">
    {{-- Baris 1: Produsen & Agen Tunggal --}}
    <div class="row">
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Apakah pemasok sekaligus sebagai produsen? @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="q1_is_manufacturer" label="" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['q1_is_manufacturer'] ?? 'no'"
                    class="toggle-input" data-target="#wrap_q1" :readonly="$isReadOnly" />

                <div id="wrap_q1"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['q1_is_manufacturer'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <x-vendor-input name="q1_manufacturer_name" label="Sebutkan nama perusahaan produsen:"
                        :value="$draft['q1_manufacturer_name'] ?? ''" :readonly="$isReadOnly" placeholder="Nama perusahaan..." />
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Apakah ada penunjukkan sebagai agen tunggal? @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="q2_is_sole_agent" label="" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['q2_is_sole_agent'] ?? 'no'"
                    class="toggle-input" data-target="#wrap_q2" :readonly="$isReadOnly" />

                <div id="wrap_q2"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['q2_is_sole_agent'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    @php $q2File = $draft['q2_auth_letter'] ?? null; @endphp

                    <div class="form-group">
                        <label>Surat Penunjukkan Keagenan</label>
                        <x-vendor-input type="file" name="q2_auth_letter" :value="$draft['q2_auth_letter'] ?? null" :readonly="$isReadOnly"
                            placeholder="Upload Surat Penunjukkan..." />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Baris 2: Transportasi & Gudang --}}
    <div class="row">
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Angkutan yang dipakai untuk pengiriman? @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="q3_transportation" label="" :options="[
                    ['value' => 'owned', 'label' => 'Milik Sendiri'],
                    ['value' => '3pl', 'label' => 'Pihak Ketiga'],
                ]" :selected="$draft['q3_transportation'] ?? 'owned'"
                    class="toggle-input" data-target="#wrap_q3" :readonly="$isReadOnly" />

                <div id="wrap_q3"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['q3_transportation'] ?? '') === '3pl' ? '' : 'd-none' }}">
                    <x-vendor-input name="q3_3pl_name" label="Sebutkan nama perusahaan 3PL:" :value="$draft['q3_3pl_name'] ?? ''"
                        :readonly="$isReadOnly" placeholder="Nama logistik..." />
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Apakah memiliki gudang sendiri? @if (!$isReadOnly)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <x-vendor-radio name="q4_has_warehouse" label="" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['q4_has_warehouse'] ?? 'no'"
                    class="toggle-input" data-target="#wrap_q4" :readonly="$isReadOnly" />

                <div id="wrap_q4"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['q4_has_warehouse'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <x-vendor-input name="q4_warehouse_address" label="Alamat Gudang:" :value="$draft['q4_warehouse_address'] ?? ''"
                        :readonly="$isReadOnly" />

                    <x-vendor-radio name="q4_warehouse_condition" label="Kondisi Gudang:" :options="[
                        ['value' => 'cold', 'label' => 'Cold Storage'],
                        ['value' => 'ac', 'label' => 'AC Room'],
                        ['value' => 'ambient', 'label' => 'Non Controlled (Ambient) temperature'],
                        ['value' => 'grey', 'label' => 'Grey Area According to GMP Regulation'],
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
                <label class="question-label">Sertifikat CDOB</label>
                <div class="form-row">
                    <div class="col-7">
                        <x-vendor-input name="q5_num" label="No. Sertifikat" placeholder="No. Sertifikat"
                            :value="$draft['q5_num'] ?? ''" :readonly="$isReadOnly" />
                    </div>
                    <div class="col-5">
                        <x-vendor-input name="q5_date" label="Masa Berlaku" type="date" :value="$draft['q5_date'] ?? ''"
                            :readonly="$isReadOnly" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h6 class="font-weight-bolder mb-4 text-primary">KHUSUS UNTUK PEMASOK BAHAN BAKU LOKAL</h6>

    <div class="row">
        <div class="col-12">
            <div class="question-wrapper">
                <label class="question-label">SIPA APJ</label>
                <div class="form-row">
                    <div class="col-4">
                        <x-vendor-input name="q6_name" label="Nama" placeholder="Nama" :value="$draft['q6_name'] ?? ''"
                            :readonly="$isReadOnly" />
                    </div>
                    <div class="col-4">
                        <x-vendor-input name="q6_num" label="No. SIPA" placeholder="No. SIPA" :value="$draft['q6_num'] ?? ''"
                            :readonly="$isReadOnly" />
                    </div>
                    <div class="col-4">
                        <x-vendor-input name="q6_date" label="Masa Berlaku" type="date" :value="$draft['q6_date'] ?? ''"
                            :readonly="$isReadOnly" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h6 class="font-weight-bolder mb-4 text-primary">KHUSUS UNTUK PEMASOK BAHAN KEMAS</h6>

    {{-- Baris 4: Peralatan (Repeater) --}}
    <div class="row">
        <div class="col-12">
            <div class="question-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="question-label font-weight-bolder">Data Peralatan & Mesin</label>
                    @if (!$isReadOnly)
                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold btn-add-repeater"
                            data-target-tbody="#equipmentBody" data-template="#tpl_equipment">
                            <i class="flaticon2-plus icon-sm"></i> Tambah Alat
                        </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th width="50px">No</th>
                                <th>Jenis Alat</th>
                                <th width="100px">Jml</th>
                                <th>Kapasitas/Output saat ini</th>
                                <th>Merk/Tipe</th>
                                <th width="120px">Tahun Pembuatan</th>
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
                                                    class="flaticon2-trash"></i></button></td>
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
                <label class="question-label">Apakah menggunakan material dari luar negeri (impor) untuk
                    produksi?</label>
                <x-vendor-radio name="q8_is_import" :options="[
                    ['value' => 'yes', 'label' => 'Yes, dari negara mana?'],
                    ['value' => 'no', 'label' => 'No'],
                ]" :selected="$draft['q8_is_import'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_q8" :readonly="$isReadOnly" />

                <div id="wrap_q8"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['q8_is_import'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <x-vendor-input name="q8_country_name" label="Sebutkan nama negara:" :value="$draft['q8_country_name'] ?? ''"
                        :readonly="$isReadOnly" placeholder="Nama negara asal impor..." />
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
            <button type="button" class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater"><i class="flaticon2-trash"></i></button>
        </td>
    </tr>
</script>
@endif
