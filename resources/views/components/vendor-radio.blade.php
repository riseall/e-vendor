@props([
    'name',
    'label' => '',
    'options' => [],
    'selected' => '',
    'readonly' => false,
    'required' => false,
    'radioClass' => '',
])

<div class="form-group mb-4">
    <label class="question-label">
        {{ $label }}
        @if ($required && !$readonly)
            <span class="text-danger">*</span>
        @endif
    </label>

    <div class="radio-inline flex-wrap mb-2">
        @foreach ($options as $opt)
            <label class="radio radio-primary mr-5 mb-3 {{ $readonly ? 'radio-disabled' : '' }}">
                <input type="radio" name="{{ $name }}" value="{{ $opt['value'] }}" class="{{ $radioClass }}"
                    {{ $selected === $opt['value'] ? 'checked' : '' }} {{ $readonly ? 'disabled' : '' }}>
                <span></span> {{ $opt['label'] }}
            </label>
        @endforeach
    </div>

    @if (isset($revisionNotes[$name]))
        <div class="text-danger mt-2 font-size-sm font-weight-bold">
            <i class="fas fa-exclamation-circle text-danger mr-1"></i> Revisi: {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
