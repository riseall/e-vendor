@php
    $totalVendors = $vendors->count();
    if ($activeTab == 'monthly') {
        $countEvaluated = $monthlyEvaluations->count();
        $countBaik = $monthlyEvaluations->where('category', 'BAIK')->count();
        $countAttention = $monthlyEvaluations->whereIn('category', ['CUKUP', 'KURANG'])->count();
    } else {
        $countEvaluated = $annualEvaluations->count();
        $countBaik = $annualEvaluations->where('category', 'BAIK')->count();
        $countAttention = $annualEvaluations
            ->filter(function ($item) {
                return $item->has_score_drop_alert || $item->has_consecutive_low_alert || $item->category == 'KURANG';
            })
            ->count();
    }
@endphp

<div class="row mb-6">
    <x-dash-card :value="$totalVendors" label="Total Vendor Approved" icon="flaticon2-group" type="primary" />
    <x-dash-card :value="$countEvaluated" label="{{ $activeTab == 'monthly' ? 'Data Progress Berjalan' : 'Evaluasi Tahunan' }}"
        icon="flaticon2-layers-1" type="info" />
    <x-dash-card :value="$countBaik" label="Kategori Baik" icon="flaticon2-check-mark" type="success" />
    <x-dash-card :value="$countAttention" label="Perlu Perhatian / Warning" icon="flaticon2-warning" type="danger" />
</div>
