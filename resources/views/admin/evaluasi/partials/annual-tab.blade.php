<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap">
    <div>
        <h5 class="font-weight-bolder text-dark mb-1">
            <i class="fas fa-award text-warning mr-2"></i>Pengesahan Rapor Kinerja Akhir (Tahun {{ $year }})
        </h5>
        <span class="text-muted font-size-sm">
            Laporan rapor kinerja tahunan hanya dipublish ke vendor setelah berstatus APPROVED oleh Management.
        </span>
    </div>
</div>

<div class="table-responsive">
    <table class="table tbl-vendor table-bordered table-hover" id="tableAnnual">
        <thead>
            <tr>
                <th width="50" class="text-center">No</th>
                <th style="min-width: 200px;">Nama Vendor</th>
                <th class="text-center" style="min-width: 210px;">Rata-rata QAD (Del/Qual/Qty)</th>
                <th class="text-center" style="min-width: 210px;">Rata-rata QA (Comp/Inc/Safe)</th>
                <th class="text-center" style="min-width: 110px;">Skor Akhir</th>
                <th class="text-center" style="min-width: 100px;">Kategori</th>
                <th class="text-center" style="min-width: 140px;">Status Approval</th>
                <th class="text-center" style="min-width: 150px;">Alert Peringatan</th>
                <th class="text-center" style="min-width: 160px;">Aksi Management</th>
            </tr>
        </thead>
        <tbody>
            @forelse($annualEvaluations as $ann)
                @php
                    $vName = optional($ann->vendor)->name ?: '-';
                    $vEmail = optional($ann->vendor)->email ?: '-';
                    $initials = collect(explode(' ', $vName))
                        ->take(2)
                        ->map(function ($w) {
                            return strtoupper(substr($w, 0, 1));
                        })
                        ->implode('');
                @endphp
                <tr>
                    <td class="text-center align-middle font-weight-bold text-muted">
                        {{ $loop->iteration }}
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <div class="vnd-avatar mr-3">{{ $initials }}</div>
                            <div>
                                <div class="vnd-vendor-name font-weight-bolder text-dark">{{ $vName }}</div>
                                <span class="vnd-vendor-email text-muted font-size-xs">{{ $vEmail }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <span class="badge badge-light-primary font-weight-bold px-2 py-1 mb-1">Del: {{ number_format($ann->delivery_score_avg, 2) }}</span>
                        <span class="badge badge-light-primary font-weight-bold px-2 py-1 mb-1">Qual: {{ number_format($ann->quality_score_avg, 2) }}</span>
                        <span class="badge badge-light-primary font-weight-bold px-2 py-1 mb-1">Qty: {{ number_format($ann->quantity_score_avg, 2) }}</span>
                    </td>
                    <td class="text-center align-middle">
                        <span class="badge badge-light-warning font-weight-bold px-2 py-1 mb-1">Comp: {{ number_format($ann->complain_score_avg, 2) }}</span>
                        <span class="badge badge-light-warning font-weight-bold px-2 py-1 mb-1">Inc: {{ number_format($ann->incoming_material_score_avg, 2) }}</span>
                        <span class="badge badge-light-warning font-weight-bold px-2 py-1 mb-1">Safe: {{ number_format($ann->safety_environment_score_avg, 2) }}</span>
                    </td>
                    <td class="text-center align-middle font-weight-bolder text-primary vnd-score font-size-h5">
                        {{ number_format($ann->final_score, 2) }}
                    </td>
                    <td class="text-center align-middle">
                        @if ($ann->category == 'BAIK')
                            <span class="vnd-status vnd-status--success">BAIK</span>
                        @elseif($ann->category == 'CUKUP')
                            <span class="vnd-status vnd-status--warning">CUKUP</span>
                        @else
                            <span class="vnd-status vnd-status--danger">KURANG</span>
                        @endif
                    </td>
                    <td class="text-center align-middle">
                        @if ($ann->status == 'approved')
                            <span class="vnd-status vnd-status--success">
                                <i class="fas fa-check-circle mr-1"></i>APPROVED
                            </span>
                            <div class="vnd-cell-muted font-size-xs mt-1">
                                {{ $ann->approved_at ? $ann->approved_at->format('d/m/Y H:i') : '' }}
                            </div>
                        @else
                            <span class="vnd-status vnd-status--warning">
                                <i class="fas fa-clock mr-1"></i>DRAFT / WAITING
                            </span>
                        @endif
                    </td>
                    <td class="text-center align-middle">
                        @if ($ann->has_score_drop_alert)
                            <span class="badge badge-danger font-weight-bold mb-1 d-inline-block px-2 py-1"
                                title="Skor turun >= 20% dari tahun lalu">
                                <i class="fas fa-arrow-down mr-1"></i>DROP SKOR &ge; 20%
                            </span>
                        @endif
                        @if ($ann->has_consecutive_low_alert)
                            <span class="badge badge-dark font-weight-bold d-inline-block px-2 py-1"
                                title="Predikat KURANG 2 tahun berturut-turut">
                                <i class="fas fa-exclamation-triangle text-warning mr-1"></i>2 THN KURANG
                            </span>
                        @endif
                        @if (!$ann->has_score_drop_alert && !$ann->has_consecutive_low_alert)
                            <span class="vnd-status vnd-status--success"><i class="fas fa-check mr-1"></i>Normal</span>
                        @endif

                        @if ($ann->decision_status != 'none')
                            <div class="mt-1">
                                <span class="badge badge-info font-weight-bold">Aksi: {{ strtoupper(str_replace('_', ' ', $ann->decision_status)) }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="text-center align-middle">
                        @if ($ann->status != 'approved')
                            <form action="{{ route('admin.evaluasi.annual.approve', $ann->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-light-success font-weight-bold mr-1 mb-1"
                                    onclick="return confirm('Approve evaluasi tahunan vendor {{ $vName }}?')">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                        @endif

                        @if ($ann->has_score_drop_alert || $ann->has_consecutive_low_alert)
                            <button type="button" class="btn btn-xs btn-light-danger font-weight-bold mb-1 btn-open-action-modal"
                                data-id="{{ $ann->id }}"
                                data-name="{{ $vName }}"
                                data-score="{{ number_format($ann->final_score, 2) }}"
                                data-action-url="{{ route('admin.evaluasi.annual.trigger-action', $ann->id) }}">
                                <i class="fas fa-cog"></i> Tindakan
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="p-0">
                        <div class="vnd-empty py-8 text-center">
                            <div class="vnd-empty-icon mb-2"><i class="flaticon2-award text-muted icon-3x"></i></div>
                            <div class="vnd-empty-title font-weight-bold text-dark font-size-h6">Belum Ada Data Evaluasi Tahunan {{ $year }}</div>
                            <div class="vnd-empty-sub text-muted font-size-sm">Klik tombol <strong>Generate Rapor {{ $year }}</strong> di atas untuk mengkalkulasi laporan evaluasi tahunan.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
