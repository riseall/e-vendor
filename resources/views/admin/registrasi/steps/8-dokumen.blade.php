@php
    $dokumenList = [
        ['name' => 'dok_nib', 'label' => 'NIB (Nomor Induk Berusaha)', 'type' => 'General'],
        ['name' => 'dok_npwp', 'label' => 'NPWP Perusahaan', 'type' => 'General'],
        ['name' => 'dok_company_profile', 'label' => 'Company Profile', 'type' => 'General'],
        ['name' => 'dok_struktur_org', 'label' => 'Struktur Organisasi', 'type' => 'General'],
        ['name' => 'dok_sertifikat_halal', 'label' => 'Sertifikat Halal PBF', 'type' => 'General'],
        // Khusus Lokal
        ['name' => 'dok_akte_pendirian', 'label' => 'Akte Pendirian', 'type' => 'Local Vendor Only'],
        ['name' => 'dok_akte_direksi', 'label' => 'Akte Pengangkatan Direksi', 'type' => 'Local Vendor Only'],
        ['name' => 'dok_sppkp', 'label' => 'Surat Pengukuhan PKP (SPPKP)', 'type' => 'Local Vendor Only'],
        ['name' => 'dok_ktp_pj', 'label' => 'KTP Penanggung Jawab Perusahaan ', 'type' => 'Local Vendor Only'],
        [
            'name' => 'dok_pernyataan_keaslian',
            'label' => 'Surat Pernyataan Keaslian Dokumen',
            'type' => 'Local Vendor Only',
            'template' => 'Surat pernyataan Keaslian Dokumen.docx',
        ],
        [
            'name' => 'dok_pakta_integritas',
            'label' => 'Pakta Integritas',
            'type' => 'Local Vendor Only',
            'template' => 'Pakta Integritas.docx',
        ],
        [
            'name' => 'dok_bebas_perkara',
            'label' => 'Surat Pernyataan Tidak Dalam Pengawasan Pengadilan dan atau Tidak Masuk Dalam Daftar Hitam',
            'type' => 'Local Vendor Only',
            'template' =>
                'Surat Pernyataan Tidak Dalam Pengawasan Pengadilan dan atau Tidak Masuk Dalam Daftar Hitam.docx',
        ],
    ];
@endphp

<div class="form-section-title mb-4">Lampiran Dokumen</div>
<p class="text-muted mb-8">
    Seluruh dokumen bersifat opsional. Jika diunggah, gunakan format PDF/Gambar dengan ukuran maksimal 5 MB.
</p>

<div class="modern-doc-list">
    @foreach (collect($dokumenList)->groupBy('type') as $type => $items)
        <h5 class="text-primary font-weight-bold mb-4 mt-6">{{ $type }} Documents</h5>
        <div class="list-container mb-4">
            @foreach ($items as $dok)
                @php
                    $isUploaded = isset($uploadedDocs[$dok['name']]);
                    $docData = $uploadedDocs[$dok['name']] ?? null;
                    $revisionNote = $revisionNotes[$dok['name']] ?? null;
                @endphp

                <div class="doc-item {{ $revisionNote ? 'status-pending has-revision' : ($isUploaded ? 'status-uploaded' : 'status-pending') }}"
                    id="item-{{ $dok['name'] }}" data-field-name="{{ $dok['name'] }}">
                    <div class="doc-info">
                        <div class="doc-icon-wrapper">
                            <i class="flaticon2-file icon-lg"></i>
                        </div>
                        <div class="doc-text">
                            <span class="doc-label text-dark-75 font-weight-bolder">{{ $dok['label'] }}</span>
                            <div class="doc-meta">
                                @if ($isUploaded)
                                    <span class="text-success font-weight-bold file-name-text">
                                        <i class="ki ki-check-circle icon-nm"></i> {{ $docData['original_name'] }}
                                    </span>
                                @else
                                    <span class="text-muted file-name-text">Belum ada file terpilih</span>
                                @endif
                                @if ($revisionNote)
                                    <div class="revision-note-message text-danger font-weight-bold mt-1"
                                        data-revision-field="{{ $dok['name'] }}">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Revisi: {{ $revisionNote }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="doc-actions">
                        @if (!empty($dok['template']))
                            <a href="{{ Storage::url('doc_template/' . $dok['template']) }}" target="_blank"
                                class="btn btn-light-warning btn-icon btn-sm mr-2 btn-download-template"
                                title="Download Template {{ $dok['label'] }}">
                                <i class="flaticon2-download icon-md"></i>
                            </a>
                        @endif

                        @if ($isUploaded)
                            <button type="button" data-url="{{ $docData['url'] ?? '' }}"
                                data-title="Preview {{ $dok['label'] ?? 'Dokumen' }}"
                                class="btn btn-light-success btn-icon btn-sm mr-2 btn-preview-doc"
                                title="Klik untuk preview dokumen" @if (empty($docData['url'])) disabled @endif>
                                <i class="flaticon-eye icon-md"></i>
                            </button>
                        @endif

                        @if (!$isReadOnly)
                            <label class="btn btn-light-primary btn-sm font-weight-bold mb-0 btn-upload">
                                <span>{{ $isUploaded ? 'Ganti' : 'Upload' }}</span>
                                <input type="file" name="{{ $dok['name'] }}" class="file-upload-validate d-none"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </label>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[Step 8 Dokumen] Initializing...');

            // Log semua preview buttons dan URL mereka
            const previewButtons = document.querySelectorAll('.btn-preview-doc');
            console.log(`[Step 8 Dokumen] Found ${previewButtons.length} preview buttons:`);

            previewButtons.forEach((btn, index) => {
                const url = btn.getAttribute('data-url');
                const title = btn.getAttribute('data-title');
                console.log(`  [Button ${index}] URL: ${url || '(kosong)'}, Title: ${title}`);
            });

            // File upload handler
            $(document).on('change', '.file-upload-validate', function(e) {
                const fileInput = $(this);
                const file = e.target.files[0];
                const container = fileInput.closest('.doc-item');
                const fileNameDisplay = container.find('.file-name-text');
                const btnText = container.find('.btn-upload span');

                if (file) {
                    // Update UI ke mode "Siap Upload"
                    container.removeClass('status-uploaded status-pending').addClass('status-ready');
                    fileNameDisplay.html(
                        `<i class="flaticon-upload text-primary"></i> Siap upload: <strong>${file.name}</strong>`
                        );
                    btnText.text('Ganti');

                    // Kasih feedback visual dikit
                    container.fadeOut(100).fadeIn(400);
                }
            });

            console.log('[Step 8 Dokumen] Ready');
        });
    </script>
@endpush
