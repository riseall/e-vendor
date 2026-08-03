@php
    $collection = $suppliers->getCollection();
    $countTotal = $suppliers->total();
    $countLow = $collection->filter(fn($s) => strtolower($s->risk_level ?: 'low') === 'low')->count();
    $countMedium = $collection->filter(fn($s) => strtolower($s->risk_level) === 'medium')->count();
    $countHigh = $collection->filter(fn($s) => strtolower($s->risk_level) === 'high')->count();
@endphp

<div class="row mb-6">
    <x-dash-card :value="$countTotal" label="Total Supplier Approved" icon="flaticon2-layers-1" type="primary" />
    <x-dash-card :value="$countLow" label="Low Risk" icon="flaticon2-check-mark" type="success" />
    <x-dash-card :value="$countMedium" label="Medium Risk" icon="flaticon-warning" type="warning" />
    <x-dash-card :value="$countHigh" label="High Risk" icon="flaticon-danger" type="danger" />
</div>
