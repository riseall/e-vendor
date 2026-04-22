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

    <x-vendor-checkbox name="iso_certificates" label="Sertifikat ISO yang Dimiliki" :options="$isoOptions" :selected="$selectedIso"
        :readonly="$isReadOnly">
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
                    value="{{ $draft['general']->iso_other ?? '' }}" {{ $isReadOnly ? 'readonly disabled' : '' }}>
            </div>
        </div>
    </x-vendor-checkbox>
</div>

<div class="row">
    {{-- 2. Komitmen Kualitas, Lingkungan & K3 --}}
    <div class="col-md-6">
        <div class="question-wrapper">
            <x-vendor-radio name="komitmen_kualitas" label="Komitmen Kualitas, Lingkungan & K3 (Selain ISO)"
                :options="[['value' => 'yes', 'label' => 'Ya, Punya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['general']->komitmen_kualitas ?? 'no'" :readonly="$isReadOnly" radioClass="komitmen-radio" />

            <div id="komitmenKualitasDetail"
                style="{{ ($draft['general']->komitmen_kualitas ?? '') === 'yes' ? '' : 'display:none' }}">
                <x-vendor-input name="komitmen_kualitas_detail" class="form-control form-control-sm"
                    label="Sebutkan dokumen..." :value="$draft['general']->komitmen_kualitas_detail ?? ''" :readonly="$isReadOnly" />
            </div>
        </div>
    </div>

    {{-- 3. Sertifikasi Halal --}}
    <div class="col-md-6">
        <div class="question-wrapper">
            <x-vendor-radio name="sertifikat_halal" label="Sertifikasi Halal BPJPH" :options="[['value' => 'yes', 'label' => 'Ya'], ['value' => 'no', 'label' => 'Tidak']]" :selected="$draft['general']->sertifikat_halal ?? 'no'"
                :readonly="$isReadOnly" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('#isoOtherCb').on('change', function() {
            if ($(this).is(':checked')) {
                $('#isoOtherContainer').show();
                $('#isoOtherInput').focus();
            } else {
                $('#isoOtherContainer').hide();
                $('#isoOtherInput').val('');
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
