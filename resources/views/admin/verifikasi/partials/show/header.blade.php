<div id="verificationHeader" class="card verif-header-card mb-5">
    <div class="card-body py-6 px-7">
        <div class="d-flex flex-wrap justify-content-between align-items-start">
            <div class="d-flex align-items-start">
                @php
                    $backUrl = url()->previous() && url()->previous() !== url()->current()
                        ? url()->previous()
                        : (auth()->user() && auth()->user()->can('verifikasi-list')
                            ? route('verifikasi.index')
                            : (auth()->user() && auth()->user()->can('supplier-list')
                                ? route('supplier.index')
                                : route('home')));
                @endphp
                <a href="{{ $backUrl }}" class="btn btn-icon btn-sm btn-light-primary mr-5 mt-1"
                    title="Kembali ke daftar">
                    <i class="ki ki-arrow-back icon-sm"></i>
                </a>
                <div>
                    <div class="text-muted font-size-xs font-weight-bold text-uppercase mb-1">
                        Nomor Permohonan
                    </div>
                    <h4 class="font-weight-bolder mb-1 d-flex align-items-center flex-wrap"
                        style="color:var(--brand-primary); font-size:1.2rem; gap:6px;">
                        <span>{{ $application->application_number ?? '-' }}</span>
                        @if ($application->isRekualifikasi())
                            <span class="badge badge-warning font-weight-bold"
                                style="font-size:0.72rem; padding:4px 8px;">
                                <i class="fas fa-sync-alt mr-1 font-size-xs text-dark"></i> Rekualifikasi Vendor
                            </span>
                        @else
                            <span class="badge badge-info font-weight-bold" style="font-size:0.72rem; padding:4px 8px;">
                                <i class="fas fa-user-plus mr-1 font-size-xs"></i> Registrasi Vendor Baru
                            </span>
                        @endif
                    </h4>
                    @if ($application->isRekualifikasi() && $application->requalification_reason)
                        @php
                            $reasonLabels = \App\Models\VendorApplication::REASON_LABELS;
                            $reasonText =
                                $reasonLabels[$application->requalification_reason] ??
                                $application->requalification_reason;
                        @endphp
                        <div class="font-size-xs text-warning font-weight-bold mb-1">
                            <i class="flaticon-info icon-xs mr-1"></i> Alasan: {{ $reasonText }}
                        </div>
                    @endif
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

            <div class="d-flex flex-column align-items-end mt-4 mt-lg-0" style="gap:.75rem;">
                <span class="verif-status-badge {{ $statusMeta['state_class'] }}">
                    {{ $statusMeta['label'] }}
                </span>

                <div class="d-flex flex-wrap justify-content-end" style="gap:.6rem;">
                    <div class="stat-box">
                        <div class="stat-label">Submit</div>
                        <div class="stat-value">{{ optional($application->submitted_at)->format('d/m/Y') ?? '-' }}</div>
                        <div class="stat-sub">{{ optional($application->submitted_at)->format('H:i') ?? '' }}</div>
                    </div>

                    @if ($application->revision_submitted_at)
                        <div class="stat-box">
                            <div class="stat-label">Revisi Dikirim</div>
                            <div class="stat-value" style="color:var(--brand-warning);">
                                {{ $application->revision_submitted_at->format('d/m/Y') }}
                            </div>
                            <div class="stat-sub">
                                Ke-{{ (int) $application->revision_count }} &middot;
                                {{ $application->revision_submitted_at->format('H:i') }}
                            </div>
                        </div>
                    @endif

                    <div class="stat-box">
                        <div class="stat-label">Deadline 10 Hari Kalender</div>
                        <div class="stat-value">{{ optional($deadline)->format('d/m/Y') ?? '-' }}</div>
                        @if ($statusMeta['deadline_text'])
                            <span class="verif-pill {{ $statusMeta['deadline_class'] }}">
                                {{ $statusMeta['deadline_text'] }}
                            </span>
                        @endif
                    </div>

                    <div class="stat-box">
                        <div class="stat-label">Progress</div>
                        <div class="stat-value" style="color:{{ $verificationSummary['progress_color'] }}">
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

        @if ($application->isRekualifikasi())
            @if ($application->status === \App\Models\VendorApplication::STATUS_APPROVED)
                <div class="alert alert-custom alert-light-success mb-0 mt-4 py-3 px-4" role="alert">
                    <div class="alert-icon"><i class="fas fa-check-circle text-success"></i></div>
                    <div class="alert-text font-size-sm">
                        <strong class="text-dark">Rekualifikasi Selesai:</strong> Seluruh bagian data pembaruan profil vendor telah diverifikasi dan disetujui (Approved).
                    </div>
                </div>
            @else
                <div class="alert alert-custom alert-light-warning mb-0 mt-4 py-3 px-4" role="alert">
                    <div class="alert-icon"><i class="fas fa-sync-alt text-warning"></i></div>
                    <div class="alert-text font-size-sm">
                        <strong class="text-dark">Permohonan Rekualifikasi (Pembaruan Profil):</strong> Vendor telah
                        memperbarui data profilnya. Seluruh bagian verifikasi telah dikembalikan ke status <strong>Menunggu
                            Verifikasi Ulang</strong> agar dapat Anda periksa kembali.
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
