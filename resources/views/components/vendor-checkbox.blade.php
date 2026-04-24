@props(['name', 'label' => '', 'options' => [], 'selected' => [], 'readonly' => false, 'required' => false])

<div class="form-group mb-2">
    @if ($label)
        <label class="question-label">
            {!! $label !!}
            @if ($required && !$readonly)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="checkbox-inline flex-wrap mb-2">
        @foreach ($options as $opt)
            <label class="checkbox checkbox-primary mr-5 mb-3 {{ $readonly ? 'checkbox-disabled' : '' }}">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $opt['value'] }}"
                    {{ $attributes->merge(['class' => '']) }} {{ in_array($opt['value'], $selected) ? 'checked' : '' }}
                    {{ $readonly ? 'disabled' : '' }}>
                <span></span> {{ $opt['label'] }}
            </label>
        @endforeach
    </div>

    {{ $slot }}

    @if (isset($revisionNotes[$name]))
        <div class="text-danger mt-2 font-size-sm font-weight-bold">
            <i class="fas fa-exclamation-circle text-danger mr-1"></i> Revisi: {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
