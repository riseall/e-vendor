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

<div class="form-group {{ $wrapperClass }}" {{ $attributes->whereStartsWith('data-') }}>
    @if ($label)
        <label class="question-label d-block mb-2">
            {!! $label !!}
            @if ($required && !$readonly)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <select name="{{ $name }}" id="{{ $name }}_select"
        class="form-control {{ $isSimple ? 'custom-select' : 'selectpicker' }} {{ $readonly ? 'form-control-solid' : '' }}"
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

    @if (isset($revisionNotes) && isset($revisionNotes[$name]))
        <div class="text-danger mt-1 font-size-sm font-weight-bold">
            <i class="fas fa-exclamation-circle text-danger mr-1"></i> Revisi: {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
