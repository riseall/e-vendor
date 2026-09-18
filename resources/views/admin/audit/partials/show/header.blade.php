    {{-- ════ HEADER ════ --}}
    <div class="card verif-header-card mb-5">
        <div class="card-body py-6 px-7">
            <div class="d-flex flex-wrap justify-content-between align-items-start">
                <div class="d-flex align-items-start">
                    <a href="{{ route('qa.audit.index') }}" class="btn btn-icon btn-sm btn-light-primary mr-5 mt-1"
                        title="Kembali ke daftar">
                        <i class="ki ki-arrow-back icon-sm"></i>
                    </a>
                    <div>
                        <div class="text-muted font-size-xs font-weight-bold text-uppercase mb-1">
                            Nomor Permohonan &middot; Audit #{{ $audit->id }}
                            ({{ strtoupper(str_replace('_', '-', $audit->audit_type)) }})
                        </div>
                        <h4 class="font-weight-bolder mb-1" style="color:var(--brand-primary); font-size:1.2rem;">
                            {{ $application->application_number ?? '-' }}
                        </h4>
                        <div class="font-size-sm mb-1" style="color:var(--text-secondary);">
                            <i class="flaticon2-group icon-xs mr-1"></i>
                            {{ $vendorName }}
                        </div>
                        <div class="font-size-xs" style="color:var(--text-muted);">
                            <i class="flaticon2-user icon-xs mr-1"></i>
                            {{ $vendorEmail }}
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column align-items-end mt-4 mt-lg-0" style="gap:.75rem;">
                    <span class="verif-status-badge is-pending">
                        {{ strtoupper(str_replace('_', ' ', $audit->status)) }}
                    </span>
                </div>
            </div>

            <div class="verif-summary-row mt-6">
                <span class="verif-pill pending">
                    <i class="flaticon-profile-1 icon-xs"></i>
                    QA Lead: {{ optional($audit->qaLead)->name ?? '—' }}
                </span>
                <span class="verif-pill pending">
                    <i class="flaticon-calendar-with-a-clock-time-tools icon-xs"></i>
                    Dibuat: {{ optional($audit->created_at)->format('d M Y H:i') ?? '—' }}
                </span>
                <span class="verif-pill {{ $audit->completed_at ? 'approved' : 'pending' }}">
                    <i class="flaticon2-check-mark icon-xs"></i>
                    Selesai: {{ optional($audit->completed_at)->format('d M Y H:i') ?? '—' }}
                </span>
                <span class="verif-pill warning">
                    <i class="flaticon-warning icon-xs"></i>
                    Risk Level: {{ strtoupper($application->risk_level ?? '—') }}
                </span>
            </div>
        </div>
    </div>
