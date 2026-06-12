@php($meta = $application->verification_meta)

<tr>
    <td>
        <span class="vp-appnum">{{ $application->application_number ?? '-' }}</span>
    </td>

    <td>
        <div class="d-flex align-items-center" style="gap:.65rem;">
            <div class="vp-avatar">{{ $meta['pic_initials'] }}</div>
            <div>
                <div class="font-weight-bold text-dark" style="font-size:.84rem;">
                    {{ $meta['pic_name'] ?: '-' }}
                </div>
                <div style="font-size:.72rem; color:var(--vp-muted);">
                    {{ $meta['pic_email'] ?: '-' }}
                </div>
                @if ($meta['pic_phone'])
                    <div style="font-size:.72rem; color:var(--vp-muted);">
                        {{ $meta['pic_phone'] }}
                    </div>
                @endif
            </div>
        </div>
    </td>

    <td>
        <div class="font-weight-bold" style="font-size:.84rem; color:var(--vp-ink);">
            {{ optional($application->general)->nama_perusahaan ?? '-' }}
        </div>
        <div style="font-size:.72rem; color:var(--vp-muted);">
            NPWP: {{ optional($application->general)->npwp ?? '-' }}
        </div>
    </td>

    <td>
        <span class="vp-badge {{ $meta['status_class'] }}">
            <i class="{{ $meta['status_icon'] }}"></i>
            {{ $meta['status_label'] }}
        </span>

        @if ($meta['is_verification_in_progress'])
            <div class="vp-progress-note">
                <div class="vp-progress-text">
                    <span>{{ $meta['verification_processed'] }}/{{ $meta['verification_total'] }} selesai</span>
                    <span>{{ $meta['verification_pending'] }} sisa</span>
                </div>
                <div class="vp-progress-track">
                    <div class="vp-progress-fill"
                        style="width: {{ min(100, max(0, $meta['verification_progress_pct'])) }}%;"></div>
                </div>
            </div>
        @elseif ($meta['is_revision_resubmitted'])
            <div class="vp-progress-note">
                <div class="vp-progress-text">
                    <span>Revisi ke-{{ (int) $application->revision_count }}</span>
                </div>
            </div>
        @endif
    </td>

    <td>
        @if ($application->submitted_at)
            <div style="font-size:.83rem; font-weight:600; color:var(--vp-ink);">
                {{ $application->submitted_at->format('d/m/Y') }}
            </div>
            <div style="font-size:.72rem; color:var(--vp-muted);">
                {{ $application->submitted_at->format('H:i') }}
            </div>
            @if ($meta['is_revision_resubmitted'] && $application->revision_submitted_at)
                <div class="mt-1" style="font-size:.7rem; color:#b45309; font-weight:700;">
                    Revisi: {{ $application->revision_submitted_at->format('d/m/Y H:i') }}
                </div>
            @endif
        @else
            <span style="color:var(--vp-muted);">-</span>
        @endif
    </td>

    <td>
        @if ($meta['deadline'])
            <div style="font-size:.83rem; font-weight:600; color:var(--vp-ink);">
                {{ $meta['deadline']->format('d/m/Y') }}
            </div>
            @if ($meta['deadline_text'])
                <span class="vp-hk {{ $meta['deadline_class'] }}">{{ $meta['deadline_text'] }}</span>
            @endif
        @else
            <span style="color:var(--vp-muted);">-</span>
        @endif
    </td>

    <td class="text-right">
        <a href="{{ route('verifikasi.show', $application) }}" class="vp-btn-detail">
            <i class="flaticon-eye" style="font-size:.75rem;"></i>
            {{ $meta['action_label'] }}
        </a>
    </td>
</tr>
