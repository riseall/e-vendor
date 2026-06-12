@props(['value' => 0, 'label', 'icon', 'color' => 'text-dark'])

<div class="col-xl col-lg-4 col-md-4 col-sm-6 col-6">
    <div class="card card-custom bgi-no-repeat card-stretch gutter-b">
        <div class="card-body py-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 d-block">
                    {{ $value }}
                </span>
                <i class="fas {{ $icon }} {{ $color }} icon-lg"></i>
            </div>
            <span class="font-weight-bold text-muted font-size-sm">{{ $label }}</span>
        </div>
    </div>
</div>
