<div class="form-section-title mt-10">{{ __('agency_advertising_vendor_header') }}</div>

<div class="specific-container">
    {{-- Keanggotaan Asosiasi --}}
    <div class="row">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('q_association_membership_with_proof') }}</label>
                <div class="form-row">
                    <div class="col-md-7">
                        <input type="text" name="h1_association" class="form-control"
                            placeholder="{{ __('association_name_status_placeholder') }}" value="{{ $draft['h1_association'] ?? '' }}"
                            {{ $isReadOnly ? 'readonly' : '' }}>
                        <x-revision-note name="h1_association" :notes="$revisionNotes ?? []" />
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <x-vendor-input type="file" name="h1_association_file" :value="$draft['h1_association_file'] ?? null"
                                :readonly="$isReadOnly" :placeholder="__('upload_proof_placeholder')" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kategori Spesialisasi --}}
    <div class="row mt-6">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('specialization_category') }}</label>
                <input type="text" name="h2_specialization" class="form-control"
                    placeholder="{{ __('specialization_placeholder') }}"
                    value="{{ $draft['h2_specialization'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                <x-revision-note name="h2_specialization" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    {{-- Pengalaman Project 2 Tahun Terakhir --}}
    <div class="row mt-6">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">{{ __('project_types_last_2_years') }}</label>
                <p class="text-muted font-size-xs mb-3">{{ __('mention_client_and_work_done') }}</p>
                <textarea name="h3_project_experience" class="form-control" rows="5"
                    placeholder="{{ __('project_experience_placeholder') }}"
                    {{ $isReadOnly ? 'readonly' : '' }}>{{ $draft['h3_project_experience'] ?? '' }}</textarea>
                <x-revision-note name="h3_project_experience" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>
</div>