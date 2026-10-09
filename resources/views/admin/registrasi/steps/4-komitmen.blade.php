<div class="form-section-title">
    {{ __('commitment_to_standards') }}
</div>

{{-- 1. Sertifikat ISO (Menggunakan Komponen Checkbox & Slot) --}}
<div class="question-wrapper">
    @php
        $isoOptions = [
            ['value' => 'ISO 9001', 'label' => 'ISO 9001'],
            ['value' => 'ISO 14001', 'label' => 'ISO 14001'],
            ['value' => 'ISO 45001', 'label' => 'ISO 45001'],
        ];
        $selectedIso = $draft['general']->iso_certificates ?? [];
    @endphp

    <label class="question-label">{{ __('iso_certificate_owned') }} @if (!$isReadOnly)
            <span class="text-danger">*</span>
        @endif
    </label>

    @if ($isReadOnly)
        {{-- ponytail: Clean read-only badges instead of disabled checkboxes and raw input fields --}}
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            @php $hasAnyIso = false; @endphp
            @foreach ($isoOptions as $iso)
                @if (in_array($iso['value'], $selectedIso))
                    @php $hasAnyIso = true; @endphp
                    <span class="badge badge-light-primary font-weight-bolder px-3 py-2" style="font-size: 0.85rem;">
                        <i class="fas fa-certificate text-primary mr-1"></i> {{ $iso['label'] }}
                    </span>
                @endif
            @endforeach

            @if (in_array('other', $selectedIso) && !empty($draft['general']->iso_other))
                @php $hasAnyIso = true; @endphp
                <span class="badge badge-light-info font-weight-bolder px-3 py-2" style="font-size: 0.85rem;">
                    <i class="fas fa-award text-info mr-1"></i> {{ __('other') }}: {{ $draft['general']->iso_other }}
                </span>
            @endif

            @if (!$hasAnyIso)
                <span class="text-muted font-size-sm font-weight-bold">-</span>
            @endif
        </div>
    @else
        <x-vendor-checkbox name="iso_certificates" :options="$isoOptions" :selected="$selectedIso" :readonly="false" required>
            <div class="d-flex align-items-center mt-3">
                <label class="checkbox checkbox-primary mr-3 mb-0">
                    <input type="checkbox" name="iso_certificates[]" value="other" id="isoOtherCb"
                        {{ in_array('other', $selectedIso) ? 'checked' : '' }}>
                    <span></span> {{ __('other') }}:
                </label>

                {{-- Container input dinamis --}}
                <div id="isoOtherContainer"
                    style="max-width: 320px; flex: 1; {{ in_array('other', $selectedIso) ? '' : 'display: none;' }}">
                    <input type="text" name="iso_other" id="isoOtherInput" class="form-control form-control-sm"
                        placeholder="{{ __('iso_other_placeholder') }}" value="{{ $draft['general']->iso_other ?? '' }}"
                        {{ !in_array('other', $selectedIso) ? 'disabled' : '' }}>
                </div>
            </div>
        </x-vendor-checkbox>
    @endif

    {{-- Upload Dokumen Sertifikat ISO (multi-file) --}}
    <div class="mt-5">
        <label class="question-label">
            {{ __('iso_certificate_document') }}
            @if (!$isReadOnly)
                <span class="text-muted font-weight-normal">({{ __('multiple_files_allowed') }})</span>
            @endif
        </label>

        @php $uploadedIso = $uploadedIsoCertificates ?? []; @endphp

        @if (!$isReadOnly)
            <div class="custom-file custom-file-sm">
                <input type="file" class="custom-file-input product-file-input ajax-file-upload"
                    data-field="iso_files" multiple accept=".pdf,.jpg,.jpeg,.png">
                <label class="custom-file-label text-truncate" for="isoFilesInput"
                    style="border-radius:6px; font-size:0.8rem;">
                    {{ __('choose_file') }}
                </label>

                <!-- Indikator loading (otomatis di-handle JS) -->
                <span class="upload-status" style="display:none;"></span>
                {{-- Hidden input akan auto-di-create oleh JS handler (name="iso_files[]") --}}
            </div>

            <small class="text-muted">{{ __('file_upload_limit_hint') }}</small>

            {{-- Hidden inputs untuk path file yang masih disimpan --}}
            <div id="isoExistingPaths">
                <input type="hidden" name="existing_iso_files_present" value="1">
                @foreach ($uploadedIso as $isoDoc)
                    <input type="hidden" name="existing_iso_files[]" value="{{ $isoDoc['file_path'] }}">
                @endforeach
            </div>
        @endif

        {{-- Daftar file yang sudah ter-upload --}}
        <ul class="list-unstyled mt-3 {{ empty($uploadedIso) ? 'd-none' : '' }}" id="isoUploadedList">
            @foreach ($uploadedIso as $isoDoc)
                @php
                    $cleanName = preg_replace('/^\d+_/', '', $isoDoc['original_name']);
                @endphp
                <li class="d-flex justify-content-between align-items-center py-2 px-3 rounded mb-2 border"
                    data-path="{{ $isoDoc['file_path'] }}"
                    style="background-color: #fafbfc; border-color: #ebedf2 !important;">
                    <div class="d-flex align-items-center text-truncate mr-2" style="min-width: 0;">
                        <span class="btn btn-icon btn-xs btn-light-danger mr-2 flex-shrink-0"
                            style="width: 26px; height: 26px; pointer-events: none;">
                            <i class="fas fa-file-pdf font-size-sm"></i>
                        </span>
                        <span class="text-dark-75 font-weight-bold font-size-sm text-truncate"
                            title="{{ $cleanName }}">
                            {{ $cleanName }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center flex-shrink-0 ml-2" style="gap: 6px;">
                        <button type="button"
                            class="btn btn-xs btn-icon btn-light-primary btn-preview-doc font-weight-bold py-1 px-3"
                            data-url="{{ $isoDoc['url'] }}" data-title="{{ $cleanName }}"
                            title="{{ __('view') }}">
                            <i class="fas fa-eye"></i>
                        </button>
                        @if (!$isReadOnly)
                            <button type="button" class="btn btn-icon btn-xs btn-light-danger btn-iso-remove"
                                title="{{ __('delete') }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<div class="row">
    {{-- 2. Komitmen Kualitas, Lingkungan & K3 --}}
    <div class="col-md-12">
        <div class="question-wrapper">
            <label class="question-label">{{ __('company_commitment_qeohs') }} @if (!$isReadOnly)
                    {{-- <span class="text-danger">*</span> --}}
                @endif
            </label>

            <x-vendor-radio name="komitmen_kualitas" :options="[['value' => 'yes', 'label' => __('yes_have')], ['value' => 'no', 'label' => __('no')]]" :selected="$draft['general']->komitmen_kualitas ?? 'no'" :readonly="$isReadOnly"
                radioClass="komitmen-radio" />

            <div id="komitmenKualitasDetail"
                style="{{ ($draft['general']->komitmen_kualitas ?? '') === 'yes' ? '' : 'display:none' }}">
                <x-vendor-input name="komitmen_kualitas_detail" class="form-control form-control-sm" :label="__('mention_documents')"
                    :value="$draft['general']->komitmen_kualitas_detail ?? ''" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>
</div>

{{-- Komponen modal preview (idempotent via @once) --}}
<x-document-preview />

@push('scripts')
    <script>
        $('#isoOtherCb').on('change', function() {
            if ($(this).is(':checked')) {
                $('#isoOtherContainer').show();
                $('#isoOtherInput').prop('disabled', false).focus();
            } else {
                $('#isoOtherContainer').hide();
                $('#isoOtherInput').val('').prop('disabled', true);
            }
        });


        $('.komitmen-radio').on('change', function() {
            if ($(this).val() === 'yes') {
                $('#komitmenKualitasDetail').slideDown();
            } else {
                $('#komitmenKualitasDetail').slideUp();
                $('#komitmenKualitasDetail input').val('');
            }
        });

        // Preview instan saat file ISO selesai di-upload via AJAX
        $(document).on('ajax-upload-done', function(e) {
            const detail = e.originalEvent ? e.originalEvent.detail : e.detail;
            if (!detail || detail.field !== 'iso_files' || !detail.files) return;

            const $list = $('#isoUploadedList');
            $list.removeClass('d-none');

            detail.files.forEach(function(item) {
                const cleanName = item.original_name.replace(/^\d+_/, '');
                const li = `
                    <li class="d-flex justify-content-between align-items-center py-2 px-3 rounded mb-2 border"
                        data-temp-path="${item.path}" style="background-color: #fafbfc; border-color: #ebedf2 !important;">
                        <div class="d-flex align-items-center text-truncate mr-2" style="min-width: 0;">
                            <span class="btn btn-icon btn-xs btn-light-danger mr-2 flex-shrink-0" style="width: 26px; height: 26px; pointer-events: none;">
                                <i class="fas fa-file-pdf font-size-sm"></i>
                            </span>
                            <span class="text-dark-75 font-weight-bold font-size-sm text-truncate" title="${cleanName}">
                                ${cleanName}
                            </span>
                        </div>
                        <div class="d-flex align-items-center flex-shrink-0 ml-2" style="gap: 6px;">
                            <button type="button" class="btn btn-xs btn-light-primary btn-preview-doc font-weight-bold py-1 px-3"
                                data-url="${item.url}" data-title="${cleanName}">
                                <i class="fas fa-eye mr-1"></i> {{ __('preview') }}
                            </button>
                            <button type="button" class="btn btn-icon btn-xs btn-light-danger btn-iso-remove"
                                title="{{ __('delete') }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </li>
                `;
                $list.append(li);
            });
        });

        // Hapus sertifikat ISO (baik existing dari database maupun baru di-upload)
        $(document).on('click', '.btn-iso-remove', function() {
            const li = $(this).closest('li');
            const path = li.data('path');
            const tempPath = li.data('temp-path');
            li.remove();

            if (path) {
                $('#isoExistingPaths input[value="' + path + '"]').remove();
            }

            if (tempPath) {
                const hidden = document.getElementById('iso_files__hidden[]');
                if (hidden && hidden.value) {
                    const paths = hidden.value.split(',').map(s => s.trim()).filter(s => s && s !== tempPath);
                    hidden.value = paths.join(',');
                    const $status = $('input[data-field="iso_files"]').closest('.custom-file').find(
                        '.upload-status');
                    if (paths.length > 0) {
                        $status.text('✓ Tersisa ' + paths.length + ' file baru');
                    } else {
                        $status.text('').hide();
                    }
                }
            }

            if ($('#isoUploadedList li').length === 0) {
                $('#isoUploadedList').addClass('d-none');
            }
        });
    </script>
@endpush
