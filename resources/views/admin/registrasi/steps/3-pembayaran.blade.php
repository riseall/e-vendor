<div class="form-section-title">{{ __('payment_info') }}</div>

{{-- 1. Termin Pembayaran --}}
<div class="question-wrapper">
    @php
        $termOptions = [
            ['value' => '14D', 'label' => '14D'],
            ['value' => '30D', 'label' => '30D'],
            ['value' => '60D', 'label' => '60D'],
            ['value' => 'other', 'label' => __('other')],
        ];
    @endphp

    <label class="question-label">{{ __('preferred_payment_term') }} @if (!$isReadOnly)
            <span class="text-danger">*</span>
        @endif
    </label>

    <x-vendor-radio name="payment_term" :options="$termOptions" :selected="$draft['general']->payment_term ?? ''" :readonly="$isReadOnly"
        radioClass="payment-term-radio" required />

    {{-- Input text muncul jika pilih 'Lainnya' --}}
    <div id="paymentTermOther" style="{{ ($draft['general']->payment_term ?? '') === 'other' ? '' : 'display:none' }}">
        <x-vendor-input name="payment_term_other" :placeholder="__('example') . ': 90D'" :value="$draft['general']->payment_term_other ?? ''" :readonly="$isReadOnly" />
    </div>
</div>

{{-- 2. Data Rekening Bank --}}
<div class="question-wrapper">
    <label class="question-label">{{ __('bank_account_data') }} @if (!$isReadOnly)
            <span class="text-danger">*</span>
        @endif
    </label>

    <div class="row mt-3">
        <div class="col-md-6">
            <x-vendor-input name="pemegang_rekening" :label="__('account_holder')" :value="$draft['general']->pemegang_rekening ?? ''" :readonly="$isReadOnly"
                required />
        </div>
        <div class="col-md-6">
            <x-vendor-input name="nomor_rekening" :label="__('account_number')" :value="$draft['general']->nomor_rekening ?? ''" :readonly="$isReadOnly" required />
        </div>
        <div class="col-md-6">
            <x-vendor-input name="nama_bank" :label="__('bank_name')" :value="$draft['general']->nama_bank ?? ''" :readonly="$isReadOnly" required />
        </div>
        <div class="col-md-6">
            <x-vendor-input name="swift_code" :label="__('swift_code')" placeholder="XXXXXXXX" :value="$draft['general']->swift_code ?? ''"
                :readonly="$isReadOnly" required />
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mt-2">
            <x-vendor-input type="textarea" name="alamat_bank" :label="__('bank_address')" :placeholder="__('bank_address_placeholder')"
                :value="$draft['general']->alamat_bank ?? ''" :readonly="$isReadOnly" required />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        //Toggle field "lainnya" termin pembayaran
        $('.payment-term-radio').on('change', function() {
            if ($(this).val() === 'other') {
                $('#paymentTermOther').slideDown('fast');
            } else {
                $('#paymentTermOther').slideUp('fast');
                $('#paymentTermOther input').val('');
            }
        });
    </script>
@endpush
