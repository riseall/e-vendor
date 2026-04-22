<div class="form-section-title">
    Informasi Lain
</div>

<div class="row">
    <div class="col-md-6">
        <div class="question-wrapper">
            <x-vendor-input type="text" name="lead_time" labelClass="question-label" label="Jangka Waktu Pengiriman"
                placeholder="Contoh: 7 hari kerja" :value="$draft['general']->lead_time ?? ''" :readonly="$isReadOnly" />
        </div>
    </div>
    <div class="col-md-6">
        <div class="question-wrapper">
            <x-vendor-input type="textarea" name="customer_list" labelClass="question-label"
                label="Daftar Pelanggan Farmasi" placeholder="Sebutkan perusahaan farmasi..." :value="$draft['general']->customer_list ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
</div>
