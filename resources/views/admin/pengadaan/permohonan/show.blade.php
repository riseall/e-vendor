@extends('layouts.app', ['title' => 'Detail Verifikasi'])

@section('breadcrumb', 'Pengadaan')
@section('step', 'Verifikasi')
@section('page_title', 'Verifikasi Data Calon Penyedia')
@section('page_desc', 'Verifikasi setiap bagian data vendor melalui tab berikut sebelum permohonan diteruskan.')

@push('style')
    <style>
        /* ── Design Tokens ──────────────────────────────────────── */
        :root {
            --brand-primary: #4f46e5;
            /* indigo */
            --brand-primary-light: #eef2ff;
            --brand-accent: #06b6d4;
            /* cyan accent */
            --brand-success: #10b981;
            --brand-success-light: #d1fae5;
            --brand-danger: #ef4444;
            --brand-danger-light: #fee2e2;
            --brand-warning: #f59e0b;
            --brand-warning-light: #fef3c7;
            --surface-0: #ffffff;
            --surface-1: #f8fafc;
            --surface-2: #f1f5f9;
            --border-light: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
            --shadow-md: 0 4px 16px rgba(15, 23, 42, .08), 0 1px 4px rgba(15, 23, 42, .04);
            --shadow-focus: 0 0 0 3px rgba(79, 70, 229, .18);
        }

        /* ── Header Card ─────────────────────────────────────────── */
        .verif-header-card {
            background: var(--surface-0);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: none;
            overflow: hidden;
        }

        .verif-header-card::before {
            content: '';
            display: block;
            height: 4px;
            background: linear-gradient(90deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
        }

        /* ── Stat Boxes ──────────────────────────────────────────── */
        .stat-box {
            background: var(--surface-1);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: .65rem 1.1rem;
            text-align: center;
            min-width: 110px;
            transition: box-shadow .15s ease, transform .15s ease;
        }

        .stat-box:hover {
            box-shadow: var(--shadow-sm);
            transform: translateY(-1px);
        }

        .stat-box .stat-label {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .55px;
            color: var(--text-muted);
            margin-bottom: .3rem;
        }

        .stat-box .stat-value {
            font-size: .9rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .stat-box .stat-sub {
            font-size: .7rem;
            color: var(--text-muted);
            margin-top: .15rem;
        }

        /* ── Summary Pills ───────────────────────────────────────── */
        .verif-summary-row {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            padding: 1rem 1.5rem 1.25rem;
            border-top: 1px solid var(--border-light);
        }

        .verif-pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .3rem .85rem;
            border-radius: 99px;
            font-size: .76rem;
            font-weight: 700;
        }

        .verif-pill.pending {
            background: var(--brand-primary-light);
            color: var(--brand-primary);
        }

        .verif-pill.approved {
            background: var(--brand-success-light);
            color: #059669;
        }

        .verif-pill.rejected {
            background: var(--brand-danger-light);
            color: #dc2626;
        }

        /* ── Progress Bar ────────────────────────────────────────── */
        .verif-progress-wrap {
            background: var(--surface-2);
            border-radius: 99px;
            height: 7px;
            overflow: hidden;
        }

        .verif-progress-bar {
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, var(--brand-primary), var(--brand-accent));
            transition: width .5s cubic-bezier(.4, 0, .2, 1);
        }

        .verif-progress-bar.has-rejected {
            background: linear-gradient(90deg, var(--brand-danger), #f97316);
        }

        /* ── Tab Nav ─────────────────────────────────────────────── */
        .tab-nav-wrap {
            position: relative;
        }

        .tab-nav-wrap::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 48px;
            height: 100%;
            background: linear-gradient(to right, transparent, #fff);
            pointer-events: none;
            z-index: 1;
        }

        .verif-tabs.nav-tabs {
            border-bottom: 2px solid var(--border-light);
            scrollbar-width: none;
            gap: .15rem;
        }

        .verif-tabs.nav-tabs::-webkit-scrollbar {
            display: none;
        }

        .verif-tabs .nav-link {
            font-size: .82rem;
            font-weight: 600;
            color: var(--text-secondary);
            padding: .8rem 1rem;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            border-radius: var(--radius-sm) var(--radius-sm) 0 0;
            white-space: nowrap;
            transition: color .15s, border-color .15s, background .15s;
        }

        .verif-tabs .nav-link:hover {
            color: var(--brand-primary);
            background: var(--brand-primary-light);
        }

        .reject-field-list {
            max-height: 190px;
            overflow: auto;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            padding: .5rem;
            margin-bottom: 1rem;
            background: #fff;
        }

        .reject-field-option {
            display: flex;
            align-items: flex-start;
            gap: .65rem;
            padding: .55rem .6rem;
            margin: 0;
            border-radius: var(--radius-sm);
            cursor: pointer;
            line-height: 1.35;
        }

        .reject-field-option:hover {
            background: var(--surface-1);
        }

        .reject-field-option input {
            flex: 0 0 auto;
            width: 16px;
            height: 16px;
            margin-top: .1rem;
        }

        .reject-field-option span {
            min-width: 0;
            color: var(--text-primary);
            font-size: .82rem;
            font-weight: 700;
            word-break: break-word;
        }

        .verif-tabs .nav-link.active {
            color: var(--brand-primary);
            border-bottom-color: var(--brand-primary);
            background: transparent;
        }

        .verif-tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 99px;
            font-size: .62rem;
            font-weight: 700;
            margin-left: .4rem;
        }

        .verif-tab-badge.is-pending {
            background: var(--brand-primary);
            color: #fff;
        }

        .verif-tab-badge.is-rejected {
            background: var(--brand-danger);
            color: #fff;
        }

        .verif-tab-badge.is-done {
            background: var(--brand-success);
            color: #fff;
        }

        /* ── Verification Panel ──────────────────────────────────── */
        .verification-panel {
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            overflow: hidden;
            background: var(--surface-0);
            box-shadow: var(--shadow-sm);
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .verification-panel:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .verification-panel.is-approved {
            border-color: #a7f3d0;
            box-shadow: 0 0 0 1px #a7f3d0, var(--shadow-sm);
        }

        .verification-panel.is-rejected {
            border-color: #fca5a5;
            box-shadow: 0 0 0 1px #fca5a5, var(--shadow-sm);
        }

        /* ── Panel Head ──────────────────────────────────────────── */
        .verification-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background: var(--surface-1);
            border-bottom: 1px solid var(--border-light);
        }

        .verification-panel.is-approved .verification-panel-head {
            background: #f0fdf4;
            border-bottom-color: #bbf7d0;
        }

        .verification-panel.is-rejected .verification-panel-head {
            background: #fff5f5;
            border-bottom-color: #fecaca;
        }

        .panel-head-title {
            font-size: .9rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: .2rem;
            line-height: 1.3;
        }

        .panel-head-meta {
            font-size: .73rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* ── Status Badge ────────────────────────────────────────── */
        .verif-status-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .28rem .75rem;
            border-radius: 99px;
            font-size: .72rem;
            font-weight: 700;
        }

        .verif-status-badge.is-pending {
            background: var(--brand-primary-light);
            color: var(--brand-primary);
        }

        .verif-status-badge.is-approved {
            background: var(--brand-success-light);
            color: #065f46;
        }

        .verif-status-badge.is-rejected {
            background: var(--brand-danger-light);
            color: #991b1b;
        }

        /* ── Action Buttons ──────────────────────────────────────── */
        .btn-approve {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .9rem;
            border-radius: var(--radius-sm);
            font-size: .78rem;
            font-weight: 700;
            background: var(--brand-success);
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background .15s, transform .1s, box-shadow .15s;
        }

        .btn-approve:hover {
            background: #059669;
            box-shadow: 0 3px 10px rgba(16, 185, 129, .3);
            transform: translateY(-1px);
        }

        .btn-reject {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .9rem;
            border-radius: var(--radius-sm);
            font-size: .78rem;
            font-weight: 700;
            background: var(--brand-danger-light);
            color: var(--brand-danger);
            border: 1px solid #fca5a5;
            cursor: pointer;
            transition: background .15s, transform .1s, box-shadow .15s;
        }

        .btn-reject:hover {
            background: var(--brand-danger);
            color: #fff;
            box-shadow: 0 3px 10px rgba(239, 68, 68, .25);
            transform: translateY(-1px);
        }

        .btn-undo {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .28rem .7rem;
            border-radius: var(--radius-sm);
            font-size: .72rem;
            font-weight: 600;
            background: var(--surface-2);
            color: var(--text-secondary);
            border: 1px solid var(--border-light);
            cursor: pointer;
            transition: background .15s;
        }

        .btn-undo:hover {
            background: var(--border-light);
        }

        /* ── Rejection Note ──────────────────────────────────────── */
        .rejection-note {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .85rem 1.25rem;
            background: #fff5f5;
            border-bottom: 1px solid #fecaca;
            font-size: .82rem;
        }

        .rejection-note-icon {
            width: 24px;
            height: 24px;
            border-radius: 99px;
            background: var(--brand-danger);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: .65rem;
            margin-top: .05rem;
        }

        .rejection-note-text {
            color: #7f1d1d;
            line-height: 1.5;
        }

        /* ── Field Grid ──────────────────────────────────────────── */
        .verification-field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .verification-field {
            display: grid;
            grid-template-columns: minmax(140px, 32%) minmax(0, 1fr);
            min-height: 46px;
            border-bottom: 1px solid #f1f5f9;
        }

        .verification-field:nth-child(odd) {
            border-right: 1px solid #f1f5f9;
        }

        .verification-field-label {
            padding: .65rem 1rem;
            color: var(--text-muted);
            font-size: .72rem;
            font-weight: 700;
            background: var(--surface-1);
            border-right: 1px solid #f1f5f9;
            text-transform: uppercase;
            letter-spacing: .4px;
            display: flex;
            align-items: center;
        }

        .verification-field-value {
            min-width: 0;
            padding: .65rem 1rem;
            color: var(--text-primary);
            font-size: .85rem;
            word-break: break-word;
            white-space: normal;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .35rem;
        }

        /* ── Subsection / Category Headers ───────────────────────── */
        .verification-subsection-title {
            grid-column: 1 / -1;
            padding: .7rem 1rem;
            color: var(--brand-primary);
            font-size: .8rem;
            font-weight: 700;
            background: var(--brand-primary-light);
            border-top: 1px solid #c7d2fe;
            border-bottom: 1px solid #c7d2fe;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .specific-category-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin: 1.5rem 0 .6rem;
            padding: .75rem 1.1rem;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8eeff 100%);
            border-left: 3px solid var(--brand-primary);
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            color: var(--text-primary);
            font-weight: 700;
            font-size: .88rem;
        }

        /* ── Table Wrap ──────────────────────────────────────────── */
        .verification-table-wrap {
            grid-column: 1 / -1;
            overflow-x: auto;
        }

        .verification-table-wrap .table {
            min-width: 980px;
            margin-bottom: 0;
        }

        .verification-table-wrap .table thead th {
            background: var(--surface-1);
            border-bottom: 2px solid var(--border-light);
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: var(--text-secondary);
            padding: .7rem 1rem;
        }

        .verification-table-wrap .table tbody td {
            font-size: .82rem;
            padding: .65rem 1rem;
            vertical-align: middle;
            border-color: #f1f5f9;
        }

        .verification-table-wrap .table tbody tr:hover td {
            background: var(--surface-1);
        }

        /* ── Doc Preview Button ──────────────────────────────────── */
        .btn-preview-doc {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .25rem .65rem;
            border-radius: var(--radius-sm);
            font-size: .72rem;
            font-weight: 700;
            background: var(--brand-primary-light);
            color: var(--brand-primary);
            border: 1px solid #c7d2fe;
            cursor: pointer;
            transition: background .15s, box-shadow .15s;
        }

        .btn-preview-doc:hover {
            background: var(--brand-primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(79, 70, 229, .25);
        }

        /* ── Min-w utility ───────────────────────────────────────── */
        .min-w-0 {
            min-width: 0;
        }

        /* ── Responsive ──────────────────────────────────────────── */
        @media (max-width: 991.98px) {
            .verification-panel-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .verification-field-grid,
            .verification-field {
                grid-template-columns: 1fr;
            }

            .verification-field:nth-child(odd) {
                border-right: 0;
            }

            .verification-field-label {
                border-right: 0;
                border-bottom: 1px solid #f1f5f9;
                padding-bottom: .4rem;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $general = $application->general;

        $statusMeta = [
            'submitted' => ['label' => 'Proses Verifikasi', 'class' => 'label-light-primary'],
            'need_revision' => ['label' => 'Revisi Data', 'class' => 'label-light-warning'],
            'verified' => ['label' => 'Verified', 'class' => 'label-light-success'],
        ][$application->status] ?? [
            'label' => ucwords(str_replace('_', ' ', $application->status)),
            'class' => 'label-light',
        ];

        $itemStatus = [
            'pending' => [
                'label' => 'Belum Diverifikasi',
                'class' => 'label-light-primary',
                'icon' => 'flaticon2-hourglass',
            ],
            'approved' => ['label' => 'Disetujui', 'class' => 'label-light-success', 'icon' => 'flaticon2-check-mark'],
            'rejected' => ['label' => 'Tidak Disetujui', 'class' => 'label-light-danger', 'icon' => 'flaticon2-cross'],
        ];

        $documentRows = $application->documents
            ->map(function ($d) {
                return [
                    'label' => ucwords(str_replace('_', ' ', $d->field_name)),
                    'value' => $d->original_name,
                    'url' => asset('storage/' . $d->file_path),
                ];
            })
            ->values();

        $sectionRows = [
            'profile' => [
                ['label' => 'Bentuk Perusahaan', 'value' => optional($general)->status_perusahaan],
                ['label' => 'Nama Perusahaan', 'value' => optional($general)->nama_perusahaan],
                ['label' => 'Alamat', 'value' => optional($general)->alamat_perusahaan],
                ['label' => 'Website', 'value' => optional($general)->website],
                ['label' => 'Email', 'value' => optional($general)->email_perusahaan],
                ['label' => 'Telepon', 'value' => optional($general)->telepon_perusahaan],
                ['label' => 'Narahubung', 'value' => optional($general)->pic_nama],
                ['label' => 'Email Narahubung', 'value' => optional($general)->pic_email],
                ['label' => 'Telepon Narahubung', 'value' => optional($general)->pic_telepon],
            ],
            'structure' => [
                [
                    'label' => 'Dok. Struktur Org.',
                    'value' => optional($application->documents->firstWhere('field_name', 'dok_struktur_org'))
                        ->original_name,
                ],
                [
                    'label' => 'Perusahaan Terkait',
                    'value' => optional($general)->has_other_company === 'yes' ? 'Ada' : 'Tidak ada',
                ],
            ],
            'legal' => [
                ['label' => 'NIB', 'value' => optional($general)->nib],
                ['label' => 'NPWP', 'value' => optional($general)->npwp],
                [
                    'label' => 'Akte Pendirian',
                    'value' => optional($application->documents->firstWhere('field_name', 'dok_akte_pendirian'))
                        ->original_name,
                ],
                [
                    'label' => 'Akte Direksi',
                    'value' => optional($application->documents->firstWhere('field_name', 'dok_akte_direksi'))
                        ->original_name,
                ],
            ],
            'goods' => $application->products
                ->map(function ($p) {
                    return [
                        'label' => $p->erp_product_id ?: 'Produk',
                        'value' => trim(($p->product_name ?: '-') . ' / ' . ($p->manufaktur ?: '-')),
                    ];
                })
                ->values()
                ->all(),
            'certificate' => [
                ['label' => 'Sertifikat ISO', 'value' => implode(', ', optional($general)->iso_certificates ?? [])],
                ['label' => 'Komitmen Kualitas', 'value' => optional($general)->komitmen_kualitas],
                ['label' => 'Sertifikat Halal', 'value' => optional($general)->sertifikat_halal],
                [
                    'label' => 'Dok. Sertifikat Halal',
                    'value' => optional($application->documents->firstWhere('field_name', 'dok_sertifikat_halal'))
                        ->original_name,
                ],
            ],
            'tax_finance' => [
                ['label' => 'Status Pajak', 'value' => optional($general)->status_pajak],
                [
                    'label' => 'SPPKP',
                    'value' => optional($application->documents->firstWhere('field_name', 'dok_sppkp'))->original_name,
                ],
                ['label' => 'Payment Term', 'value' => optional($general)->payment_term],
                ['label' => 'Nama Bank', 'value' => optional($general)->nama_bank],
                ['label' => 'Nomor Rekening', 'value' => optional($general)->nomor_rekening],
                ['label' => 'Pemegang Rekening', 'value' => optional($general)->pemegang_rekening],
            ],
            'documents' => $documentRows->all(),
            'specific' => $application->categories
                ->map(function ($c) {
                    return ['label' => 'Kategori', 'value' => $c->category_label];
                })
                ->values()
                ->all(),
        ];

        // ── Progress hitung total item across all sections ──────────────────
        $allItems = collect($verificationSections)->flatMap(function ($s) {
            return $s['items'];
        });
        $totalItems = $allItems->count();
        $approvedItems = $allItems->where('status', 'approved')->count();
        $rejectedItems = $allItems->where('status', 'rejected')->count();
        $pendingItems = $totalItems - $approvedItems - $rejectedItems;
        $progressPct = $totalItems > 0 ? round(($approvedItems / $totalItems) * 100) : 0;
    @endphp

    {{-- ───── Flash Alerts ───── --}}
    @if (session('success'))
        <div class="alert alert-custom alert-light-success fade show mb-5" role="alert">
            <div class="alert-icon"><i class="flaticon2-check-mark text-success"></i></div>
            <div class="alert-text">{{ session('success') }}</div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
            <div class="alert-icon"><i class="flaticon-warning text-danger"></i></div>
            <div class="alert-text">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        </div>
    @endif

    {{-- ───── Header Card ───── --}}
    <div class="card verif-header-card mb-5">
        <div class="card-body py-6 px-7">
            <div class="d-flex flex-wrap justify-content-between align-items-start">

                {{-- Kiri: nomor + perusahaan --}}
                <div class="d-flex align-items-start">
                    <a href="{{ route('pengadaan.permohonan.index') }}"
                        class="btn btn-icon btn-sm btn-light-primary mr-5 mt-1" title="Kembali ke daftar">
                        <i class="ki ki-arrow-back icon-sm"></i>
                    </a>
                    <div>
                        <div class="text-muted font-size-xs font-weight-bold text-uppercase mb-1"
                            style="letter-spacing:.6px; color:var(--text-muted);">Nomor Permohonan</div>
                        <h4 class="font-weight-bolder mb-1" style="color:var(--brand-primary); font-size:1.2rem;">
                            {{ $application->application_number ?? '-' }}
                        </h4>
                        <div class="font-size-sm mb-1" style="color:var(--text-secondary);">
                            <i class="flaticon2-group icon-xs mr-1"></i>
                            {{ optional($general)->nama_perusahaan ?? '-' }}
                        </div>
                        <div class="font-size-xs" style="color:var(--text-muted);">
                            <i class="flaticon2-user icon-xs mr-1"></i>
                            {{ optional($application->user)->name ?? '-' }}
                            &middot; {{ optional($application->user)->email ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- Kanan: status + stat boxes --}}
                <div class="d-flex flex-column align-items-end mt-4 mt-lg-0" style="gap:.75rem;">
                    <span
                        class="verif-status-badge {{ in_array($application->status, ['verified']) ? 'is-approved' : (in_array($application->status, ['need_revision']) ? 'is-rejected' : 'is-pending') }}"
                        style="font-size:.8rem; padding: .4rem 1rem; border-radius:99px;">
                        {{ $statusMeta['label'] }}
                    </span>
                    <div class="d-flex flex-wrap justify-content-end" style="gap:.6rem;">
                        <div class="stat-box">
                            <div class="stat-label">Submit</div>
                            <div class="stat-value">
                                {{ optional($application->submitted_at)->format('d/m/Y') ?? '-' }}
                            </div>
                            <div class="stat-sub">
                                {{ optional($application->submitted_at)->format('H:i') ?? '' }}
                            </div>
                        </div>
                        @if ($application->revision_submitted_at)
                            <div class="stat-box">
                                <div class="stat-label">Revisi Dikirim</div>
                                <div class="stat-value" style="color:var(--brand-warning);">
                                    {{ $application->revision_submitted_at->format('d/m/Y') }}
                                </div>
                                <div class="stat-sub">
                                    Ke-{{ (int) $application->revision_count }} ·
                                    {{ $application->revision_submitted_at->format('H:i') }}
                                </div>
                            </div>
                        @endif
                        <div class="stat-box">
                            <div class="stat-label">Deadline 10 HK</div>
                            <div class="stat-value">
                                {{ optional($deadline)->format('d/m/Y') ?? '-' }}
                            </div>
                            @if ($remainingBusinessDays !== null && $application->status === 'submitted')
                                <span
                                    class="verif-pill {{ $remainingBusinessDays < 0 ? 'rejected' : ($remainingBusinessDays <= 3 ? '' : 'pending') }}"
                                    style="{{ $remainingBusinessDays <= 3 && $remainingBusinessDays >= 0 ? 'background:var(--brand-warning-light);color:#92400e;' : '' }} font-size:.66rem; padding:.15rem .55rem;">
                                    {{ $remainingBusinessDays < 0
                                        ? abs($remainingBusinessDays) . ' HK terlambat'
                                        : $remainingBusinessDays . ' HK tersisa' }}
                                </span>
                            @elseif ($application->status === 'verified')
                                <span class="verif-pill approved"
                                    style="font-size:.66rem; padding:.15rem .55rem;">Selesai</span>
                            @endif
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Progress</div>
                            <div class="stat-value {{ $rejectedItems > 0 ? '' : ($progressPct === 100 ? '' : '') }}"
                                style="color:{{ $rejectedItems > 0 ? 'var(--brand-danger)' : ($progressPct === 100 ? 'var(--brand-success)' : 'var(--brand-primary)') }}">
                                {{ $approvedItems }}/{{ $totalItems }}
                            </div>
                            <div class="verif-progress-wrap mt-1" style="width:80px;">
                                <div class="verif-progress-bar {{ $rejectedItems > 0 ? 'has-rejected' : '' }}"
                                    style="width:{{ $progressPct }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Summary pill row --}}
            <div class="verif-summary-row">
                <span class="verif-pill pending">
                    <i class="flaticon2-hourglass icon-xs"></i>
                    {{ $pendingItems }} Menunggu
                </span>
                <span class="verif-pill approved">
                    <i class="flaticon2-check-mark icon-xs"></i>
                    {{ $approvedItems }} Disetujui
                </span>
                @if ($rejectedItems > 0)
                    <span class="verif-pill rejected">
                        <i class="flaticon2-cross icon-xs"></i>
                        {{ $rejectedItems }} Ditolak
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ───── Main Verification Card ───── --}}
    <div class="card card-custom" style="border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:none;">
        <div class="card-header border-bottom-0 pt-6 pb-0">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-check-mark" style="color:var(--brand-primary);"></i>
                </span>
                <h5 class="card-label font-weight-bolder" style="color:var(--text-primary);">Verifikasi Data Calon Penyedia
                </h5>
            </div>
        </div>

        <div class="card-body pt-3">

            {{-- Tab Nav --}}
            <div class="tab-nav-wrap">
                <ul class="nav verif-tabs nav-tabs mb-0 flex-nowrap overflow-auto" role="tablist">
                    @foreach ($verificationSections as $section)
                        <li class="nav-item flex-shrink-0">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab"
                                href="#tab-{{ $section['key'] }}" role="tab">
                                {{ $section['label'] }}
                                @if ($section['rejected'] > 0)
                                    <span class="verif-tab-badge is-rejected">{{ $section['rejected'] }}</span>
                                @elseif ($section['pending'] > 0)
                                    <span class="verif-tab-badge is-pending">{{ $section['pending'] }}</span>
                                @else
                                    <span class="verif-tab-badge is-done">✓</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Tab Content --}}
            <div class="tab-content pt-7">
                @foreach ($verificationSections as $section)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $section['key'] }}"
                        role="tabpanel">

                        <div class="verification-item-list">
                            @php $lastSpecificHeader = null; @endphp
                            @foreach ($section['items'] as $item)
                                @php
                                    $meta = $itemStatus[$item->status] ?? $itemStatus['pending'];
                                    $rows = $verificationItemRows[$item->item_key] ?? [];
                                    $specificHeader =
                                        $section['key'] === 'specific'
                                            ? $specificCategoryHeaders[$item->item_key] ?? null
                                            : null;
                                    $showActionsInSpecificHeader = $section['key'] === 'specific' && $specificHeader;
                                    $panelClass = '';
                                    if ($item->status === 'approved') {
                                        $panelClass = 'is-approved';
                                    } elseif ($item->status === 'rejected') {
                                        $panelClass = 'is-rejected';
                                    }
                                @endphp

                                @if ($specificHeader && $specificHeader !== $lastSpecificHeader)
                                    <div class="specific-category-header">
                                        <span>{{ $specificHeader }}</span>

                                        @if ($showActionsInSpecificHeader)
                                            <div class="d-flex align-items-center flex-wrap justify-content-end"
                                                style="gap:.5rem;">
                                                <span
                                                    class="verif-status-badge {{ $item->status === 'approved' ? 'is-approved' : ($item->status === 'rejected' ? 'is-rejected' : 'is-pending') }}">
                                                    <i class="{{ $meta['icon'] }} icon-xs"></i>
                                                    {{ $meta['label'] }}
                                                </span>

                                                @if ($item->status === 'pending')
                                                    <form method="POST"
                                                        action="{{ route('pengadaan.permohonan.items.approve', [$application, $item]) }}"
                                                        class="d-inline js-confirm-approve"
                                                        data-label="{{ $specificHeader }}">
                                                        @csrf
                                                        <button type="submit" class="btn-approve">
                                                            <i class="flaticon2-check-mark icon-xs"></i> Setuju
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn-reject js-reject-btn"
                                                        data-toggle="modal" data-target="#modalRejectItem"
                                                        data-action="{{ route('pengadaan.permohonan.items.reject', [$application, $item]) }}"
                                                        data-label="{{ $specificHeader }}">
                                                        <i class="ki ki-close icon-xs"></i> Tidak Setuju
                                                    </button>
                                                @elseif ($item->status === 'approved')
                                                    <button type="button" class="btn-undo js-reject-btn"
                                                        data-toggle="modal" data-target="#modalRejectItem"
                                                        data-action="{{ route('pengadaan.permohonan.items.reject', [$application, $item]) }}"
                                                        data-label="{{ $specificHeader }}"
                                                        title="Ubah menjadi Tidak Setuju">
                                                        <i class="ki ki-close icon-xs"></i> Tolak
                                                    </button>
                                                @elseif ($item->status === 'rejected')
                                                    <form method="POST"
                                                        action="{{ route('pengadaan.permohonan.items.approve', [$application, $item]) }}"
                                                        class="d-inline js-confirm-approve"
                                                        data-label="{{ $specificHeader }}">
                                                        @csrf
                                                        <button type="submit" class="btn-undo"
                                                            title="Ubah menjadi Setuju">
                                                            <i class="flaticon2-check-mark icon-xs"></i> Setujui
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    @php $lastSpecificHeader = $specificHeader; @endphp
                                @endif

                                <div class="verification-panel mb-4 {{ $panelClass }}">

                                    {{-- Panel Head --}}
                                    <div class="verification-panel-head">
                                        <div class="min-w-0">
                                            <div class="panel-head-title">
                                                {{ $showActionsInSpecificHeader ? 'Detail Persyaratan' : $item->item_label }}
                                            </div>
                                            <div class="panel-head-meta">
                                                @if ($item->verifier)
                                                    <i class="flaticon2-user icon-xs"></i>
                                                    {{ $item->verifier->name }}
                                                    &middot;
                                                    {{ optional($item->verified_at)->format('d/m/Y H:i') }}
                                                @else
                                                    <i class="flaticon2-hourglass icon-xs"></i>
                                                    Belum ada verifikator
                                                @endif
                                            </div>
                                        </div>

                                        @if (!$showActionsInSpecificHeader)
                                            <div class="d-flex align-items-center flex-wrap justify-content-end"
                                                style="gap:.5rem;">
                                                {{-- Status badge --}}
                                                <span
                                                    class="verif-status-badge {{ $item->status === 'approved' ? 'is-approved' : ($item->status === 'rejected' ? 'is-rejected' : 'is-pending') }}">
                                                    <i class="{{ $meta['icon'] }} icon-xs"></i>
                                                    {{ $meta['label'] }}
                                                </span>

                                                {{-- Aksi --}}
                                                @if ($item->status === 'pending')
                                                    <form method="POST"
                                                        action="{{ route('pengadaan.permohonan.items.approve', [$application, $item]) }}"
                                                        class="d-inline js-confirm-approve"
                                                        data-label="{{ $item->item_label }}">
                                                        @csrf
                                                        <button type="submit" class="btn-approve">
                                                            <i class="flaticon2-check-mark icon-xs"></i> Setuju
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn-reject js-reject-btn"
                                                        data-toggle="modal" data-target="#modalRejectItem"
                                                        data-action="{{ route('pengadaan.permohonan.items.reject', [$application, $item]) }}"
                                                        data-label="{{ $item->item_label }}">
                                                        <i class="ki ki-close icon-xs"></i> Tidak Setuju
                                                    </button>
                                                @elseif ($item->status === 'approved')
                                                    <button type="button" class="btn-undo js-reject-btn"
                                                        data-toggle="modal" data-target="#modalRejectItem"
                                                        data-action="{{ route('pengadaan.permohonan.items.reject', [$application, $item]) }}"
                                                        data-label="{{ $item->item_label }}"
                                                        title="Ubah menjadi Tidak Setuju">
                                                        <i class="ki ki-close icon-xs"></i> Tolak
                                                    </button>
                                                @elseif ($item->status === 'rejected')
                                                    <form method="POST"
                                                        action="{{ route('pengadaan.permohonan.items.approve', [$application, $item]) }}"
                                                        class="d-inline js-confirm-approve"
                                                        data-label="{{ $item->item_label }}">
                                                        @csrf
                                                        <button type="submit" class="btn-undo"
                                                            title="Ubah menjadi Setuju">
                                                            <i class="flaticon2-check-mark icon-xs"></i> Setujui
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Rejection Note --}}
                                    @if ($item->note)
                                        <div class="rejection-note">
                                            <div class="rejection-note-icon">
                                                <i class="ki ki-close"></i>
                                            </div>
                                            <div class="rejection-note-text">
                                                <span style="font-weight:700;">
                                                    {{ $item->status === 'pending' ? 'Catatan Revisi Sebelumnya:' : 'Catatan Penolakan:' }}
                                                </span>
                                                {{ $item->note }}
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Field Grid --}}
                                    <div class="verification-field-grid">
                                        @forelse ($rows as $row)
                                            @if (($row['type'] ?? null) === 'section_title')
                                                <div class="verification-subsection-title">
                                                    {{ $row['label'] }}
                                                </div>
                                            @elseif (($row['type'] ?? null) === 'other_company_table')
                                                <div class="verification-table-wrap p-4 js-revision-field"
                                                    data-field="{{ $row['field'] ?? $row['label'] }}"
                                                    data-label="{{ $row['label'] }}">
                                                    <div class="font-weight-bold text-dark mb-3">{{ $row['label'] }}</div>
                                                    <table class="table table-sm table-bordered table-hover mb-0">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width:60px;">No</th>
                                                                <th style="width:260px;">Nama Perusahaan</th>
                                                                <th>Alamat</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse (($row['value'] ?? []) as $company)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td class="font-weight-bold text-dark">
                                                                        {{ $company['nama'] ?? '-' }}</td>
                                                                    <td>{{ $company['alamat'] ?? '-' }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="3"
                                                                        class="text-center text-muted py-5">
                                                                        Tidak ada perusahaan lain milik pimpinan.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @elseif (($row['type'] ?? null) === 'product_table')
                                                <div class="verification-table-wrap p-4">
                                                    <div class="font-weight-bold text-dark mb-3">{{ $row['label'] }}</div>
                                                    <table class="table table-sm table-bordered table-hover mb-0">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width:220px;">Produk</th>
                                                                <th style="width:100px;">Kode ERP</th>
                                                                <th style="width:160px;">Manufaktur / Asal</th>
                                                                <th style="width:140px;">Rantai Pasok</th>
                                                                <th style="width:120px;">Surat Keagenan</th>
                                                                <th style="width:100px;">TKDN</th>
                                                                <th style="width:110px;">SNI</th>
                                                                <th style="width:110px;">Halal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse (($row['value'] ?? []) as $product)
                                                                <tr class="js-revision-field"
                                                                    data-field="{{ $product['field'] ?? 'products.' . ($product['erp'] ?? $loop->iteration) }}"
                                                                    data-label="{{ $product['label'] ?? ($product['product'] ?? 'Produk') }}">
                                                                    <td class="font-weight-bold text-dark">
                                                                        {{ $product['product'] ?? '-' }}</td>
                                                                    <td>{{ $product['erp'] ?? '-' }}</td>
                                                                    <td>{{ $product['manufaktur'] ?? '-' }}</td>
                                                                    <td>{{ $product['rantai_pasok'] ?? '-' }}</td>
                                                                    <td>
                                                                        @if (!empty($product['surat']))
                                                                            <button type="button"
                                                                                class="btn btn-xs btn-light-primary btn-preview-doc"
                                                                                data-url="{{ $product['surat'] }}"
                                                                                data-title="Surat Keagenan - {{ $product['product'] ?? 'Produk' }}">
                                                                                <i class="flaticon2-document icon-xs"></i>
                                                                                Lihat
                                                                            </button>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $product['tkdn'] ?? '-' }}</td>
                                                                    <td>{{ $product['sni'] ?? '-' }}</td>
                                                                    <td>{{ $product['halal'] ?? '-' }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="8"
                                                                        class="text-center text-muted py-5">
                                                                        Belum ada produk yang dipilih.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="verification-field js-revision-field"
                                                    data-field="{{ $row['field'] ?? $row['label'] }}"
                                                    data-label="{{ $row['label'] }}">
                                                    <div class="verification-field-label">{{ $row['label'] }}</div>
                                                    <div class="verification-field-value">
                                                        @if (!empty($row['url']))
                                                            <button type="button" class="btn-preview-doc btn-preview-doc"
                                                                data-url="{{ $row['url'] }}"
                                                                data-title="{{ $row['label'] ?? 'Preview Dokumen' }}">
                                                                <i class="flaticon2-document icon-xs"></i>
                                                                {{ $row['file_label'] ?? 'Lihat Dok.' }}
                                                            </button>
                                                        @endif
                                                        {!! nl2br(e(($row['value'] ?? null) ?: '-')) !!}
                                                    </div>
                                                </div>
                                            @endif
                                        @empty
                                            <div
                                                style="grid-column:1/-1; padding:2rem 1.5rem; color:var(--text-muted); font-size:.83rem; display:flex; align-items:center; gap:.5rem;">
                                                <i class="flaticon2-information"></i>
                                                Belum ada data tersimpan untuk item ini.
                                            </div>
                                        @endforelse
                                    </div>

                                </div>{{-- /verification-panel --}}
                            @endforeach
                        </div>{{-- /verification-item-list --}}
                    </div>{{-- /tab-pane --}}
                @endforeach
            </div>{{-- /tab-content --}}

        </div>
    </div>

    {{-- ───── Modal Tolak ───── --}}
    <div class="modal fade" id="modalPreviewDoc" tabindex="-1" role="dialog" aria-labelledby="modalPreviewDocLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content"
                style="border:none;border-radius:var(--radius-lg);box-shadow:0 20px 60px rgba(15,23,42,.15);overflow:hidden;">
                <div class="modal-header"
                    style="border-bottom:1px solid var(--border-light);background:var(--surface-1);padding:1rem 1.5rem;">
                    <h5 class="modal-title font-weight-bolder" id="modalPreviewDocLabel"
                        style="color:var(--text-primary);font-size:.95rem;">Preview Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body p-0" style="height:80vh;background:#f8fafc;">
                    <div id="previewContainer" class="h-100 d-flex align-items-center justify-content-center">
                        <div class="spinner spinner-primary spinner-lg"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border-light);background:var(--surface-1);">
                    <a href="#" target="_blank" class="btn-approve" id="btnDownloadDoc"
                        style="text-decoration:none;">
                        <i class="flaticon-download icon-xs"></i> Download
                    </a>
                    <button type="button" class="btn-undo" data-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalRejectItem" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:460px;" role="document">
            <form method="POST" action="#" class="modal-content" id="formRejectItem"
                style="border:none;border-radius:var(--radius-lg);box-shadow:0 20px 60px rgba(15,23,42,.15);overflow:hidden;">
                @csrf
                <div class="modal-header"
                    style="border-bottom:1px solid var(--border-light);background:var(--surface-1);padding:1rem 1.5rem;">
                    <div class="d-flex align-items-center">
                        <span
                            style="width:30px;height:30px;border-radius:99px;background:var(--brand-danger);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:.75rem;">
                            <i class="ki ki-close text-white" style="font-size:.6rem;"></i>
                        </span>
                        <h6 class="modal-title font-weight-bolder mb-0"
                            style="color:var(--text-primary);font-size:.9rem;">Tolak Item Verifikasi</h6>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body" style="padding:1.5rem;">
                    <div
                        style="background:var(--brand-danger-light);border:1px solid #fca5a5;border-radius:var(--radius-md);padding:1rem 1.25rem;margin-bottom:1.25rem;">
                        <div
                            style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);margin-bottom:.35rem;">
                            Item yang Ditolak</div>
                        <div style="font-weight:700;color:var(--brand-danger);font-size:.87rem;" id="rejectItemLabel">-
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label
                            style="font-weight:700;font-size:.83rem;color:var(--text-primary);margin-bottom:.5rem;display:block;">
                            Field/Dokumen yang Harus Direvisi <span style="color:var(--brand-danger);">*</span>
                        </label>
                        <div id="rejectFieldList" class="reject-field-list">
                        </div>

                        <label
                            style="font-weight:700;font-size:.83rem;color:var(--text-primary);margin-bottom:.5rem;display:block;">
                            Alasan Penolakan <span style="color:var(--brand-danger);">*</span>
                        </label>
                        <textarea name="note" rows="4" class="form-control" required
                            placeholder="Tuliskan alasan data tidak disetujui…"
                            style="border-color:var(--border-light);border-radius:var(--radius-sm);font-size:.83rem;resize:vertical;"></textarea>
                        <div style="color:var(--text-muted);font-size:.72rem;margin-top:.5rem;">
                            Alasan ini akan ditampilkan ke vendor sebagai catatan revisi.
                        </div>
                    </div>
                </div>
                <div class="modal-footer"
                    style="border-top:1px solid var(--border-light);background:var(--surface-1);gap:.5rem;">
                    <button type="button" class="btn-undo" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-reject"
                        style="background:var(--brand-danger);color:#fff;border-color:var(--brand-danger);">
                        <i class="ki ki-close icon-xs"></i> Tolak Item
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Gunakan SweetAlert2 (sudah bundle di Metronic) daripada confirm() native
        $(document).on('submit', '.js-confirm-approve', function(e) {
            if (this.dataset.confirmed === 'true') {
                return true;
            }

            e.preventDefault();
            var $form = $(this);
            var label = $form.data('label') || 'item ini';

            Swal.fire({
                title: 'Setujui Data?',
                html: 'Anda akan menyetujui <strong>' + label +
                    '</strong>.<br>Keputusan masih bisa diubah setelahnya.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setuju',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
                reverseButtons: true,
            }).then(function(result) {
                if (result.isConfirmed || result.value) {
                    $form[0].dataset.confirmed = 'true';
                    $form.trigger('submit');
                }
            });
        });

        $(document).on('click', '.btn-preview-doc', function(e) {
            e.preventDefault();

            var url = $(this).data('url');
            var title = $(this).data('title') || 'Preview Dokumen';
            var container = $('#previewContainer');
            var extension = String(url || '').split('.').pop().toLowerCase();

            $('#modalPreviewDocLabel').text(title);
            $('#btnDownloadDoc').attr('href', url || '#');
            container.html('<div class="spinner spinner-primary spinner-lg"></div>');
            $('#modalPreviewDoc').modal('show');

            setTimeout(function() {
                if (!url) {
                    container.html('<div class="text-muted">Dokumen tidak tersedia.</div>');
                } else if (extension === 'pdf') {
                    container.html('<iframe src="' + url +
                        '" frameborder="0" class="w-100 h-100"></iframe>');
                } else if (['jpg', 'jpeg', 'png'].indexOf(extension) !== -1) {
                    container.html('<img src="' + url +
                        '" class="img-fluid shadow-sm rounded" style="max-height:95%; object-fit:contain;">'
                        );
                } else {
                    container.html(
                        '<div class="text-center px-5">' +
                        '<i class="flaticon-file-2 display-1 text-muted"></i>' +
                        '<p class="mt-4 mb-0">Format file tidak mendukung preview langsung.<br>Silakan gunakan tombol Download.</p>' +
                        '</div>'
                    );
                }
            }, 250);
        });

        // Inject action + label ke modal reject
        $('#modalRejectItem').on('show.bs.modal', function(event) {
            var btn = $(event.relatedTarget);
            $('#formRejectItem').attr('action', btn.data('action'));
            $('#rejectItemLabel').text(btn.data('label') || '-');
            $('#formRejectItem textarea[name="note"]').val('');

            var $sourcePanel = btn.closest('.verification-panel');
            if (!$sourcePanel.length) {
                $sourcePanel = btn.closest('.specific-category-header').nextAll('.verification-panel').first();
            }

            var fields = [];
            $sourcePanel.find('.js-revision-field').each(function() {
                var field = String($(this).data('field') || '').trim();
                var label = String($(this).data('label') || field).trim();

                if (!field || fields.some(function(item) {
                    return item.field === field;
                })) {
                    return;
                }

                fields.push({
                    field: field,
                    label: label
                });
            });

            var fieldHtml = '';
            fields.forEach(function(item, index) {
                fieldHtml +=
                    '<label class="reject-field-option">' +
                    '<input type="checkbox" name="revision_fields[]" value="' + $('<div>').text(item.field).html() + '">' +
                    '<span>' +
                    $('<div>').text(item.label).html() +
                    '</span>' +
                    '<input type="hidden" name="revision_labels[' + $('<div>').text(item.field).html() + ']" value="' + $('<div>').text(item.label).html() + '">' +
                    '</label>';
            });

            $('#rejectFieldList').html(fieldHtml ||
                '<div class="text-muted font-size-sm">Tidak ada field yang dapat dipilih.</div>');
        });

        $('#formRejectItem').on('submit', function(e) {
            if ($(this).find('input[name="revision_fields[]"]:checked').length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Field Revisi',
                    text: 'Pilih minimal satu field atau dokumen yang harus diperbaiki vendor.'
                });
            }
        });

        // Hide scrollbar tapi masih bisa scroll (tab nav)
        document.querySelector('.nav-tabs')?.addEventListener('wheel', function(e) {
            e.preventDefault();
            this.scrollLeft += e.deltaY;
        }, {
            passive: false
        });
    </script>
@endpush
