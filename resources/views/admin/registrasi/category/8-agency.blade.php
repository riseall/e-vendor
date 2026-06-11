<div class="form-section-title mt-10">Pemasok Jasa Agency Advertising</div>

<div class="specific-container">
    {{-- Keanggotaan Asosiasi --}}
    <div class="row">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">Keanggotaan asosiasi (Sebutkan nama asosiasi, status, dan lampirkan bukti
                    keanggotaan)</label>
                <div class="form-row">
                    <div class="col-md-7">
                        <input type="text" name="h1_association" class="form-control"
                            placeholder="Nama Asosiasi & Status..." value="{{ $draft['h1_association'] ?? '' }}"
                            {{ $isReadOnly ? 'readonly' : '' }}>
                        <x-revision-note name="h1_association" :notes="$revisionNotes ?? []" />
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <x-vendor-input type="file" name="h1_association_file" :value="$draft['h1_association_file'] ?? null"
                                :readonly="$isReadOnly" placeholder="Upload Bukti..." />
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
                <label class="question-label">Kategori spesialisasi</label>
                <input type="text" name="h2_specialization" class="form-control"
                    placeholder="Contoh: Digital Ads, Creative Design, Media Placement, dll"
                    value="{{ $draft['h2_specialization'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                <x-revision-note name="h2_specialization" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>

    {{-- Pengalaman Project 2 Tahun Terakhir --}}
    <div class="row mt-6">
        <div class="col-md-12">
            <div class="question-wrapper">
                <label class="question-label">Jenis project yang dikerjakan dalam 2 tahun terakhir</label>
                <p class="text-muted font-size-xs mb-3">Mohon sebutkan nama perusahaan dan jenis pekerjaan yang
                    dilakukan</p>
                <textarea name="h3_project_experience" class="form-control" rows="5"
                    placeholder="1. PT. ABC - Campaign Ramadhan 2025&#10;2. PT. XYZ - Social Media Management"
                    {{ $isReadOnly ? 'readonly' : '' }}>{{ $draft['h3_project_experience'] ?? '' }}</textarea>
                <x-revision-note name="h3_project_experience" :notes="$revisionNotes ?? []" />
            </div>
        </div>
    </div>
</div>
