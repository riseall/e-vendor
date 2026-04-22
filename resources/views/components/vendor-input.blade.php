@props([
    'name',
    'label' => '',
    'value' => '',
    'type' => 'text',
    'readonly' => false,
    'required' => false,
    'placeholder' => '',
    'labelClass' => 'font-size-sm font-weight-bold text-muted',
])

<div class="form-group mb-4">
    @if ($label)
        <label class="{{ $labelClass }} d-block">
            {{ $label }}
            @if ($required && !$readonly)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="input-group">
        @if ($type === 'textarea')
            <textarea name="{{ $name }}" rows="3" class="form-control @error($name) is-invalid @enderror"
                placeholder="{{ $placeholder }}" {{ $readonly ? 'readonly disabled' : '' }}>{{ old($name, $value) }}</textarea>
        @else
            <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
                class="form-control @error($name) is-invalid @enderror" placeholder="{{ $placeholder }}"
                {{ $readonly ? 'readonly disabled' : '' }}>
        @endif
    </div>

    @if (isset($revisionNotes) && isset($revisionNotes[$name]))
        <div class="text-danger mt-1 font-size-xs font-weight-bold">
            <i class="fas fa-exclamation-circle text-danger mr-1" style="font-size: 10px;"></i>
            Revisi: {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
