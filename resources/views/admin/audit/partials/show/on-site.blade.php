        {{-- ==================== ON SITE ==================== --}}
        <div class="row">
            <div class="col-md-6">
                <div class="card card-custom mb-5 h-100"
                    style="border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:none;">
                    <div class="card-header border-bottom-0 pt-6 pb-0">
                        <div class="card-title">
                            <span class="card-icon"><i class="flaticon2-map"
                                    style="color:var(--brand-primary);"></i></span>
                            <h5 class="card-label font-weight-bolder" style="color:var(--text-primary);">Info Audit
                                On-Site</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="audit-info-panel">
                            <div class="audit-info-label">Jadwal Fix</div>
                            <div class="audit-info-value">
                                {{ optional($audit->confirmed_schedule_at)->format('d M Y H:i') ?? '—' }}</div>
                        </div>
                        <div class="audit-info-panel">
                            <div class="audit-info-label">Lokasi</div>
                            <div class="audit-info-value">{{ $audit->audit_location }}</div>
                        </div>
                        <div class="audit-info-panel mb-0">
                            <div class="audit-info-label">Tim Auditor</div>
                            <ul class="pl-4 mt-2 mb-0"
                                style="font-size:1.05rem; font-weight:600; color:var(--text-primary);">
                                @foreach ($audit->auditor_team ?? [] as $tm)
                                    <li>{{ $tm['name'] }} <span
                                            class="text-muted font-weight-normal font-size-sm">({{ $tm['role'] }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-custom mb-5 h-100"
                    style="border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:none;">
                    <div class="card-header border-bottom-0 pt-6 pb-0">
                        <div class="card-title">
                            <span class="card-icon"><i class="flaticon-clipboard"
                                    style="color:var(--brand-primary);"></i></span>
                            <h5 class="card-label font-weight-bolder" style="color:var(--text-primary);">Agenda &amp;
                                Catatan</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="audit-info-panel">
                            <div class="audit-info-label">Agenda Audit</div>
                            <div class="audit-info-value"
                                style="font-size:1.05rem; font-weight:600; color:var(--text-primary);">
                                {!! nl2br(e($audit->audit_agenda)) !!}
                            </div>
                        </div>
                        <div class="audit-info-panel mb-0">
                            <div class="audit-info-label">Catatan Setup Awal</div>
                            <div class="audit-info-value"
                                style="font-size:1.05rem; font-weight:600; color:var(--text-primary);">
                                {{ $audit->summary ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom mb-5 mt-5"
            style="border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:none;">
            <div class="card-header border-bottom-0 pt-6 pb-0">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="flaticon2-document" style="color:var(--brand-primary);"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder" style="color:var(--text-primary);">Unggah Hasil Audit
                    </h5>
                </div>
            </div>

            <div class="card-body">
                @if (in_array($audit->status, [\App\Models\VendorAudit::STATUS_COMPLETED, \App\Models\VendorAudit::STATUS_REJECTED], true) &&
                        $audit->audit_result_path)
                    @php
                        $cat = $audit->audit_result_category;
                        if ($cat === 'terekomendasi') {
                            $badgeClass = 'badge-light-success text-success';
                            $catLabel = 'TEREKOMENDASI';
                        } elseif ($cat === 'tdk_rekomendasi') {
                            $badgeClass = 'badge-light-danger text-danger';
                            $catLabel = 'TIDAK REKOMENDASI';
                        } elseif ($cat === 'on_hold') {
                            $badgeClass = 'badge-light-warning text-warning';
                            $catLabel = 'ON HOLD';
                        } else {
                            $badgeClass = 'badge-light-primary text-primary';
                            $catLabel = strtoupper(str_replace('_', ' ', $cat ?? '—'));
                        }
                    @endphp
                    <div class="verification-panel is-approved p-5">
                        <div class="verification-panel-head mb-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                            <div class="min-w-0">
                                <div class="panel-head-title font-weight-bolder" style="font-size:1.05rem; color:var(--text-primary);">
                                    File Hasil Audit On-Site
                                </div>
                            </div>
                            <div>
                                <span class="verif-status-badge is-approved">FINAL</span>
                            </div>
                        </div>
                        <div class="verification-panel-body">
                            <div class="mb-4">
                                <x-preview-doc-button :url="Storage::url($audit->audit_result_path)" label="Lihat File Hasil Audit" />
                            </div>

                            <div class="row pt-2">
                                <div class="col-md-4 mb-3">
                                    <div class="text-muted font-weight-bold font-size-xs text-uppercase mb-2" style="letter-spacing: 0.5px;">Kategori Rekomendasi</div>
                                    <div>
                                        <span class="badge {{ $badgeClass }} font-weight-bolder px-3 py-2" style="font-size: 0.85rem; border-radius: 6px;">
                                            {{ $catLabel }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <div class="text-muted font-weight-bold font-size-xs text-uppercase mb-2" style="letter-spacing: 0.5px;">Keterangan / Catatan</div>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.95rem; line-height: 1.6;">
                                        {!! nl2br(e($audit->summary ?? '—')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('qa.audit.store-result', $audit->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-3 col-form-label font-weight-bold text-right text-dark">File Hasil Audit
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                @if ($audit->audit_result_path)
                                    <div class="mb-3">
                                        <x-preview-doc-button :url="Storage::url($audit->audit_result_path)" label="Lihat File Saat Ini" />
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" name="audit_result_file" class="custom-file-input"
                                        id="audit_result_file" {{ !$audit->audit_result_path ? 'required' : '' }}>
                                    <label class="custom-file-label" for="audit_result_file">Pilih file</label>
                                </div>
                                <span class="form-text text-muted">Format: PDF, Word, JPEG, PNG (Maks 10MB)</span>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-3 col-form-label font-weight-bold text-right text-dark">Kategori <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <x-vendor-radio name="audit_result_category" :options="[
                                    ['value' => 'terekomendasi', 'label' => 'Terekomendasi'],
                                    ['value' => 'tdk_rekomendasi', 'label' => 'Tidak Rekomendasi'],
                                    ['value' => 'on_hold', 'label' => 'On Hold'],
                                ]" :selected="$audit->audit_result_category"
                                    required />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-3 col-form-label font-weight-bold text-right text-dark">Keterangan <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <x-vendor-input type="textarea" name="summary" :value="old('summary', $audit->summary)" required />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-3"></div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary font-weight-bold">
                                    <i class="flaticon2-save"></i> Simpan Hasil Audit
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
