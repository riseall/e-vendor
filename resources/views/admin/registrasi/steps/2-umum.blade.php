@if (empty($isProfileMode))
    <h4 class="font-weight-bold text-dark mb-2">Formulir Pendaftaran Vendor</h4>
    <p class="text-muted mb-8">
        Silakan lengkapi seluruh data perusahaan Anda. Tanda <span class="text-danger font-weight-bold">*</span> wajib
        diisi.
    </p>
@endif

<div class="form-section-title mt-0">
    Informasi Umum Perusahaan
</div>

<div class="question-wrapper">
    <label class="question-label">Informasi Perusahaan</label>
    <div class="row">
        <div class="col-md-6">
            <x-vendor-input name="nama_perusahaan" label="Nama Perusahaan (example: PHAPROS TBK PT.)"
                placeholder="example: PHAPROS TBK PT." :value="$draft['general']->nama_perusahaan ?? ''" :readonly="$isReadOnly" required />
        </div>
        <div class="col-md-6">
            <x-vendor-input type="url" name="website" label="Situs Resmi Perusahaan"
                placeholder="https://www.company.com" :value="$draft['general']->website ?? ''" :readonly="$isReadOnly" required />
        </div>
    </div>

    <div class="mt-4">
        <x-vendor-input type="textarea" name="alamat_perusahaan" label="Alamat Lengkap Perusahaan"
            placeholder="Tuliskan alamat lengkap..." :value="$draft['general']->alamat_perusahaan ?? ''" :readonly="$isReadOnly" required />
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <x-vendor-input type="email" name="email_perusahaan" label="Email Perusahaan"
                placeholder="info@company.com" :value="$draft['general']->email_perusahaan ?? ''" :readonly="$isReadOnly" required />
        </div>
        <div class="col-md-6">
            <x-vendor-input name="telepon_perusahaan" label="Nomor Telepon Perusahaan" placeholder="021xxxxxxx"
                :value="$draft['general']->telepon_perusahaan ?? ''" :readonly="$isReadOnly" required />
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <x-vendor-input name="nib" label="Nomor NIB" placeholder="xxxxxxxxxxxxxxx" :value="$draft['general']->nib ?? ''"
                :readonly="$isReadOnly" required />
        </div>
        <div class="col-md-6">
            <x-vendor-input name="npwp" label="Nomor NPWP" placeholder="00.000.000.0-000.000" :value="$draft['general']->npwp ?? ''"
                :readonly="$isReadOnly" required />
        </div>
    </div>
</div>

{{-- Contact Person --}}
<div class="question-wrapper mt-8">
    <label class="question-label">Contact Person (PIC)</label>

    <x-vendor-input name="pic_nama" label="Nama" placeholder="Nama lengkap penanggung jawab" :value="$draft['general']->pic_nama ?? ''"
        :readonly="$isReadOnly" required />

    <div class="row mt-4">
        <div class="col-md-6">
            <x-vendor-input type="email" name="pic_email" label="Email" placeholder="pic@company.com"
                :value="$draft['general']->pic_email ?? ''" :readonly="$isReadOnly" required />
        </div>
        <div class="col-md-6">
            <x-vendor-input name="pic_telepon" label="Nomor Handphone" placeholder="08xxxxxxxxxx" :value="$draft['general']->pic_telepon ?? ''"
                :readonly="$isReadOnly" required />
        </div>
    </div>
</div>

{{-- Perusahaan Lain (Repeater) --}}
<div class="question-wrapper mt-8">
    <label class="question-label">Perusahaan Lain Milik Pimpinan (Jika Ada)</label>

    <x-vendor-radio name="has_other_company" :options="[['value' => 'yes', 'label' => 'Ya, Punya'], ['value' => 'no', 'label' => 'Tidak Punya']]" :selected="$draft['general']->has_other_company ?? 'no'" class="toggle-input"
        data-target="#otherCompanyTable" :readonly="$isReadOnly" />

    <div id="otherCompanyTable" class="{{ ($draft['general']->has_other_company ?? 'no') === 'yes' ? '' : 'd-none' }}">
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Perusahaan</th>
                        <th>Alamat</th>
                        @if (!$isReadOnly)
                            <th class="text-center" style="width: 80px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="otherCompanyRows">
                    @php $otherCompanies = $draft['general']['other_companies'] ?? [['nama' => '', 'alamat' => '']]; @endphp
                    @foreach ($otherCompanies as $i => $oc)
                        <tr>
                            <td class="pl-0 pb-3">
                                <input type="text" name="other_companies[{{ $i }}][nama]"
                                    value="{{ $oc['nama'] ?? '' }}" class="form-control form-control-sm"
                                    placeholder="Nama perusahaan..." {{ $isReadOnly ? 'readonly disabled' : '' }}>
                            </td>
                            <td class="pb-3">
                                <input type="text" name="other_companies[{{ $i }}][alamat]"
                                    value="{{ $oc['alamat'] ?? '' }}" class="form-control form-control-sm"
                                    placeholder="Alamat..." {{ $isReadOnly ? 'readonly disabled' : '' }}>
                            </td>

                            @if (!$isReadOnly)
                                <td class="text-center pr-0 pb-3">
                                    <button type="button"
                                        class="btn btn-icon btn-light-danger btn-sm btn-remove-repeater"
                                        title="Hapus Baris" {{ $i == 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if (!$isReadOnly)
                {{-- Tombol tambah memanggil global JS --}}
                <button type="button" class="btn btn-light-primary btn-sm font-weight-bold mt-2 btn-add-repeater"
                    data-target-tbody="#otherCompanyRows" data-template="#template-other-company">
                    <i class="flaticon2-plus icon-xs mr-1"></i> Tambah Perusahaan
                </button>

                <template id="template-other-company">
                    <tr>
                        <td class="pl-0 pb-3">
                            <input type="text" name="other_companies[__INDEX__][nama]"
                                class="form-control form-control-sm" placeholder="Nama perusahaan...">
                        </td>
                        <td class="pb-3">
                            <input type="text" name="other_companies[__INDEX__][alamat]"
                                class="form-control form-control-sm" placeholder="Alamat...">
                        </td>
                        <td class="text-center pr-0 pb-3">
                            <button type="button" class="btn btn-icon btn-light-danger btn-sm btn-remove-repeater"
                                title="Hapus Baris">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            @endif
        </div>
    </div>
</div>
