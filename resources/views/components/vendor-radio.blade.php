@props([
    'name',
    'label' => '',
    'options' => [],
    'selected' => '',
    'readonly' => false,
    'required' => false,
    'radioClass' => '',
])

@php $hasRevision = isset($revisionNotes) && isset($revisionNotes[$name]); @endphp

<div class="form-group mb-2 {{ $hasRevision ? 'has-revision' : '' }}" data-field-name="{{ $name }}">
    @if ($label)
        <label class="question-label">
            {!! $label !!}
            @if ($required && !$readonly)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="radio-inline flex-wrap mb-2">
        @foreach ($options as $opt)
            <label class="radio radio-primary mr-5 mb-3 {{ $readonly ? 'radio-disabled' : '' }}">
                <input type="radio" name="{{ $name }}" value="{{ $opt['value'] }}"
                    class="{{ $radioClass }} {{ $attributes->get('class') }}"
                    {{ $attributes->whereStartsWith('data-') }} {{ $selected === $opt['value'] ? 'checked' : '' }}
                    {{ $readonly ? 'disabled' : '' }}>
                <span></span> {{ $opt['label'] }}
            </label>
        @endforeach
    </div>

    @if ($hasRevision)
        <div class="revision-note-message text-danger mt-2 font-size-sm font-weight-bold"
            data-revision-field="{{ $name }}">
            <i class="fas fa-exclamation-circle text-danger mr-1"></i> Revisi: {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
