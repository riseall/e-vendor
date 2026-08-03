@php
    $typeMap = [
        'yes_no' => [
            'label' => 'Yes / No',
            'class' => 'vnd-status--verified',
            'icon' => 'flaticon2-check-mark text-success',
        ],
        'multiple_choice' => [
            'label' => 'Pilihan Ganda',
            'class' => 'vnd-status--revision',
            'icon' => 'flaticon-list text-warning',
        ],
        'text' => [
            'label' => 'Teks Bebas',
            'class' => 'vnd-status--purple',
            'icon' => 'flaticon-edit text-info',
        ],
        'document' => [
            'label' => 'Unggah Dokumen',
            'class' => 'vnd-status--primary',
            'icon' => 'flaticon2-file text-primary',
        ],
    ];

    $typeInfo = $typeMap[$q->answer_type] ?? [
        'label' => $q->answer_type,
        'class' => 'vnd-status--info',
        'icon' => 'flaticon2-information',
    ];
@endphp

@if ($currentSection !== $q->section)
    <tr class="vnd-section-header-row">
        <td colspan="7" class="py-3 px-4 font-weight-bolder text-dark"
            style="background: #f1f5f9; border-top: 2px solid var(--vnd-border, #e2e8f0); border-bottom: 1.5px solid var(--vnd-border, #e2e8f0);">
            <div class="d-flex align-items-center" style="gap:.5rem;">
                <i class="flaticon2-folder text-primary" style="font-size:.9rem;"></i>
                <span class="text-uppercase"
                    style="font-size:.82rem; letter-spacing:0.5px;">{{ $q->section ?: 'Umum' }}</span>
            </div>
        </td>
    </tr>
@endif

<tr>
    <td class="text-center font-weight-bold vnd-cell-muted">
        {{ $q->order }}
    </td>
    <td>
        <div class="font-weight-bold text-dark mb-1" style="font-size:.88rem; line-height:1.4;">
            {!! nl2br(e($q->question)) !!}
        </div>
        @if ($q->answer_type === 'multiple_choice' && is_array($q->options))
            <div class="mt-1">
                @foreach ($q->options as $opt)
                    <span class="vnd-cat-chip">{{ $opt }}</span>
                @endforeach
            </div>
        @endif
    </td>
    <td>
        <span class="vnd-status {{ $typeInfo['class'] }}">
            <i class="{{ $typeInfo['icon'] }}" style="font-size:.6rem;"></i>
            {{ $typeInfo['label'] }}
        </span>
    </td>
    <td class="text-center font-weight-bolder text-dark" style="font-size:.88rem;">
        {{ $q->weight }}
    </td>
    <td class="text-center">
        @if ($q->is_required)
            <span class="vnd-tag vnd-tag--high">Ya</span>
        @else
            <span class="vnd-tag vnd-tag--muted">Tidak</span>
        @endif
    </td>
    <td class="text-center">
        @if ($q->is_active)
            <span class="vnd-status vnd-status--success">
                <i class="flaticon2-check-mark text-success" style="font-size:.6rem;"></i> Aktif
            </span>
        @else
            <span class="vnd-status vnd-status--rejected">
                <i class="flaticon2-cross text-danger" style="font-size:.6rem;"></i> Non-Aktif
            </span>
        @endif
    </td>
    <td class="text-right">
        <div class="d-inline-flex flex-wrap justify-content-end align-items-center" style="gap:.35rem;">
            <button type="button" class="vnd-btn-detail vnd-btn-detail--warning" title="Edit Pertanyaan"
                data-form="{{ json_encode($q) }}" onclick="openEditModal(this)">
                <i class="fas fa-edit text-warning" style="font-size:.9rem;"></i>
            </button>

            <form action="{{ route('questionnaire-form.questions.destroy', [$form->id, $q->id]) }}" method="POST"
                class="d-inline delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="vnd-btn-detail vnd-btn-detail--danger btn-delete-question"
                    data-question="{{ Str::limit($q->question, 40) }}" title="Hapus Pertanyaan">
                    <i class="fas fa-trash-alt text-danger" style="font-size:.9rem;"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
