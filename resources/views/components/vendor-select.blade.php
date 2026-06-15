@props([
    'name',
    'label' => '',
    'options' => [],
    'selected' => '',
    'readonly' => false,
    'required' => false,
    'placeholder' => 'Pilih...',
    'wrapperClass' => 'mb-4',
    'isSimple' => false,
])

@php $hasRevision = isset($revisionNotes) && isset($revisionNotes[$name]); @endphp

<div class="form-group {{ $wrapperClass }} {{ $hasRevision ? 'has-revision' : '' }}"
    data-field-name="{{ $name }}">
    @if ($label)
        <label class="question-label d-block mb-2">
            {!! $label !!}
            @if ($required && !$readonly)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <select name="{{ $name }}" id="{{ $name }}_select"
        {{ $attributes->merge(['class' => 'form-control ' . ($isSimple ? 'custom-select' : 'selectpicker ') . ($readonly ? 'form-control-solid' : '')]) }}
        {{ $readonly ? 'disabled' : '' }} data-size="7" data-live-search="true" title="{{ $placeholder }}" width="100%">

        @if ($isSimple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $val => $txt)
            <option value="{{ $val }}" {{ old($name, $selected) == $val ? 'selected' : '' }}>
                {{ $txt }}
            </option>
        @endforeach
    </select>

    @if ($hasRevision)
        <div class="revision-note-message text-danger mt-1 font-size-sm font-weight-bold"
            data-revision-field="{{ $name }}">
            <i class="fas fa-exclamation-circle text-danger mr-1"></i> Revisi: {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
