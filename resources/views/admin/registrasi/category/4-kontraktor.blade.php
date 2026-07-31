<div class="form-section-title mt-10">Pemasok Jasa Kontraktor, Perbaikan, Pemeliharaan</div>

<div class="specific-container">
    <div class="row">
        {{-- Tenaga Profesional --}}
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">Tenaga profesional yang menangani (Contoh: Ahli K3 Umum)</label>
                <x-vendor-radio name="k1_pro_staff" :options="[
                    ['value' => 'yes', 'label' => 'Ya, lampirkan sertifikatnya'],
                    ['value' => 'no', 'label' => 'Tidak'],
                ]" :selected="$draft['k1_pro_staff'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_k1" :readonly="$isReadOnly" />

                <div id="wrap_k1"
                    class="toggle-content mt-3 {{ ($draft['k1_pro_staff'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <div class="form-group">
                        <label>Pilih sertifikat</label>
                        <x-vendor-input 
                            type="file" 
                            name="k1_cert_file" 
                            :value="$draft['k1_cert_file'] ?? null" 
                            :readonly="$isReadOnly" 
                            placeholder="Pilih sertifikat..." 
                        />
                    </div>
                </div>
            </div>
        </div>

        {{-- Komitmen K3 --}}
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">Komitmen terhadap K3, safety dan security</label>
                <div class="text-muted font-size-xs mb-3">Contoh: Kotak P3K, APAR, Gembok, Teralis besi, dll</div>
                <x-vendor-radio name="k2_safety_commitment" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['k2_safety_commitment'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    <div class="row mt-6">
        {{-- BPJS Ketenagakerjaan --}}
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Karyawan terdaftar sebagai peserta aktif di BPJS Ketenagakerjaan</label>
                <x-vendor-radio name="k3_bpjs" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['k3_bpjs'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>

        {{-- APD --}}
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Karyawan dilengkapi dengan APD saat bekerja</label>
                <x-vendor-radio name="k4_apd" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['k4_apd'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    {{-- Keanggotaan Asosiasi --}}
    <div class="row mt-6">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">Keanggotaan asosiasi (Mohon sebutkan nama asosiasi dan status
                    keanggotaan)</label>
                <input type="text" name="k5_association" class="form-control" placeholder="Nama Asosiasi & Status..."
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
                    <label class="question-label font-weight-bolder">Daftar peralatan pemeriksaan dan mesin</label>
                    @if (!$isReadOnly)
                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold btn-add-repeater"
                            data-target-tbody="#mesinKontraktorBody" data-template="#k6_equipment">
                            <i class="flaticon2-plus icon-sm"></i> Tambah Alat
                        </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th width="50px" class="text-center">No</th>
                                <th>Jenis Alat</th>
                                <th width="100px">Jml</th>
                                <th>Kapasitas/Output</th>
                                <th>Merk / Type</th>
                                <th width="120px">Tahun</th>
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
