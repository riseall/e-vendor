<div class="form-section-title mt-10">Pemasok Jasa Pelatihan, Konsultan, Notaris, Alih Daya</div>

<div class="specific-container">
    {{-- Umum: Keanggotaan Asosiasi --}}
    <div class="row">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">Keanggotaan asosiasi (Mohon sebutkan nama asosiasi dan status
                    keanggotaan)</label>
                <input type="text" name="g1_association" class="form-control" placeholder="Nama Asosiasi & Status..."
                    value="{{ $draft['g1_association'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                <x-revision-note name="g1_association" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    {{-- SUB-SECTION: JASA PELATIHAN --}}
    <h6 class="font-weight-bolder mb-4 text-primary">UNTUK JASA PELATIHAN</h6>

    <div class="question-wrapper">
        <label class="question-label">Jaminan Sertifikasi Trainer</label>
        <x-vendor-radio name="g2_trainer_cert" :options="[['value' => 'yes', 'label' => 'Ada'], ['value' => 'no', 'label' => 'Tidak Ada']]" :selected="$draft['g2_trainer_cert'] ?? 'no'" class="toggle-input"
            data-target="#wrap_g2" :readonly="$isReadOnly" />

        <div id="wrap_g2" class="mt-4 {{ ($draft['g2_trainer_cert'] ?? '') === 'yes' ? '' : 'd-none' }}">
            <x-vendor-input name="g2_cert_source" label="Sebutkan dari mana (lembaga penerbit):"
                placeholder="Contoh: BNSP, Kemnaker, dll" :value="$draft['g2_cert_source'] ?? ''" :readonly="$isReadOnly" />
        </div>
    </div>

    {{-- SUB-SECTION: JASA KONSULTAN DAN NOTARIS --}}
    <h6 class="font-weight-bolder mb-4 text-primary">UNTUK JASA KONSULTAN DAN NOTARIS</h6>

    <div class="row mt-6">
        <div class="col-12">
            <div class="question-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="question-label">Nomor ijin yang dimiliki dan masa berlaku</label>
                    @if (!$isReadOnly)
                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold btn-add-repeater"
                            data-target-tbody="#notarisBody" data-template="#tpl_notaris">
                            <i class="flaticon2-plus icon-sm"></i> Tambah Ijin
                        </button>
                    @endif
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-borderless">
                        <thead class="thead-light">
                            <tr>
                                <th>Jenis Ijin / Deskripsi</th>
                                <th width="200px">Nomor Ijin</th>
                                <th width="180px">Masa Berlaku</th>
                                @if (!$isReadOnly)
                                    <th width="50px"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="notarisBody">
                            @php $permits = $draft['g3_permits'] ?? []; @endphp
                            @foreach ($permits as $index => $item)
                                <tr>
                                    <td>
                                        <input type="text" name="g3_permits[{{ $index }}][desc]"
                                            class="form-control form-control-sm"
                                            placeholder="Contoh: Ijin Notaris Kemenkumham"
                                            value="{{ $item['desc'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}
                                            required>
                                    </td>
                                    <td>
                                        <input type="text" name="g3_permits[{{ $index }}][no]"
                                            class="form-control form-control-sm" placeholder="Nomor Ijin"
                                            value="{{ $item['no'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}
                                            required>
                                    </td>
                                    <td>
                                        <input type="date" name="g3_permits[{{ $index }}][date]"
                                            class="form-control form-control-sm" value="{{ $item['date'] ?? '' }}"
                                            {{ $isReadOnly ? 'readonly' : '' }} required>
                                    </td>
                                    @if (!$isReadOnly)
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater">
                                                <i class="flaticon2-trash"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-revision-note name="g3_permits" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    {{-- SUB-SECTION: JASA ALIH DAYA TENAGA KERJA --}}
    <h6 class="font-weight-bolder mb-4 text-primary mt-6">UNTUK JASA ALIH DAYA TENAGA KERJA</h6>

    <div class="row">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">Ijin operasional dari Kementerian Ketenagakerjaan</label>
                <x-vendor-input name="g4_labor_permit" placeholder="Masukkan nomor ijin operasional..."
                    :value="$draft['g4_labor_permit'] ?? ''" :readonly="$isReadOnly" />
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <div class="question-wrapper">
                <label class="question-label">Apakah karyawan perusahaan Anda terdaftar sebagai peserta aktif di BPJS
                    Ketenagakerjaan?</label>
                <x-vendor-radio name="g5_bpjs" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['g5_bpjs'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>
</div>

{{-- Template untuk Repeater Notaris --}}
@if (!$isReadOnly)
    <script type="text/template" id="tpl_notaris">
    <tr>
        <td><input type="text" name="g3_permits[__INDEX__][desc]" class="form-control form-control-sm" placeholder="Cth. Notaris: ijin dari Kemenkumham" required></td>
        <td><input type="text" name="g3_permits[__INDEX__][no]" class="form-control form-control-sm" placeholder="Nomor Ijin" required></td>
        <td><input type="date" name="g3_permits[__INDEX__][date]" class="form-control form-control-sm" required></td>
        <td class="text-center">
            <button type="button" class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater">
                <i class="flaticon2-trash"></i>
            </button>
        </td>
    </tr>
</script>
@endif
