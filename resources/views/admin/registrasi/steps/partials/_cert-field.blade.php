@if ($isReadOnly)
    @if ($filePath)
        <button type="button" class="btn btn-sm btn-light-primary mt-2 btn-preview-doc w-100"
            style="gap:4px; border-radius:5px; font-size:0.78rem;"
            data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $filePath) }}"
            data-title="{{ $previewTitle }}">
            <i class="flaticon-eye icon-sm"></i> Lihat
        </button>
    @else
        <span class="text-muted" style="font-size:0.78rem;">—</span>
    @endif
@elseif ($filePath)
    {{-- Ada file: tampilkan badge compact + preview + link ganti --}}
    <input type="file" class="product-file-input d-none ajax-file-upload" data-field="{{ $fieldName }}"
        id="{{ $fieldId }}" accept=".pdf,.jpg,.jpeg,.png">
    <span class="upload-status" style="display:none;"></span>

    <div class="d-flex align-items-center px-2 py-1 bg-light-success rounded" style="gap:6px; border-radius:6px;">
        <i class="flaticon2-check-mark text-success" style="font-size:0.75rem;"></i>
        <span class="text-success font-weight-bold flex-grow-1"
            style="font-size:0.75rem; white-space:nowrap;">Terlampir</span>
        <button type="button" class="btn btn-icon btn-xs btn-success btn-preview-doc"
            style="width:22px; height:22px; border-radius:4px; padding:0;"
            data-url="{{ app(\App\Services\VendorFileService::class)->url($application, $filePath) }}"
            data-title="{{ $previewTitle }}" title="Preview">
            <i class="flaticon-eye" style="font-size:0.7rem;"></i>
        </button>
    </div>
    <a href="#" class="d-block mt-1 text-muted cert-change-trigger" style="font-size:0.72rem;"
        data-target="#{{ $fieldId }}">
        Ganti file...
    </a>
@else
    {{-- Belum ada file: tampilkan file input biasa --}}
    <div class="custom-file custom-file-sm">
        <input type="file" class="custom-file-input product-file-input ajax-file-upload"
            data-field="{{ $fieldName }}" id="{{ $fieldId }}" accept=".pdf,.jpg,.jpeg,.png"
            {{ $required ? 'required' : '' }}>
        <label class="custom-file-label text-truncate" for="{{ $fieldId }}"
            style="border-radius:6px; font-size:0.78rem;">
            Pilih File
        </label>
        <span class="upload-status" style="display:none;"></span>
    </div>
@endif
