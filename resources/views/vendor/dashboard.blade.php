@extends('layouts.app', ['title' => 'Dashboard Vendor'])

@push('style')
<style>
    /* ── Stepper Styles ── */
    .vnd-stepper {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 1.5rem 0 0.5rem;
    }
    .vnd-stepper::before {
        content: '';
        position: absolute;
        top: 22px;
        left: 5%;
        right: 5%;
        height: 3px;
        background: #e2e8f0;
        z-index: 1;
    }
    .vnd-step-item {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
        padding: 0 0.5rem;
    }
    .vnd-step-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        margin: 0 auto 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .vnd-step-circle--completed {
        background: #10b981;
        color: #ffffff;
        border: 3px solid #d1fae5;
    }
    .vnd-step-circle--current {
        background: #005db6;
        color: #ffffff;
        border: 3px solid #c2d9f5;
        box-shadow: 0 0 0 4px rgba(0, 93, 182, 0.15);
    }
    .vnd-step-circle--danger {
        background: #ef4444;
        color: #ffffff;
        border: 3px solid #fee2e2;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
    }
    .vnd-step-circle--pending {
        background: #f1f5f9;
        color: #94a3b8;
        border: 2px solid #e2e8f0;
    }
    .vnd-step-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.2rem;
    }
    .vnd-step-desc {
        font-size: 0.73rem;
        color: #64748b;
        line-height: 1.25;
    }
    .vnd-step-date {
        font-size: 0.7rem;
        color: #005db6;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    /* ── Banner Notification Styles ── */
    .vnd-alert-banner {
        border-radius: 12px;
        padding: 1.2rem 1.4rem;
        border-left: 5px solid;
        display: flex;
        align-items: flex-start;
        gap: 1.1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .vnd-alert-banner--danger {
        background-color: #fef2f2;
        border-color: #ef4444;
        color: #991b1b;
    }
    .vnd-alert-banner--warning {
        background-color: #fffbeb;
        border-color: #f59e0b;
        color: #92400e;
    }
    .vnd-alert-banner--primary {
        background-color: #eff6ff;
        border-color: #005db6;
        color: #1e40af;
    }
    .vnd-alert-banner--success {
        background-color: #f0fdf4;
        border-color: #10b981;
        color: #065f46;
    }

    /* ── Metric Bar Styles ── */
    .metric-row {
        margin-bottom: 0.85rem;
    }
    .metric-row:last-child {
        margin-bottom: 0;
    }
    .metric-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.35rem;
    }

    /* ── Category Pill Styling ── */
    .vnd-category-pill {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        color: #0369a1;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.35rem 0.85rem;
        border-radius: 50rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        margin-right: 0.5rem;
        margin-bottom: 0.35rem;
    }
    .vnd-category-pill:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        color: #0284c7;
    }
    .vnd-category-pill i {
        font-size: 0.72rem;
        margin-right: 0.4rem;
        color: #0284c7;
    }

    @media (max-width: 767.98px) {
        .vnd-stepper {
            flex-direction: column;
            gap: 1.25rem;
        }
        .vnd-stepper::before {
            display: none;
        }
        .vnd-step-item {
            display: flex;
            align-items: center;
            text-align: left;
            gap: 1rem;
        }
        .vnd-step-circle {
            margin: 0;
            flex-shrink: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    {{-- ═══════════════════════════════════════════════════════════════════
         1. WELCOME CARD
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="card welcome-card p-6 py-7 shadow-sm mb-6 position-relative overflow-hidden">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div class="mb-3 mb-md-0">
                <h2 class="mb-1 text-white font-weight-bolder">
                    {{ __('welcome') }}, <b>{{ $companyName }}!</b>
                </h2>
                <p class="mb-0 text-white-75 small">
                    <i class="far fa-calendar-alt mr-2 text-white-50"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>

                @if($categories->isNotEmpty())
                    <div class="d-flex align-items-center flex-wrap mt-3">
                        <span class="text-white-75 small font-weight-bold mr-2 mb-1">
                            <i class="fas fa-tags mr-1 text-white-50"></i> Kategori:
                        </span>
                        @foreach($categories as $cat)
                            <span class="vnd-category-pill">
                                <i class="fas fa-tag"></i> {{ $cat }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="text-md-right my-auto">
                <span class="label label-xl {{ $statusInfo['badge_class'] }} label-inline font-weight-bolder py-4 px-5 text-uppercase shadow-xs">
                    @if($status === \App\Models\VendorApplication::STATUS_APPROVED)
                        <i class="fas fa-check-circle mr-2 text-success"></i>
                    @elseif($status === \App\Models\VendorApplication::STATUS_NEED_REVISION)
                        <i class="fas fa-exclamation-circle mr-2 text-danger"></i>
                    @elseif($status === \App\Models\VendorApplication::STATUS_SUBMITTED)
                        <i class="fas fa-hourglass-half mr-2 text-primary"></i>
                    @else
                        <i class="fas fa-info-circle mr-2"></i>
                    @endif
                    {{ $statusInfo['label'] }}
                </span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         2. NOTIFICATION BANNER (Dynamic based on Status & Action Required)
         ═══════════════════════════════════════════════════════════════════ --}}
    @if($status === \App\Models\VendorApplication::STATUS_NEED_REVISION)
        {{-- Banner: Perlu Revisi Dokumen --}}
        <div class="vnd-alert-banner vnd-alert-banner--danger">
            <div class="mt-1">
                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bolder mb-1 text-danger">
                    Permohonan Memerlukan Perbaikan / Revisi Dokumen
                </h5>
                <p class="mb-2 small" style="color: #7f1d1d;">
                    Tim Verifikator Pengadaan telah memeriksa berkas Anda dan meminta perbaikan data berikut sebelum permohonan dapat dilanjutkan:
                </p>

                @if(!empty($revisionNotes))
                    <div class="bg-white rounded p-3 mb-3 border border-danger-light" style="border: 1px solid #fecaca;">
                        <ul class="mb-0 pl-4 small text-dark">
                            @foreach($revisionNotes as $rev)
                                <li class="mb-1">
                                    <strong class="text-danger">{{ $rev['field'] }}:</strong> {{ $rev['note'] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <a href="{{ route('registrasi.index') }}" class="btn btn-danger btn-sm font-weight-bolder px-4 shadow-sm">
                    <i class="fas fa-edit mr-1"></i> Buka Formulir & Perbaiki Dokumen
                </a>
            </div>
        </div>

    @elseif($activeAudit && $activeAudit->can_fill_questionnaire)
        {{-- Banner: Kuesioner Audit Perlu Diisi --}}
        <div class="vnd-alert-banner vnd-alert-banner--primary">
            <div class="mt-1">
                <i class="fas fa-clipboard-list fa-2x text-primary"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bolder mb-1 text-primary">
                    Tugas Audit: Kuesioner Audit On-Desk Perlu Diisi
                </h5>
                <p class="mb-3 small" style="color: #1e3a8a;">
                    Tim Quality Assurance PT Phapros Tbk telah menjadwalkan audit On-Desk untuk kualifikasi produk/jasa perusahaan Anda. Silakan isi dan submit form kuesioner audit yang tersedia.
                </p>
                <a href="{{ route('vendor.audit.questionnaire', $activeAudit->id) }}" class="btn btn-primary btn-sm font-weight-bolder px-4 shadow-sm">
                    <i class="fas fa-pen mr-1"></i> Isi Kuesioner Audit Sekarang
                </a>
            </div>
        </div>

    @elseif($status === \App\Models\VendorApplication::STATUS_APPROVED && $validityStatus === 'expiring_soon')
        {{-- Banner: Masa Berlaku Kemitraan Mendekati Kadaluarsa --}}
        <div class="vnd-alert-banner vnd-alert-banner--warning">
            <div class="mt-1">
                <i class="fas fa-hourglass-end fa-2x text-warning"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bolder mb-1 text-warning">
                    Peringatan: Masa Berlaku Kemitraan Tersisa {{ $daysUntilExpiry }} Hari
                </h5>
                <p class="mb-3 small" style="color: #78350f;">
                    Status rekanan Anda akan berakhir pada <b>{{ $validUntilDisplay }}</b>. Anda dapat melakukan pembaruan profil atau mengajukan proses rekualifikasi vendor agar status kemitraan tetap aktif.
                </p>
                <form action="{{ route('rekualifikasi.initiate') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm font-weight-bolder px-4 shadow-sm">
                        <i class="fas fa-sync-alt mr-1"></i> Ajukan Pembaruan Data / Rekualifikasi
                    </button>
                </form>
            </div>
        </div>

    @elseif($status === \App\Models\VendorApplication::STATUS_SUBMITTED)
        {{-- Banner: Menunggu Verifikasi --}}
        <div class="vnd-alert-banner vnd-alert-banner--primary">
            <div class="mt-1">
                <i class="fas fa-info-circle fa-2x text-primary"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bolder mb-1 text-primary">
                    Permohonan Sedang Dalam Antrean Verifikasi
                </h5>
                <p class="mb-0 small" style="color: #1e3a8a;">
                    Berkas dan data pendaftaran Anda telah berhasil diserahkan pada {{ $application->submitted_at ? $application->submitted_at->format('d M Y H:i') : '' }}. Tim Pengadaan PT Phapros Tbk sedang melakukan verifikasi kelengkapan dokumen. Anda akan menerima notifikasi jika proses verifikasi selesai.
                </p>
            </div>
        </div>

    @elseif(!$application || $status === \App\Models\VendorApplication::STATUS_DRAFT)
        {{-- Banner: Draf Formulir Belum Dikirim --}}
        <div class="vnd-alert-banner vnd-alert-banner--warning">
            <div class="mt-1">
                <i class="fas fa-clipboard-check fa-2x text-warning"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bolder mb-1 text-warning">
                    Lengkapi Pendaftaran Rekanan Anda
                </h5>
                <p class="mb-3 small" style="color: #78350f;">
                    Formulir pendaftaran rekanan belum diserahkan. Silakan lengkapi data legalitas, sertifikasi, dan profil perusahaan Anda untuk dapat bermitra dengan PT Phapros Tbk.
                </p>
                <a href="{{ route('registrasi.index') }}" class="btn btn-warning btn-sm font-weight-bolder px-4 shadow-sm">
                    <i class="fas fa-arrow-right mr-1"></i> Lanjutkan Pengisian Formulir
                </a>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════
         3. STEPPER TAHAPAN KUALIFIKASI VENDOR
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="vnd-card mb-6">
        <div class="vnd-card-head">
            <div class="vnd-card-title">
                <span class="vnd-card-title-dot"></span>
                <i class="fas fa-route text-primary mr-1"></i>
                Tahapan Kualifikasi & Status Kemitraan
            </div>
            <div>
                <a href="{{ route('registrasi.index') }}" class="btn btn-sm btn-light-primary font-weight-bolder">
                    <i class="fas fa-external-link-alt mr-1"></i> Detail Formulir
                </a>
            </div>
        </div>
        <div class="card-body py-6 px-4 px-md-8">
            <div class="vnd-stepper">
                @foreach($pipelineSteps as $step)
                    <div class="vnd-step-item">
                        <div class="vnd-step-circle vnd-step-circle--{{ $step['status'] }}">
                            @if($step['status'] === 'completed')
                                <i class="fas fa-check text-white"></i>
                            @elseif($step['status'] === 'danger')
                                <i class="fas fa-exclamation text-white"></i>
                            @elseif($step['status'] === 'current')
                                <i class="{{ $step['icon'] }} text-white"></i>
                            @else
                                <span>{{ $step['step'] }}</span>
                            @endif
                        </div>
                        <div class="vnd-step-title">{{ $step['title'] }}</div>
                        <div class="vnd-step-desc">{{ $step['desc'] }}</div>
                        @if(!empty($step['date']))
                            <div class="vnd-step-date">
                                <i class="far fa-calendar-check mr-1"></i> {{ $step['date'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-5 p-3 rounded bg-light border text-muted small d-flex align-items-center">
                <i class="fas fa-info-circle text-primary mr-2 fa-lg"></i>
                <div>
                    <b>Keterangan Status Saat Ini:</b> {{ $statusInfo['desc'] }}
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         4. KONTEN DUA KOLOM (Dokumen Legalitas & Riwayat Audit / Evaluasi)
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="row">
        {{-- KOLOM KIRI: Monitoring Dokumen & Profil --}}
        <div class="col-xl-7 col-lg-12 mb-4">
            {{-- Card Monitoring Dokumen --}}
            <div class="vnd-card mb-4">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        <i class="fas fa-file-contract text-primary mr-1"></i>
                        Dokumen Legalitas & Masa Berlaku
                    </div>
                    <div>
                        <span class="badge badge-light-primary font-weight-bolder">
                            {{ count($monitoredDocuments) }} Dokumen
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table tbl-vendor table-borderless table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Dokumen</th>
                                    <th>Tgl. Terbit</th>
                                    <th>Masa Berlaku</th>
                                    <th>Status</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monitoredDocuments as $doc)
                                    <tr>
                                        <td>
                                            <div class="vnd-vendor-name">{{ $doc->name }}</div>
                                            <div class="vnd-vendor-email">{{ $doc->doc_type }}</div>
                                        </td>
                                        <td>
                                            <span class="vnd-cell-muted">{{ $doc->issue_date }}</span>
                                        </td>
                                        <td>
                                            @if($doc->expiry_date !== '-')
                                                <span class="font-weight-bolder text-dark">{{ $doc->expiry_date }}</span>
                                                @if($doc->days_left !== null)
                                                    <span class="small d-block {{ $doc->days_left < 0 ? 'text-danger' : ($doc->days_left <= 60 ? 'text-warning' : 'text-muted') }}">
                                                        {{ $doc->days_left < 0 ? 'Expired' : ($doc->days_left . ' hari lagi') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="vnd-cell-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($doc->status === 'valid')
                                                <span class="vnd-tag vnd-tag--verified">VALID</span>
                                            @elseif($doc->status === 'expiring_soon')
                                                <span class="vnd-tag vnd-tag--medium">EXPIRING SOON</span>
                                            @elseif($doc->status === 'expired')
                                                <span class="vnd-tag vnd-tag--high">EXPIRED</span>
                                            @elseif($doc->status === 'unuploaded')
                                                <span class="vnd-tag vnd-tag--muted">BELUM DIUNGGAH</span>
                                            @else
                                                <span class="vnd-tag vnd-tag--muted">TERUNGGAH</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if($doc->file_url)
                                                <x-preview-doc-button
                                                    :url="$doc->file_url"
                                                    :title="$doc->name"
                                                    compact="true"
                                                />
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="vnd-empty py-5">
                                                <div class="vnd-empty-icon"><i class="fas fa-folder-open"></i></div>
                                                <div class="vnd-empty-title">Belum Ada Dokumen Terunggah</div>
                                                <div class="vnd-empty-sub">Dokumen legalitas akan muncul setelah Anda mengunggahnya pada formulir pendaftaran.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Card Detail Profil Perusahaan --}}
            <div class="vnd-card mb-4">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        <i class="fas fa-building text-primary mr-1"></i>
                        Profil Singkat Perusahaan
                    </div>
                    <a href="{{ route('registrasi.index') }}" class="vnd-btn-detail vnd-btn-detail--secondary">
                        Lihat Profil Lengkap <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-5">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">Nama Perusahaan Resmi</label>
                            <div class="font-weight-bolder text-dark">{{ $companyName }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">Bentuk Badan Usaha</label>
                            <div class="font-weight-bolder text-dark">{{ ($general && $general->badan_usaha) ? $general->badan_usaha : '—' }}</div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small mb-1">Alamat Kantor / Pabrik</label>
                            <div class="font-weight-normal text-dark">{{ ($general && $general->alamat_perusahaan) ? $general->alamat_perusahaan : '—' }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">Email Perusahaan</label>
                            <div class="font-weight-normal text-dark">{{ ($general && $general->email_perusahaan) ? $general->email_perusahaan : $user->email }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">Telepon Kantor</label>
                            <div class="font-weight-normal text-dark">{{ ($general && $general->telepon_perusahaan) ? $general->telepon_perusahaan : '—' }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">Person in Charge (PIC)</label>
                            <div class="font-weight-bolder text-dark">
                                {{ ($general && $general->pic_nama) ? $general->pic_nama : '—' }}
                                @if($general && $general->pic_jabatan)
                                    <span class="text-muted font-weight-normal small">({{ $general->pic_jabatan }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small mb-1">Kontak PIC</label>
                            <div class="font-weight-normal text-dark">
                                {{ ($general && $general->pic_telepon) ? $general->pic_telepon : '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Riwayat Audit, Evaluasi & Support --}}
        <div class="col-xl-5 col-lg-12 mb-4">
            {{-- Card Riwayat Audit --}}
            <div class="vnd-card mb-4">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        <i class="fas fa-clipboard-check text-primary mr-1"></i>
                        Riwayat & Jadwal Audit
                    </div>
                    @if($audits->isNotEmpty())
                        <a href="{{ route('vendor.audit.results') }}" class="vnd-btn-detail vnd-btn-detail--secondary">
                            Semua Hasil <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @forelse($audits as $audit)
                        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
                            <div>
                                <div class="font-weight-bolder text-dark">
                                    Audit {{ $audit->audit_type_label }}
                                    <span class="badge {{ $audit->status_cls }} ml-2">{{ $audit->status_label }}</span>
                                </div>
                                <div class="text-muted small mt-1">
                                    <i class="far fa-calendar-alt mr-1"></i> {{ $audit->schedule_date }}
                                </div>
                            </div>
                            <div>
                                @if($audit->can_fill_questionnaire)
                                    <a href="{{ route('vendor.audit.questionnaire', $audit->id) }}" class="btn btn-xs btn-primary font-weight-bold">
                                        Isi Kuesioner
                                    </a>
                                @else
                                    <a href="{{ route('vendor.audit.results') }}" class="btn btn-xs btn-light font-weight-bold">
                                        Lihat
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="vnd-empty py-5">
                            <div class="vnd-empty-icon"><i class="fas fa-calendar-times"></i></div>
                            <div class="vnd-empty-title">Belum Ada Riwayat Audit</div>
                            <div class="vnd-empty-sub">Audit vendor (On-Desk / On-Site) akan dijadwalkan oleh Tim QA sesuai kategori & profil risiko material.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Card Evaluasi Kinerja Vendor --}}
            <div class="vnd-card mb-4">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        <i class="fas fa-award text-primary mr-1"></i>
                        Evaluasi Kinerja Vendor
                    </div>
                    @if($latestEvaluation)
                        <a href="{{ route('vendor.evaluasi.index') }}" class="vnd-btn-detail vnd-btn-detail--secondary">
                            Rincian <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @endif
                </div>
                <div class="card-body p-5">
                    @if($latestEvaluation)
                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                            <div>
                                <div class="text-muted small">Penilaian Tahun</div>
                                <div class="font-weight-bolder text-dark h4 mb-0">Tahun {{ $latestEvaluation->year }}</div>
                            </div>
                            <div class="text-right">
                                <span class="badge {{ $latestEvaluation->category === 'BAIK' ? 'badge-success' : ($latestEvaluation->category === 'CUKUP' ? 'badge-warning' : 'badge-danger') }} font-weight-bolder px-3 py-2 h6 mb-0">
                                    {{ $latestEvaluation->category }} ({{ number_format($latestEvaluation->final_score, 1) }})
                                </span>
                            </div>
                        </div>

                        <div class="metric-row">
                            <div class="metric-label">
                                <span>Ketepatan Pengiriman (Delivery)</span>
                                <b>{{ number_format($latestEvaluation->delivery_score_avg, 1) }}</b>
                            </div>
                            <div class="vnd-progress-track">
                                <div class="vnd-progress-fill" style="width: {{ min(100, $latestEvaluation->delivery_score_avg) }}%;"></div>
                            </div>
                        </div>

                        <div class="metric-row">
                            <div class="metric-label">
                                <span>Kualitas Produk / Mutu (Quality)</span>
                                <b>{{ number_format($latestEvaluation->quality_score_avg, 1) }}</b>
                            </div>
                            <div class="vnd-progress-track">
                                <div class="vnd-progress-fill" style="width: {{ min(100, $latestEvaluation->quality_score_avg) }}%;"></div>
                            </div>
                        </div>

                        <div class="metric-row">
                            <div class="metric-label">
                                <span>Kuantitas Barang (Quantity)</span>
                                <b>{{ number_format($latestEvaluation->quantity_score_avg, 1) }}</b>
                            </div>
                            <div class="vnd-progress-track">
                                <div class="vnd-progress-fill" style="width: {{ min(100, $latestEvaluation->quantity_score_avg) }}%;"></div>
                            </div>
                        </div>

                        <div class="metric-row">
                            <div class="metric-label">
                                <span>Penanganan Komplain</span>
                                <b>{{ number_format($latestEvaluation->complain_score_avg, 1) }}</b>
                            </div>
                            <div class="vnd-progress-track">
                                <div class="vnd-progress-fill" style="width: {{ min(100, $latestEvaluation->complain_score_avg) }}%;"></div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-right">
                            <a href="{{ route('vendor.evaluasi.index', ['year' => $latestEvaluation->year]) }}" class="btn btn-sm btn-light-primary font-weight-bold">
                                <i class="fas fa-file-alt mr-1"></i> Buka Lembar Evaluasi Lengkap
                            </a>
                        </div>
                    @else
                        <div class="vnd-empty py-4">
                            <div class="vnd-empty-icon"><i class="fas fa-chart-line"></i></div>
                            <div class="vnd-empty-title">Belum Ada Laporan Evaluasi</div>
                            <div class="vnd-empty-sub">Evaluasi kinerja diterbitkan secara tahunan untuk vendor terdaftar yang telah memiliki transaksi pemesanan (PO).</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card Bantuan & Panduan Rekanan --}}
            <div class="vnd-card">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        <i class="fas fa-question-circle text-primary mr-1"></i>
                        Bantuan & Kontak Pengadaan
                    </div>
                </div>
                <div class="card-body p-5">
                    <p class="text-muted small mb-3">
                        Membutuhkan bantuan teknis atau pertanyaan seputar proses pendaftaran rekanan PT Phapros Tbk?
                    </p>
                    <div class="d-flex align-items-center mb-3">
                        <div class="btn btn-icon btn-light-primary btn-sm mr-3">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Email Pengadaan</div>
                            <a href="mailto:pengadaan@phapros.co.id" class="font-weight-bolder text-dark">pengadaan@phapros.co.id</a>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="btn btn-icon btn-light-success btn-sm mr-3">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Telepon Kantor Pusat</div>
                            <span class="font-weight-bolder text-dark">(024) 7604616 (Ext. Pengadaan)</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('tutorial') }}" class="btn btn-sm btn-light font-weight-bold flex-grow-1 mr-2">
                            <i class="fas fa-book-open mr-1"></i> Panduan
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-sm btn-light font-weight-bold flex-grow-1">
                            <i class="fas fa-headset mr-1"></i> Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Preview Dokumen Reusable --}}
<x-document-preview />
@endsection
