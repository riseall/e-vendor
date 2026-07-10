<div class="form-section-title">
    Komitmen Terhadap Standar
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

    <label class="question-label">Sertifikat ISO yang Dimiliki @if (!$isReadOnly)
            <span class="text-danger">*</span>
        @endif
    </label>

    <x-vendor-checkbox name="iso_certificates" :options="$isoOptions" :selected="$selectedIso" :readonly="$isReadOnly">
        <div class="d-flex align-items-center mt-3">
            <label class="checkbox checkbox-primary mr-3 mb-0 {{ $isReadOnly ? 'checkbox-disabled' : '' }}">
                <input type="checkbox" name="iso_certificates[]" value="other" id="isoOtherCb"
                    {{ in_array('other', $selectedIso) ? 'checked' : '' }} {{ $isReadOnly ? 'disabled' : '' }}>
                <span></span> Lainnya:
            </label>

            {{-- Container input dinamis --}}
            <div id="isoOtherContainer" style="flex: 1; {{ in_array('other', $selectedIso) ? '' : 'display: none;' }}">
                <input type="text" name="iso_other" id="isoOtherInput" class="form-control form-control-sm"
                    placeholder="Pisahkan dengan koma (contoh: ISO 27001, ISO 50001)"
                    value="{{ $draft['general']->iso_other ?? '' }}"
                    {{ !in_array('other', $selectedIso) || $isReadOnly ? 'disabled' : '' }}
                    {{ $isReadOnly ? 'readonly' : '' }}>
            </div>
        </div>
    </x-vendor-checkbox>

    {{-- Upload Dokumen Sertifikat ISO (multi-file) --}}
    <div class="mt-4">
        <label class="question-label">
            Dokumen Sertifikat ISO
            @if (!$isReadOnly)
                <span class="text-muted font-weight-normal">(boleh lebih dari satu file)</span>
            @endif
        </label>

        @php $uploadedIso = $uploadedIsoCertificates ?? []; @endphp

        @if (!$isReadOnly)
            <div class="custom-file custom-file-sm">
                <input type="file" class="custom-file-input product-file-input" name="iso_files[]" id="isoFilesInput"
                    multiple accept=".pdf,.jpg,.jpeg,.png">
                <label class="custom-file-label text-truncate" for="isoFilesInput"
                    style="border-radius:6px; font-size:0.8rem;">
                    Pilih File
                </label>
            </div>

            <small class="text-muted">Format: PDF/JPG/PNG. Maks 5 MB per file.</small>

            {{-- Hidden inputs untuk path file yang masih disimpan --}}
            <div id="isoExistingPaths">
                @foreach ($uploadedIso as $isoDoc)
                    <input type="hidden" name="existing_iso_files[]" value="{{ $isoDoc['file_path'] }}">
                @endforeach
            </div>
        @endif

        {{-- Daftar file yang sudah ter-upload --}}
        @if (!empty($uploadedIso))
            <ul class="list-group list-group-sm mt-3" id="isoUploadedList">
                @foreach ($uploadedIso as $isoDoc)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2"
                        data-path="{{ $isoDoc['file_path'] }}">
                        <a href="{{ $isoDoc['url'] }}" target="_blank" class="text-primary btn-preview-doc"
                            data-url="{{ $isoDoc['url'] }}" data-title="{{ $isoDoc['original_name'] }}">
                            <i class="flaticon2-file mr-2"></i>{{ $isoDoc['original_name'] }}
                        </a>
                        @if (!$isReadOnly)
                            <button type="button" class="btn btn-icon btn-xs btn-light-danger btn-iso-remove"
                                title="Hapus">
                                <i class="flaticon2-trash"></i>
                            </button>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<div class="row">
    {{-- 2. Komitmen Kualitas, Lingkungan & K3 --}}
    <div class="col-md-12">
        <div class="question-wrapper">
            <label class="question-label">Komitmen Kualitas, Lingkungan & K3 (Selain ISO) @if (!$isReadOnly)
                    {{-- <span class="text-danger">*</span> --}}
                @endif
            </label>

            <x-vendor-radio name="komitmen_kualitas" :options="[['value' => 'yes', 'label' => 'Ya, Punya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['general']->komitmen_kualitas ?? 'no'" :readonly="$isReadOnly"
                radioClass="komitmen-radio" />

            <div id="komitmenKualitasDetail"
                style="{{ ($draft['general']->komitmen_kualitas ?? '') === 'yes' ? '' : 'display:none' }}">
                <x-vendor-input name="komitmen_kualitas_detail" class="form-control form-control-sm"
                    label="Sebutkan dokumen..." :value="$draft['general']->komitmen_kualitas_detail ?? ''" :readonly="$isReadOnly" />
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

        // Hapus sertifikat ISO existing dari daftar "kept"
        $(document).on('click', '.btn-iso-remove', function() {
            const li = $(this).closest('li[data-path]');
            const path = li.data('path');
            li.remove();
            $('#isoExistingPaths input[value="' + path + '"]').remove();
        });
    </script>
@endpush
