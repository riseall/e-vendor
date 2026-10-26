@push('style')
    <style>
        #selectedProductsTable th {
            vertical-align: middle;
            font-size: 0.85rem;
            padding: 10px 12px;
        }

        #selectedProductsTable td {
            vertical-align: middle;
            position: relative;
            font-size: 0.85rem;
            padding: 10px 12px;
        }

        #selectedProductsContainer .table-responsive {
            min-height: {{ $isReadOnly ? 'auto' : '320px' }};
        }

        #selectedProductsTable .custom-file,
        #selectedProductsTable .custom-file-input {
            height: calc(1.5em + 0.65rem + 2px);
        }

        #selectedProductsTable .custom-file-label {
            height: calc(1.5em + 0.65rem + 2px);
            padding: 0.35rem 0.75rem;
            padding-right: 75px !important;
            font-size: 0.8rem;
            line-height: 1.5;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }

        #selectedProductsTable .custom-file-label::after {
            height: calc(1.5em + 0.65rem);
            padding: 0.35rem 0.65rem;
            line-height: 1.5;
            font-size: 0.8rem;
        }

        #selectedProductsTable .upload-status {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
        }

        #selectedProductsTable .prod-summary-docs .label {
            display: inline-flex;
            align-items: center;
            height: auto;
            padding: 0.22rem 0.55rem;
            font-size: 0.72rem;
            line-height: 1.25;
            border-radius: 0.35rem;
        }

        #selectedProductsTable .prod-summary-docs .label i {
            font-size: 0.72rem !important;
            margin-right: 4px;
            vertical-align: middle;
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
                        <span
                            class="form-text text-primary font-size-xs mt-2">{{ __('type_min_3_chars_raw_material') }}</span>
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
                        <span
                            class="form-text text-info font-size-xs mt-2">{{ __('select_packaging_from_list') }}</span>
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
                        <span
                            class="form-text text-success font-size-xs mt-2">{{ __('if_product_not_in_list') }}</span>
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
                    @if ($isReadOnly)
                        <th style="width: 28%;">{{ __('product') }}</th>
                        <th style="width: 24%;">{{ __('manufacturer_origin') }}</th>
                        <th style="width: 20%;">{{ __('supply_chain') }}</th>
                        <th style="width: 28%;">{{ __('documents_and_certifications') }}</th>
                    @else
                        <th style="width: 26%;">{{ __('product') }}</th>
                        <th style="width: 22%;">{{ __('manufacturer_origin') }}</th>
                        <th style="width: 18%;">{{ __('supply_chain') }}</th>
                        <th style="width: 22%;">{{ __('documents_and_certifications') }}</th>
                        <th style="width: 12%; text-align: center;">{{ __('action') }}</th>
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

{{-- Container Modal Edit Produk (di luar table agar tidak terganggu pagination DataTables) --}}
<div id="productModalsContainer">
    @if (!$isReadOnly)
        @foreach ($savedProducts as $prodId => $prodData)
            @include('admin.registrasi.steps.partials.product-modal', [
                'id' => $prodId,
                'name' => $prodData['product_name'],
                'data' => $prodData,
            ])
        @endforeach
    @endif
</div>

@if (!$isReadOnly)
    <template id="template-product-row">
        @include('admin.registrasi.steps.partials.product-card', [
            'id' => '__PRODUCT_ID__',
            'name' => '__PRODUCT_NAME__',
            'data' => [],
        ])
    </template>
    <template id="template-product-modal">
        @include('admin.registrasi.steps.partials.product-modal', [
            'id' => '__PRODUCT_ID__',
            'name' => '__PRODUCT_NAME__',
            'data' => [],
        ])
    </template>
@endif

@push('scripts')
    <script>
        $(document).ready(function() {
            const isReadOnly = {{ $isReadOnly ? 'true' : 'false' }};
            const productTable = $('#selectedProductsTable').DataTable({
                responsive: false,
                scrollX: false,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "{{ __('all') }}"]
                ],
                order: [],
                columnDefs: isReadOnly ? [] : [{
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
                // Helper Toast SweetAlert2 sesuai guideline workspace (.vnd-swal-toast)
                function showProductToast(type, title, message) {
                    const isSuccess = type === 'success';
                    const iconClass = isSuccess ? 'fa-check text-success' : 'fa-info text-info';
                    const btnClass = isSuccess ? 'btn-outline-success' : 'btn-outline-info';

                    Swal.fire({
                        html: '<div class="vnd-swal-toast-body">' +
                            '<div class="btn btn-icon ' + btnClass + ' btn-circle btn-sm m-0">' +
                            '<i class="fas ' + iconClass + '" style="font-size:1rem;"></i>' +
                            '</div>' +
                            '<div class="vnd-swal-toast-content">' +
                            '<div class="vnd-swal-toast__title">' + (title || "{{ __('success') }}") +
                            '</div>' +
                            '<div class="vnd-swal-toast__text">' + message + '</div>' +
                            '</div>' +
                            '</div>',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        showCloseButton: true,
                        timer: 3000,
                        timerProgressBar: true,
                        width: 360,
                        padding: '0',
                        customClass: {
                            popup: 'vnd-swal-toast shadow-sm',
                            closeButton: 'vnd-swal-toast__close',
                        },
                    });
                }

                function productExists(id) {
                    let exists = false;
                    productTable.rows().every(function() {
                        if (String($(this.node()).data('product-id')) === String(id)) {
                            exists = true;
                        }
                    });
                    return exists;
                }

                // Membuka modal produk baru dalam status pending (belum masuk tabel)
                function openProductModalForAdd(id, name, type) {
                    if (type === undefined) type = 'baku';

                    // Bersihkan jika ada modal pending sebelumnya dengan ID yang sama
                    $('#modal-product-' + id).remove();

                    let modalHtml = $('#template-product-modal').html()
                        .replace(/__PRODUCT_ID__/g, id)
                        .replace(/__PRODUCT_NAME__/g, name);

                    let $modal = $(modalHtml);
                    $modal.attr('data-pending-add', 'true');
                    $modal.attr('data-product-name', name);
                    $modal.attr('data-product-id', id);
                    $modal.attr('data-product-type', type);

                    $('#productModalsContainer').append($modal);

                    $modal.find('.product-type-radio[value="' + type + '"]').prop('checked', true).trigger(
                    'change');

                    if ($.fn.selectpicker) {
                        $modal.find('.selectpicker').selectpicker();
                    }

                    $modal.modal('show');
                }

                // Backward-compatibility alias
                function addProductRow(id, name, type, autoOpenModal) {
                    if (autoOpenModal === undefined || autoOpenModal) {
                        openProductModalForAdd(id, name, type);
                    } else {
                        let rowHtml = $('#template-product-row').html()
                            .replace(/__PRODUCT_ID__/g, id)
                            .replace(/__PRODUCT_NAME__/g, name);
                        productTable.row.add($(rowHtml)[0]).draw(false);
                    }
                }

                // Inisialisasi awal untuk selectpicker di dalam modals
                if ($.fn.selectpicker) {
                    $('#productModalsContainer .selectpicker').selectpicker();
                }

                // Pastikan selectpicker selalu rapi saat modal dibuka
                $(document).on('shown.bs.modal', '[id^="modal-product-"]', function() {
                    $(this).find('.selectpicker').selectpicker('refresh');
                });

                // Bersihkan modal jika pengguna membatalkan/menutup modal sebelum disimpan
                $(document).on('hidden.bs.modal', '[id^="modal-product-"]', function() {
                    const $modal = $(this);
                    if ($modal.attr('data-pending-add') === 'true') {
                        $modal.remove();
                    }
                });

                // Toggle dinamis kewajiban & tampilan GMP berdasarkan kategori produk (Bahan Baku vs Kemas)
                $(document).on('change', '.product-type-radio', function() {
                    const id = $(this).data('id');
                    const isBaku = $(this).val() === 'baku';
                    const $modal = $('#modal-product-' + id);
                    const $gmpCol = $modal.find('.gmp-col-' + id);
                    const $supplyCol = $modal.find('.supply-col-' + id);
                    const $agencyCol = $modal.find('.agency-col-' + id);
                    const $fileInput = $gmpCol.find('input[type="file"]');
                    const hasExisting = Boolean($gmpCol.find('input[name*="existing_gmp_file"]').val());

                    let $label = $gmpCol.find('label').first();
                    $label.find('.text-danger').remove();

                    if (isBaku) {
                        $supplyCol.removeClass('col-md-6').addClass('col-md-4');
                        $agencyCol.removeClass('col-md-6').addClass('col-md-4');
                        $gmpCol.stop(true, true).fadeIn(150);

                        $label.append(' <span class="text-danger font-weight-bold">*</span>');
                        if (!hasExisting) {
                            $fileInput.prop('required', true);
                        }
                    } else {
                        $gmpCol.hide();
                        $supplyCol.removeClass('col-md-4').addClass('col-md-6');
                        $agencyCol.removeClass('col-md-4').addClass('col-md-6');

                        $fileInput.prop('required', false);
                        $gmpCol.find('.is-invalid').removeClass('is-invalid');
                        $gmpCol.find('.invalid-feedback.dynamic-error').remove();
                    }
                });

                // Update ringkasan pada baris tabel
                function updateProductRowSummary(id) {
                    const $modal = $('#modal-product-' + id);
                    const $row = $('#product-row-' + id);
                    if (!$modal.length || !$row.length) return;

                    const manufaktur = $modal.find('input[name="products[' + id + '][manufaktur]"]').val() || '-';
                    const negara = $modal.find('select[name="products[' + id + '][negara]"]').val() || '';
                    const rantai = $modal.find('select[name="products[' + id + '][rantai_pasok]"]').val() || '-';

                    // Cek kelengkapan dokumen surat & GMP
                    const hasSurat = Boolean(
                        $modal.find('input[name="products[' + id + '][existing_file_surat]"]').val() ||
                        $modal.find('input[type="hidden"][name="products[' + id + '][file_surat]"]').val() ||
                        $modal.find('input[data-field="products[' + id + '][file_surat]"]').val() ||
                        ($modal.find('input[data-field="products[' + id + '][file_surat]"]')[0] &&
                         $modal.find('input[data-field="products[' + id + '][file_surat]"]')[0].files &&
                         $modal.find('input[data-field="products[' + id + '][file_surat]"]')[0].files.length > 0)
                    );

                    const isBaku = $modal.find('.product-type-radio:checked').val() === 'baku';
                    const hasGmp = isBaku && Boolean(
                        $modal.find('input[name="products[' + id + '][existing_gmp_file]"]').val() ||
                        $modal.find('input[type="hidden"][name="products[' + id + '][gmp_file]"]').val() ||
                        $modal.find('input[data-field="products[' + id + '][gmp_file]"]').val() ||
                        ($modal.find('input[data-field="products[' + id + '][gmp_file]"]')[0] &&
                         $modal.find('input[data-field="products[' + id + '][gmp_file]"]')[0].files &&
                         $modal.find('input[data-field="products[' + id + '][gmp_file]"]')[0].files.length > 0)
                    );

                    $row.find('.prod-summary-manufaktur').text(manufaktur);
                    $row.find('.prod-summary-negara').html(negara ?
                        '<i class="fas fa-map-marker-alt text-muted mr-1" style="font-size: 0.7rem;"></i>' +
                        negara : '');
                    $row.find('.prod-summary-rantai').text(rantai);

                    let badges = '';
                    if (hasSurat) badges +=
                        '<span class="label label-light-primary label-inline font-size-xs font-weight-bolder mr-1 mb-1"><i class="fas fa-file-alt text-primary mr-1" style="font-size: 0.72rem;"></i>{{ __('agency_letter') }}</span>';
                    if (hasGmp) badges +=
                        '<span class="label label-light-info label-inline font-size-xs font-weight-bolder mr-1 mb-1"><i class="fas fa-microscope text-info mr-1" style="font-size: 0.72rem;"></i>GMP</span>';

                    const certKeys = ['tkdn', 'sni', 'halal', 'bse_tse'];
                    const certLabels = {
                        tkdn: 'TKDN',
                        sni: 'SNI',
                        halal: 'Halal',
                        bse_tse: 'BSE/TSE'
                    };
                    const certStyles = {
                        tkdn: 'dark',
                        sni: 'warning',
                        halal: 'success',
                        bse_tse: 'danger'
                    };
                    const certIcons = {
                        tkdn: 'fas fa-cogs text-dark',
                        sni: 'fas fa-award text-warning',
                        halal: 'fas fa-leaf text-success',
                        bse_tse: 'fas fa-heartbeat text-danger'
                    };

                    certKeys.forEach(function(cert) {
                        const isYes = $modal.find('select[name="products[' + id + '][has_' + cert + ']"]').val() === 'yes';
                        const hasCertFile = Boolean(
                            $modal.find('input[name="products[' + id + '][existing_' + cert + '_file]"]').val() ||
                            $modal.find('input[type="hidden"][name="products[' + id + '][' + cert + '_file]"]').val() ||
                            $modal.find('input[data-field="products[' + id + '][' + cert + '_file]"]').val() ||
                            ($modal.find('input[data-field="products[' + id + '][' + cert + '_file]"]')[0] &&
                             $modal.find('input[data-field="products[' + id + '][' + cert + '_file]"]')[0].files &&
                             $modal.find('input[data-field="products[' + id + '][' + cert + '_file]"]')[0].files.length > 0)
                        );

                        // Hanya tampilkan badge jika user memilih 'yes' DAN dokumen file sudah diisi/diupload
                        if (isYes && hasCertFile) {
                            badges += '<span class="label label-light-' + certStyles[cert] +
                                ' label-inline font-size-xs font-weight-bolder mr-1 mb-1"><i class="' +
                                certIcons[cert] + ' mr-1" style="font-size: 0.72rem;"></i>' + certLabels[cert] + '</span>';
                        }
                    });

                    if (!badges) {
                        badges =
                            '<span class="label label-light-warning label-inline font-size-xs font-weight-bold">{{ __('not_filled') }}</span>';
                    }
                    $row.find('.prod-summary-docs').html(badges);
                }

                // Klik Simpan pada Modal: Tambah baris baru jika pending, atau perbarui jika edit
                $(document).on('click', '.btn-save-modal-product', function() {
                    const id = $(this).data('id');
                    const $modal = $('#modal-product-' + id);
                    if (!$modal.length) return;

                    const isPending = $modal.attr('data-pending-add') === 'true';

                    if (isPending) {
                        const name = $modal.attr('data-product-name') || $modal.find('.modal-title').text()
                            .trim();
                        let rowHtml = $('#template-product-row').html()
                            .replace(/__PRODUCT_ID__/g, id)
                            .replace(/__PRODUCT_NAME__/g, name);

                        productTable.row.add($(rowHtml)[0]).draw(false);
                        $modal.removeAttr('data-pending-add');

                        showProductToast('success', "{{ __('success') }}",
                            "{{ __('product_added_to_list') }}");
                    } else {
                        showProductToast('success', "{{ __('success') }}",
                            "{{ __('product_details_updated') }}");
                    }

                    updateProductRowSummary(id);
                    $modal.modal('hide');
                });

                // 1. Select2 untuk cari produk (QAD - Bahan Baku)
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
                        openProductModalForAdd(p.id, p.product_name, 'baku');
                    }
                    $(this).val(null).trigger('change');
                });

                // 1b. Select2 untuk Bahan Kemas (Non-Bahan Baku)
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
                        openProductModalForAdd(id, name, 'kemas');
                    }
                    $(this).val(null).trigger('change');
                });

                // 1c. Tambah Produk Manual (Free Text - Default Baku)
                $('#btnCustomProduct').on('click', function() {
                    const name = $('#customProductName').val().trim();
                    if (!name) return;

                    const id = 'CUSTOM-' + Date.now();
                    openProductModalForAdd(id, name, 'baku');
                    $('#customProductName').val('');
                });

                // 2. Hapus Row & Modal terkait
                $('#selectedProductsTable').on('click', '.btn-hapus-produk', function() {
                    const id = $(this).data('id');
                    productTable.row($(this).closest('tr')).remove().draw(false);
                    $('#modal-product-' + id).remove();
                });

                // 3. Toggle input detail sertifikat di dalam modal
                $('#productModalsContainer').on('change', '.product-cert-toggle', function() {
                    const target = $(this).data('target');
                    const $target = $(target);
                    const $fileInput = $target.find('input[type="file"]');
                    const hasExistingFile = Boolean(
                        $(this).closest('.border').find('input[type="hidden"][name*="existing_"]').val()
                    );

                    if ($(this).val() === 'yes') {
                        $fileInput.prop('required', !hasExistingFile);
                        $target.removeClass('d-none').slideDown(150);
                    } else {
                        $fileInput.prop('required', false);
                        $target.slideUp(150, function() {
                            $fileInput.val('');
                            $fileInput.siblings('.custom-file-label').text(
                                "{{ __('choose_file') }}");
                        });
                    }
                });
            @endif
        });
    </script>
@endpush
