<form action="{{ route('admin.evaluasi.store-batch-monthly') }}" method="POST" id="formBatchMonthly">
    @csrf
    <input type="hidden" name="year" value="{{ $year }}">
    <input type="hidden" name="month" value="{{ $month }}">

    <div class="d-flex align-items-center justify-content-between mb-5 flex-wrap">
        <div class="d-flex flex-column mr-4 mb-2 mb-md-0">
            <h5 class="font-weight-bolder text-dark mb-1">
                <span class="svg-icon svg-icon-primary svg-icon-lg mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24"
                        version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <path
                                d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z"
                                fill="#000000" />
                            <path
                                d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z"
                                fill="#000000" opacity="0.3" />
                        </g>
                    </svg>
                </span>
                Progress Kinerja: {{ $months[$month] }} {{ $year }}
            </h5>
            <span class="text-muted font-size-sm">Data bulanan adalah progress kinerja berjalan.</span>
        </div>
        <div class="d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
            {{-- <button type="button" class="btn btn-sm btn-light-info font-weight-bolder" id="btnSyncAllQad">
                <i class="fas fa-cloud-download-alt mr-1"></i>Fetch QAD Massal
            </button> --}}
            <button type="button" class="btn btn-sm btn-light-success font-weight-bolder" data-toggle="modal"
                data-target="#modalImportQaExcel">
                <i class="fas fa-file-excel mr-1"></i>Upload Nilai Manual
            </button>
            {{-- <button type="submit" class="btn btn-sm btn-primary font-weight-bolder px-6">
                <i class="fas fa-save mr-1"></i>Simpan Progress
            </button> --}}
        </div>
    </div>

    <div class="table-responsive">
        <table class="table tbl-vendor table-bordered table-hover" id="tableMonthly"
            data-w-del="{{ $settings->weight_delivery }}" data-w-qual="{{ $settings->weight_quality }}"
            data-w-qty="{{ $settings->weight_quantity }}" data-w-comp="{{ $settings->weight_complain }}"
            data-w-inc="{{ $settings->weight_incoming_material }}"
            data-w-safe="{{ $settings->weight_safety_environment }}" data-th-baik="{{ $settings->threshold_baik }}"
            data-th-cukup="{{ $settings->threshold_cukup }}">
            <thead>
                <tr>
                    <th width="50" class="text-center">No</th>
                    <th style="min-width: 220px;">Nama Vendor & Info</th>
                    <th width="105" class="text-center text-primary">
                        Delivery
                    </th>
                    <th width="105" class="text-center text-primary">
                        Quality
                    </th>
                    <th width="105" class="text-center text-primary">
                        Quantity
                    </th>
                    <th width="105" class="text-center text-warning">
                        Complain
                    </th>
                    <th width="105" class="text-center text-warning">
                        Incoming
                    </th>
                    <th width="105" class="text-center text-warning">
                        Safety/Env
                    </th>
                    <th width="100" class="text-center" style="min-width: 90px;">Total Skor</th>
                    <th width="110" class="text-center" style="min-width: 100px;">Kategori</th>
                    {{-- <th width="110" class="text-center" style="min-width: 100px;">Aksi QAD</th> --}}
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $v)
                    @php
                        $eval = $monthlyEvaluations->get($v->id);
                        $dScore = $eval ? $eval->delivery_score : 0;
                        $qScore = $eval ? $eval->quality_score : 0;
                        $qtyScore = $eval ? $eval->quantity_score : 0;
                        $compScore = $eval ? $eval->complain_score : 0;
                        $incScore = $eval ? $eval->incoming_material_score : 0;
                        $safeScore = $eval ? $eval->safety_environment_score : 0;
                        $totScore = $eval ? $eval->total_score : 0;
                        $cat = $eval ? $eval->category : '-';
                        $initials = collect(explode(' ', $v->name))
                            ->take(2)
                            ->map(function ($w) {
                                return strtoupper(substr($w, 0, 1));
                            })
                            ->implode('');
                    @endphp
                    <tr data-vendor-id="{{ $v->id }}">
                        <td class="text-center align-middle font-weight-bold text-muted">
                            {{ $loop->iteration }}
                            <input type="hidden" name="evaluations[{{ $v->id }}][enabled]" value="1">
                        </td>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="vnd-avatar mr-3">{{ $initials }}</div>
                                <div>
                                    <div class="vnd-vendor-name font-weight-bolder text-dark">{{ $v->name }}</div>
                                    <div class="d-flex flex-wrap" style="gap:4px;">
                                        @if (!empty($v->qad_supplier_code))
                                            <span class="vnd-vendor-email text-muted font-size-xs">
                                                </i>Supplier Code:
                                                {{ $v->qad_supplier_code }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Skor QAD --}}
                        <td class="align-middle">
                            <input disabled type="text" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][delivery_score]"
                                class="form-control form-control-sm text-center font-weight-bold delivery-input score-input"
                                value="{{ number_format($dScore, 2, '.', '') }}">
                        </td>
                        <td class="align-middle">
                            <input disabled type="text" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][quality_score]"
                                class="form-control form-control-sm text-center font-weight-bold quality-input score-input"
                                value="{{ number_format($qScore, 2, '.', '') }}">
                        </td>
                        <td class="align-middle">
                            <input disabled type="text" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][quantity_score]"
                                class="form-control form-control-sm text-center font-weight-bold quantity-input score-input"
                                value="{{ number_format($qtyScore, 2, '.', '') }}">
                        </td>

                        {{-- Skor QA Manual --}}
                        <td class="align-middle">
                            <input disabled type="text" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][complain_score]"
                                class="form-control form-control-sm text-center font-weight-bold complain-input score-input"
                                value="{{ number_format($compScore, 2, '.', '') }}">
                        </td>
                        <td class="align-middle">
                            <input disabled type="text" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][incoming_material_score]"
                                class="form-control form-control-sm text-center font-weight-bold incoming-input score-input"
                                value="{{ number_format($incScore, 2, '.', '') }}">
                        </td>
                        <td class="align-middle">
                            <input disabled type="text" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][safety_environment_score]"
                                class="form-control form-control-sm text-center font-weight-bold safety-input score-input"
                                value="{{ number_format($safeScore, 2, '.', '') }}">
                        </td>

                        <td
                            class="text-center align-middle font-weight-bolder text-primary total-score-cell vnd-score font-size-h6">
                            {{ number_format($totScore, 2) }}
                        </td>
                        <td class="text-center align-middle category-cell">
                            @if ($cat == 'BAIK')
                                <span class="vnd-status vnd-status--success">BAIK</span>
                            @elseif($cat == 'CUKUP')
                                <span class="vnd-status vnd-status--warning">CUKUP</span>
                            @elseif($cat == 'KURANG')
                                <span class="vnd-status vnd-status--danger">KURANG</span>
                            @else
                                <span class="vnd-status vnd-status--muted">-</span>
                            @endif
                        </td>
                        {{-- <td class="text-center align-middle">
                            <button type="button"
                                class="btn btn-xs btn-light-primary font-weight-bold btn-fetch-single-qad"
                                data-vendor-id="{{ $v->id }}">
                                <i class="fas fa-sync-alt mr-1"></i> Fetch QAD
                            </button>
                        </td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="p-0">
                            <div class="vnd-empty py-8 text-center">
                                <div class="vnd-empty-icon mb-2"><i class="flaticon2-group text-muted icon-3x"></i>
                                </div>
                                <div class="vnd-empty-title font-weight-bold text-dark font-size-h6">Belum Ada Vendor
                                    Approved</div>
                                <div class="vnd-empty-sub text-muted font-size-sm">Belum ada vendor dengan status
                                    Approved yang tersedia untuk dievaluasi.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>

@push('scripts')
    <script>
        $(document).ready(function() {
            /* 1. Initialize DataTables Monthly */
            var dtMonthly = null;
            if ($('#tableMonthly').length && $('#tableMonthly tbody tr').find('.vnd-empty').length === 0) {
                dtMonthly = $('#tableMonthly').DataTable({
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "Semua"]
                    ],
                    order: [
                        [1, 'asc']
                    ], // Sort by Vendor Name
                    columnDefs: [{
                        targets: [0],
                        orderable: false,
                        width: '40px',
                        className: 'text-center align-middle'
                    }],
                    dom: '<"d-flex justify-content-between align-items-center mb-4 flex-wrap"lf>rtip',
                });
            }

            // Adjust columns on load
            setTimeout(function() {
                if (dtMonthly) dtMonthly.columns.adjust();
            }, 100);

            /* 2. Realtime score recalculation */
            let table = $('#tableMonthly');

            function updateRowScore(row) {
                if (!table.length) return;
                let wDel = parseFloat(table.data('w-del')) || 20;
                let wQual = parseFloat(table.data('w-qual')) || 20;
                let wQty = parseFloat(table.data('w-qty')) || 20;
                let wComp = parseFloat(table.data('w-comp')) || 15;
                let wInc = parseFloat(table.data('w-inc')) || 15;
                let wSafe = parseFloat(table.data('w-safe')) || 10;
                let thBaik = parseFloat(table.data('th-baik')) || 80;
                let thCukup = parseFloat(table.data('th-cukup')) || 60;

                let d = parseFloat(row.find('.delivery-input').val()) || 0;
                let q = parseFloat(row.find('.quality-input').val()) || 0;
                let qty = parseFloat(row.find('.quantity-input').val()) || 0;
                let comp = parseFloat(row.find('.complain-input').val()) || 0;
                let inc = parseFloat(row.find('.incoming-input').val()) || 0;
                let safe = parseFloat(row.find('.safety-input').val()) || 0;

                let total = ((d * wDel) + (q * wQual) + (qty * wQty) + (comp * wComp) + (inc * wInc) + (safe *
                    wSafe)) / 100;
                row.find('.total-score-cell').text(total.toFixed(2));

                let catCell = row.find('.category-cell');
                if (total >= thBaik) {
                    catCell.html('<span class="vnd-status vnd-status--success">BAIK</span>');
                } else if (total >= thCukup) {
                    catCell.html('<span class="vnd-status vnd-status--warning">CUKUP</span>');
                } else {
                    catCell.html('<span class="vnd-status vnd-status--danger">KURANG</span>');
                }
            }

            // Score Input Live Recalculation (Event delegation for DataTables pages)
            $(document).on('input change', '.score-input', function() {
                let row = $(this).closest('tr');
                updateRowScore(row);
            });

            // Submit handler: ensure inputs from all DataTables pages are submitted
            $('#formBatchMonthly').on('submit', function(e) {
                if (dtMonthly) {
                    var form = this;
                    var serializedData = dtMonthly.$('input, select').serializeArray();
                    $.each(serializedData, function(i, item) {
                        if (!$.contains(document, form[item.name])) {
                            $(form).append(
                                $('<input>').attr('type', 'hidden').attr('name', item.name).val(
                                    item.value)
                            );
                        }
                    });
                }
            });

            // Single Fetch QAD via AJAX
            $(document).on('click', '.btn-fetch-single-qad', function() {
                let btn = $(this);
                let vendorId = btn.data('vendor-id');
                let row = btn.closest('tr');

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Syncing...');

                $.ajax({
                    url: "{{ route('admin.evaluasi.fetch-qad') }}",
                    type: "GET",
                    data: {
                        vendor_id: vendorId,
                        month: "{{ $month }}",
                        year: "{{ $year }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            row.find('.delivery-input').val(response.scores.delivery_score);
                            row.find('.quality-input').val(response.scores.quality_score);
                            row.find('.quantity-input').val(response.scores.quantity_score);

                            updateRowScore(row);
                            if (typeof window.showToast === 'function') {
                                window.showToast('success', 'Sukses',
                                    'Data QAD vendor berhasil ditarik');
                            }
                        }
                    },
                    error: function() {
                        if (typeof window.showToast === 'function') {
                            window.showToast('error', 'Gagal', 'Gagal menarik data QAD vendor');
                        }
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-sync-alt mr-1"></i> Fetch QAD');
                    }
                });
            });

            // Fetch All QAD (handles all vendors across DataTables pages)
            $('#btnSyncAllQad').on('click', function() {
                let btns = dtMonthly ? dtMonthly.$('.btn-fetch-single-qad') : $('.btn-fetch-single-qad');
                if (btns.length === 0) return;

                let btnAll = $(this);
                btnAll.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i> Fetching All...');

                let completed = 0;
                btns.each(function() {
                    let singleBtn = $(this);
                    let vendorId = singleBtn.data('vendor-id');
                    let row = singleBtn.closest('tr');

                    $.ajax({
                        url: "{{ route('admin.evaluasi.fetch-qad') }}",
                        type: "GET",
                        data: {
                            vendor_id: vendorId,
                            month: "{{ $month }}",
                            year: "{{ $year }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                row.find('.delivery-input').val(response.scores
                                    .delivery_score);
                                row.find('.quality-input').val(response.scores
                                    .quality_score);
                                row.find('.quantity-input').val(response.scores
                                    .quantity_score);
                                updateRowScore(row);
                            }
                        },
                        complete: function() {
                            completed++;
                            if (completed === btns.length) {
                                btnAll.prop('disabled', false).html(
                                    '<i class="fas fa-cloud-download-alt mr-1"></i>Fetch QAD Massal'
                                );
                                if (typeof window.showToast === 'function') {
                                    window.showToast('success', 'Sukses',
                                        'Selesai sinkronisasi QAD seluruh vendor');
                                }
                            }
                        }
                    });
                });
            });
        });
    </script>
@endpush
