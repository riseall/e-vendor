<div class="form-section-title mt-10">Pemasok Jasa Pengujian Laboratorium, Kalibrasi, Radiasi, Sertifikasi</div>

<div class="specific-container">
    {{-- 1. Layanan Jasa --}}
    <div class="row">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">Layanan jasa yang ditawarkan</label>
                <x-vendor-checkbox name="l1_services" :options="[
                    ['value' => 'klinik', 'label' => 'Uji Klinik'],
                    ['value' => 'mikro', 'label' => 'Uji Mikrobiologi'],
                    ['value' => 'fisika', 'label' => 'Uji Fisika Kimia'],
                    ['value' => 'sertifikasi', 'label' => 'Sertifikasi'],
                ]" :selected="$draft['l1_services'] ?? []" :readonly="$isReadOnly">

                    {{-- Opsi Kalibrasi dengan Toggle Input --}}
                    <div class="d-flex flex-column mt-3">
                        <label class="checkbox checkbox-primary {{ $isReadOnly ? 'checkbox-disabled' : '' }}">
                            <input type="checkbox" name="l1_services[]" value="kalibrasi" class="toggle-input"
                                data-target="#wrap_l1_kalibrasi"
                                {{ in_array('kalibrasi', $draft['l1_services'] ?? []) ? 'checked' : '' }}
                                {{ $isReadOnly ? 'disabled' : '' }}>
                            <span></span> Uji kalibrasi, sebutkan ruang lingkup kalibrasinya:
                        </label>

                        <div id="wrap_l1_kalibrasi"
                            class="mt-2 {{ in_array('kalibrasi', $draft['l1_services'] ?? []) ? '' : 'd-none' }}">
                            <input type="text" name="l1_kalibrasi_scope" class="form-control form-control-sm"
                                placeholder="Contoh: Suhu, Tekanan, Massa..."
                                value="{{ $draft['l1_kalibrasi_scope'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                            <x-revision-note name="l1_kalibrasi_scope" :notes="$revisionNotes ?? []" />
                        </div>
                    </div>
                </x-vendor-checkbox>
            </div>
        </div>
    </div>

    {{-- 2. Sertifikasi yang Dimiliki --}}
    <div class="row mt-6">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">Sertifikasi yang dimiliki (Lampirkan sertifikat & masa berlaku)</label>

                @php
                    $certs = [
                        'kan' => 'Akreditasi KAN',
                        'cukb' => 'Sertifikat CUKB',
                        'iso17025' => 'Sertifikat ISO 17025',
                        'glp' => 'Sertifikat GLP',
                        'bapeten' => 'Perijinan BAPETEN',
                    ];
                    $selectedCerts = $draft['l2_selected_certs'] ?? [];
                @endphp

                @foreach ($certs as $key => $label)
                    <div class="cert-row mb-3 p-4 border rounded bg-light-only">
                        <label
                            class="checkbox checkbox-primary font-weight-bolder mb-0 {{ $isReadOnly ? 'checkbox-disabled' : '' }}">
                            <input type="checkbox" name="l2_selected_certs[]" value="{{ $key }}"
                                class="toggle-input" data-target="#wrap_cert_{{ $key }}"
                                {{ in_array($key, $selectedCerts) ? 'checked' : '' }}
                                {{ $isReadOnly ? 'disabled' : '' }}>
                            <span></span> {{ $label }}
                        </label>

                        <div id="wrap_cert_{{ $key }}"
                            class="mt-2 pt-2 border-top {{ in_array($key, $selectedCerts) ? '' : 'd-none' }}">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label class="font-size-xs text-muted">Nomor Sertifikat:</label>
                                    <input type="text" name="l2_{{ $key }}_no"
                                        class="form-control form-control-sm" value="{{ $draft["l2_{$key}_no"] ?? '' }}"
                                        {{ $isReadOnly ? 'readonly' : '' }}>
                                    <x-revision-note name="l2_{{ $key }}_no" :notes="$revisionNotes ?? []" />
                                </div>
                                <div class="col-md-3">
                                    <label class="font-size-xs text-muted">Masa Berlaku:</label>
                                    <input type="date" name="l2_{{ $key }}_date"
                                        class="form-control form-control-sm"
                                        value="{{ $draft["l2_{$key}_date"] ?? '' }}"
                                        {{ $isReadOnly ? 'readonly' : '' }}>
                                    <x-revision-note name="l2_{{ $key }}_date" :notes="$revisionNotes ?? []" />
                                </div>
                                <div class="col-md-5">
                                    <label class="font-size-xs text-muted">Lampiran File:</label>

                                    @php $certFile = $draft["l2_{$key}_file"] ?? null; @endphp

                                    @if ($certFile)
                                        <input type="hidden" name="existing_l2_{{ $key }}_file"
                                            value="{{ $certFile }}">
                                    @endif

                                    <div class="custom-file mb-2">
                                        <input type="file" name="l2_{{ $key }}_file"
                                            class="custom-file-input" accept=".pdf,.jpg,.jpeg,.png"
                                            {{ $isReadOnly ? 'disabled' : '' }}>
                                        <label
                                            class="custom-file-label form-control-sm text-truncate">{{ $certFile ? 'Ganti berkas...' : 'Pilih berkas...' }}</label>
                                    </div>

                                    @if ($certFile && !$isReadOnly)
                                        <div class="d-flex justify-content-between p-1 bg-light-success rounded">
                                            <span class="text-success font-size-xs font-weight-bold mr-2 ml-2">File
                                                Tersimpan</span>
                                            <button type="button" data-url="{{ asset('storage/' . $certFile) }}"
                                                class="btn btn-xs btn-success btn-icon btn-preview-doc"><i
                                                    class="flaticon-eye"></i></button>
                                        </div>
                                    @elseif($certFile && $isReadOnly)
                                        <button type="button" data-url="{{ asset('storage/' . $certFile) }}"
                                            class="btn btn-xs btn-light-primary mt-1 btn-preview-doc"><i
                                                class="flaticon-eye"></i>
                                            Lihat File</button>
                                    @endif
                                    <x-revision-note name="l2_{{ $key }}_file" :notes="$revisionNotes ?? []" />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 3. Authorized Agent --}}
    <div class="row mt-6">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">Apakah merupakan authorized agent?</label>
                <x-vendor-radio name="l3_is_agent" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['l3_is_agent'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_l3" :readonly="$isReadOnly" />

                <div id="wrap_l3"
                    class="mt-4 p-4 bg-light rounded border-left border-primary {{ ($draft['l3_is_agent'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <x-vendor-input name="l3_principal_name" label="Nama Perusahaan Principalnya:" :value="$draft['l3_principal_name'] ?? ''"
                        :readonly="$isReadOnly" />
                </div>
            </div>
        </div>
    </div>
</div>
