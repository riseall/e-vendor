@php
    $vendorName = optional($app->general)->nama_perusahaan ?: (optional($app->user)->name ?: 'Vendor');
    $vendorEmail = optional($app->general)->email_perusahaan ?: (optional($app->user)->email ?: '-');
    $vendorPhone = optional($app->general)->telepon_perusahaan ?: (optional($app->user)->phone ?: '');
    $vendorNpwp = optional($app->general)->npwp;
    $statusCompany = optional($app->general)->status_perusahaan;
    $initial = strtoupper(substr(strip_tags($vendorName), 0, 1));

    $level = $app->risk_level ? strtolower($app->risk_level) : null;
    $riskMap = [
        'low' => [
            'class' => 'vnd-status--success',
            'icon' => 'flaticon2-check-mark text-success',
            'label' => 'LOW RISK',
        ],
        'medium' => [
            'class' => 'vnd-status--revision',
            'icon' => 'flaticon-warning text-warning',
            'label' => 'MEDIUM RISK',
        ],
        'high' => ['class' => 'vnd-status--rejected', 'icon' => 'flaticon-danger text-danger', 'label' => 'HIGH RISK'],
    ];
    $riskInfo = $level && isset($riskMap[$level]) ? $riskMap[$level] : null;

    $cats = isset($app->categories)
        ? $app->categories
            ->pluck('category_id')
            ->map(function ($id) {
                return \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id;
            })
            ->filter()
            ->values()
        : collect();

    $qualification = $app->qualification;

    $now = now();
    $isValid = false;
    $isExpiring = false;
    $isExpired = false;
    $daysRemaining = null;

    if ($app->valid_until) {
        if ($app->valid_until->isPast()) {
            $isExpired = true;
        } else {
            $daysRemaining = $now->diffInDays($app->valid_until, false);
            if ($daysRemaining <= 60) {
                $isExpiring = true;
            } else {
                $isValid = true;
            }
        }
    } else {
        $isValid = true;
    }
    $qadCode = $app->qad_supplier_code ?: (optional($app->general)->qad_supplier_code ?: '');
    $supplierType = $app->supplier_type ?: (optional($app->general)->supplier_type ?: '');
    $currency = $app->currency ?: (optional($app->general)->currency ?: 'IDR');
@endphp

<tr>
    <td class="vnd-cell-muted">
        {{ $suppliers->firstItem() + $loop->index }}
    </td>
    <td>
        <div class="d-flex align-items-center" style="gap:.65rem;">
            <div class="vnd-avatar">{{ $initial }}</div>
            <div>
                <div class="d-flex align-items-center flex-wrap" style="gap:4px;">
                    <span class="vnd-vendor-name">{{ $vendorName }}</span>
                </div>
                <div class="d-flex align-items-center flex-wrap mt-1"
                    style="gap:6px; font-size:.72rem; color:var(--vnd-muted);">
                    @if ($qadCode)
                        <span class="badge badge-light-primary font-weight-bold"
                            style="font-size:0.68rem; padding:2px 6px;" title="Kode Supplier QAD">
                            <i class="fas fa-barcode text-primary mr-1 icon-xs"></i>QAD: {{ $qadCode }}
                        </span>
                    @else
                        <span class="badge badge-light-warning font-weight-bold"
                            style="font-size:0.68rem; padding:2px 6px;">
                            <i class="fas fa-exclamation-triangle text-warning mr-1 icon-xs"></i>Belum Ada Kode QAD
                        </span>
                    @endif
                    @if ($supplierType)
                        <span class="badge badge-light-info font-weight-bold"
                            style="font-size:0.68rem; padding:2px 6px;" title="Supplier Type">
                            <i class="fas fa-tag text-info mr-1 icon-xs"></i>Type: {{ $supplierType }}
                        </span>
                    @endif
                    @if ($currency)
                        <span class="badge badge-light-success font-weight-bold"
                            style="font-size:0.68rem; padding:2px 6px;" title="Currency">
                            <i class="fas fa-coins text-success mr-1 icon-xs"></i>{{ $currency }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </td>
    <td>
        @forelse ($cats as $cat)
            <span class="vnd-cat-chip mb-1 display-inline-block">{{ $cat }}</span>
        @empty
            <span class="vnd-cell-muted">&mdash;</span>
        @endforelse
    </td>
    <td>
        <div class="d-flex flex-column align-items-start" style="gap:3px;">
            @if ($qualification)
                <div style="font-size:.85rem; font-weight:700; color:var(--vnd-ink);">
                    Skor: {{ number_format($qualification->total_score, 0, ',', '.') }}
                </div>
            @else
                <span class="vnd-cell-muted" style="font-size:.75rem;">Skor &mdash;</span>
            @endif
            @if ($riskInfo)
                <span class="vnd-status {{ $riskInfo['class'] }}" style="padding:2px 8px; font-size:.68rem;">
                    <i class="{{ $riskInfo['icon'] }}" style="font-size:.55rem;"></i>
                    {{ $riskInfo['label'] }}
                </span>
            @else
                <span class="vnd-status vnd-status--info" style="padding:2px 8px; font-size:.68rem;">
                    Belum Evaluasi Risk
                </span>
            @endif
        </div>
    </td>
    <td>
        <div style="font-size:.82rem; font-weight:600; color:var(--vnd-ink);">
            Approved: {{ $app->approved_at ? $app->approved_at->format('d/m/Y') : '-' }}
        </div>
        <div style="font-size:.74rem; color:var(--vnd-muted);" class="mb-1">
            Valid s/d: {{ $app->valid_until ? $app->valid_until->format('d/m/Y') : 'Tanpa Batas' }}
        </div>
    </td>
    <td class="text-right" style="white-space:nowrap;">
        <div class="d-inline-flex align-items-center justify-content-end flex-nowrap" style="gap:.35rem;">
            @can('rekualifikasi-initiate')
                @if ($app->requalification_reason && !$app->qualification)
                    <button type="button" class="vnd-btn-detail vnd-btn-detail--warning btn-trigger-rekualifikasi"
                        data-url="{{ route('rekualifikasi.trigger', $app->id) }}" data-vendor-name="{{ $vendorName }}"
                        title="Rekualifikasi sedang dipicu/berjalan. Klik untuk mengubah alasan.">
                        <i class="fas fa-redo icon-sm" style="font-size:.7rem;"></i> Rekualifikasi Dipicu
                    </button>
                @else
                    <button type="button" class="vnd-btn-detail vnd-btn-detail--warning btn-trigger-rekualifikasi"
                        data-url="{{ route('rekualifikasi.trigger', $app->id) }}" data-vendor-name="{{ $vendorName }}"
                        title="Picu Rekualifikasi Manual Vendor">
                        <i class="fas fa-redo icon-sm" style="font-size:.7rem;"></i> Picu Rekualifikasi
                    </button>
                @endif
            @endcan

            @can('supplier-edit-qad')
                <button type="button" class="vnd-btn-detail vnd-btn-detail--info btn-input-qad-code"
                    data-url="{{ route('supplier.update-qad', $app->id) }}"
                    data-vendor-name="{{ $vendorName }}"
                    data-qad-code="{{ $qadCode }}"
                    data-supplier-type="{{ $supplierType }}"
                    data-currency="{{ $currency }}"
                    title="Input / Edit Data Master Supplier QAD">
                    <i class="fas fa-barcode icon-sm text-info" style="font-size:.7rem;"></i> Kode QAD
                </button>
            @endcan

            @canany(['verifikasi-list', 'verifikasi-detail'])
                <a href="{{ route('verifikasi.show', $app->id) }}" class="vnd-btn-detail"
                    title="Lihat Profil / Permohonan">
                    <i class="fas fa-eye icon-sm text-primary" style="font-size:.7rem;"></i> Detail
                </a>
            @endcanany
        </div>
    </td>
</tr>
