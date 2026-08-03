@php
    $formsColl = collect($forms ?? []);
    $totalCount = $formsColl->count();
    // $activeCount = $formsColl->filter(fn($f) => (bool) $f->is_active)->count();
    $bakuCount = $formsColl->filter(fn($f) => $f->material_type === 'bahan_baku')->count();
    $kemasCount = $formsColl->filter(fn($f) => $f->material_type === 'bahan_kemas')->count();
@endphp

<div class="row mb-6">
    <x-dash-card :value="$totalCount" label="Total Form Template" icon="flaticon2-layers-1" type="primary" />
    {{-- <x-dash-card :value="$activeCount" label="Form Aktif" icon="flaticon2-check-mark" type="success" /> --}}
    <x-dash-card :value="$bakuCount" label="Bahan Baku" icon="fas fa-pills" type="success" />
    <x-dash-card :value="$kemasCount" label="Bahan Kemas" icon="fas fa-flask" type="info" />
</div>
