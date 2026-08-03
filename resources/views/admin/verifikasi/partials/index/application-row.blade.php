@php($meta = $application->verification_meta)

<tr>
    <td class="vnd-cell-muted">
        {{ $applications->firstItem() + $loop->index }}
    </td>

    <td>
        <div class="d-flex flex-column align-items-start" style="gap:4px;">
            <span class="vnd-appnum">{{ $application->application_number ?? '-' }}</span>
            @if ($application->isRekualifikasi())
                <span class="badge badge-warning font-weight-bolder text-uppercase"
                    style="font-size:0.65rem; padding:3px 6px; letter-spacing:0.3px;">
                    <i class="fas fa-sync-alt mr-1 text-dark" style="font-size:0.6rem;"></i> Rekualifikasi
                </span>
            @else
                <span class="badge badge-light-info font-weight-bolder text-uppercase"
                    style="font-size:0.65rem; padding:3px 6px; letter-spacing:0.3px;">
                    Registrasi Baru
                </span>
            @endif
        </div>
    </td>

    <td>
        <div class="d-flex align-items-center" style="gap:.65rem;">
            <div class="vnd-avatar">{{ $meta['pic_initials'] }}</div>
            <div>
                <div class="font-weight-bold text-dark" style="font-size:.84rem;">
                    {{ $meta['pic_name'] ?: '-' }}
                </div>
                <div style="font-size:.72rem; color:var(--vnd-muted);">
                    {{ $meta['pic_email'] ?: '-' }}
                </div>
                @if ($meta['pic_phone'])
                    <div style="font-size:.72rem; color:var(--vnd-muted);">
                        {{ $meta['pic_phone'] }}
                    </div>
                @endif
            </div>
        </div>
    </td>

    <td>
        <div class="font-weight-bold" style="font-size:.84rem; color:var(--vnd-ink);">
            {{ optional($application->general)->nama_perusahaan ?? '-' }}
        </div>
        <div style="font-size:.72rem; color:var(--vnd-muted);">
            NPWP: {{ optional($application->general)->npwp ?? '-' }}
        </div>
    </td>

    <td>
        <span class="vnd-status {{ $meta['status_class'] }}">
            <i class="{{ $meta['status_icon'] }}"></i>
            {{ $meta['status_label'] }}
        </span>

        @if ($meta['is_verification_in_progress'])
            <div class="vnd-progress-note">
                <div class="vnd-progress-text">
                    <span>{{ $meta['verification_processed'] }}/{{ $meta['verification_total'] }} selesai</span>
                    <span>{{ $meta['verification_pending'] }} sisa</span>
                </div>
                <div class="vnd-progress-track">
                    <div class="vnd-progress-fill"
                        style="width: {{ min(100, max(0, $meta['verification_progress_pct'])) }}%;"></div>
                </div>
            </div>
        @elseif ($meta['is_revision_resubmitted'])
            <div class="vnd-progress-note">
                <div class="vnd-progress-text">
                    <span>Revisi ke-{{ (int) $application->revision_count }}</span>
                </div>
            </div>
        @endif
    </td>

    <td>
        @if ($application->submitted_at)
            <div style="font-size:.83rem; font-weight:600; color:var(--vnd-ink);">
                {{ $application->submitted_at->format('d/m/Y') }}
            </div>
            <div style="font-size:.72rem; color:var(--vnd-muted);">
                {{ $application->submitted_at->format('H:i') }}
            </div>
            @if ($meta['is_revision_resubmitted'] && $application->revision_submitted_at)
                <div class="mt-1" style="font-size:.7rem; color:#b45309; font-weight:700;">
                    Revisi: {{ $application->revision_submitted_at->format('d/m/Y H:i') }}
                </div>
            @endif
        @else
            <span style="color:var(--vnd-muted);">-</span>
        @endif
    </td>

    <td>
        @if ($meta['deadline'])
            <div style="font-size:.83rem; font-weight:600; color:var(--vnd-ink);">
                {{ $meta['deadline']->format('d/m/Y') }}
            </div>
            @if ($meta['deadline_text'])
                <span class="vnd-hk {{ $meta['deadline_class'] }}">{{ $meta['deadline_text'] }}</span>
            @endif
        @else
            <span style="color:var(--vnd-muted);">-</span>
        @endif
    </td>

    <td class="text-right">
        <a href="{{ route('verifikasi.show', $application) }}" class="vnd-btn-detail">
            <i class="flaticon-eye icon-sm text-primary"></i>
            {{ $meta['action_label'] }}
        </a>
    </td>
</tr>
