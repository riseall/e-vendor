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

@php $hasRevision = isset($revisionNotes) && isset($revisionNotes[$name]); @endphp

<div class="form-group mb-2 {{ $hasRevision ? 'has-revision' : '' }}" data-field-name="{{ $name }}">
    @if ($label)
        <label class="{{ $labelClass }} d-block">
            {!! $label !!}
            @if ($required && !$readonly)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="input-group">
        @if ($type === 'textarea')
            <textarea name="{{ $name }}" rows="3"
                {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
                placeholder="{{ $placeholder }}" {{ $readonly ? 'readonly disabled' : '' }}>{{ old($name, $value) }}</textarea>
        @elseif ($type === 'file')
            <div class="custom-file w-100">
                <input type="{{ $type }}" name="{{ $name }}"
                    class="custom-file-input {{ $errors->has($name) ? 'is-invalid' : '' }}" id="{{ $name }}"
                    accept=".pdf,.jpg,.jpeg,.png" {{ $readonly ? 'disabled' : '' }}>
                <label class="custom-file-label" for="{{ $name }}">
                    {{ $value ? 'Ganti file...' : $placeholder ?? 'Upload Dokumen...' }}
                </label>
            </div>

            @if ($value)
                <input type="hidden" name="existing_{{ $name }}" value="{{ $value }}">
            @endif

            @if ($value && !$readonly)
                <div class="mt-2 w-100 d-flex justify-content-between p-2 bg-light-success rounded">
                    <span class="text-success font-size-xs font-weight-bold mr-3 align-self-center">File
                        Tersimpan</span>
                    <button type="button" data-url="{{ asset('storage/' . $value) }}"
                        class="btn btn-xs btn-success btn-icon btn-preview-doc" title="Lihat Dokumen">
                        <i class="flaticon-eye"></i>
                    </button>
                </div>
            @elseif($value && $readonly)
                <button type="button" data-url="{{ asset('storage/' . $value) }}"
                    class="btn btn-sm btn-light-primary mt-2 btn-preview-doc w-100">
                    <i class="flaticon-eye mr-2"></i> Lihat Dokumen
                </button>
            @endif
        @else
            <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
                {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
                placeholder="{{ $placeholder }}" {{ $readonly ? 'readonly disabled' : '' }}>
        @endif
    </div>

    @if ($hasRevision)
        <div class="revision-note-message text-danger mt-1 font-size-xs font-weight-bold"
            data-revision-field="{{ $name }}">
            <i class="fas fa-exclamation-circle text-danger mr-1" style="font-size: 10px;"></i>
            Revisi: {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
