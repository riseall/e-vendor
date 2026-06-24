@extends('layouts.app', ['title' => 'Risk Assessment Pemasok Baru'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Risk Assessment')
@section('page_title', 'Risk Assessment Pemasok Baru')
@section('page_desc', 'Formulir risk assessment supplier baru sesuai format QA 2026.')

@section('content')
    @php
        $vendorName = optional($application->general)->nama_perusahaan ?? $application->user->name;
        $vendorEmail = optional($application->general)->email_perusahaan ?? $application->user->email;
        $vendorAddress = optional($application->general)->alamat_perusahaan ?? '-';
        $categoryLabels = $application->categories->pluck('category_id')->map(function ($id) {
            return \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id;
        })->implode(', ');
        $supplierType = $autoScores['supplier_type_label'];
        $actionLabel = optional($qualification)->audit_type === 'on_site'
            ? 'Audit On Site'
            : (optional($qualification)->audit_type === 'on_desk' ? 'Desk Evaluation / Document / Questionnaire' : 'Qualified');
    @endphp

    <div class="card card-custom gutter-b">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">
                    Formulir Risk Assessment Supplier Baru
                    <small>{{ $vendorName }}</small>
                </h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('qa.risk-assessment.index') }}" class="btn btn-light-primary font-weight-bold">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <form action="{{ route('qa.risk-assessment.store', $application->id) }}" method="POST" id="form-risk-assessment">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-custom alert-light-danger fade show mb-6" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                        <div class="alert-text">{{ $errors->first() }}</div>
                    </div>
                @endif

                <div class="alert alert-custom alert-light-info fade show mb-8" role="alert">
                    <div class="alert-icon"><i class="flaticon-info"></i></div>
                    <div class="alert-text">
                        Rumus total nilai: <strong>(Safety Efficacy + Availability) x (Detectability + Probability)</strong>.
                        Kategori: 12-88 Low, 89-164 Medium, 165-240 High.
                    </div>
                </div>

                <div class="row mb-8">
                    <div class="col-md-6">
                        <label>Nama Pemasok</label>
                        <input type="text" class="form-control form-control-solid" value="{{ $vendorName }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Alamat Pemasok</label>
                        <input type="text" class="form-control form-control-solid" value="{{ $vendorAddress }}" readonly>
                    </div>
                    <div class="col-md-4 mt-4">
                        <label>Jenis Pemasok</label>
                        <input type="text" class="form-control form-control-solid"
                            value="{{ $supplierType }} (Skor: {{ $autoScores['supplier_type_score'] }})" readonly>
                    </div>
                    <div class="col-md-4 mt-4">
                        <label>Pemasok</label>
                        <input type="text" class="form-control form-control-solid" value="{{ $categoryLabels ?: '-' }}" readonly>
                    </div>
                    <div class="col-md-4 mt-4">
                        <label>No Permohonan</label>
                        <input type="text" class="form-control form-control-solid"
                            value="{{ $application->application_number ?? '-' }}" readonly>
                    </div>
                </div>

                <div class="separator separator-dashed my-8"></div>

                <h4 class="text-primary mb-5">A. Safety Efficacy (Severity)</h4>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Kelengkapan Dokumen</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control form-control-solid"
                            value="{{ $autoScores['doc_label'] }} (Skor: {{ $autoScores['doc_score'] }})" readonly>
                        <span class="form-text text-muted">
                            Dokumen lengkap: 1, kurang lengkap: 3, tidak lengkap/N/A: 4.
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Bahan memiliki critical attribute</label>
                    <div class="col-md-8">
                        @php $selectedAttr = old('score_safety_efficacy_attr', optional($qualification)->score_safety_efficacy_attr); @endphp
                        <div class="radio-inline">
                            <label class="radio radio-danger">
                                <input type="radio" name="score_safety_efficacy_attr" value="8"
                                    class="calc-trigger" {{ (string) $selectedAttr === '8' ? 'checked' : '' }} required>
                                <span></span>Memiliki critical attribute (8)
                            </label>
                            <label class="radio radio-success">
                                <input type="radio" name="score_safety_efficacy_attr" value="1"
                                    class="calc-trigger" {{ (string) $selectedAttr === '1' ? 'checked' : '' }}>
                                <span></span>Tidak memiliki critical attribute (1)
                            </label>
                        </div>
                        @error('score_safety_efficacy_attr')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h4 class="text-primary mb-5 mt-10">B. Availability (Severity)</h4>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Traceability Supply Chain</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control form-control-solid"
                            value="{{ $autoScores['traceability_label'] }} (Skor: {{ $autoScores['traceability_score'] }})" readonly>
                        <span class="form-text text-muted">Lengkap: 1, tidak lengkap: 4.</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Jenis Pemasok</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control form-control-solid"
                            value="{{ $supplierType }} (Skor: {{ $autoScores['supplier_type_score'] }})" readonly>
                        <span class="form-text text-muted">Manufaktur: 1, Distributor: 2, Repacker: 3, Trader: 4.</span>
                    </div>
                </div>

                <h4 class="text-primary mb-5 mt-10">C. Detectability</h4>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Country / Regulatory Risk</label>
                    <div class="col-md-8">
                        @php $selectedCountry = old('score_detectability_country', optional($qualification)->score_detectability_country); @endphp
                        <select class="form-control select2 calc-trigger @error('score_detectability_country') is-invalid @enderror"
                            name="score_detectability_country" id="score_detectability_country" required>
                            <option value="">-- Pilih Risiko Negara --</option>
                            <option value="4" {{ (string) $selectedCountry === '4' ? 'selected' : '' }}>Negara high risk / regulasi lemah (4)</option>
                            <option value="3" {{ (string) $selectedCountry === '3' ? 'selected' : '' }}>Negara berkembang / kontrol terbatas (3)</option>
                            <option value="2" {{ (string) $selectedCountry === '2' ? 'selected' : '' }}>Negara dengan regulasi menengah (2)</option>
                            <option value="1" {{ (string) $selectedCountry === '1' ? 'selected' : '' }}>Negara dengan otoritas kuat / FDA (1)</option>
                        </select>
                        @error('score_detectability_country')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Warning Letter / Hasil Audit</label>
                    <div class="col-md-8">
                        @php $selectedWarning = old('score_detectability_warning', optional($qualification)->score_detectability_warning); @endphp
                        <div class="radio-inline">
                            <label class="radio radio-danger">
                                <input type="radio" name="score_detectability_warning" value="4"
                                    class="calc-trigger" {{ (string) $selectedWarning === '4' ? 'checked' : '' }} required>
                                <span></span>Ada (4)
                            </label>
                            <label class="radio radio-success">
                                <input type="radio" name="score_detectability_warning" value="1"
                                    class="calc-trigger" {{ (string) $selectedWarning === '1' ? 'checked' : '' }}>
                                <span></span>Tidak Ada (1)
                            </label>
                        </div>
                        @error('score_detectability_warning')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h4 class="text-primary mb-5 mt-10">D. Probability</h4>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Fungsi Bahan</label>
                    <div class="col-md-8">
                        @php $selectedFunction = old('score_probability_function', optional($qualification)->score_probability_function); @endphp
                        <select class="form-control select2 calc-trigger @error('score_probability_function') is-invalid @enderror"
                            name="score_probability_function" id="score_probability_function" required>
                            <option value="">-- Pilih Fungsi Bahan --</option>
                            <option value="4" {{ (string) $selectedFunction === '4' ? 'selected' : '' }}>API (4)</option>
                            <option value="3" {{ (string) $selectedFunction === '3' ? 'selected' : '' }}>Eksipien / Primary Packaging (3)</option>
                            <option value="2" {{ (string) $selectedFunction === '2' ? 'selected' : '' }}>Secondary Packaging (2)</option>
                            <option value="1" {{ (string) $selectedFunction === '1' ? 'selected' : '' }}>Non Kontak Produk (1)</option>
                        </select>
                        @error('score_probability_function')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mt-10">
                    <label class="col-md-4 col-form-label">Manager QA</label>
                    <div class="col-md-8">
                        <select class="form-control select2 @error('qa_manager_id') is-invalid @enderror" name="qa_manager_id"
                            id="qa_manager_id">
                            <option value="">-- Pilih Manager QA jika diperlukan --</option>
                            @foreach ($qaManagers as $manager)
                                <option value="{{ $manager->id }}"
                                    {{ (string) old('qa_manager_id', optional($qualification)->qa_manager_id) === (string) $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}{{ $manager->email ? ' - ' . $manager->email : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('qa_manager_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Keterangan / Catatan QA</label>
                    <div class="col-md-8">
                        <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="4"
                            placeholder="Masukkan keterangan penilaian jika ada...">{{ old('notes', optional($qualification)->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="separator separator-solid my-8"></div>

                <div class="bg-gray-100 p-8 rounded">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            <h4 class="font-weight-bolder text-dark">
                                Total Nilai Pemasok:
                                <span id="display_total_score" class="text-primary font-size-h1 ml-3">
                                    {{ optional($qualification)->total_score ?? 0 }}
                                </span>
                            </h4>
                            <p class="text-muted mt-2 mb-0">
                                (<span id="display_score_a">0</span> + <span id="display_score_b">0</span>) x
                                (<span id="display_score_c">0</span> + <span id="display_score_d">0</span>)
                            </p>
                        </div>
                        <div class="col-lg-5 text-lg-right mt-4 mt-lg-0">
                            <h5 class="mb-2">Kategori & Action</h5>
                            <span id="display_risk_badge"
                                class="label label-xl label-inline label-light-dark font-weight-bold mr-2">Belum Dihitung</span>
                            <span id="display_action_badge"
                                class="label label-xl label-inline label-light-dark font-weight-bold">{{ $actionLabel }}</span>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="auto_score_doc" value="{{ $autoScores['doc_score'] }}">
                <input type="hidden" id="auto_score_trace" value="{{ $autoScores['traceability_score'] }}">
                <input type="hidden" id="auto_score_type" value="{{ $autoScores['supplier_type_score'] }}">
                <input type="hidden" name="vendor_application_id" value="{{ $application->id }}">
            </div>

            <div class="card-footer text-right">
                <a href="{{ route('qa.risk-assessment.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary" id="btn-submit" disabled>Simpan Penilaian</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            var lowThreshold = {{ $lowThreshold }};
            var highThreshold = {{ $highThreshold }};

            $('.calc-trigger').on('input change', calculateRiskScore);
            calculateRiskScore();

            function scoreValue(selector) {
                var value = parseInt($(selector).val(), 10);
                return Number.isNaN(value) ? 0 : value;
            }

            function radioValue(name) {
                var value = parseInt($('input[name="' + name + '"]:checked').val(), 10);
                return Number.isNaN(value) ? 0 : value;
            }

            function calculateRiskScore() {
                var doc = scoreValue('#auto_score_doc');
                var trace = scoreValue('#auto_score_trace');
                var type = scoreValue('#auto_score_type');
                var attr = radioValue('score_safety_efficacy_attr');
                var country = scoreValue('#score_detectability_country');
                var warning = radioValue('score_detectability_warning');
                var materialFunction = scoreValue('#score_probability_function');

                var isComplete = attr > 0 && country > 0 && warning > 0 && materialFunction > 0;
                $('#btn-submit').prop('disabled', !isComplete);

                var scoreA = doc + attr;
                var scoreB = trace + type;
                var scoreC = country + warning;
                var scoreD = materialFunction;
                var total = isComplete ? (scoreA + scoreB) * (scoreC + scoreD) : 0;

                $('#display_score_a').text(isComplete ? scoreA : 0);
                $('#display_score_b').text(isComplete ? scoreB : 0);
                $('#display_score_c').text(isComplete ? scoreC : 0);
                $('#display_score_d').text(isComplete ? scoreD : 0);
                $('#display_total_score').text(total);

                if (!isComplete) {
                    setBadges('Belum Dihitung', 'label-light-dark', 'Lengkapi Form', 'label-light-dark');
                    return;
                }

                if (total <= lowThreshold) {
                    setBadges('LOW', 'label-light-success', 'Qualified', 'label-light-success');
                } else if (total <= highThreshold) {
                    setBadges('MEDIUM', 'label-light-warning', 'Desk Evaluation / Document / Questionnaire', 'label-light-warning');
                } else {
                    setBadges('HIGH', 'label-light-danger', 'Audit On Site', 'label-light-danger');
                }
            }

            function setBadges(riskText, riskClass, actionText, actionClass) {
                $('#display_risk_badge')
                    .removeClass('label-light-dark label-light-success label-light-warning label-light-danger')
                    .addClass(riskClass)
                    .text(riskText);

                $('#display_action_badge')
                    .removeClass('label-light-dark label-light-success label-light-warning label-light-danger')
                    .addClass(actionClass)
                    .text(actionText);
            }
        });
    </script>
@endpush
