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
            <span class="text-muted font-size-sm">Data bulanan adalah progress kinerja berjalan. (Skor QAD ditarik
                otomatis, QA diinput manual)</span>
        </div>
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-light-info font-weight-bolder mr-3" id="btnSyncAllQad">
                <i class="fas fa-cloud-download-alt mr-1"></i>Fetch QAD Massal
            </button>
            <button type="submit" class="btn btn-sm btn-primary font-weight-bolder px-6">
                <i class="fas fa-save mr-1"></i>Simpan Progress
            </button>
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
                    <th width="105" class="text-center bg-light-primary text-primary">
                        Delivery
                    </th>
                    <th width="105" class="text-center bg-light-primary text-primary">
                        Quality
                    </th>
                    <th width="105" class="text-center bg-light-primary text-primary">
                        Quantity
                    </th>
                    <th width="105" class="text-center bg-light-warning text-warning">
                        Complain
                    </th>
                    <th width="105" class="text-center bg-light-warning text-warning">
                        Incoming
                    </th>
                    <th width="105" class="text-center bg-light-warning text-warning">
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
                        $compScore = $eval ? $eval->complain_score : 100;
                        $incScore = $eval ? $eval->incoming_material_score : 100;
                        $safeScore = $eval ? $eval->safety_environment_score : 100;
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
                                    <div class="d-flex align-items-center flex-wrap" style="gap:4px;">
                                        <span
                                            class="vnd-vendor-email text-muted font-size-xs">{{ $v->email }}</span>
                                        @if (!empty($v->qad_supplier_code))
                                            <span class="badge badge-light-primary font-weight-bold"
                                                style="font-size:0.65rem; padding:1px 5px;">
                                                <i class="fas fa-barcode text-primary mr-1"></i>QAD:
                                                {{ $v->qad_supplier_code }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Skor QAD --}}
                        <td class="bg-light-primary align-middle">
                            <input type="number" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][delivery_score]"
                                class="form-control form-control-sm text-center font-weight-bold delivery-input score-input"
                                value="{{ number_format($dScore, 2, '.', '') }}">
                        </td>
                        <td class="bg-light-primary align-middle">
                            <input type="number" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][quality_score]"
                                class="form-control form-control-sm text-center font-weight-bold quality-input score-input"
                                value="{{ number_format($qScore, 2, '.', '') }}">
                        </td>
                        <td class="bg-light-primary align-middle">
                            <input type="number" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][quantity_score]"
                                class="form-control form-control-sm text-center font-weight-bold quantity-input score-input"
                                value="{{ number_format($qtyScore, 2, '.', '') }}">
                        </td>

                        {{-- Skor QA Manual --}}
                        <td class="bg-light-warning align-middle">
                            <input type="number" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][complain_score]"
                                class="form-control form-control-sm text-center font-weight-bold complain-input score-input"
                                value="{{ number_format($compScore, 2, '.', '') }}">
                        </td>
                        <td class="bg-light-warning align-middle">
                            <input type="number" step="0.01" min="0" max="100"
                                name="evaluations[{{ $v->id }}][incoming_material_score]"
                                class="form-control form-control-sm text-center font-weight-bold incoming-input score-input"
                                value="{{ number_format($incScore, 2, '.', '') }}">
                        </td>
                        <td class="bg-light-warning align-middle">
                            <input type="number" step="0.01" min="0" max="100"
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
