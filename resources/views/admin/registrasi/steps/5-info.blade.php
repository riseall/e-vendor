<div class="form-section-title">
    Informasi Lain
</div>

<div class="row">
    <div class="col-md-6">
        <div class="question-wrapper">
            <label class="question-label">Jangka Waktu Pengiriman @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>

            <x-vendor-input name="lead_time" label="" labelClass="question-label font-weight-bolder"
                placeholder="Contoh: 7 hari kerja setelah PO" :value="$draft['general']->lead_time ?? ''" :readonly="$isReadOnly" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="question-wrapper">
            <label class="question-label">Daftar Pelanggan Farmasi @if (!$isReadOnly)
                    <span class="text-danger">*</span>
                @endif
            </label>

            <x-vendor-input type="textarea" name="customer_list" label=""
                labelClass="question-label font-weight-bolder"
                placeholder="Sebutkan beberapa perusahaan farmasi yang pernah bekerja sama..." :value="$draft['general']->customer_list ?? ''"
                :readonly="$isReadOnly" />
        </div>
    </div>
</div>
