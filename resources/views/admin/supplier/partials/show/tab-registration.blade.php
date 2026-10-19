@php
    $application = $app;
    view()->share('application', $app);
    $cats = isset($app->categories)
        ? $app->categories
            ->pluck('category_id')
            ->map(function ($id) {
                return \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id;
            })
            ->filter()
            ->values()
        : collect();

    $docTitles = [
        'dok_nib' => 'NIB (Nomor Induk Berusaha)',
        'dok_npwp' => 'NPWP Perusahaan',
        'dok_company_profile' => 'Company Profile',
        'dok_struktur_org' => 'Bagan Struktur Organisasi',
        'dok_sertifikat_halal' => 'Sertifikat Halal',
        'dok_akte_pendirian' => 'Akta Pendirian Perusahaan',
        'dok_akte_direksi' => 'Akta Pengangkatan Direksi',
        'dok_sppkp' => 'Surat Pengukuhan PKP (SPPKP)',
        'dok_ktp_pj' => 'KTP Penanggung Jawab',
        'dok_pernyataan_keaslian' => 'Surat Pernyataan Keaslian Dokumen',
        'dok_pakta_integritas' => 'Pakta Integritas',
        'dok_bebas_perkara' => 'Surat Bebas Perkara / Tidak Masuk Daftar Hitam',
        'iso_certificate' => 'Sertifikat ISO / Standar Mutu',
        // Fallback backward-compatibility keys
        'dokumen_nib' => 'Nomor Induk Berusaha (NIB)',
        'dokumen_npwp' => 'NPWP Perusahaan',
        'dokumen_sppkp' => 'Surat Pengukuhan PKP (SPPKP)',
        'dokumen_akta_pendirian' => 'Akta Pendirian Perusahaan',
        'dokumen_akta_perubahan' => 'Akta Perubahan Terakhir',
        'dokumen_sk_kemenkumham' => 'SK Pengesahan Kemenkumham',
        'dokumen_ktp_pengurus' => 'KTP Pengurus / Direksi',
        'dokumen_npwp_pengurus' => 'NPWP Pengurus / Direksi',
        'dokumen_rekening_koran' => 'Rekening Koran Perusahaan',
        'dokumen_struktur_organisasi' => 'Bagan Struktur Organisasi',
        'dokumen_profil_perusahaan' => 'Company Profile',
        'dokumen_sertifikat_iso' => 'Sertifikat Standar / ISO',
    ];

    $selectedCategoryIds = $app->categories ? $app->categories->pluck('category_id')->all() : [];

    $hasAnyTkdn =
        !empty($app->products) &&
        $app->products->contains(function ($p) {
            return !empty($p->has_tkdn) || !empty($p->tkdn_file_path);
        });
    $hasAnySni =
        !empty($app->products) &&
        $app->products->contains(function ($p) {
            return !empty($p->has_sni) || !empty($p->sni_file_path);
        });
@endphp

{{-- 1. INFORMASI UMUM PERUSAHAAN (COMPANY'S INFORMATION) --}}
<div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:10px;">
    <div class="card-header border-bottom py-3 px-5 d-flex align-items-center">
        <div class="card-title m-0">
            <span class="card-icon mr-2 text-primary">
                <i class="fas fa-building text-primary"></i>
            </span>
            <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                Informasi Umum Perusahaan <span class="text-muted font-weight-normal font-size-sm ml-1">(Company's
                    Information)</span>
            </h5>
        </div>
    </div>
    <div class="card-body p-5">
        <div class="row">
            {{-- Kolom Kiri: Identitas & Kontak Perusahaan --}}
            <div class="col-lg-6 pr-lg-5">
                <table class="table table-sm table-borderless table-detail-keyval m-0" style="font-size:0.875rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted font-weight-bold" style="width: 38%;">Nama Perusahaan</td>
                            <td class="font-weight-bolder text-dark">{{ optional($general)->nama_perusahaan ?: '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Alamat Perusahaan</td>
                            <td class="text-dark">{{ optional($general)->alamat_perusahaan ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Situs Resmi Perusahaan</td>
                            <td>
                                @if (optional($general)->website)
                                    <a href="{{ optional($general)->website }}" target="_blank" rel="noopener"
                                        class="text-primary font-weight-bold">
                                        <i
                                            class="fas fa-globe mr-1 text-primary font-size-xs"></i>{{ optional($general)->website }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Email Perusahaan</td>
                            <td>
                                @if (optional($general)->email_perusahaan)
                                    <a href="mailto:{{ optional($general)->email_perusahaan }}"
                                        class="text-dark font-weight-bold text-hover-primary">
                                        <i
                                            class="fas fa-envelope mr-1 text-info font-size-xs"></i>{{ optional($general)->email_perusahaan }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Nomor Telepon Perusahaan</td>
                            <td class="text-dark font-weight-bold">
                                @if (optional($general)->telepon_perusahaan)
                                    <i
                                        class="fas fa-phone-alt mr-1 text-primary font-size-xs"></i>{{ optional($general)->telepon_perusahaan }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Nomor NIB</td>
                            <td>
                                @if (optional($general)->nib)
                                    <span
                                        class="font-weight-bold text-dark font-monospace px-2 py-0.5 bg-light rounded">
                                        {{ optional($general)->nib }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Nomor NPWP</td>
                            <td>
                                @if (optional($general)->npwp)
                                    <span
                                        class="font-weight-bold text-dark font-monospace px-2 py-0.5 bg-light rounded">
                                        {{ optional($general)->npwp }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Kolom Kanan: Contact Person (PIC), Perusahaan Afiliasi, & Bidang Usaha --}}
            <div class="col-lg-6 pl-lg-5 border-left-md">
                <table class="table table-sm table-borderless table-detail-keyval m-0" style="font-size:0.875rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted font-weight-bold" style="width: 38%;">Nama Contact Person</td>
                            <td class="font-weight-bolder text-dark">{{ optional($general)->pic_nama ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Jabatan Contact Person</td>
                            <td class="font-weight-bold text-dark">{{ optional($general)->pic_jabatan ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Email Contact Person</td>
                            <td>
                                @if (optional($general)->pic_email)
                                    <a href="mailto:{{ optional($general)->pic_email }}"
                                        class="text-dark text-hover-primary">
                                        <i
                                            class="fas fa-envelope mr-1 text-info font-size-xs"></i>{{ optional($general)->pic_email }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Nomor Telepon Contact Person</td>
                            <td>
                                @if (optional($general)->pic_telepon)
                                    <i class="fas fa-phone-alt mr-1 text-success font-size-xs"></i>
                                    <span
                                        class="text-dark font-weight-bold">{{ optional($general)->pic_telepon }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold align-top">Perusahaan Lain Milik Pimpinan</td>
                            <td>
                                @php
                                    $hasOtherComp = optional($general)->has_other_company === 'yes';
                                    $otherCompList = optional($general)->other_companies ?: [];
                                @endphp
                                @if ($hasOtherComp && !empty($otherCompList))
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0 bg-light rounded"
                                            style="font-size:0.8rem;">
                                            <thead class="text-muted bg-white">
                                                <tr>
                                                    <th>Nama Perusahaan</th>
                                                    <th>Alamat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($otherCompList as $oc)
                                                    @if (!empty($oc['nama']))
                                                        <tr>
                                                            <td class="font-weight-bold text-dark">{{ $oc['nama'] }}
                                                            </td>
                                                            <td class="text-muted">{{ $oc['alamat'] ?? '-' }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <span class="text-muted">Tidak Ada</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold align-top">Bidang Usaha</td>
                            <td>
                                <div class="d-flex flex-wrap" style="gap:5px;">
                                    @forelse ($cats as $cat)
                                        <span class="badge badge-light-primary font-weight-bolder py-1.5 px-2.5"
                                            style="font-size:0.8rem; border-radius: 6px;">
                                            <i class="fas fa-check-circle mr-1 text-primary"></i> {{ $cat }}
                                        </span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 2. INFORMASI PEMBAYARAN & 3. KOMITMEN TERHADAP STANDAR --}}
<div class="row">
    {{-- Card Kiri: Informasi Pembayaran --}}
    <div class="col-lg-6 mb-6">
        <div class="card card-custom gutter-b shadow-sm border-0 mb-0 h-100" style="border-radius:10px;">
            <div class="card-header border-bottom py-3 px-5 d-flex align-items-center">
                <div class="card-title m-0">
                    <span class="card-icon mr-2 text-primary">
                        <i class="fas fa-money-check-alt text-primary"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                        Informasi Pembayaran <span class="text-muted font-weight-normal font-size-sm ml-1">(Payment
                            Preference)</span>
                    </h5>
                </div>
            </div>
            <div class="card-body p-5">
                <table class="table table-sm table-borderless table-detail-keyval m-0" style="font-size:0.875rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted font-weight-bold" style="width: 38%;">Kesanggupan Termin Pembayaran
                            </td>
                            <td>
                                @php
                                    $termValue = optional($general)->payment_term;
                                    $termDisplay =
                                        $termValue === 'other'
                                            ? (optional($general)->payment_term_other ?:
                                            'Lainnya')
                                            : ($termValue ?:
                                            'Sesuai Kontrak / PO');
                                @endphp
                                <span class="badge badge-light-primary font-weight-bolder px-2.5 py-1">
                                    {{ $termDisplay }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Pemegang Rekening</td>
                            <td class="font-weight-bold text-dark">{{ optional($general)->pemegang_rekening ?: '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Nomor Rekening</td>
                            <td>
                                @if (optional($general)->nomor_rekening)
                                    <span class="font-weight-bold text-dark font-monospace">
                                        {{ optional($general)->nomor_rekening }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Nama Bank</td>
                            <td class="font-weight-bolder text-dark">{{ optional($general)->nama_bank ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Alamat Bank</td>
                            <td class="text-dark">{{ optional($general)->alamat_bank ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">SWIFT Code</td>
                            <td>
                                @if (optional($general)->swift_code)
                                    <span class="font-weight-bold text-dark font-monospace">
                                        {{ optional($general)->swift_code }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Card Kanan: Komitmen terhadap Standar --}}
    <div class="col-lg-6 mb-6">
        <div class="card card-custom gutter-b shadow-sm border-0 mb-0 h-100" style="border-radius:10px;">
            <div class="card-header border-bottom py-3 px-5 d-flex align-items-center">
                <div class="card-title m-0">
                    <span class="card-icon mr-2 text-primary">
                        <i class="fas fa-award text-primary"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                        Komitmen terhadap Standar <span
                            class="text-muted font-weight-normal font-size-sm ml-1">(Commitment to Standards)</span>
                    </h5>
                </div>
            </div>
            <div class="card-body p-5">
                <table class="table table-sm table-borderless table-detail-keyval m-0" style="font-size:0.875rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted font-weight-bold align-top" style="width: 38%;">Sertifikat ISO yang
                                Dimiliki</td>
                            <td>
                                @php
                                    $isoRaw = optional($general)->iso_certificates;
                                    $rawList = [];
                                    if (is_array($isoRaw)) {
                                        $rawList = $isoRaw;
                                    } elseif (is_string($isoRaw) && trim($isoRaw) !== '') {
                                        $decoded = json_decode($isoRaw, true);
                                        $rawList = is_array($decoded)
                                            ? $decoded
                                            : array_filter(array_map('trim', explode(',', $isoRaw)));
                                    }

                                    // Filter out literal 'other' or 'lainnya'
                                    $cleanIsoList = [];
                                    foreach ($rawList as $item) {
                                        $trimmed = trim((string) $item);
                                        if (
                                            $trimmed !== '' &&
                                            strtolower($trimmed) !== 'other' &&
                                            strtolower($trimmed) !== 'lainnya'
                                        ) {
                                            $cleanIsoList[] = $trimmed;
                                        }
                                    }

                                    // Add parsed comma-separated items from iso_other
                                    if (!empty($general->iso_other)) {
                                        $otherParts = array_filter(
                                            array_map('trim', explode(',', $general->iso_other)),
                                        );
                                        foreach ($otherParts as $op) {
                                            if ($op !== '' && !in_array($op, $cleanIsoList, true)) {
                                                $cleanIsoList[] = $op;
                                            }
                                        }
                                    }
                                @endphp
                                @if (!empty($cleanIsoList))
                                    <div class="d-flex flex-wrap mb-2" style="gap:5px;">
                                        @foreach ($cleanIsoList as $iso)
                                            <span class="badge badge-light-primary font-weight-bolder py-1.5 px-2.5"
                                                style="font-size:0.8rem; border-radius:6px; border:1px solid #c7d2fe;">
                                                <i class="fab fa-expeditedssl text-primary mr-1"></i>
                                                {{ $iso }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">Tidak Ada</span>
                                @endif

                                {{-- Berkas ISO Terunggah jika ada --}}
                                @php
                                    $uploadedIsoDocs = $app->documents
                                        ? $app->documents->where('field_name', 'iso_certificate')
                                        : collect();
                                @endphp
                                @if ($uploadedIsoDocs->isNotEmpty())
                                    <div class="d-flex flex-wrap mt-2" style="gap:5px;">
                                        @foreach ($uploadedIsoDocs as $isoDoc)
                                            @php
                                                $rawIsoName = $isoDoc->original_name ?: basename($isoDoc->file_path);
                                                $cleanIsoName = preg_replace('/^\d+_\d*_?/', '', $rawIsoName);
                                            @endphp
                                            <button type="button"
                                                data-url="{{ app(\App\Services\VendorFileService::class)->url($app, $isoDoc->file_path) }}"
                                                data-title="Sertifikat ISO - {{ $cleanIsoName }}"
                                                class="btn btn-xs btn-light-primary btn-preview-doc font-weight-bolder py-1 px-2"
                                                title="{{ $cleanIsoName }}">
                                                <i class="far fa-file-pdf"></i> {{ $cleanIsoName }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold align-top">Komitmen Kualitas, Lingkungan & K3</td>
                            <td>
                                <span
                                    class="badge badge-{{ optional($general)->komitmen_kualitas === 'yes' ? 'light-success' : 'light-secondary' }} font-weight-bolder px-2.5 py-1">
                                    {{ optional($general)->komitmen_kualitas === 'yes' ? 'Tersedia' : 'Tidak Ada' }}
                                </span>
                                @if (optional($general)->komitmen_kualitas === 'yes' && optional($general)->komitmen_kualitas_detail)
                                    <div class="mt-2 px-3 py-2 bg-light rounded font-size-xs text-dark"
                                        style="line-height: 1.4;">
                                        {{ $general->komitmen_kualitas_detail }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 4. INFORMASI LAIN & 5. ISIAN KHUSUS UNTUK PEMASOK LOKAL --}}
<div class="row">
    {{-- Card Kiri: Informasi Lain --}}
    <div class="col-lg-6 mb-6">
        <div class="card card-custom gutter-b shadow-sm border-0 mb-0 h-100" style="border-radius:10px;">
            <div class="card-header border-bottom py-3 px-5 d-flex align-items-center">
                <div class="card-title m-0">
                    <span class="card-icon mr-2 text-primary">
                        <i class="fas fa-info-circle text-primary"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                        Informasi Lain <span class="text-muted font-weight-normal font-size-sm ml-1">(Other
                            Information)</span>
                    </h5>
                </div>
            </div>
            <div class="card-body p-5">
                <table class="table table-sm table-borderless table-detail-keyval m-0" style="font-size:0.875rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted font-weight-bold" style="width: 38%;">Kesanggupan Jangka Waktu
                                Pengiriman</td>
                            <td>
                                @if (optional($general)->lead_time)
                                    <span class="badge badge-light-info font-weight-bolder px-2.5 py-1">
                                        <i class="fas fa-clock mr-1 text-info"></i>{{ optional($general)->lead_time }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold align-top">Pelanggan Utama Perusahaan Farmasi</td>
                            <td class="text-dark">
                                @if (optional($general)->customer_list)
                                    <div class="p-3 bg-light rounded font-size-sm"
                                        style="white-space: pre-line; line-height: 1.5;">{{ $general->customer_list }}
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Card Kanan: Isian Khusus untuk Pemasok Lokal --}}
    <div class="col-lg-6 mb-6">
        <div class="card card-custom gutter-b shadow-sm border-0 mb-0 h-100" style="border-radius:10px;">
            <div class="card-header border-bottom py-3 px-5 d-flex align-items-center">
                <div class="card-title m-0">
                    <span class="card-icon mr-2 text-primary">
                        <i class="fas fa-flag text-primary"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                        Isian Khusus untuk Pemasok Lokal <span
                            class="text-muted font-weight-normal font-size-sm ml-1">(For Local Vendor Only)</span>
                    </h5>
                </div>
            </div>
            <div class="card-body p-5">
                <table class="table table-sm table-borderless table-detail-keyval m-0" style="font-size:0.875rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted font-weight-bold" style="width: 38%;">Status Perusahaan</td>
                            <td>
                                @if (optional($general)->status_perusahaan)
                                    <span
                                        class="badge badge-light-primary font-weight-bolder text-capitalize px-2.5 py-1">
                                        {{ optional($general)->status_perusahaan }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Status Pajak</td>
                            <td>
                                @if (optional($general)->status_pajak)
                                    <span class="badge badge-light-dark font-weight-bolder text-uppercase px-2.5 py-1">
                                        {{ optional($general)->status_pajak }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Skala Perusahaan (NIB)</td>
                            <td>
                                @if (optional($general)->skala_perusahaan)
                                    <span
                                        class="badge badge-light-warning font-weight-bolder text-capitalize px-2.5 py-1">
                                        {{ optional($general)->skala_perusahaan }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Jenis Penanaman Modal</td>
                            <td>
                                @if (optional($general)->jenis_modal)
                                    <span
                                        class="badge badge-light-secondary font-weight-bolder text-uppercase px-2.5 py-1">
                                        {{ optional($general)->jenis_modal }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">KBLI yang Dimiliki</td>
                            <td>
                                @if (optional($general)->kbli)
                                    <span
                                        class="font-weight-bold text-dark font-monospace px-2 py-0.5 bg-light rounded">
                                        {{ optional($general)->kbli }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 6. LAMPIRAN DOKUMEN (ATTACHED DOCUMENTS) --}}
<div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:10px;">
    <div class="card-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between">
        <div class="card-title m-0">
            <span class="card-icon mr-2 text-primary">
                <i class="fas fa-file-contract text-primary"></i>
            </span>
            <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                Lampiran Dokumen <span class="text-muted font-weight-normal font-size-sm ml-1">(Attached
                    Documents)</span>
            </h5>
        </div>
        <span class="badge badge-light-primary font-weight-bolder py-1 px-2.5">
            {{ $app->documents->count() }} Dokumen Terlampir
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table table-custom-clean table-vertical-center table-hover mb-0" style="font-size:0.85rem;">
                <thead>
                    <tr>
                        <th class="pl-5" style="width: 35%;">Jenis Dokumen</th>
                        <th style="width: 45%;">Nama Berkas</th>
                        <th class="text-center" style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($app->documents as $doc)
                        @php
                            $docLabel =
                                $docTitles[$doc->field_name] ??
                                ucwords(str_replace(['dok_', 'dokumen_', '_'], ['', '', ' '], $doc->field_name));
                            $fileSizeStr = $doc->file_size ? round($doc->file_size / 1024, 1) . ' KB' : '';
                            $rawName = $doc->original_name ?: basename($doc->file_path);
                            $cleanFileName = preg_replace('/^\d+_\d*_?/', '', $rawName);
                        @endphp
                        <tr>
                            <td class="pl-5">
                                <div class="font-weight-bolder text-dark">{{ $docLabel }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-30 symbol-light-primary mr-3 flex-shrink-0">
                                        <span class="symbol-label"><i
                                                class="far fa-file-pdf text-primary font-size-h6"></i></span>
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="text-truncate font-weight-bold text-dark"
                                            style="max-width: 300px;" title="{{ $rawName }}">
                                            {{ $cleanFileName }}
                                        </div>
                                        @if ($fileSizeStr)
                                            <span class="text-muted font-size-xs">{{ $fileSizeStr }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center pr-4">
                                @if ($doc->file_url)
                                    <x-preview-doc-button :url="$doc->file_url" :title="$docLabel . ' - ' . $cleanFileName" :compact="true" />
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">
                                <i class="far fa-folder-open icon-2x d-block mb-2 text-muted"></i>
                                Tidak ada dokumen legalitas terlampir.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 7. DAFTAR PRODUK YANG DISUPLAI (LIST OF SUPPLIED PRODUCTS) --}}
<div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:10px;">
    <div class="card-body p-6">
        <div class="form-section-title">{{ __('supplied_products_list') }}</div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="selectedProductsTable">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 28%;">{{ __('product') }}</th>
                        <th style="width: 24%;">{{ __('manufacturer_origin') }}</th>
                        <th style="width: 20%;">{{ __('supply_chain') }}</th>
                        <th style="width: 28%;">{{ __('documents_and_certifications') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $suppliedProducts = [];
                        if (!empty($app->products) && $app->products->isNotEmpty()) {
                            foreach ($app->products as $p) {
                                $key = $p->erp_product_id ?: $p->id;
                                $suppliedProducts[$key] = is_array($p)
                                    ? $p
                                    : [
                                        'erp_product_id' => $p->erp_product_id,
                                        'product_name' => $p->product_name,
                                        'manufaktur' => $p->manufaktur,
                                        'negara' => $p->negara,
                                        'rantai_pasok' => $p->rantai_pasok,
                                        'file_surat_path' => $p->file_surat_path,
                                        'gmp_file_path' => $p->gmp_file_path,
                                        'has_tkdn' => $p->has_tkdn,
                                        'tkdn_file_path' => $p->tkdn_file_path,
                                        'has_sni' => $p->has_sni,
                                        'sni_file_path' => $p->sni_file_path,
                                        'has_halal' => $p->has_halal,
                                        'halal_file_path' => $p->halal_file_path,
                                        'has_bse_tse' => $p->has_bse_tse,
                                        'bse_tse_file_path' => $p->bse_tse_file_path,
                                    ];
                            }
                        } elseif (!empty($app->draft['products'])) {
                            $suppliedProducts = $app->draft['products'];
                        }
                    @endphp
                    @foreach ($suppliedProducts as $prodId => $prodData)
                        @include('admin.registrasi.steps.partials.product-card', [
                            'id' => $prodId,
                            'name' => $prodData['product_name'] ?? $prodId,
                            'data' => $prodData,
                            'isReadOnly' => true,
                            'application' => $app,
                            'revisionNotes' => [],
                        ])
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 8. PERSYARATAN KHUSUS (SPECIFIC REQUIREMENTS / KUESIONER TEKNIS SPESIFIK) --}}
@php
    $specificCategoryMap = [
        1 => [
            'key' => 'specific_baku',
            'title' => 'Bahan Baku, Bahan Kemas & Produk Jadi Farmasi/Alkes',
            'subtitle' => 'Raw Material, Packaging Material, Finished Drug Product & Medical Devices',
            'icon' => 'fas fa-pills',
        ],
        2 => [
            'key' => 'specific_varia',
            'title' => 'Varia Teknik, Umum, Reagen & Barang Investasi',
            'subtitle' => 'Technical & General Stores, Reagent, Investment Goods',
            'icon' => 'fas fa-tools',
        ],
        3 => [
            'key' => 'specific_trans',
            'title' => 'Jasa Transporter, Forwarder & PPJK',
            'subtitle' => 'Transporter, Freight Forwarder & Customs Clearance',
            'icon' => 'fas fa-shipping-fast',
        ],
        4 => [
            'key' => 'specific_kontraktor',
            'title' => 'Jasa Kontraktor, Perbaikan & Pemeliharaan',
            'subtitle' => 'Contractor, Maintenance & Repair Services',
            'icon' => 'fas fa-hard-hat',
        ],
        5 => [
            'key' => 'specific_pengujian',
            'title' => 'Jasa Pengujian, Kalibrasi, Radiasi & Sertifikasi',
            'subtitle' => 'Laboratory Testing, Calibration, Radiation & Certification',
            'icon' => 'fas fa-microscope',
        ],
        6 => [
            'key' => 'specific_facility',
            'title' => 'Jasa Facility Service, Sewa, Security, Katering & MCU',
            'subtitle' => 'Facility Service, Rental, Security, Catering & MCU',
            'icon' => 'fas fa-concierge-bell',
        ],
        7 => [
            'key' => 'specific_pelatihan',
            'title' => 'Jasa Pelatihan, Konsultan, Notaris & Alih Daya',
            'subtitle' => 'Training, Consultant, Notary & Outsourcing Services',
            'icon' => 'fas fa-chalkboard-teacher',
        ],
        8 => [
            'key' => 'specific_agency',
            'title' => 'Jasa Agency Advertising',
            'subtitle' => 'Advertising Agency',
            'icon' => 'fas fa-ad',
        ],
    ];

    $allVerifRows = app(\App\Services\ProcurementVerificationService::class)->verificationItemRows($app);
@endphp

@foreach ($selectedCategoryIds as $catId)
    @php
        $specConfig = $specificCategoryMap[$catId] ?? null;
        $specKey = $specConfig ? $specConfig['key'] : null;
        $specRows = $specKey ? $allVerifRows[$specKey] ?? [] : [];
        $catTitle = $specConfig
            ? $specConfig['title']
            : \App\Models\VendorApplication::CATEGORY_LABELS[$catId] ?? "Kategori {$catId}";
        $catSubtitle = $specConfig ? $specConfig['subtitle'] : 'Specific Requirements';
        $catIcon = $specConfig ? $specConfig['icon'] : 'fas fa-clipboard-list';
    @endphp
    @if (!empty($specRows))
        <div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:10px;">
            <div class="card-header border-bottom py-3 px-5 d-flex align-items-center">
                <div class="card-title m-0">
                    <span class="card-icon mr-2 text-primary">
                        <i class="{{ $catIcon }} text-primary"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                        Persyaratan Khusus: {{ $catTitle }} <span
                            class="text-muted font-weight-normal font-size-sm ml-1">({{ $catSubtitle }})</span>
                    </h5>
                </div>
            </div>
            <div class="card-body p-5">
                <table class="table table-sm table-borderless table-detail-keyval m-0" style="font-size:0.875rem;">
                    <tbody>
                        @foreach ($specRows as $row)
                            @if (($row['type'] ?? null) === 'section_title')
                                <tr class="bg-light">
                                    <td colspan="2"
                                        class="font-weight-bolder text-primary py-2 px-3 text-uppercase font-size-xs"
                                        style="letter-spacing: 0.5px;">
                                        <i class="fas fa-layer-group mr-1 text-primary"></i> {{ $row['label'] }}
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="text-muted font-weight-bold align-top" style="width: 38%;">
                                        {{ $row['label'] }}
                                    </td>
                                    <td>
                                        @if (!empty($row['url']))
                                            <button type="button"
                                                class="btn btn-xs btn-light-primary btn-preview-doc font-weight-bolder py-1 px-2"
                                                data-url="{{ $row['url'] }}"
                                                data-title="{{ $row['label'] ?? 'Dokumen' }}"
                                                title="Lihat {{ $row['label'] ?? 'Dokumen' }}"
                                                style="border-radius: 6px; font-size: 0.8rem; gap: 5px;">
                                                <i class="far fa-file-pdf"></i>
                                                <span>Lihat Dokumen</span>
                                            </button>
                                        @elseif (!empty($row['is_document']))
                                            <span class="text-muted">Tidak dilampirkan</span>
                                        @elseif (isset($row['value']) && ($row['value'] === 'Ya' || $row['value'] === 'ya' || $row['value'] === true))
                                            <span
                                                class="badge badge-light-success font-weight-bolder px-2.5 py-1">Ya</span>
                                        @elseif (isset($row['value']) && ($row['value'] === 'Tidak' || $row['value'] === 'tidak' || $row['value'] === false))
                                            <span
                                                class="badge badge-light-secondary font-weight-bolder px-2.5 py-1">Tidak</span>
                                        @elseif (!empty($row['value']) && $row['value'] !== '-')
                                            <span class="font-weight-bold text-dark">{!! nl2br(e($row['value'])) !!}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endforeach

@push('style')
    <style>
        #selectedProductsTable th {
            vertical-align: middle;
            font-size: 0.85rem;
            padding: 10px 12px;
        }

        #selectedProductsTable td {
            vertical-align: middle;
            position: relative;
            font-size: 0.85rem;
            padding: 10px 12px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            if ($('#selectedProductsTable').length && !$.fn.DataTable.isDataTable('#selectedProductsTable')) {
                $('#selectedProductsTable').DataTable({
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "{{ __('all') }}"]
                    ],
                    order: []
                });
            }
        });
    </script>
@endpush
