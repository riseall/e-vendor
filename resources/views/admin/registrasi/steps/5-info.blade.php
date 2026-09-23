<div class="form-section-title">
    {{ __('other_information') }}
</div>

<div class="row">
    <div class="col-md-6">
        <div class="question-wrapper">
            <label class="question-label">{{ __('delivery_lead_time') }} @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>

            <x-vendor-input name="lead_time" label="" labelClass="question-label font-weight-bolder"
                :placeholder="__('lead_time_placeholder')" :value="$draft['general']->lead_time ?? ''" :readonly="$isReadOnly" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="question-wrapper">
            <label class="question-label">{{ __('pharmaceutical_customer_list') }} @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>

            <x-vendor-input type="textarea" name="customer_list" label=""
                labelClass="question-label font-weight-bolder"
                :placeholder="__('customer_list_placeholder')" :value="$draft['general']->customer_list ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
</div>
