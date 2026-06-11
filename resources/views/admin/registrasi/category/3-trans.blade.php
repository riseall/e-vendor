<div class="form-section-title mt-10">Pemasok Jasa Transporter, Forwarder, PPJK</div>

<div class="specific-container">
    {{-- Baris 1: Komitmen K3 & Asuransi --}}
    <div class="row">
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Komitmen terhadap K3, safety dan security</label>
                <div class="text-muted font-size-xs mb-3">Contoh: P3K, APAR, Gembok, Segel pengaman, dll</div>
                <x-vendor-radio name="t1_k3_commitment" :options="[
                    ['value' => 'yes', 'label' => 'Ya, lampirkan buktinya'],
                    ['value' => 'no', 'label' => 'Tidak'],
                ]" :selected="$draft['t1_k3_commitment'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_t1" :readonly="$isReadOnly" />

                <div id="wrap_t1"
                    class="mt-3 p-4 bg-light rounded border-left border-primary {{ ($draft['t1_k3_commitment'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <div class="form-group">
                        <label>Upload bukti komitmen K3</label>
                        <x-vendor-input type="file" name="t1_safety_file" :value="$draft['t1_safety_file'] ?? null" :readonly="$isReadOnly"
                            placeholder="Upload bukti komitmen K3..." />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Apakah setiap pengiriman diasuransikan</label>
                <x-vendor-radio name="t4_is_insured" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['t4_is_insured'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_t4" :readonly="$isReadOnly" />

                <div id="wrap_t4" class="mt-3 {{ ($draft['t4_is_insured'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <x-vendor-input name="t4_insurance_pct" placeholder="Penggantian maksimal ....%" :value="$draft['t4_insurance_pct'] ?? ''"
                        :readonly="$isReadOnly" />
                </div>
            </div>
        </div>
    </div>

    {{-- Baris 2: Data Logger & Armada --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Memiliki armada truk</label>
                <x-vendor-radio name="t2_truck_type" :options="[['value' => 'ac', 'label' => 'AC'], ['value' => 'non_ac', 'label' => 'Non AC']]" :selected="$draft['t2_truck_type'] ?? ''" :readonly="$isReadOnly" />
            </div>
        </div>

        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Memiliki/bisa menyediakan data logger untuk suhu dan tracking GPS</label>
                <x-vendor-radio name="t3_has_logger" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['t3_has_logger'] ?? 'no'" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    {{-- Baris 3: Armada Sendiri & Pihak Ketiga --}}
    <div class="row mt-6">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">Apakah memiliki armada sendiri untuk transportasi ke luar Jawa</label>

                <x-vendor-radio name="t5_own_fleet_outer_island" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['t5_own_fleet_outer_island'] ?? 'no'"
                    class="toggle-input" data-target="#wrap_t6" data-trigger="no" :readonly="$isReadOnly" />

                <div id="wrap_t6"
                    class="toggle-content mt-4 {{ ($draft['t5_own_fleet_outer_island'] ?? '') === 'no' ? '' : 'd-none' }}">
                    <label class="font-weight-bold mb-3">Mohon sebutkan pihak ketiga (3PL) yang digunakan:</label>
                    <div class="form-row">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text">Darat</span></div>
                                <input type="text" name="t6_3pl_darat" class="form-control" placeholder="..."
                                    value="{{ $draft['t6_3pl_darat'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                            </div>
                            <x-revision-note name="t6_3pl_darat" :notes="$revisionNotes ?? []" />
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text">Laut</span></div>
                                <input type="text" name="t6_3pl_laut" class="form-control" placeholder="..."
                                    value="{{ $draft['t6_3pl_laut'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                            </div>
                            <x-revision-note name="t6_3pl_laut" :notes="$revisionNotes ?? []" />
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text">Udara</span></div>
                                <input type="text" name="t6_3pl_udara" class="form-control" placeholder="..."
                                    value="{{ $draft['t6_3pl_udara'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                            </div>
                            <x-revision-note name="t6_3pl_udara" :notes="$revisionNotes ?? []" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Baris 4: Asosiasi --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="question-wrapper">
                <label class="question-label">Keanggotaan asosiasi (Sebutkan nama asosiasi dan status
                    keanggotaan)</label>
                <x-vendor-input name="t7_association" placeholder="Nama Asosiasi & Status..." :value="$draft['t7_association'] ?? ''"
                    :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    <hr class="my-8">
    <h6 class="font-weight-bolder mb-4 text-primary">KHUSUS UNTUK FORWARDER DAN PPJK </h6>

    <div class="row">
        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Nama ahli kepabeanan (Lampirkan sertifikat lulus ujian
                    kepabeanan)</label>
                <x-vendor-input name="t8_customs_expert" placeholder="Nama Ahli" :value="$draft['t8_customs_expert'] ?? ''" :readonly="$isReadOnly"
                    class="mb-2" />

                <div class="form-group">
                    <label>Upload Sertifikat Ahli</label>
                    <x-vendor-input type="file" name="t8_expert_cert" :value="$draft['t8_expert_cert'] ?? null" :readonly="$isReadOnly"
                        placeholder="Upload Sertifikat Ahli..." />
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="question-wrapper">
                <label class="question-label">Apakah memiliki rekanan/ afiliasi kargo di negara lain</label>
                <x-vendor-radio name="t9_has_intl_affiliate" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['t9_has_intl_affiliate'] ?? 'no'" class="toggle-input"
                    data-target="#wrap_t9" :readonly="$isReadOnly" />

                <div id="wrap_t9"
                    class="mt-3 {{ ($draft['t9_has_intl_affiliate'] ?? '') === 'yes' ? '' : 'd-none' }}">
                    <x-vendor-input name="t9_countries" placeholder="Sebutkan negaranya..." :value="$draft['t9_countries'] ?? ''"
                        :readonly="$isReadOnly" />
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="question-wrapper">
                <label class="question-label">Layanan lain yang dimiliki (misal: packing cargo, warehouse, dll)</label>
                <textarea name="t10_other_services" class="form-control" rows="3" placeholder="Sebutkan layanan lainnya..."
                    {{ $isReadOnly ? 'readonly' : '' }}>{{ $draft['t10_other_services'] ?? '' }}</textarea>
                <x-revision-note name="t10_other_services" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>
</div>
