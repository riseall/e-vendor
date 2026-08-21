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
                    @if ($statusCompany)
                        <span class="badge badge-secondary"
                            style="font-size:0.65rem; padding:1px 4px;">{{ strtoupper($statusCompany) }}</span>
                    @endif
                </div>
                <div class="vnd-vendor-email">{{ $vendorEmail }}</div>
                <div class="d-flex align-items-center flex-wrap mt-1"
                    style="gap:6px; font-size:.72rem; color:var(--vnd-muted);">
                    @if ($vendorPhone)
                        <span>{{ $vendorPhone }}</span>
                    @endif
                    @if ($qadCode)
                        <span class="badge badge-light-primary font-weight-bold" style="font-size:0.68rem; padding:2px 6px;" title="Kode Supplier QAD">
                            <i class="fas fa-barcode text-primary mr-1"></i>QAD: {{ $qadCode }}
                        </span>
                    @else
                        <span class="badge badge-light-warning font-weight-bold" style="font-size:0.68rem; padding:2px 6px;">
                            <i class="fas fa-exclamation-triangle text-warning mr-1"></i>Belum Ada Kode QAD
                        </span>
                    @endif
                    @if ($supplierType)
                        <span class="badge badge-light-info font-weight-bold" style="font-size:0.68rem; padding:2px 6px;" title="Supplier Type">
                            <i class="fas fa-tag text-info mr-1"></i>{{ $supplierType }}
                        </span>
                    @endif
                    @if ($currency)
                        <span class="badge badge-light-success font-weight-bold" style="font-size:0.68rem; padding:2px 6px;" title="Currency Transaksi">
                            <i class="fas fa-money-bill-wave text-success mr-1"></i>{{ $currency }}
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
        <div>
            @if ($app->requalification_reason && !$app->qualification)
                <span class="badge badge-light-warning font-weight-bolder text-uppercase"
                    style="font-size:0.62rem; padding:3px 8px;">
                    <i class="fas fa-redo text-warning mr-1" style="font-size:0.55rem;"></i> Rekualifikasi Dipicu
                </span>
            @elseif ($isExpired)
                <span class="badge badge-light-danger font-weight-bolder text-uppercase"
                    style="font-size:0.62rem; padding:3px 8px;">
                    <i class="fas fa-exclamation-circle text-danger mr-1" style="font-size:0.55rem;"></i> Kadaluarsa
                </span>
            @elseif ($isExpiring)
                <span class="badge badge-light-warning font-weight-bolder text-uppercase"
                    style="font-size:0.62rem; padding:3px 8px;">
                    <i class="fas fa-exclamation-triangle text-warning mr-1" style="font-size:0.55rem;"></i> Expire ({{ $daysRemaining }} hr)
                </span>
            @else
                <span class="badge badge-light-success font-weight-bolder text-uppercase"
                    style="font-size:0.62rem; padding:3px 8px;">
                    <i class="fas fa-check text-success mr-1" style="font-size:0.55rem;"></i> Valid & Aktif
                </span>
            @endif
        </div>
    </td>
    <td class="text-right" style="white-space:nowrap;">
        <div class="d-inline-flex align-items-center justify-content-end flex-nowrap" style="gap:.35rem;">
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

            <button type="button" class="vnd-btn-detail btn-input-qad-code" data-toggle="modal"
                data-target="#modalInputQad{{ $app->id }}" title="Input / Edit Kode Supplier QAD">
                <i class="fas fa-barcode icon-sm text-info" style="font-size:.7rem;"></i> Kode QAD
            </button>

            <a href="{{ route('verifikasi.show', $app->id) }}" class="vnd-btn-detail"
                title="Lihat Profil / Permohonan">
                <i class="fas fa-eye icon-sm text-primary" style="font-size:.7rem;"></i> Detail
            </a>
        </div>

        {{-- Modal Input Data Master Supplier QAD --}}
        <div class="modal fade text-left" id="modalInputQad{{ $app->id }}" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalInputQadLabel{{ $app->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0" style="border-radius:12px;">
                    <form action="{{ route('supplier.update-qad', $app->id) }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom py-3 px-5 bg-light">
                            <h6 class="modal-title font-weight-bolder text-dark mb-0" id="modalInputQadLabel{{ $app->id }}">
                                <i class="fas fa-barcode text-primary mr-2"></i>Data Master Supplier QAD
                            </h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body p-5">
                            <div class="form-group mb-3">
                                <label class="font-weight-bolder text-dark small mb-1">
                                    Kode Supplier (QAD): <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white"><i class="fas fa-barcode text-primary"></i></span>
                                    </div>
                                    <input type="text" name="qad_supplier_code" class="form-control font-weight-bold" value="{{ $qadCode }}" placeholder="Contoh: V-00123 / 100234" required>
                                </div>
                                <span class="form-text text-muted small mt-1">Kode supplier resmi yang terdaftar di QAD ERP.</span>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bolder text-dark small mb-1">
                                    Supplier Type:
                                </label>
                                <select name="supplier_type" class="form-control form-control-sm font-weight-bold">
                                    <option value="">-- Pilih Supplier Type --</option>
                                    @php
                                        $types = [
                                            'Bahan Baku (API)',
                                            'Eksipien',
                                            'Bahan Kemas',
                                            'Produk Jadi Farmasi & Alkes',
                                            'Varia Teknik & Umum',
                                            'Reagen & Barang Investasi',
                                            'Jasa Transporter / Forwarder / PPJK',
                                            'Jasa Kontraktor & Perbaikan',
                                            'Jasa Pengujian & Kalibrasi',
                                            'Jasa Facility Service',
                                            'Jasa Konsultan & Pelatihan',
                                            'Jasa Agency Advertising',
                                            'Lainnya'
                                        ];
                                    @endphp
                                    @foreach($types as $t)
                                        <option value="{{ $t }}" {{ $supplierType == $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bolder text-dark small mb-1">
                                    Currency (Mata Uang Transaksi):
                                </label>
                                <select name="currency" class="form-control form-control-sm font-weight-bold">
                                    @php
                                        $currencies = [
                                            'IDR' => 'IDR - Indonesian Rupiah (Rp)',
                                            'USD' => 'USD - US Dollar ($)',
                                            'EUR' => 'EUR - Euro (€)',
                                            'SGD' => 'SGD - Singapore Dollar (S$)',
                                            'JPY' => 'JPY - Japanese Yen (¥)',
                                            'GBP' => 'GBP - British Pound (£)',
                                            'CNY' => 'CNY - Chinese Yuan (¥)',
                                            'AUD' => 'AUD - Australian Dollar (A$)',
                                            'CHF' => 'CHF - Swiss Franc (CHF)',
                                        ];
                                    @endphp
                                    @foreach($currencies as $code => $label)
                                        <option value="{{ $code }}" {{ $currency == $code ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer py-2 px-5 border-top bg-light">
                            <button type="button" class="btn btn-sm btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary font-weight-bold">
                                <i class="fas fa-save mr-1"></i>Simpan Data QAD
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </td>
</tr>
