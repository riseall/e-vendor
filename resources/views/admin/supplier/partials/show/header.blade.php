@php
    $vendorName = optional($general)->nama_perusahaan ?: (optional($app->user)->name ?: 'Vendor');
    $initial = strtoupper(substr(strip_tags($vendorName), 0, 1));

    $qualification = $app->qualification;
    $riskLevel = $qualification
        ? strtolower($qualification->risk_level)
        : ($app->risk_level
            ? strtolower($app->risk_level)
            : null);
    $riskScore = $qualification ? number_format($qualification->total_score, 0, ',', '.') : '-';

    $latestAnnual = $annualEvaluations->first();
    $latestGrade = $latestAnnual ? ($latestAnnual->category ?: '-') : '-';
    $latestAnnualScore = $latestAnnual ? number_format($latestAnnual->final_score, 1, ',', '.') : null;

    $now = now();
    $validityText = 'Tanpa Batas';
    $validityClass = 'text-success';
    if ($app->valid_until) {
        $validityText = $app->valid_until->format('d/m/Y');
        if ($app->valid_until->isPast()) {
            $validityClass = 'text-danger font-weight-bold';
            $validityText .= ' (Kadaluarsa)';
        } elseif ($now->diffInDays($app->valid_until, false) <= 60) {
            $validityClass = 'text-warning font-weight-bold';
            $validityText .= ' (Segera Habis)';
        }
    }
@endphp

<div class="card verif-header-card mb-6 shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
    <div class="card-body py-5 px-7 bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            {{-- Vendor Identity --}}
            <div class="d-flex align-items-center mb-4 mb-xl-0">
                <a href="{{ route('supplier.index') }}" class="btn btn-icon btn-sm btn-light-primary mr-3 flex-shrink-0"
                    title="Kembali ke Daftar Supplier">
                    <i class="fas fa-arrow-left"></i>
                </a>

                <div class="d-flex align-items-center justify-content-center mr-3 rounded-circle bg-light-primary text-primary font-weight-bolder flex-shrink-0"
                    style="width: 48px; height: 48px; font-size: 1.3rem; border: 2px solid #c7d2fe;">
                    {{ $initial }}
                </div>

                <div>
                    <div class="text-muted font-size-xs font-weight-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">
                        Supplier Terekomendasi &middot; {{ $app->application_number ?? '-' }}
                    </div>
                    <h3 class="font-weight-bolder text-dark mb-0 font-size-h4">
                        {{ $vendorName }}
                    </h3>
                </div>
            </div>

            {{-- Stat KPI Boxes --}}
            <div class="d-flex flex-wrap align-items-center mt-3 mt-xl-0" style="gap:10px;">
                {{-- Stat 1: Risk Assessment --}}
                <div class="stat-box" style="min-width: 120px;">
                    <div class="font-size-xs text-muted font-weight-bold text-uppercase mb-1">Risk Level</div>
                    <div>
                        @if ($riskLevel === 'low')
                            <span class="badge badge-light-success font-weight-bolder" style="font-size:0.8rem;">
                                <i class="fas fa-shield-alt text-success mr-1"></i> Low Risk
                            </span>
                        @elseif ($riskLevel === 'medium')
                            <span class="badge badge-light-warning font-weight-bolder" style="font-size:0.8rem;">
                                <i class="fas fa-exclamation-triangle text-warning mr-1"></i> Med Risk
                            </span>
                        @elseif ($riskLevel === 'high')
                            <span class="badge badge-light-danger font-weight-bolder" style="font-size:0.8rem;">
                                <i class="fas fa-times-circle text-danger mr-1"></i> High Risk
                            </span>
                        @else
                            <span class="text-muted font-size-sm font-weight-bold">N/A</span>
                        @endif
                    </div>
                    <div class="font-size-xs text-muted mt-1">Skor: {{ $riskScore }}</div>
                </div>

                {{-- Stat 2: Audit Status --}}
                <div class="stat-box" style="min-width: 120px;">
                    <div class="font-size-xs text-muted font-weight-bold text-uppercase mb-1">Status Audit</div>
                    <div>
                        @if ($app->audits->count() > 0)
                            @php
                                $lastAudit = $app->audits->first();
                            @endphp
                            @if ($lastAudit->status === \App\Models\VendorAudit::STATUS_COMPLETED)
                                <span class="badge badge-light-success font-weight-bolder"
                                    style="font-size:0.8rem; padding: 4px 10px; border-radius: 6px;">
                                    <i class="fas fa-check-circle mr-1 text-success"></i> Selesai
                                </span>
                            @else
                                <span class="badge badge-light-warning font-weight-bolder"
                                    style="font-size:0.8rem; padding: 4px 10px; border-radius: 6px;">
                                    <i class="fas fa-clock mr-1 text-warning"></i>
                                    {{ ucfirst(str_replace('_', ' ', $lastAudit->status)) }}
                                </span>
                            @endif
                        @else
                            <span class="badge badge-light-info font-weight-bolder"
                                style="font-size:0.8rem; padding: 4px 10px; border-radius: 6px;">
                                Bebas Audit
                            </span>
                        @endif
                    </div>
                    <div class="font-size-xs text-muted mt-1">{{ $app->audits->count() }} Audit Recorded</div>
                </div>

                {{-- Stat 3: Evaluasi Kinerja --}}
                <div class="stat-box" style="min-width: 120px;">
                    <div class="font-size-xs text-muted font-weight-bold text-uppercase mb-1">Evaluasi Kinerja</div>
                    <div>
                        @if ($latestGrade !== '-')
                            @php
                                $gradeUpper = strtoupper($latestGrade);
                                if (in_array($gradeUpper, ['A', 'BAIK', 'SANGAT BAIK'])) {
                                    $gradeBadgeClass = 'badge-light-success text-success';
                                } elseif (in_array($gradeUpper, ['B', 'CUKUP'])) {
                                    $gradeBadgeClass = 'badge-light-primary text-primary';
                                } elseif (in_array($gradeUpper, ['C'])) {
                                    $gradeBadgeClass = 'badge-light-warning text-warning';
                                } else {
                                    $gradeBadgeClass = 'badge-light-danger text-danger';
                                }
                            @endphp
                            <span class="badge {{ $gradeBadgeClass }} font-weight-bolder"
                                style="font-size:0.8rem; padding: 4px 10px; border-radius: 6px;">
                                Grade {{ $latestGrade }}
                            </span>
                        @else
                            <span class="text-muted font-size-sm font-weight-bold">Belum Ada</span>
                        @endif
                    </div>
                    <div class="font-size-xs text-muted mt-1">
                        {{ $latestAnnualScore ? 'Skor ' . $latestAnnualScore : $monthlyEvaluations->count() . ' Bulan Data' }}
                    </div>
                </div>

                {{-- Stat 4: Masa Berlaku --}}
                <div class="stat-box" style="min-width: 130px;">
                    <div class="font-size-xs text-muted font-weight-bold text-uppercase mb-1">Masa Berlaku</div>
                    <div class="font-weight-bolder font-size-sm {{ $validityClass }}" style="line-height: 1.5;">
                        {{ $validityText }}
                    </div>
                    <div class="font-size-xs text-muted mt-1">
                        Approved: {{ $app->approved_at ? $app->approved_at->format('d/m/Y') : '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
