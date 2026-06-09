<h4 class="font-weight-bold text-dark mb-2">Pilih Kategori Vendor</h4>
<p class="text-muted mb-8">Pilih satu atau lebih kategori
    yang sesuai dengan jenis usaha Anda. Formulir khusus
    akan muncul sesuai kategori yang dipilih.</p>

@php
    $categories = [
        [
            'id' => 1,
            'icon' => 'flaticon2-box-1',
            'title' => 'Bahan Baku, Bahan Kemas, Produk Jadi & Alkes',
            'sub' => 'Bahan Baku & Farmasi',
        ],
        [
            'id' => 2,
            'icon' => 'flaticon2-settings',
            'title' => 'Varia Teknik dan Umum, Reagen, Barang Investasi',
            'sub' => 'Varia Teknik & Umum',
        ],
        [
            'id' => 3,
            'icon' => 'fas fa-shipping-fast',
            'title' => 'Jasa Transporter, Forwarder & PPJK',
            'sub' => 'Transporter & Forwarder',
        ],
        [
            'id' => 4,
            'icon' => 'fas fa-city',
            'title' => 'Jasa Kontraktor, Perbaikan & Pemeliharaan',
            'sub' => 'Kontraktor & Perbaikan',
        ],
        [
            'id' => 5,
            'icon' => 'flaticon2-analytics',
            'title' => 'Jasa Pengujian Laboratorium, Kalibrasi, Radiasi & Sertifikasi',
            'sub' => 'Pengujian & Kalibrasi',
        ],
        [
            'id' => 6,
            'icon' => 'fas fa-clinic-medical',
            'title' => 'Jasa Facility Service, Sewa, Security, Katering & MCU',
            'sub' => 'Facility Service',
        ],
        [
            'id' => 7,
            'icon' => 'fas fa-people-carry',
            'title' => 'Jasa Pelatihan,Konsultan, Notaris & Alih Daya Tenaga Kerja',
            'sub' => 'Pelatihan & Konsultan',
        ],
        [
            'id' => 8,
            'icon' => 'fas fa-bullhorn',
            'title' => 'Jasa Agency Advertising',
            'sub' => 'Media & promosi',
        ],
    ];
    $selectedCategories = $draft['categories'] ?? [];
@endphp

<div class="row">
    @foreach ($categories as $cat)
        <div class="col-xl-3 col-lg-4 col-md-6 mb-5">

            {{-- Hidden checkbox --}}
            <input type="checkbox" name="categories[]" id="cat_{{ $cat['id'] }}" value="{{ $cat['id'] }}"
                class="category-checkbox d-none" {{ in_array($cat['id'], $selectedCategories) ? 'checked' : '' }}
                {{ $isReadOnly ? 'disabled' : '' }}>

            {{-- Card --}}
            <div class="cat-card {{ in_array($cat['id'], $selectedCategories) ? 'selected' : '' }} {{ $isReadOnly ? 'readonly' : '' }}"
                @if (!$isReadOnly) onclick="toggleCat({{ $cat['id'] }})" @endif>

                {{-- Checkmark badge --}}
                <div class="cat-check">
                    <svg width="11" height="11" viewBox="0 0 11 11">
                        <polyline points="2,5.5 4.5,8 9,3" stroke="white" stroke-width="1.8" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

                <div class="cat-icon">
                    <i class="{{ $cat['icon'] }} text-primary" style="font-size:20px"></i>
                </div>
                <div class="cat-title">{{ $cat['title'] }}</div>
                <div class="cat-sub">{{ $cat['sub'] }}</div>
            </div>

        </div>
    @endforeach
</div>

{{-- Info bar kategori terpilih --}}
<div class="selected-info-bar" id="selectedInfo">
    <i class="flaticon2-information text-success mr-2"></i>
    <strong id="selCount">0</strong> kategori dipilih:
    <span id="selTags" class="ml-1"></span>
</div>

{{-- Error --}}
<div id="errKategori" class="text-danger font-size-md mt-3 d-none">
    <div class="alert alert-custom alert-notice alert-light-danger fade show mb-5" role="alert">
        <div class="alert-icon"><i class="flaticon-warning icon-md"></i></div>
        <div class="alert-text">Pilih minimal 1 kategori untuk melanjutkan.</div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        var categoryLabels = {
            1: 'Bahan Baku & Farmasi',
            2: 'Varia Teknik & Umum',
            3: 'Transporter & Forwarder',
            4: 'Kontraktor & Perbaikan',
            5: 'Pengujian & Kalibrasi',
            6: 'Facility Service',
            7: 'Pelatihan & Konsultan',
            8: 'Media & promosi',
        };

        // TOGGLE CATEGORY CARD
        $(document).ready(function() {
            window.toggleCat = function(id) {
                @if ($isReadOnly)
                    return;
                @endif

                var cb = $('#cat_' + id);
                var card = cb.closest('.col-xl-3, .col-lg-4, .col-md-6').find('.cat-card');
                cb.prop('checked', !cb.prop('checked'));
                card.toggleClass('selected', cb.prop('checked'));
                updateCatInfo();
                // Preview nav langsung update saat pilih kategori
                buildActiveSteps();
                renderNav();
            };

            function updateCatInfo() {
                var checked = $('.category-checkbox:checked');
                if (checked.length === 0) {
                    $('#selectedInfo').hide();
                    return;
                }
                var tags = '';
                checked.each(function() {
                    tags += '<span class="badge-cat">' + categoryLabels[$(this).val()] + '</span>';
                });
                $('#selCount').text(checked.length);
                $('#selTags').html(tags);
                $('#selectedInfo').show();
            }

            updateCatInfo();
        })
    </script>
@endpush
