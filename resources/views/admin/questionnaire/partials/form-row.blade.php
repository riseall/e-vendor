@php
    $materialTypeTags = [
        'bahan_baku' => 'vnd-tag--low',
        'bahan_kemas' => 'vnd-tag--medium',
    ];
    $materialTypeTag = $materialTypeTags[$form->material_type] ?? '';
@endphp

<tr>
    <td class="text-center font-weight-bold vnd-cell-muted">
        {{ $form->order }}
    </td>
    <td>
        <code>{{ $form->code }}</code>
    </td>
    <td>
        <div class="font-weight-bolder text-dark" style="font-size:.9rem;">
            {{ $form->name }}
        </div>
    </td>
    <td>
        <span class="vnd-tag {{ $materialTypeTag }}">
            {{ method_exists($form, 'materialTypeLabel') ? $form->materialTypeLabel() : ucfirst(str_replace('_', ' ', $form->material_type)) }}
        </span>
    </td>
    <td>
        <span class="text-dark-75 font-weight-bold" style="font-size:.83rem;">
            {{ $form->document_number ?? '-' }}
        </span>
    </td>
    <td>
        @if ($form->is_active)
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
            <a href="{{ route('questionnaire-form.questions', $form->id) }}"
                class="vnd-btn-detail vnd-btn-detail--primary" title="Kelola Daftar Pertanyaan">
                <i class="flaticon2-list-1 icon-sm text-white" style="font-size:.7rem;"></i> Pertanyaan
            </a>

            <button type="button" class="vnd-btn-detail vnd-btn-detail--warning" title="Edit Form Kuesioner"
                data-form="{{ json_encode($form) }}" onclick="openEditModal(this)">
                <i class="fas fa-edit text-warning" style="font-size:.9rem;"></i>
            </button>

            <form action="{{ route('questionnaire-form.destroy', $form->id) }}" method="POST"
                class="d-inline delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="vnd-btn-detail vnd-btn-detail--danger btn-delete-form"
                    data-form-name="{{ $form->name }}" title="Hapus Form Kuesioner">
                    <i class="fas fa-trash-alt text-danger" style="font-size:.9rem;"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
