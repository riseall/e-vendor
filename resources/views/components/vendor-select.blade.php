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
    'plainText' => false,
    'labelClass' => 'question-label d-block mb-2',
    'id' => null,
    'container' => 'body',
])

@php $hasRevision = isset($revisionNotes) && isset($revisionNotes[$name]); @endphp

<div class="form-group {{ $wrapperClass }} {{ $hasRevision ? 'has-revision' : '' }}"
    data-field-name="{{ $name }}">
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
            $selectedText = '';
            foreach ($options as $val => $txt) {
                if (old($name, $selected) == $val) {
                    $selectedText = $txt;
                    break;
                }
            }
            if (!$selectedText && $isSimple) $selectedText = $placeholder;
            if (!$selectedText) $selectedText = '-';
        @endphp
        @if ($plainText)
            <div class="font-weight-bolder text-dark mb-1 font-size-sm">{{ $selectedText }}</div>
            <input type="hidden" name="{{ $name }}" value="{{ old($name, $selected) }}">
        @else
            <input type="text" class="form-control form-control-solid" value="{{ $selectedText }}" readonly disabled>
            <input type="hidden" name="{{ $name }}" value="{{ old($name, $selected) }}">
        @endif
    @else
        <select name="{{ $name }}" id="{{ $id ?? ($name . '_select') }}"
            {{ $attributes->merge(['class' => 'form-control ' . ($isSimple ? 'custom-select w-100' : 'selectpicker')]) }}
            {{ $required ? 'required' : '' }} data-size="7" data-dropup-auto="false" data-display="static"
            @if(!empty($container) && $container !== 'false') data-container="{{ $container }}" @endif
            data-width="100%" title="{{ $placeholder }}">

            @if ($isSimple && !empty($placeholder))
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
            <i class="fas fa-exclamation-circle text-danger mr-1"></i> Revisi: {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
