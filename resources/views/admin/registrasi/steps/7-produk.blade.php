<div class="form-section-title">Daftar Produk yang Disuplai</div>
<p class="text-muted mb-6">Pilih produk, lalu lengkapi informasi manufaktur dan rantai pasok.</p>

@if (!$isReadOnly)
    <div class="form-group bg-light-primary p-5 rounded border border-primary border-dashed">
        <label class="font-weight-bolder">Cari & Tambah Produk <span class="text-danger">*</span></label>
        <select id="erpProductSelect" class="form-control select2" style="width: 100%;"></select>
        <span class="form-text text-primary font-size-xs mt-2">Ketik minimal 3 karakter nama produk yang ingin
            disuplai.</span>
    </div>
@endif

<div id="selectedProductsContainer" class="mt-8">
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                // 1. Select2 untuk cari produk
                $('#erpProductSelect').select2({
                    placeholder: "Cari Produk...",
                    ajax: {
                        url: '{{ route('search-products') }}',
                        dataType: 'json',
                        delay: 400,
                        data: (params) => ({
                            keyword: params.term,
                            page: params.page || 1
                        }),
                        processResults: (data) => ({
                            results: data.results,
                            pagination: data.pagination
                        })
                    }
                }).on('select2:select', function(e) {
                    const p = e.params.data;
                    if ($('#product-card-' + p.id).length > 0) {
                        Swal.fire({
                            text: 'Produk sudah ada di daftar.',
                            icon: 'warning'
                        });
                    } else {
                        let html = $('#template-product-card').html()
                            .replace(/__PRODUCT_ID__/g, p.id)
                            .replace(/__PRODUCT_NAME__/g, p.product_name);
                        $('#selectedProductsContainer').append(html);
                        // Re-init selectpicker jika card baru punya selectpicker
                        $('.selectpicker').selectpicker('refresh');
                    }
                    $(this).val(null).trigger('change');
                });

                // 2. Hapus Card
                $('#selectedProductsContainer').on('click', '.btn-hapus-produk', function() {
                    $(this).closest('.product-card').slideUp('fast', function() {
                        $(this).remove();
                    });
                });

                // 3. Nama File Upload
                $('#selectedProductsContainer').on('change', '.product-file-input', function() {
                    let name = $(this).val().split('\\').pop();
                    $(this).siblings('.custom-file-label').text(name || 'Pilih File');
                });
            });
        </script>
    @endpush
@endif
