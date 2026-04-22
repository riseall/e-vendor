<div class="form-section-title">Daftar Produk yang Disuplai</div>
<p class="text-muted mb-4">Pilih produk, lalu lengkapi informasi manufaktur dan rantai pasok untuk masing-masing
    produk.</p>

@if (!$isReadOnly)
    <div class="form-group">
        <label class="font-weight-bold">Cari & Tambah Produk <span class="text-danger">*</span></label>
        <select id="erpProductSelect" class="form-control form-control-solid" style="width: 100%;">
        </select>
    </div>
@endif

<div id="selectedProductsContainer">
    @php $savedProducts = $draft['products'] ?? []; @endphp
    @foreach ($savedProducts as $prodId => $prodData)
        @include('admin.registrasi.steps.partials.product-card', [
            'id' => $prodId,
            'name' => $prodData['product_name'],
            'data' => $prodData,
        ])
    @endforeach
</div>

@if (!$isReadOnly)
    <template id="template-product-card">
        @include('admin.registrasi.steps.partials.product-card', [
            'id' => '__PRODUCT_ID__',
            'name' => '__PRODUCT_NAME__',
            'data' => [],
        ])
    </template>
@endif


@push('scripts')
    <script>
        $(document).ready(function() {
            if ($('#erpProductSelect').length) {
                const urlProd = '{{ route('search-products') }}';

                $('#erpProductSelect').select2({
                    placeholder: "Ketik Nama atau Kode Produk...",
                    minimumInputLength: 3,
                    allowClear: true,
                    ajax: {
                        url: urlProd,
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            return {
                                keyword: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    }
                });

                $('#erpProductSelect').on('select2:select', function(e) {
                    var productId = e.params.data.id;
                    var productName = e.params.data
                        .product_name;

                    if ($('#product-card-' + productId).length > 0) {
                        Swal.fire({
                            text: 'Produk ini sudah ditambahkan.',
                            icon: 'warning'
                        });
                        $(this).val(null).trigger('change');
                        return;
                    }
                    // Ambil Template Card
                    var templateHtml = $('#template-product-card').html();

                    // Ganti Placeholder dengan Data Real
                    var cardHtml = templateHtml
                        .replace(/__PRODUCT_ID__/g, productId)
                        .replace(/__PRODUCT_NAME__/g, productName);

                    // Append ke Container
                    $('#selectedProductsContainer').append(cardHtml);

                    // Reset Select2 ke posisi kosong
                    $(this).val(null).trigger('change');
                });
            }

            // Hapus Card Produk
            $('#selectedProductsContainer').on('click', '.btn-hapus-produk', function() {
                var id = $(this).data('id');
                $('#product-card-' + id).slideUp('fast', function() {
                    $(this).remove();
                });
            });

            // Nama File Custom Upload
            $('#selectedProductsContainer').on('change', '.product-file-input', function() {
                var name = $(this).val().split('\\').pop();
                $(this).siblings('.custom-file-label').text(name || 'Browse...');
            });

            // SMART TOGGLE (Mengendalikan TKDN, SNI, dan Halal sekaligus!)
            $(document).on('change', '.toggle-select select', function() {
                var target = $(this).closest('.toggle-select').data('target');
                if ($(this).val() === 'yes') {
                    $(target).slideDown();
                } else {
                    $(target).slideUp();
                }
            });
        });
    </script>
@endpush
