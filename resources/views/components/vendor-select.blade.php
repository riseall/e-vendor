@props([
    'name',
    'label' => '',
    'options' => [],
    'selected' => '',
    'readonly' => false,
    'required' => false,
    'placeholder' => __('select') . '...',
    'labelClass' => 'font-size-sm font-weight-bold text-muted',
])

@php
    $hasRevision = isset($revisionNotes) && isset($revisionNotes[$name]);
@endphp

<div class="form-group mb-2 {{ $hasRevision ? 'has-revision' : '' }}" data-field-name="{{ $name }}">
    @if ($label)
        <label class="{{ $labelClass }} d-block">
            {!! $label !!}
            @if ($required && !$readonly)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if ($readonly)
        @php
            $displayVal = $selected;
            if (isset($options[$selected])) {
                $displayVal = $options[$selected];
            } else {
                foreach ($options as $opt) {
                    if (is_array($opt) && isset($opt['value']) && $opt['value'] == $selected) {
                        $displayVal = $opt['label'] ?? $opt['value'];
                        break;
                    }
                }
            }
        @endphp
        <input type="text" class="form-control form-control-solid" value="{{ $displayVal ?: '-' }}" readonly disabled>
    @else
        <select name="{{ $name }}" {{ $attributes->merge(['class' => 'form-control selectpicker ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
            data-size="7" data-live-search="true" {{ $required ? 'required' : '' }}>

            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach ($options as $val => $txt)
                <option value="{{ $val }}" {{ old($name, $selected) == $val ? 'selected' : '' }}>
                    {{ $txt }}
                </option>
            @endforeach
        </select>
    @endif

    @if ($hasRevision)
        <div class="revision-note-message text-danger mt-1 font-size-sm font-weight-bold"
            data-revision-field="{{ $name }}">
            <i class="fas fa-exclamation-circle text-danger mr-1"></i> {{ __('revision') }}: {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
