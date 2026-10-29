@props(['name', 'options' => [], 'selected' => [], 'readonly' => false, 'required' => false])

@php $hasRevision = isset($revisionNotes) && isset($revisionNotes[$name]); @endphp

<div class="form-group mb-2 {{ $hasRevision ? 'has-revision' : '' }}" data-field-name="{{ $name }}">
    <div class="checkbox-inline flex-wrap mb-2">
        @foreach ($options as $opt)
            <label class="checkbox checkbox-primary mr-5 mb-3 {{ $readonly ? 'checkbox-disabled' : '' }}">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $opt['value'] }}"
                    {{ in_array($opt['value'], (array) $selected) ? 'checked' : '' }} {{ $readonly ? 'disabled' : '' }}>
                <span></span> {{ $opt['label'] }}
            </label>
        @endforeach
    </div>

    {{ $slot }}

    @if ($hasRevision)
        <div class="revision-note-message text-danger mt-2 font-size-sm font-weight-bold"
            data-revision-field="{{ $name }}">
            <i class="fas fa-exclamation-circle text-danger mr-1"></i> {{ __('revision') }}:
            {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
