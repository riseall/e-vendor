@props([
    'name',
    'label' => '',
    'value' => '',
    'type' => 'text',
    'readonly' => false,
    'required' => false,
    'placeholder' => '',
    'labelClass' => 'font-size-sm font-weight-bold text-muted',
    'application' => null,
    'rightIcon' => null,
    'leftIcon' => null,
    'existingName' => null,
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

    @if ($type === 'textarea')
        <div class="input-group">
            <textarea name="{{ $name }}" rows="3"
                {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '') . ($readonly ? ' form-control-solid' : '')]) }}
                placeholder="{{ $placeholder }}" {{ $readonly ? 'readonly disabled' : '' }}>{{ old($name, $value) }}</textarea>
        </div>
    @elseif ($type === 'file')
        @if (!$readonly)
            <div class="custom-file w-100">
                <input type="{{ $type }}" data-field="{{ $name }}"
                    class="custom-file-input ajax-file-upload {{ $errors->has($name) ? 'is-invalid' : '' }}"
                    id="{{ $name }}" accept=".pdf,.jpg,.jpeg,.png" placeholder="{{ $placeholder }}"
                    {{ $required && !$value ? 'required' : '' }}>
                <label class="custom-file-label font-size-xs text-truncate" for="{{ $name }}"
                    style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; padding-right:75px;">
                    {{ $value ? __('change_file') . '...' : ($placeholder ?: __('upload_file') . '...') }}
                </label>
            </div>
            <div class="upload-status mt-1 font-weight-bold" style="display:none; font-size:0.75rem;"></div>
        @endif

        @if ($value)
            <input type="hidden" name="{{ $existingName ?? 'existing_' . $name }}" value="{{ $value }}">
        @endif

        @if ($value && !$readonly)
            <div class="mt-1 d-flex align-items-center justify-content-between py-1 px-2 rounded bg-light-primary"
                style="font-size:0.75rem;">
                <span class="text-primary font-weight-bold text-truncate mr-2">
                    <i class="ki ki-check icon-xs text-primary mr-1"></i>{{ __('file_saved') }}
                </span>
                <button type="button"
                    data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $value) }}"
                    class="btn btn-xs btn-light-primary btn-icon btn-preview-doc flex-shrink-0"
                    title="{{ __('view') }}" style="width:20px; height:20px; min-width:20px; line-height:20px;">
                    <i class="flaticon-eye" style="font-size:10px;"></i>
                </button>
            </div>
        @elseif($value && $readonly)
            <button type="button"
                data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $value) }}"
                class="btn btn-xs btn-light-primary btn-preview-doc w-100 py-1 font-weight-bold">
                <i class="flaticon-eye mr-1 icon-xs"></i> {{ __('view') }}
            </button>
        @elseif(!$value && $readonly)
            <input type="text" class="form-control form-control-sm form-control-solid font-size-xs text-muted py-1"
                readonly disabled value="{{ __('no_document') }}">
        @endif
    @else
        <div class="input-group">
            @if ($leftIcon)
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="{{ $leftIcon }}"></i></span>
                </div>
            @endif
            @php
                $isDatepicker = strpos($attributes->get('class', ''), 'datepicker') !== false || $type === 'date';
                $defaultPlaceholder = $isDatepicker ? __('select_date') . '...' : '';
                $finalPlaceholder = $placeholder ?: $defaultPlaceholder;
            @endphp
            <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
                {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '') . ($readonly ? ' form-control-solid' : '')]) }}
                placeholder="{{ $finalPlaceholder }}" {{ $readonly ? 'readonly disabled' : '' }}>
            @if ($rightIcon)
                <div class="input-group-append">
                    <span class="input-group-text"><i class="{{ $rightIcon }}"></i></span>
                </div>
            @endif
        </div>
    @endif

    @if ($hasRevision)
        <div class="revision-note-message text-danger mt-1 font-size-xs font-weight-bold"
            data-revision-field="{{ $name }}">
            <i class="fas fa-exclamation-circle text-danger mr-1" style="font-size: 10px;"></i>
            {{ __('revision') }}: {{ $revisionNotes[$name] }}
        </div>
    @endif
</div>
