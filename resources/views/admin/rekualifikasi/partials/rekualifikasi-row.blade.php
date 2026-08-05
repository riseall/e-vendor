@php
    $vName = optional($app->general)->nama_perusahaan ?: (optional($app->user)->name ?: 'Vendor');
    $vEmail = optional($app->general)->email_perusahaan ?: (optional($app->user)->email ?: '-');
    $initial = strtoupper(substr(strip_tags($vName), 0, 1));

    $reasonMap = [
        'vendor_initiative' => ['label' => 'Inisiatif Vendor (Edit Profil)', 'class' => 'vnd-tag--medium'],
        'expired_period'    => ['label' => 'Masa Berlaku Kadaluarsa (<= 60 Hari)', 'class' => 'vnd-tag--high'],
        'cdob_expiry'       => ['label' => 'Sertifikat CDOB Expired', 'class' => 'vnd-tag--high'],
        'eval_score_drop'   => ['label' => 'Penurunan Skor Evaluasi', 'class' => 'vnd-tag--high'],
        'qa_trigger'        => ['label' => 'Permintaan Pengadaan / QA', 'class' => 'vnd-tag--on-desk'],
    ];
    $reasonInfo = $reasonMap[$app->requalification_reason] ?? [
        'label' => $app->requalification_reason ?: 'Rekualifikasi',
        'class' => 'vnd-tag--muted',
    ];

    $statusMap = [
        'draft'         => ['label' => 'Draft', 'class' => 'vnd-status--warning', 'icon' => 'flaticon2-hourglass text-warning'],
        'submitted'     => ['label' => 'Menunggu Verifikasi', 'class' => 'vnd-status--primary', 'icon' => 'flaticon2-check-mark text-primary'],
        'verified'      => ['label' => 'Terverifikasi', 'class' => 'vnd-status--primary', 'icon' => 'flaticon2-check-mark text-primary'],
        'approved'      => ['label' => 'Disetujui (Approved)', 'class' => 'vnd-status--success', 'icon' => 'flaticon2-check-mark text-success'],
        'rejected'      => ['label' => 'Ditolak', 'class' => 'vnd-status--danger', 'icon' => 'flaticon2-cross text-danger'],
        'need_revision' => ['label' => 'Perlu Revisi', 'class' => 'vnd-status--warning', 'icon' => 'flaticon-warning text-warning'],
    ];
    $statusInfo = $statusMap[$app->status] ?? [
        'label' => ucfirst(str_replace('_', ' ', $app->status)),
        'class' => 'vnd-status--info',
        'icon'  => 'flaticon2-information',
    ];

    if ($app->status === 'approved' && $app->requalification_reason && !$app->qualification) {
        $statusInfo = [
            'label' => 'Rekualifikasi Dipicu',
            'class' => 'vnd-status--warning',
            'icon'  => 'flaticon2-reload text-warning',
        ];
    }
@endphp

<tr>
    <td class="vnd-cell-muted">
        {{ $applications->firstItem() + $loop->index }}
    </td>
    <td>
        <div class="d-flex flex-column align-items-start" style="gap:3px;">
            <span class="vnd-appnum">{{ $app->application_number ?: 'Draft' }}</span>
            <span style="font-size:.7rem; color:var(--vnd-muted);">ID: {{ $app->id }}</span>
        </div>
    </td>
    <td>
        <div class="d-flex align-items-center" style="gap:.65rem;">
            <div class="vnd-avatar">{{ $initial }}</div>
            <div>
                <div class="vnd-vendor-name">{{ $vName }}</div>
                <div class="vnd-vendor-email">{{ $vEmail }}</div>
            </div>
        </div>
    </td>
    <td>
        <span class="vnd-tag {{ $reasonInfo['class'] }}">
            {{ $reasonInfo['label'] }}
        </span>
    </td>
    <td>
        <span class="vnd-status {{ $statusInfo['class'] }}">
            <i class="{{ $statusInfo['icon'] }}" style="font-size:.6rem;"></i>
            {{ $statusInfo['label'] }}
        </span>
    </td>
    <td>
        <div style="font-size:.82rem; font-weight:600; color:var(--vnd-ink);">
            {{ $app->created_at ? $app->created_at->format('d/m/Y') : '-' }}
        </div>
        <div style="font-size:.72rem; color:var(--vnd-muted);">
            {{ $app->created_at ? $app->created_at->format('H:i') : '' }}
        </div>
    </td>
    <td class="text-right">
        <div class="d-inline-flex justify-content-end" style="gap:4px;">
            @if (auth()->user()->role === 'supplier' && $app->status === 'draft')
                <a href="{{ route('registrasi.index') }}" class="vnd-btn-detail vnd-btn-detail--warning"
                    title="Edit Form Rekualifikasi">
                    <i class="flaticon2-edit icon-sm text-warning" style="font-size:.7rem;"></i> Edit Form
                </a>
            @else
                <a href="{{ route('registrasi.tracking', $app->application_number ?: $app->id) }}"
                    class="vnd-btn-detail" title="Lihat Progress & Tracking">
                    <i class="flaticon-eye icon-sm text-primary" style="font-size:.7rem;"></i> Detail
                </a>
            @endif
        </div>
    </td>
</tr>
