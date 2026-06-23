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
</div>

<div class="row">
    {{-- 2. Komitmen Kualitas, Lingkungan & K3 --}}
    <div class="col-md-12">
        <div class="question-wrapper">
            <label class="question-label">Komitmen Kualitas, Lingkungan & K3 (Selain ISO) @if (!$isReadOnly)
                    <span class="text-danger">*</span>
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
    </script>
@endpush
