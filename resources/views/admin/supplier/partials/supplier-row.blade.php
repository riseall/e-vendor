@php
    $vendorName = optional($app->general)->nama_perusahaan ?: (optional($app->user)->name ?: 'Vendor');
    $vendorEmail = optional($app->general)->email_perusahaan ?: (optional($app->user)->email ?: '-');
    $vendorPhone = optional($app->general)->telepon_perusahaan ?: (optional($app->user)->phone ?: '');
    $initial = strtoupper(substr(strip_tags($vendorName), 0, 1));

    $level = strtolower($app->risk_level ?: 'low');
    $riskMap = [
        'low' => ['class' => 'vnd-status--submitted', 'icon' => 'flaticon2-check-mark text-success', 'label' => 'LOW RISK'],
        'medium' => ['class' => 'vnd-status--revision', 'icon' => 'flaticon-warning text-warning', 'label' => 'MEDIUM RISK'],
        'high' => ['class' => 'vnd-status--rejected', 'icon' => 'flaticon-danger text-danger', 'label' => 'HIGH RISK'],
    ];
    $riskInfo = $riskMap[$level] ?? $riskMap['low'];

    $cats = isset($app->categories)
        ? $app->categories
            ->pluck('category_id')
            ->map(fn($id) => \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id)
            ->filter()
            ->values()
        : collect();
@endphp

<tr>
    <td class="vnd-cell-muted">
        {{ $suppliers->firstItem() + $loop->index }}
    </td>
    <td>
        <div class="d-flex flex-column align-items-start" style="gap:3px;">
            <span class="vnd-appnum">{{ $app->application_number ?: '-' }}</span>
            @if ($app->requalification_reason)
                <span class="badge badge-light-warning font-weight-bolder text-uppercase"
                    style="font-size:0.62rem; padding:2px 5px; letter-spacing:0.3px;">
                    <i class="flaticon2-reload mr-1" style="font-size:0.55rem;"></i> Rekualifikasi Dipicu
                </span>
            @endif
        </div>
    </td>
    <td>
        <div class="d-flex align-items-center" style="gap:.65rem;">
            <div class="vnd-avatar">{{ $initial }}</div>
            <div>
                <div class="vnd-vendor-name">{{ $vendorName }}</div>
                <div class="vnd-vendor-email">{{ $vendorEmail }}</div>
                @if ($vendorPhone)
                    <div style="font-size:.72rem; color:var(--vnd-muted);">{{ $vendorPhone }}</div>
                @endif
            </div>
        </div>
    </td>
    <td>
        @forelse ($cats as $cat)
            <span class="vnd-cat-chip">{{ $cat }}</span>
        @empty
            <span class="vnd-cell-muted">&mdash;</span>
        @endforelse
    </td>
    <td>
        <span class="vnd-status {{ $riskInfo['class'] }}">
            <i class="{{ $riskInfo['icon'] }}" style="font-size:.6rem;"></i>
            {{ $riskInfo['label'] }}
        </span>
    </td>
    <td>
        <div style="font-size:.83rem; font-weight:600; color:var(--vnd-ink);">
            {{ $app->approved_at ? $app->approved_at->format('d/m/Y') : '-' }}
        </div>
        @if ($app->valid_until)
            <div style="font-size:.72rem; color:var(--vnd-muted);">
                s/d {{ $app->valid_until->format('d/m/Y') }}
            </div>
        @endif
    </td>
    <td class="text-right">
        <div class="d-inline-flex flex-wrap justify-content-end align-items-center" style="gap:.4rem;">
            <form action="{{ route('qa.rekualifikasi.trigger', $app->id) }}" method="POST"
                class="d-inline form-trigger-rekualifikasi">
                @csrf
                <input type="hidden" name="reason" value="qa_trigger">
                <button type="button" class="vnd-btn-detail vnd-btn-detail--warning btn-trigger-rekualifikasi"
                    data-vendor-name="{{ $vendorName }}" title="Picu Rekualifikasi Vendor">
                    <i class="flaticon2-reload icon-sm" style="font-size:.7rem;"></i> Picu Rekualifikasi
                </button>
            </form>

            <a href="{{ route('verifikasi.show', $app->id) }}" class="vnd-btn-detail" title="Lihat Detail Permohonan">
                <i class="flaticon-eye icon-sm text-primary" style="font-size:.7rem;"></i> Detail
            </a>
        </div>
    </td>
</tr>
