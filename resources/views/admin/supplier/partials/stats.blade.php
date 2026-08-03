@php
    $total = $countTotal ?? $suppliers->total();
    $active = $countActive ?? 0;
    $expiring = $countExpiringSoon ?? 0;
    $requal = $countInRequal ?? 0;
@endphp

<div class="row mb-6">
    <x-dash-card :value="$total" label="Total Supplier Approved" icon="flaticon2-layers-1" type="primary" />
    <x-dash-card :value="$active" label="Status Valid & Aktif" icon="flaticon2-check-mark" type="success" />
    <x-dash-card :value="$expiring" label="Mendekati Kadaluarsa" icon="flaticon-warning" type="warning" />
    <x-dash-card :value="$requal" label="Dalam Rekualifikasi" icon="flaticon2-reload" type="danger" />
</div>
