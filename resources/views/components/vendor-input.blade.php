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

        @php
            $appModel = null;
            if ($application instanceof \App\Models\VendorApplication) {
                $appModel = $application;
            } elseif (isset($__data['application']) && $__data['application'] instanceof \App\Models\VendorApplication) {
                $appModel = $__data['application'];
            } elseif (isset($__data['app']) && $__data['app'] instanceof \App\Models\VendorApplication) {
                $appModel = $__data['app'];
            } elseif (view()->shared('application') instanceof \App\Models\VendorApplication) {
                $appModel = view()->shared('application');
            } elseif (isset($app) && $app instanceof \App\Models\VendorApplication) {
                $appModel = $app;
            }

            $fileUrl = ($appModel && $value) ? app(\App\Services\VendorFileService::class)->url($appModel, $value) : '';
        @endphp

        @if ($value && !$readonly)
            <div class="existing-file-indicator mt-1 d-inline-flex align-items-center" style="font-size:0.78rem;">
                <span class="text-success font-weight-bold text-truncate mr-1">
                    ✓ {{ __('file_saved') }}
                </span>
                @if ($fileUrl)
                    <button type="button"
                        data-url="{{ $fileUrl }}"
                        class="btn btn-xs btn-light-success btn-icon btn-preview-doc ml-1"
                        title="{{ __('view') }}" style="width:20px; height:20px; min-width:20px; line-height:20px; flex-shrink:0;">
                        <i class="fas fa-eye" style="font-size:10px;"></i>
                    </button>
                @endif
            </div>
        @elseif($value && $readonly)
            @php
                $cleanFileName = basename($value);
                $cleanFileName = preg_replace('/^\d+_\d+_|^\d+_/', '', $cleanFileName);
            @endphp
            <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm form-control-solid font-size-xs text-dark-75 text-truncate font-weight-bold"
                    readonly disabled value="{{ $cleanFileName }}" title="{{ $cleanFileName }}">
                @if ($fileUrl)
                    <div class="input-group-append">
                        <button type="button"
                            data-url="{{ $fileUrl }}"
                            data-title="{{ $label ? strip_tags($label) : $cleanFileName }}"
                            class="btn btn-sm btn-light-success btn-icon btn-preview-doc"
                            title="{{ __('view') }}">
                            <i class="fas fa-eye font-size-xs"></i>
                        </button>
                    </div>
                @endif
            </div>
        @elseif(!$value && $readonly)
            <input type="text" class="form-control form-control-sm form-control-solid font-size-xs text-muted"
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
            {{ $slot ?? '' }}
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
