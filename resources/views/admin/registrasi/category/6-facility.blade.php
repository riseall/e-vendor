<div class="form-section-title mt-10">Pemasok Jasa Facility Service, Sewa, Security, Katering, MCU</div>

<div class="specific-container">
    <div class="row">
        {{-- Keanggotaan Asosiasi --}}
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">Keanggotaan asosiasi (Sebutkan nama asosiasi dan status)</label>
                <input type="text" name="f1_association" class="form-control" placeholder="Nama Asosiasi & Status..."
                    value="{{ $draft['f1_association'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                <x-revision-note name="f1_association" :notes="$revisionNotes ?? []" />
            </div>
        </div>

        {{-- BPJS --}}
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">Karyawan terdaftar sebagai peserta aktif di BPJS Ketenagakerjaan</label>
                <x-vendor-radio name="f2_bpjs" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['f2_bpjs'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    <div class="row mt-6">
        {{-- Ijin Permenaker --}}
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">Surat ijin Permenaker untuk hygiene service</label>
                <x-vendor-input name="f3_permenaker_ijin" placeholder="Masukkan nomor ijin..." :value="$draft['f3_permenaker_ijin'] ?? ''"
                    :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    {{-- Tabel Sertifikat Keterampilan Khusus (Q4) --}}
    <div class="row mt-6">
        <div class="col-12">
            <div class="question-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="question-label font-weight-bolder">Sertifikat keterampilan khusus (Cleaning Service,
                        Satpam, Katering)</label>
                    @if (!$isReadOnly)
                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold btn-add-repeater"
                            data-target-tbody="#certBody" data-template="#tpl_cert">
                            <i class="flaticon2-plus icon-sm"></i> Tambah Sertifikat
                        </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered bg-light-only">
                        <thead class="thead-light">
                            <tr>
                                <th>Jenis Keterampilan</th>
                                <th>Nama Pemegang</th>
                                <th width="180px">Tanggal Sertifikat</th>
                                @if (!$isReadOnly)
                                    <th width="50px"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="certBody">
                            @php $certs = $draft['f4_certs'] ?? []; @endphp
                            @foreach ($certs as $index => $item)
                                <tr>
                                    <td><input type="text" name="f4_certs[{{ $index }}][type]"
                                            class="form-control form-control-sm" value="{{ $item['type'] ?? '' }}"
                                            {{ $isReadOnly ? 'readonly' : '' }} required></td>
                                    <td><input type="text" name="f4_certs[{{ $index }}][name]"
                                            class="form-control form-control-sm" value="{{ $item['name'] ?? '' }}"
                                            {{ $isReadOnly ? 'readonly' : '' }} required></td>
                                    <td><input type="date" name="f4_certs[{{ $index }}][date]"
                                            class="form-control form-control-sm" value="{{ $item['date'] ?? '' }}"
                                            {{ $isReadOnly ? 'readonly' : '' }} required></td>
                                    @if (!$isReadOnly)
                                        <td class="text-center align-middle"><button type="button"
                                                class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater"><i
                                                    class="flaticon2-trash"></i></button></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-revision-note name="f4_certs" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    <div class="row mt-6">
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">Jaminan keterampilan hygiene boga (Karyawan di PT Phapros Tbk)</label>
                <x-vendor-radio name="f5_hygiene_guarantee" :options="[['value' => 'yes', 'label' => 'Ada'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['f5_hygiene_guarantee'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="question-wrapper h-100">
                <label class="question-label">Sertifikat uji sanitasi dan penjamah makanan</label>
                <x-vendor-radio name="f6_sanitation_cert" :options="[['value' => 'yes', 'label' => 'Ada, lampirkan'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['f6_sanitation_cert'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_f6" :readonly="$isReadOnly" />

                <div id="wrap_f6" class="mt-3 {{ ($draft['f6_sanitation_cert'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <div class="form-group">
                        <label>Upload Sertifikat Sanitasi</label>
                        <x-vendor-input type="file" name="f6_file" :value="$draft['f6_file'] ?? null" :readonly="$isReadOnly"
                            placeholder="Upload Sertifikat Sanitasi..." />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-6">
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Fasilitas dapur</label>
                <x-vendor-radio name="f7_kitchen_facility" :options="[
                    ['value' => 'sendiri', 'label' => 'Milik Sendiri'],
                    ['value' => 'subkontrak', 'label' => 'Subkontrak'],
                ]" :selected="$draft['f7_kitchen_facility'] ?? ''" :readonly="$isReadOnly" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Fasilitas pengangkutan makanan (Dapur ke User)</label>
                <x-vendor-radio name="f8_transport_facility" :options="[
                    ['value' => 'sendiri', 'label' => 'Milik Sendiri'],
                    ['value' => 'subkontrak', 'label' => 'Subkontrak'],
                ]" :selected="$draft['f8_transport_facility'] ?? ''" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>
</div>

{{-- Template untuk Repeater Sertifikat --}}
@if (!$isReadOnly)
    <script type="text/template" id="tpl_cert">
    <tr>
        <td><input type="text" name="f4_certs[__INDEX__][type]" class="form-control form-control-sm" required></td>
        <td><input type="text" name="f4_certs[__INDEX__][name]" class="form-control form-control-sm" required></td>
        <td><input type="date" name="f4_certs[__INDEX__][date]" class="form-control form-control-sm" required></td>
        <td class="text-center align-middle"><button type="button" class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater"><i class="flaticon2-trash"></i></button></td>
    </tr>
</script>
@endif
