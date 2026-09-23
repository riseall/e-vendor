@push('style')
    <style>
        #selectedProductsTable td {
            vertical-align: top;
            position: relative;
        }

        #selectedProductsContainer .table-responsive {
            min-height: 320px;
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

<div class="form-section-title">{{ __('supplied_products_list') }}</div>

@if (!$isReadOnly)
    <p class="text-muted mb-4">{{ __('select_product_complete_info') }}</p>


    <div class="alert alert-custom alert-light-warning fade show mb-8" role="alert" style="border: 1px dashed #f9a825;">
        <div class="alert-icon">
            <i class="fas fa-info-circle text-warning"></i>
        </div>
        <div class="alert-text font-weight-normal">
            {{ __('traceability_guide_notice') }}
            <a href="#" class="font-weight-bolder text-primary ml-1" data-toggle="modal"
                data-target="#modalRantaiPasok">
                <u>{{ __('view_guide_here') }}</u>
            </a>
        </div>
    </div>

    <!-- Modal Panduan Rantai Pasok -->
    <div class="modal fade" id="modalRantaiPasok" tabindex="-1" role="dialog" aria-labelledby="modalRantaiPasokLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRantaiPasokLabel">{{ __('supply_chain_traceability_level') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset('images/rantai_pasok.png') }}" alt="{{ __('supply_chain_guide') }}"
                        class="img-fluid rounded">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger font-weight-bold"
                        data-dismiss="modal">{{ __('close') }}</button>
                </div>
            </div>
        </div>
    </div>
    @php
        $bahanKemasList = \App\Models\PackagingMaterial::orderBy('name')->get();
    @endphp

    <div class="card card-custom mb-8">
        <div class="card-header card-header-tabs-line">
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-bold nav-tabs-line" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab_qad">
                            <span class="nav-icon"><i class="fas fa-pills"></i></span>
                            <span class="nav-text">{{ __('raw_material') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab_kemas">
                            <span class="nav-icon"><i class="fas fa-flask"></i></span>
                            <span class="nav-text">{{ __('packaging_material') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab_manual">
                            <span class="nav-icon"><i class="fas fa-pencil-alt"></i></span>
                            <span class="nav-text">{{ __('free_text') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body">
            <div class="tab-content">
                {{-- 1. QAD --}}
                <div class="tab-pane fade show active" id="tab_qad" role="tabpanel">
                    <div class="form-group bg-light-primary p-6 rounded mb-0">
                        <label class="font-weight-bolder text-primary">{{ __('search_select_raw_material') }}<span
                                class="text-danger">*</span></label>
                        <select id="erpProductSelect" class="form-control select2" style="width: 100%;">
                            <option value=""></option>
                        </select>
                        <span class="form-text text-primary font-size-xs mt-2">{{ __('type_min_3_chars_raw_material') }}</span>
                    </div>
                </div>

                {{-- 2. Bahan Kemas --}}
                <div class="tab-pane fade" id="tab_kemas" role="tabpanel">
                    <div class="form-group bg-light-info p-6 rounded mb-0">
                        <label class="font-weight-bolder text-info">{{ __('select_packaging_material') }}</label>
                        <select id="kemasProductSelect" class="form-control select2" style="width: 100%;">
                            <option value=""></option>
                            @foreach ($bahanKemasList as $kemas)
                                <option value="KEMAS-{{ $kemas->id }}" data-name="{{ $kemas->name }}">
                                    {{ $kemas->name }}</option>
                            @endforeach
                        </select>
                        <span class="form-text text-info font-size-xs mt-2">{{ __('select_packaging_from_list') }}</span>
                    </div>
                </div>

                {{-- 3. Free Text --}}
                <div class="tab-pane fade" id="tab_manual" role="tabpanel">
                    <div class="form-group bg-light-success p-6 rounded mb-0">
                        <label class="font-weight-bolder text-success">{{ __('add_manual') }}</label>
                        <div class="input-group">
                            <input type="text" id="customProductName" class="form-control form-control-solid"
                                placeholder="{{ __('type_product_name') }}">
                            <div class="input-group-append">
                                <button class="btn btn-success font-weight-bold" type="button"
                                    id="btnCustomProduct">{{ __('add') }}</button>
                            </div>
                        </div>
                        <span class="form-text text-success font-size-xs mt-2">{{ __('if_product_not_in_list') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div id="selectedProductsContainer" class="mt-8">
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="selectedProductsTable">
            <thead class="thead-light">
                <tr>
                    <th>{{ __('product') }}</th>
                    <th>{{ __('manufacturer_origin') }} <span class="text-danger">*</span></th>
                    <th>{{ __('country') }}</th>
                    <th>{{ __('supply_chain') }} <span class="text-danger">*</span></th>
                    <th>{{ __('agency_letter') }} <span class="text-danger">*</span></th>
                    <th>TKDN</th>
                    <th>SNI</th>
                    <th>Halal</th>
                    <th>BSE/TSE</th>
                    @if (!$isReadOnly)
                        <th>{{ __('action') }}</th>
                    @endif
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
    <script>
        $(document).ready(function() {
            const productTable = $('#selectedProductsTable').DataTable({
                responsive: false,
                scrollX: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "{{ __('all') }}"]
                ],
                order: [],
                columnDefs: [{
                    targets: -1,
                    orderable: false,
                    searchable: false
                }],
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

                function addProductRow(id, name) {
                    let html = $('#template-product-card').html()
                        .replace(/__PRODUCT_ID__/g, id)
                        .replace(/__PRODUCT_NAME__/g, name);
                    let rowNode = productTable.row.add($(html)[0]).draw(false).node();
                    if ($.fn.selectpicker) {
                        $(rowNode).find('.selectpicker').selectpicker();
                    }
                    window.refreshProductTable();
                }

                // Inisialisasi awal untuk row yang sudah ada di tabel
                if ($.fn.selectpicker) {
                    $('#selectedProductsTable .selectpicker').selectpicker();
                }

                // 1. Select2 untuk cari produk (QAD)
                $('#erpProductSelect').select2({
                    width: '100%',
                    placeholder: "{{ __('search_product') }}...",
                    allowClear: true,
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
                            text: "{{ __('product_already_in_list') }}",
                            icon: 'warning'
                        });
                    } else {
                        addProductRow(p.id, p.product_name);
                    }
                    $(this).val(null).trigger('change');
                });

                // 1b. Select2 untuk Bahan Kemas
                $('#kemasProductSelect').select2({
                    width: '100%',
                    placeholder: "{{ __('select_packaging_material') }}...",
                    allowClear: true
                }).on('select2:select', function(e) {
                    const el = $(e.params.data.element);
                    const id = el.val();
                    const name = el.data('name');

                    if (!id) return;

                    if (productExists(id)) {
                        Swal.fire({
                            text: "{{ __('packaging_already_in_list') }}",
                            icon: 'warning'
                        });
                    } else {
                        addProductRow(id, name);
                    }
                    $(this).val(null).trigger('change');
                });

                // 1c. Tambah Produk Manual (Free Text)
                $('#btnCustomProduct').on('click', function() {
                    const name = $('#customProductName').val().trim();
                    if (!name) return;

                    // Generate pseudo-ID based on timestamp to ensure uniqueness
                    const id = 'CUSTOM-' + Date.now();

                    addProductRow(id, name);

                    $('#customProductName').val(''); // reset input
                });

                // 2. Hapus Row
                $('#selectedProductsTable').on('click', '.btn-hapus-produk', function() {
                    productTable.row($(this).closest('tr')).remove().draw(false);
                });

                // 3. (Dihapus: Nama File Upload dihandle global oleh ajax-file-upload)

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
                            $fileInput.siblings('.custom-file-label').text("{{ __('choose_file') }}");
                        });
                    }
                });
            @endif
        });
    </script>
@endpush
