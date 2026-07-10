@push('style')
    <link href="{{ asset('plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #selectedProductsTable td {
            vertical-align: top;
        }

        #selectedProductsTable .custom-file-label {
            height: calc(1.5em + 0.65rem + 2px);
            padding: 0.35rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        #selectedProductsTable .custom-file-label::after {
            height: calc(1.5em + 0.65rem);
            padding: 0.35rem 0.75rem;
            line-height: 1.5;
        }
    </style>
@endpush

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
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="selectedProductsTable">
            <thead class="thead-light">
                <tr>
                    <th>Produk</th>
                    <th>Manufaktur / Asal <span class="text-danger">*</span></th>
                    <th>Negara</th>
                    <th>Rantai Pasok <span class="text-danger">*</span></th>
                    <th>Surat Keagenan <span class="text-danger">*</span></th>
                    <th>TKDN</th>
                    <th>SNI</th>
                    <th>Halal</th>
                    <th>BSE/TSE</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $savedProducts = $draft['products'] ?? []; @endphp
                @foreach ($savedProducts as $prodId => $prodData)
                    @include('admin.registrasi.steps.partials.product-card', [
                        'id' => $prodId,
                        'name' => $prodData['product_name'],
                        'data' => $prodData,
                    ])
                @endforeach
            </tbody>
        </table>
    </div>
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
    <script src="{{ asset('plugins/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(document).ready(function() {
            const productTable = $('#selectedProductsTable').DataTable({
                responsive: false,
                scrollX: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'Semua']
                ],
                order: [],
                columnDefs: [{
                    targets: -1,
                    orderable: false,
                    searchable: false
                }],
                // language: {
                //     search: 'Cari:',
                //     lengthMenu: 'Tampilkan _MENU_ produk',
                //     info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ produk',
                //     infoEmpty: 'Belum ada produk',
                //     emptyTable: 'Belum ada produk yang dipilih',
                //     zeroRecords: 'Produk tidak ditemukan',
                //     paginate: {
                //         previous: 'Sebelumnya',
                //         next: 'Berikutnya'
                //     }
                // }
            });
            let productTableSubmitState = null;

            window.prepareProductRowsForSubmit = function() {
                productTableSubmitState = {
                    length: productTable.page.len(),
                    page: productTable.page()
                };
                productTable.page.len(-1).draw(false);
            };

            window.restoreProductRowsAfterSubmit = function() {
                if (!productTableSubmitState) {
                    return;
                }

                productTable.page.len(productTableSubmitState.length).draw(false);
                if (productTableSubmitState.page < productTable.page.info().pages) {
                    productTable.page(productTableSubmitState.page).draw(false);
                }
                productTableSubmitState = null;
            };

            window.refreshProductTable = function() {
                productTable.columns.adjust().draw(false);
            };

            @if (!$isReadOnly)
                function productExists(id) {
                    let exists = false;
                    productTable.rows().every(function() {
                        if (String($(this.node()).data('product-id')) === String(id)) {
                            exists = true;
                        }
                    });
                    return exists;
                }

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
                    if (productExists(p.id)) {
                        Swal.fire({
                            text: 'Produk sudah ada di daftar.',
                            icon: 'warning'
                        });
                    } else {
                        let html = $('#template-product-card').html()
                            .replace(/__PRODUCT_ID__/g, p.id)
                            .replace(/__PRODUCT_NAME__/g, p.product_name);
                        productTable.row.add($(html)[0]).draw(false);
                        window.refreshProductTable();
                    }
                    $(this).val(null).trigger('change');
                });

                // 2. Hapus Row
                $('#selectedProductsTable').on('click', '.btn-hapus-produk', function() {
                    productTable.row($(this).closest('tr')).remove().draw(false);
                });

                // 3. Nama File Upload
                $('#selectedProductsTable').on('change', '.product-file-input', function() {
                    let name = $(this).val().split('\\').pop();
                    $(this).siblings('.custom-file-label').text(name || 'Pilih File');
                });

                // 4. Toggle input detail sertifikat
                $('#selectedProductsTable').on('change', '.product-cert-toggle', function() {
                    const target = $(this).data('target');
                    const $target = $(target);
                    const $fileInput = $target.find('input[type="file"]');
                    const hasExistingFile = Boolean(
                        $(this).closest('td').find('input[type="hidden"][name*="existing_"]').val()
                    );

                    if ($(this).val() === 'yes') {
                        $fileInput.prop('required', !hasExistingFile);
                        $target.removeClass('d-none').slideDown(150);
                    } else {
                        $fileInput.prop('required', false);
                        $target.slideUp(150, function() {
                            $fileInput.val('');
                            $fileInput.siblings('.custom-file-label').text('Pilih File');
                        });
                    }
                });
            @endif
        });
    </script>
@endpush
