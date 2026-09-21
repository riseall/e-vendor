<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap">
    <div>
        <h5 class="font-weight-bolder text-dark mb-1">
            <i class="fas fa-award text-warning mr-2"></i>Pengesahan Evaluasi Tahunan (Tahun {{ $year }})
        </h5>
        <span class="text-muted font-size-sm">
            Alur Pengesahan 2 Tingkat: <strong>Verifikasi Manajer Pengadaan</strong> &rarr; <strong>Pengesahan GM
                Pengadaan</strong>. Laporan evaluasi otomatis terkirim ke vendor setelah disahkan GM.
        </span>
    </div>
</div>

<div class="table-responsive">
    <table class="table tbl-vendor table-bordered table-hover w-100" id="tableAnnual">
        <thead>
            <tr>
                <th width="50" class="text-center" style="min-width: 45px;">No</th>
                <th style="min-width: 220px;">Nama Vendor</th>
                <th class="text-center" style="min-width: 200px;">Rata-rata QAD</th>
                <th width="100" class="text-center" style="min-width: 90px;">Skor Akhir</th>
                <th width="110" class="text-center" style="min-width: 100px;">Kategori</th>
                <th width="140" class="text-center" style="min-width: 130px;">Status Approval</th>
                <th width="140" class="text-center" style="min-width: 130px;">Alert Peringatan</th>
                <th width="150" class="text-center" style="min-width: 140px;">Aksi Management</th>
            </tr>
        </thead>
        <tbody>
            @forelse($annualEvaluations as $ann)
                @php
                    $vObj = $vendors->firstWhere('id', $ann->vendor_id) ?: $ann->vendor;
                    $vName = $vObj ? $vObj->name : '-';
                    $vCode = $vObj ? $vObj->qad_supplier_code ?? null : null;
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
                                @if (!empty($vCode))
                                    <div class="d-flex flex-wrap mt-1">
                                        <span class="vnd-vendor-email text-muted font-size-xs">
                                            Supplier Code: {{ $vCode }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <div class="d-flex align-items-center justify-content-center flex-wrap" style="gap: 4px;">
                            <span class="badge badge-light-primary font-weight-bold px-2 py-1"
                                title="Ketepatan Pengiriman">Del:
                                {{ number_format($ann->delivery_score_avg, 2) }}</span>
                            <span class="badge badge-light-primary font-weight-bold px-2 py-1"
                                title="Kualitas Mutu">Qual: {{ number_format($ann->quality_score_avg, 2) }}</span>
                            <span class="badge badge-light-primary font-weight-bold px-2 py-1"
                                title="Kesesuaian Jumlah">Qty: {{ number_format($ann->quantity_score_avg, 2) }}</span>
                            {{-- </div>
                    </td>
                    <td class="text-center align-middle">
                        <div class="d-flex align-items-center justify-content-center flex-wrap" style="gap: 4px;"> --}}
                            <span class="badge badge-light-warning font-weight-bold px-2 py-1"
                                title="Komplain Mutu">Comp: {{ number_format($ann->complain_score_avg, 2) }}</span>
                            <span class="badge badge-light-warning font-weight-bold px-2 py-1"
                                title="Material Masuk">Inc:
                                {{ number_format($ann->incoming_material_score_avg, 2) }}</span>
                            <span class="badge badge-light-warning font-weight-bold px-2 py-1"
                                title="K3 & Lingkungan">Safe:
                                {{ number_format($ann->safety_environment_score_avg, 2) }}</span>
                        </div>
                    </td>
                    <td class="text-center align-middle font-weight-bolder text-primary vnd-score font-size-h6">
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
                            <span class="badge badge-light-success font-weight-bolder px-2 py-1"
                                title="Disahkan oleh GM Pengadaan">
                                <i class="fas fa-check-double text-success mr-1"></i>DISAHKAN (GM)
                            </span>
                            @if ($ann->gmApprover || $ann->approver)
                                <div class="text-muted font-size-xs mt-1"
                                    title="{{ optional($ann->gmApprover)->name ?: optional($ann->approver)->name }}">
                                    <i
                                        class="fas fa-user-check text-muted mr-1"></i>{{ \Illuminate\Support\Str::limit(optional($ann->gmApprover)->name ?: optional($ann->approver)->name, 16) }}
                                </div>
                            @endif
                            <div class="text-muted font-size-xs mt-1">
                                {{ ($ann->gm_approved_at ?: $ann->approved_at) ? ($ann->gm_approved_at ?: $ann->approved_at)->format('d/m/Y H:i') : '' }}
                            </div>
                        @elseif ($ann->status == 'verified_manager')
                            <span class="badge badge-light-primary font-weight-bolder px-2 py-1"
                                title="Diverifikasi Manajer Pengadaan - Menunggu GM">
                                <i class="fas fa-check text-primary mr-1"></i>VERIFIED (MGR)
                            </span>
                            @if ($ann->managerApprover)
                                <div class="text-muted font-size-xs mt-1" title="{{ $ann->managerApprover->name }}">
                                    <i
                                        class="fas fa-user-check text-muted mr-1"></i>{{ \Illuminate\Support\Str::limit($ann->managerApprover->name, 16) }}
                                </div>
                            @endif
                            <div class="text-warning font-weight-bold font-size-xs mt-1">
                                <i class="fas fa-hourglass-half text-warning mr-1"></i>Menunggu GM
                            </div>
                        @else
                            <span class="badge badge-light-warning font-weight-bolder px-2 py-1">
                                <i class="fas fa-clock text-warning mr-1"></i>MENUNGGU MGR
                            </span>
                            <div class="text-muted font-size-xs mt-1">Belum diverifikasi</div>
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
                            <span class="badge badge-dark font-weight-bold d-inline-block px-2 py-1 mb-1"
                                title="Predikat KURANG 2 tahun berturut-turut">
                                <i class="fas fa-exclamation-triangle text-warning mr-1"></i>2 THN KURANG
                            </span>
                        @endif
                        @if (!$ann->has_score_drop_alert && !$ann->has_consecutive_low_alert)
                            <span class="badge badge-light-success font-weight-bold px-2 py-1">
                                <i class="fas fa-check text-success mr-1"></i>Normal
                            </span>
                        @endif

                        @if ($ann->decision_status != 'none')
                            <div class="mt-1">
                                <span class="badge badge-light-info font-weight-bold font-size-xs">
                                    {{ strtoupper(str_replace('_', ' ', $ann->decision_status)) }}
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="text-center align-middle">
                        @if ($ann->status == 'approved')
                            <span class="badge badge-light-success font-weight-bold mb-1">
                                <i class="fas fa-check-double text-success mr-1"></i>Disahkan
                            </span>
                        @elseif ($ann->status == 'verified_manager')
                            @can('evaluasi-approve-gm')
                                <form action="{{ route('admin.evaluasi.annual.approve-gm', $ann->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success font-weight-bold mb-1"
                                        onclick="return confirm('Sahkan (Final Approve) evaluasi tahunan vendor {{ $vName }} sebagai GM Pengadaan? Laporan akan otomatis terkirim ke vendor.')">
                                        <i class="fas fa-file-signature mr-1"></i>Sahkan (GM)
                                    </button>
                                </form>
                            @else
                                <span class="badge badge-light-warning font-weight-bold mb-1"
                                    title="Menunggu persetujuan GM Pengadaan">
                                    <i class="fas fa-user-clock text-warning mr-1"></i>Menunggu GM
                                </span>
                            @endcan
                        @else
                            @can('evaluasi-verify-manager')
                                <form action="{{ route('admin.evaluasi.annual.verify-manager', $ann->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-light-primary font-weight-bold mb-1"
                                        onclick="return confirm('Verifikasi evaluasi tahunan vendor {{ $vName }} sebagai Manajer Pengadaan?')">
                                        <i class="fas fa-check mr-1"></i>Verifikasi (Mgr)
                                    </button>
                                </form>
                            @else
                                <span class="badge badge-light-secondary font-weight-bold mb-1"
                                    title="Menunggu verifikasi Manajer Pengadaan">
                                    <i class="fas fa-user-clock text-muted mr-1"></i>Menunggu Mgr
                                </span>
                            @endcan
                        @endif

                        @if ($ann->has_score_drop_alert || $ann->has_consecutive_low_alert)
                            <button type="button"
                                class="btn btn-xs btn-light-danger font-weight-bold mb-1 ml-1 btn-open-action-modal"
                                data-id="{{ $ann->id }}" data-name="{{ $vName }}"
                                data-score="{{ number_format($ann->final_score, 2) }}"
                                data-action-url="{{ route('admin.evaluasi.annual.trigger-action', $ann->id) }}">
                                <i class="fas fa-cog mr-1"></i>Tindakan
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="p-0">
                        <div class="vnd-empty py-8 text-center">
                            <div class="vnd-empty-icon mb-2"><i class="flaticon2-award text-muted icon-3x"></i></div>
                            <div class="vnd-empty-title font-weight-bold text-dark font-size-h6">Belum Ada Data
                                Evaluasi Tahunan {{ $year }}</div>
                            <div class="vnd-empty-sub text-muted font-size-sm">Klik tombol <strong>Generate Evaluasi
                                    {{ $year }}</strong> di atas untuk mengkalkulasi laporan evaluasi tahunan.
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            var dtAnnual = null;
            if ($('#tableAnnual').length && $('#tableAnnual tbody tr').find('.vnd-empty').length === 0) {
                dtAnnual = $('#tableAnnual').DataTable({
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
                    ],
                    columnDefs: [{
                            targets: [0, -1],
                            orderable: false
                        },
                        {
                            targets: [0],
                            width: '40px'
                        }
                    ],
                    dom: '<"d-flex justify-content-between align-items-center mb-4 flex-wrap"lf>rtip',
                });
            }

            setTimeout(function() {
                if (dtAnnual) dtAnnual.columns.adjust();
            }, 100);
        });
    </script>
@endpush
