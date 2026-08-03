@props([
    'value' => 0,
    'label' => '',
    'icon' => 'flaticon2-layers-1',
    'type' => 'primary',
    'color' => null,
    'col' => 'col-6 col-sm mb-3 mb-sm-0',
])

@php
    $statType = $type;
    if ($color && $type === 'primary') {
        if (strpos($color, 'success') !== false) {
            $statType = 'success';
        } elseif (strpos($color, 'warning') !== false) {
            $statType = 'warning';
        } elseif (strpos($color, 'danger') !== false) {
            $statType = 'danger';
        } elseif (strpos($color, 'info') !== false) {
            $statType = 'info';
        }
    }

    $iconClass = strpos($icon, 'text-') === false ? $icon . ' text-white' : $icon;
@endphp

<div class="{{ $col }}">
    <div class="vnd-stat vnd-stat--{{ $statType }}">
        <div class="vnd-stat-icon">
            <i class="{{ $iconClass }}"></i>
        </div>
        <div>
            <div class="vnd-stat-num">{{ $value }}</div>
            <div class="vnd-stat-lbl">{{ $label }}</div>
        </div>
    </div>
</div>
