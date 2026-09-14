@php
    $isRekualifikasi = $application->type === \App\Models\VendorApplication::TYPE_REKUALIFIKASI;
@endphp

@extends('layouts.app', ['title' => $isRekualifikasi ? 'Tracking Rekualifikasi Vendor' : 'Tracking Permohonan'])

@section('breadcrumb', $isRekualifikasi ? 'Rekualifikasi' : 'Registrasi')
@section('step', $isRekualifikasi ? 'Evaluasi Ulang' : 'Pra Kualifikasi')
@section('page_title', $isRekualifikasi ? 'Tracking Rekualifikasi Vendor' : 'Tracking Permohonan')
@section('page_desc', $isRekualifikasi ? 'Pantau progres evaluasi ulang dan pembaruan data vendor secara transparan.' :
    'Pantau status permohonan vendor Anda secara ringkas dan aman.')

@section('content')
    @php
        $statusLabel = [
            'submitted' => ['text' => 'Dikirim', 'dot' => '#85B7EB'],
            'need_revision' => ['text' => 'Perlu Revisi', 'dot' => '#FCDE5A'],
            'verified' => ['text' => 'Terverifikasi', 'dot' => '#5DCAA5'],
            'approved' => ['text' => 'Disetujui', 'dot' => '#97C459'],
            'rejected' => ['text' => 'Ditolak', 'dot' => '#F09595'],
        ][$application->status] ?? [
            'text' => ucwords(str_replace('_', ' ', $application->status)),
            'dot' => '#B4B2A9',
        ];

        $dotClass = [
            'done' => 'tl-dot-done',
            'active' => 'tl-dot-active',
            'warning' => 'tl-dot-warning',
            'danger' => 'tl-dot-danger',
            'pending' => 'tl-dot-warning',
        ];

        $dotIcon = [
            'done' => 'fas fa-check text-white',
            'active' => 'fas fa-search text-white',
            'warning' => 'fas fa-exclamation-triangle text-white',
            'danger' => 'fas fa-times text-white',
            'pending' => 'far fa-clock text-white',
        ];
    @endphp

    <style>
        /* ── Header card ── */
        .trk-header {
            background: linear-gradient(135deg, #534AB7 0%, #3B8BD4 100%);
            border-radius: 20px;
            padding: 1.75rem;
            color: #fff;
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .trk-header::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
            pointer-events: none;
        }

        .trk-header::after {
            content: '';
            position: absolute;
            bottom: -60px;
            right: 40px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            pointer-events: none;
        }

        .trk-nomor-label {
            font-size: 11px;
            font-weight: 500;
            opacity: .75;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 4px;
        }

        .trk-nomor {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .trk-perusahaan {
            font-size: 14px;
            opacity: .85;
        }

        .trk-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 100px;
            padding: 5px 14px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 1rem;
        }

        .trk-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        .trk-meta {
            display: flex;
            gap: 1.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .trk-meta-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .trk-meta-label {
            font-size: 11px;
            opacity: .65;
        }

        .trk-meta-val {
            font-size: 13px;
            font-weight: 500;
        }

        /* ── Info cards ── */
        .trk-info-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 1.25rem;
        }

        @media (max-width: 576px) {
            .trk-info-row {
                grid-template-columns: 1fr;
            }
        }

        .trk-info-card {
            background: #fff;
            border: 0.5px solid rgba(0, 0, 0, 0.1);
            border-radius: 14px;
            padding: 14px;
        }

        .trk-info-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .trk-icon-blue {
            background: #E6F1FB;
            color: #185FA5;
        }

        .trk-icon-purple {
            background: #EEEDFE;
            color: #534AB7;
        }

        .trk-icon-teal {
            background: #E1F5EE;
            color: #0F6E56;
        }

        .trk-info-label {
            font-size: 11px;
            color: #888;
            margin-bottom: 3px;
        }

        .trk-info-val {
            font-size: 13px;
            font-weight: 600;
            color: #222;
        }

        /* ── Progress card ── */
        .trk-progress-card {
            background: #fff;
            border: 0.5px solid rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .trk-progress-header {
            padding: 1rem 1.25rem;
            border-bottom: 0.5px solid rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .trk-progress-header-icon {
            width: 28px;
            height: 28px;
            background: #EEEDFE;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #534AB7;
            font-size: 14px;
        }

        .trk-progress-title {
            font-size: 15px;
            font-weight: 600;
            color: #222;
        }

        .trk-progress-body {
            padding: 1.5rem 1.25rem;
        }

        /* ── Timeline ── */
        .trk-timeline {
            position: relative;
        }

        .trk-tl-line {
            position: absolute;
            left: 14px;
            top: 16px;
            bottom: 0;
            width: 2px;
            background: rgba(0, 0, 0, 0.07);
            z-index: 0;
        }

        .trk-tl-item {
            display: flex;
            gap: 14px;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .trk-tl-item:last-child {
            margin-bottom: 0;
        }

        .trk-dot {
            width: 30px;
            height: 30px;
            min-width: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            border: 2px solid transparent;
            position: relative;
            z-index: 1;
        }

        .tl-dot-done {
            background: #0F6E56;
            color: #fff;
            border-color: #0F6E56;
        }

        .tl-dot-active {
            background: #534AB7;
            color: #fff;
            border-color: #534AB7;
            box-shadow: 0 0 0 4px rgba(83, 74, 183, 0.15);
        }

        .tl-dot-warning {
            background: #BA7517;
            color: #fff;
            border-color: #BA7517;
        }

        .tl-dot-danger {
            background: #A32D2D;
            color: #fff;
            border-color: #A32D2D;
        }

        .tl-dot-pending {
            background: #f4f4f2;
            color: #888;
            border-color: #ccc;
        }

        .trk-tl-content {
            flex: 1;
            padding-top: 4px;
        }

        .trk-tl-title {
            font-size: 14px;
            font-weight: 600;
            color: #222;
            margin-bottom: 3px;
        }

        .trk-tl-desc {
            font-size: 12px;
            color: #888;
            margin-bottom: 3px;
        }

        .trk-tl-date {
            font-size: 11px;
            color: #aaa;
        }

        /* ── Revision alert ── */
        .trk-revision {
            background: #FAEEDA;
            border-left: 3px solid #EF9F27;
            border-radius: 0 12px 12px 0;
            padding: 14px 16px;
            margin-top: 1.25rem;
        }

        .trk-revision-title {
            font-size: 13px;
            font-weight: 600;
            color: #633806;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .trk-revision-note {
            font-size: 12px;
            color: #854F0B;
            margin-bottom: 6px;
        }

        .trk-revision-item {
            font-size: 12px;
            color: #854F0B;
            margin-top: 5px;
        }

        .trk-revision-field {
            font-weight: 600;
        }
    </style>

    {{-- Header card --}}
    <div class="trk-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div>
                <div class="trk-nomor-label">{{ $isRekualifikasi ? 'Nomor Permohonan Rekualifikasi' : 'Nomor Permohonan' }}
                </div>
                <div class="trk-nomor">{{ $applicationNumber ?: 'ID #' . $application->id }}</div>
                <div class="trk-perusahaan">{{ optional($application->general)->nama_perusahaan ?? '-' }}</div>
            </div>
            @if ($isRekualifikasi)
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-warning font-weight-bolder px-3 py-2"
                        style="border-radius: 8px; font-size: 0.8rem; color: #1e1e2d; background: #fff8e7; border: 1.5px solid #ffa800;">
                        <i class="fas fa-redo mr-1 text-warning"></i> REKUALIFIKASI VENDOR
                    </span>
                </div>
            @endif
        </div>

        <div class="trk-status-badge">
            <div class="trk-status-dot" style="background: {{ $statusLabel['dot'] }};"></div>
            {{ $statusLabel['text'] }}
        </div>

        <div class="trk-meta">
            <div class="trk-meta-item">
                <span class="trk-meta-label">Dikirim</span>
                <span class="trk-meta-val">{{ optional($application->submitted_at)->format('d/m/Y H:i') ?? '-' }}</span>
            </div>
            <div class="trk-meta-item">
                <span class="trk-meta-label">Update Terakhir</span>
                <span class="trk-meta-val">{{ optional($application->updated_at)->format('d/m/Y H:i') ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Banner Pemicu Rekualifikasi (jika bertipe Rekualifikasi) --}}
    @if ($isRekualifikasi && $application->requalification_reason)
        @php
            $reasonMap = [
                'vendor_initiative' => [
                    'label' => 'Inisiatif Vendor (Edit Profil)',
                    'icon' => 'fas fa-user-edit',
                    'desc' => 'Vendor melakukan inisiatif pembaruan data profil secara mandiri.',
                ],
                'expired_period' => [
                    'label' => 'Masa Berlaku Kadaluarsa (<= 60 Hari)',
                    'icon' => 'fas fa-calendar-times',
                    'desc' => 'Masa berlaku kualifikasi vendor mendekati batas waktu atau telah berakhir.',
                ],
                'cdob_expiry' => [
                    'label' => 'Sertifikat CDOB Expired',
                    'icon' => 'fas fa-certificate',
                    'desc' => 'Sertifikasi CDOB telah berakhir masa berlakunya dan memerlukan pembaruan dokumen.',
                ],
                'eval_score_drop' => [
                    'label' => 'Penurunan Skor Evaluasi',
                    'icon' => 'fas fa-chart-line',
                    'desc' => 'Terjadi penurunan skor evaluasi berkala sehingga diperlukan evaluasi ulang.',
                ],
                'qa_trigger' => [
                    'label' => 'Permintaan Pengadaan / QA',
                    'icon' => 'fas fa-shield-alt',
                    'desc' => 'Dipicu oleh tim Pengadaan atau QA Phapros untuk penyesuaian kualifikasi vendor.',
                ],
            ];
            $rInfo = $reasonMap[$application->requalification_reason] ?? [
                'label' => ucwords(str_replace('_', ' ', $application->requalification_reason)),
                'icon' => 'fas fa-info-circle',
                'desc' => 'Permohonan evaluasi ulang kualifikasi vendor.',
            ];
        @endphp
        <div class="card mb-5 border-0 shadow-xs"
            style="border-radius: 14px; background: #fff8e7; border-left: 4px solid #f6c000 !important;">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="btn btn-icon btn-light-warning btn-circle mr-4 flex-shrink-0"
                    style="width: 44px; height: 44px;">
                    <i class="{{ $rInfo['icon'] }} text-warning" style="font-size: 1.2rem;"></i>
                </div>
                <div>
                    <div class="font-size-xs text-uppercase font-weight-bolder text-warning letter-spacing-05">Pemicu /
                        Alasan Rekualifikasi</div>
                    <div class="font-size-h6 font-weight-bold text-dark mt-1">{{ $rInfo['label'] }}</div>
                    <div class="text-muted font-size-sm mt-1">{{ $rInfo['desc'] }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Info cards --}}
    <div class="trk-info-row">
        <div class="trk-info-card">
            <div class="trk-info-icon trk-icon-blue">
                <i class="fas fa-user"></i>
            </div>
            <div class="trk-info-label">Pemohon</div>
            <div class="trk-info-val">{{ optional($application->user)->name ?? auth()->user()->name }}</div>
        </div>
        <div class="trk-info-card">
            <div class="trk-info-icon trk-icon-purple">
                <i class="fas fa-tags"></i>
            </div>
            <div class="trk-info-label">Kategori</div>
            <div class="trk-info-val">
                {{ $categoryLabels->isNotEmpty() ? $categoryLabels->implode(', ') : '-' }}
            </div>
        </div>
        <div class="trk-info-card">
            <div class="trk-info-icon trk-icon-teal">
                <i class="{{ $isRekualifikasi ? 'fas fa-redo' : 'fas fa-building' }}"></i>
            </div>
            <div class="trk-info-label">Jenis Permohonan</div>
            <div class="trk-info-val">{{ $isRekualifikasi ? 'Rekualifikasi Vendor' : 'Pra Kualifikasi' }}</div>
        </div>
    </div>

    {{-- Progress card --}}
    <div class="trk-progress-card">
        <div class="trk-progress-header">
            <div class="trk-progress-header-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <span class="trk-progress-title">Progress Permohonan</span>
        </div>
        <div class="trk-progress-body">

            <div class="trk-timeline">
                <div class="trk-tl-line"></div>

                @foreach ($statusSteps as $step)
                    @php
                        $state = $step['state'] ?? 'pending';
                        $dotCls = $dotClass[$state] ?? 'tl-dot-pending';
                        $iconCls = $dotIcon[$state] ?? 'far fa-clock';
                    @endphp
                    <div class="trk-tl-item">
                        <div class="trk-dot {{ $dotCls }}">
                            <i class="{{ $iconCls }}"></i>
                        </div>
                        <div class="trk-tl-content">
                            <div class="trk-tl-title">{{ $step['title'] }}</div>
                            @if (!empty($step['description']))
                                <div class="trk-tl-desc">{{ $step['description'] }}</div>
                            @endif
                            <div class="trk-tl-date">
                                {{ optional($step['date'])->format('d/m/Y H:i') ?? '-' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Revision note --}}
            @if (
                $application->status === \App\Models\VendorApplication::STATUS_NEED_REVISION &&
                    ($application->admin_note || !empty($application->revision_notes)))
                <div class="trk-revision">
                    <div class="trk-revision-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Catatan Revisi
                    </div>
                    @if ($application->admin_note)
                        <div class="trk-revision-note">{{ $application->admin_note }}</div>
                    @endif
                    @if (!empty($application->revision_notes))
                        @foreach ($application->revision_notes as $revision)
                            <div class="trk-revision-item">
                                <span class="trk-revision-field">{{ $revision['field'] ?? 'Umum' }}:</span>
                                {{ $revision['note'] ?? '-' }}
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif

            {{-- Audit action --}}
            @php
                $activeAudit = $application
                    ->audits()
                    ->whereNotIn('status', [
                        \App\Models\VendorAudit::STATUS_COMPLETED,
                        \App\Models\VendorAudit::STATUS_REJECTED,
                    ])
                    ->latest('id')
                    ->first();
                $latestAudit = $application->audits()->latest('id')->first();
            @endphp
            @if ($application->status === \App\Models\VendorApplication::STATUS_AUDIT_REQUIRED && $activeAudit)
                <div class="trk-revision mt-5" style="background:#EEEDFE;border-color:#534AB7;">
                    <div class="trk-revision-title" style="color:#534AB7;">
                        <i class="fas fa-clipboard-check"></i>
                        {{ $activeAudit->audit_type === 'on_desk' ? 'Audit On Desk — Questionnaire' : 'Audit On Site — Koordinasi Offline' }}
                    </div>
                    <div class="trk-revision-note">
                        Permohonan Anda memerlukan audit
                        <strong>{{ $activeAudit->audit_type === 'on_desk' ? 'On Desk (questionnaire)' : 'On Site' }}</strong>.
                        @if ($activeAudit->audit_type === 'on_desk')
                            Silakan mengisi questionnaire dari Quality Assurance. Anda dapat menyimpan draft dan melanjutkan
                            nanti.
                        @else
                            Proses audit on-site akan dikoordinasikan secara langsung oleh tim QA Phapros dengan perusahaan
                            Anda.
                        @endif
                    </div>
                    <div class="mt-3">
                        @if ($activeAudit->audit_type === 'on_desk')
                            <a href="{{ route('vendor.audit.questionnaire', $activeAudit->id) }}"
                                class="btn btn-primary font-weight-bold">
                                <i class="fas fa-pen"></i> Isi Questionnaire
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Hasil Audit Notice --}}
            @if (
                $latestAudit &&
                    ($latestAudit->audit_result_path ||
                        in_array($latestAudit->status, [
                            \App\Models\VendorAudit::STATUS_COMPLETED,
                            \App\Models\VendorAudit::STATUS_REJECTED,
                        ])))
                <div class="trk-revision mt-5" style="background:#E8F5E9;border-color:#2E7D32;">
                    <div class="trk-revision-title" style="color:#2E7D32;">
                        <i class="fas fa-file-invoice"></i>
                        Hasil Audit Tersedia
                    </div>
                    <div class="trk-revision-note" style="color:#1B5E20; font-size:12px;">
                        Hasil evaluasi audit permohonan Anda telah dirilis. Silakan buka halaman <strong>Hasil
                            Audit</strong> untuk melihat dokumen dan detail rekomendasi resmi.
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('vendor.audit.results') }}" class="btn btn-sm btn-success font-weight-bold">
                            <i class="fas fa-external-link-alt"></i> Buka Halaman Hasil Audit
                        </a>
                    </div>
                </div>
            @endif

            <div class="mt-8 d-flex align-items-center flex-wrap" style="gap: 8px;">
                @if (auth()->user()->role === 'supplier')
                    <a href="{{ route('registrasi.index') }}" class="btn btn-light-primary font-weight-bold">
                        @if ($application->status === \App\Models\VendorApplication::STATUS_NEED_REVISION)
                            <i class="fas fa-edit mr-1"></i> Perbaiki Form
                        @else
                            <i class="fas fa-eye mr-1"></i> Lihat Form
                        @endif
                    </a>
                @else
                    {{-- Internal Staff Navigation --}}
                    @if ($isRekualifikasi)
                        <a href="{{ route('rekualifikasi.index') }}" class="btn btn-light-danger font-weight-bold">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Rekualifikasi
                        </a>
                    @endif
                    @canany(['verifikasi-list', 'verifikasi-detail'])
                        <a href="{{ route('verifikasi.show', $application->id) }}" class="btn btn-primary font-weight-bold">
                            <i class="fas fa-clipboard-check mr-1"></i> Verifikasi Berkas
                        </a>
                    @endcanany
                @endif
                <a href="{{ route('dashboard') }}" class="btn btn-light-primary font-weight-bold">
                    <i class="fas fa-tachometer-alt mr-1"></i> Ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <x-document-preview />

@endsection
