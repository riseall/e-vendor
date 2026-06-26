@props([
    'url' => null,
    'label' => null,
    'title' => null,
    'previewIconClass' => 'far fa-eye',
    'compact' => false,
])

@php
    $title = $title ?? ($label ?? 'Preview Dokumen');
    $label = $label ?? $title;
@endphp

@if ($url)
    @if ($compact)
        <button type="button" class="btn btn-icon btn-xs btn-light-primary btn-preview-doc preview-doc-action"
            data-url="{{ $url }}" data-title="{{ $title }}" aria-label="{{ $title }}"
            title="{{ $title }}">
            <i class="{{ $previewIconClass }}"></i>
            <span class="sr-only">{{ $title }}</span>
        </button>
    @else
        <div class="verification-document-entry preview-doc-entry">
            <div class="verification-document-info preview-doc-info">
                <span class="verification-document-icon preview-doc-icon">
                    <i class="flaticon2-document icon-sm"></i>
                </span>
                <span class="verification-document-label preview-doc-label">
                    {{ $label }}
                </span>
            </div>

            <button type="button" class="btn btn-icon btn-xs btn-light-primary btn-preview-doc preview-doc-action"
                data-url="{{ $url }}" data-title="{{ $title }}" aria-label="{{ $title }}"
                title="{{ $title }}">
                <i class="{{ $previewIconClass }}"></i>
                <span class="sr-only">{{ $title }}</span>
            </button>
        </div>
    @endif
@endif
