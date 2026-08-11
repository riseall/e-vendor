@extends('layouts.app', ['title' => 'Evaluasi Vendor'])

@section('content')
<div class="container-fluid">
    {{-- Header / Breadcrumb --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-chart-line text-primary mr-2"></i>Evaluasi Kinerja Vendor
            </h1>
            <p class="text-muted small mb-0">Manajemen Penilaian Berkala (Bulanan & Tahunan) Berdasarkan 6 Aspek Kinerja</p>
        </div>
        <div>
            @can('evaluasi-settings')
                <a href="{{ route('admin.evaluasi.settings') }}" class="btn btn-outline-secondary btn-sm mr-2 font-weight-bold">
                    <i class="fas fa-cog mr-1"></i>Pengaturan Bobot & Threshold
                </a>
            @endcan
            <form action="{{ route('admin.evaluasi.annual.generate') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" class="btn btn-success btn-sm font-weight-bold" onclick="return confirm('Generate / kalkulasi evaluasi tahunan {{ $year }} untuk semua vendor?')">
                    <i class="fas fa-sync-alt mr-1"></i>Generate Evaluasi Tahunan {{ $year }}
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i>{{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    {{-- Filter Periode Card --}}
    <div class="card card-custom mb-4 shadow-sm">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.evaluasi.index') }}" class="form-inline justify-content-between">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <div class="d-flex align-items-center">
                    <label class="font-weight-bold mr-2"><i class="fas fa-filter text-muted mr-1"></i>Filter Periode:</label>
                    
                    <select name="year" class="form-control form-control-sm mr-2 font-weight-bold">
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>

                    <select name="month" class="form-control form-control-sm mr-2 font-weight-bold">
                        @php
                            $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold">
                        <i class="fas fa-search mr-1"></i>Tampilkan
                    </button>
                </div>

                <div class="text-right text-muted small">
                    <span class="badge badge-light-primary px-3 py-2">
                        <i class="fas fa-balance-scale mr-1"></i>Bobot Aspek: QAD (60%) + QA Manual (40%)
                    </span>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Tabs --}}
    <div class="card card-custom card-stretch shadow-sm">
        <div class="card-header card-header-tabs-line">
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-bold nav-tabs-line" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'monthly' ? 'active' : '' }}" href="{{ route('admin.evaluasi.index', ['year' => $year, 'month' => $month, 'tab' => 'monthly']) }}">
                            <i class="fas fa-calendar-alt mr-2"></i>Evaluasi Bulanan (Monitoring Internal)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'annual' ? 'active' : '' }}" href="{{ route('admin.evaluasi.index', ['year' => $year, 'month' => $month, 'tab' => 'annual']) }}">
                            <i class="fas fa-award mr-2"></i>Evaluasi Tahunan (Laporan Final)
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body">
            @if($activeTab == 'monthly')
                {{-- TAB EVALUASI BULANAN --}}
                <form action="{{ route('admin.evaluasi.store-batch-monthly') }}" method="POST">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h5 class="font-weight-bold mb-0 text-dark">
                                Periode: {{ $months[$month] }} {{ $year }}
                            </h5>
                            <span class="text-muted small">Skor QAD (Delivery, Quality, Quantity) ditarik otomatis, Skor QA (Complain, Incoming, Safety) diinput manual.</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-info font-weight-bold mr-2" id="btnSyncAllQad">
                                <i class="fas fa-cloud-download-alt mr-1"></i>Fetch QAD Semua Vendor
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary font-weight-bold">
                                <i class="fas fa-save mr-1"></i>Simpan Batch Evaluasi Bulanan
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-head-custom table-hover" id="tableMonthly">
                            <thead class="bg-light">
                                <tr class="text-uppercase text-dark small font-weight-bold">
                                    <th width="30">#</th>
                                    <th>Nama Vendor / Supplier</th>
                                    <th width="110" class="text-center bg-light-primary">Delivery<br><span class="text-muted">(20% QAD)</span></th>
                                    <th width="110" class="text-center bg-light-primary">Quality<br><span class="text-muted">(20% QAD)</span></th>
                                    <th width="110" class="text-center bg-light-primary">Quantity<br><span class="text-muted">(20% QAD)</span></th>
                                    <th width="110" class="text-center bg-light-warning">Complain<br><span class="text-muted">(15% QA)</span></th>
                                    <th width="110" class="text-center bg-light-warning">Incoming<br><span class="text-muted">(15% QA)</span></th>
                                    <th width="110" class="text-center bg-light-warning">Safety/Env<br><span class="text-muted">(10% QA)</span></th>
                                    <th width="100" class="text-center">Total Skor</th>
                                    <th width="100" class="text-center">Kategori</th>
                                    <th width="100" class="text-center">Aksi QAD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendors as $index => $v)
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
                                    @endphp
                                    <tr data-vendor-id="{{ $v->id }}">
                                        <td class="text-center">
                                            <input type="checkbox" name="evaluations[{{ $v->id }}][enabled]" value="1" {{ $eval ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $v->name }}</div>
                                            <span class="text-muted small">{{ $v->email }}</span>
                                        </td>

                                        {{-- Skor QAD (Delivery, Quality, Quantity) --}}
                                        <td class="bg-light-primary">
                                            <input type="number" step="0.01" min="0" max="100" name="evaluations[{{ $v->id }}][delivery_score]" 
                                                   class="form-control form-control-sm text-center font-weight-bold delivery-input" value="{{ $dScore }}">
                                        </td>
                                        <td class="bg-light-primary">
                                            <input type="number" step="0.01" min="0" max="100" name="evaluations[{{ $v->id }}][quality_score]" 
                                                   class="form-control form-control-sm text-center font-weight-bold quality-input" value="{{ $qScore }}">
                                        </td>
                                        <td class="bg-light-primary">
                                            <input type="number" step="0.01" min="0" max="100" name="evaluations[{{ $v->id }}][quantity_score]" 
                                                   class="form-control form-control-sm text-center font-weight-bold quantity-input" value="{{ $qtyScore }}">
                                        </td>

                                        {{-- Skor QA Manual (Complain, Incoming, Safety) --}}
                                        <td class="bg-light-warning">
                                            <input type="number" step="0.01" min="0" max="100" name="evaluations[{{ $v->id }}][complain_score]" 
                                                   class="form-control form-control-sm text-center font-weight-bold complain-input" value="{{ $compScore }}">
                                        </td>
                                        <td class="bg-light-warning">
                                            <input type="number" step="0.01" min="0" max="100" name="evaluations[{{ $v->id }}][incoming_material_score]" 
                                                   class="form-control form-control-sm text-center font-weight-bold incoming-input" value="{{ $incScore }}">
                                        </td>
                                        <td class="bg-light-warning">
                                            <input type="number" step="0.01" min="0" max="100" name="evaluations[{{ $v->id }}][safety_environment_score]" 
                                                   class="form-control form-control-sm text-center font-weight-bold safety-input" value="{{ $safeScore }}">
                                        </td>

                                        <td class="text-center font-weight-bold text-primary total-score-cell">
                                            {{ number_format($totScore, 2) }}
                                        </td>
                                        <td class="text-center category-cell">
                                            @if($cat == 'BAIK')
                                                <span class="badge badge-success px-2 py-1">BAIK</span>
                                            @elseif($cat == 'CUKUP')
                                                <span class="badge badge-warning px-2 py-1">CUKUP</span>
                                            @elseif($cat == 'KURANG')
                                                <span class="badge badge-danger px-2 py-1">KURANG</span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-outline-primary btn-fetch-single-qad" data-vendor-id="{{ $v->id }}">
                                                <i class="fas fa-sync-alt"></i> Fetch QAD
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4 text-muted">Belum ada vendor dengan status approved untuk dievaluasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

            @else
                {{-- TAB EVALUASI TAHUNAN --}}
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="font-weight-bold mb-0 text-dark">
                        Ringkasan Evaluasi Tahunan Vendor (Tahun {{ $year }})
                    </h5>
                    <span class="text-muted small">Laporan evaluasi tahunan hanya dipublish ke vendor setelah berstatus APPROVED.</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-head-custom table-hover">
                        <thead class="bg-light">
                            <tr class="text-uppercase text-dark small font-weight-bold">
                                <th>Nama Vendor</th>
                                <th class="text-center">Rata2 QAD (Del/Qual/Qty)</th>
                                <th class="text-center">Rata2 QA (Comp/Mat/Safe)</th>
                                <th class="text-center">Skor Akhir Tahunan</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Status Approval</th>
                                <th class="text-center">Alert Peringatan</th>
                                <th class="text-center" width="180">Aksi Management</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($annualEvaluations as $ann)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $ann->vendor ? $ann->vendor->name : '-' }}</div>
                                        <span class="text-muted small">{{ $ann->vendor ? $ann->vendor->email : '-' }}</span>
                                    </td>
                                    <td class="text-center small">
                                        Del: <b>{{ $ann->delivery_score_avg }}</b> | Qual: <b>{{ $ann->quality_score_avg }}</b> | Qty: <b>{{ $ann->quantity_score_avg }}</b>
                                    </td>
                                    <td class="text-center small">
                                        Comp: <b>{{ $ann->complain_score_avg }}</b> | Inc: <b>{{ $ann->incoming_material_score_avg }}</b> | Safe: <b>{{ $ann->safety_environment_score_avg }}</b>
                                    </td>
                                    <td class="text-center font-weight-bold h6 mb-0 text-primary">
                                        {{ number_format($ann->final_score, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($ann->category == 'BAIK')
                                            <span class="badge badge-success font-weight-bold px-3 py-1">BAIK</span>
                                        @elseif($ann->category == 'CUKUP')
                                            <span class="badge badge-warning font-weight-bold px-3 py-1">CUKUP</span>
                                        @else
                                            <span class="badge badge-danger font-weight-bold px-3 py-1">KURANG</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($ann->status == 'approved')
                                            <span class="badge badge-light-success font-weight-bold">
                                                <i class="fas fa-check-circle text-success mr-1"></i>APPROVED
                                            </span>
                                            <div class="small text-muted mt-1">{{ $ann->approved_at ? $ann->approved_at->format('d/m/Y H:i') : '' }}</div>
                                        @else
                                            <span class="badge badge-light-warning font-weight-bold">DRAFT / WAITING</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($ann->has_score_drop_alert)
                                            <span class="badge badge-danger font-weight-bold mb-1 d-block" title="Skor turun >= 20% dari tahun lalu">
                                                <i class="fas fa-arrow-down mr-1"></i>DROP SKOR &ge; 20%
                                            </span>
                                        @endif
                                        @if($ann->has_consecutive_low_alert)
                                            <span class="badge badge-dark font-weight-bold d-block" title="Predikat KURANG 2 tahun berturut-turut">
                                                <i class="fas fa-exclamation-triangle text-warning mr-1"></i>2 THN KURANG
                                            </span>
                                        @endif
                                        @if(!$ann->has_score_drop_alert && !$ann->has_consecutive_low_alert)
                                            <span class="text-muted small"><i class="fas fa-check text-success mr-1"></i>Normal</span>
                                        @endif

                                        @if($ann->decision_status != 'none')
                                            <div class="mt-1">
                                                <span class="badge badge-info">Aksi: {{ strtoupper(str_replace('_', ' ', $ann->decision_status)) }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($ann->status != 'approved')
                                            <form action="{{ route('admin.evaluasi.annual.approve', $ann->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-success font-weight-bold mb-1" onclick="return confirm('Approve evaluasi tahunan vendor ini?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if($ann->has_score_drop_alert || $ann->has_consecutive_low_alert)
                                            <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold" data-toggle="modal" data-target="#modalActionAlert{{ $ann->id }}">
                                                <i class="fas fa-cog"></i> Tindakan
                                            </button>
                                        @endif

                                        {{-- Modal Action Alert --}}
                                        <div class="modal fade text-left" id="modalActionAlert{{ $ann->id }}" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalActionAlertLabel{{ $ann->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title font-weight-bold" id="modalActionAlertLabel{{ $ann->id }}">
                                                            <i class="fas fa-exclamation-triangle text-danger mr-2"></i>Tindakan Peringatan Vendor
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('admin.evaluasi.annual.trigger-action', $ann->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Pilih tindakan khusus yang akan diberikan kepada vendor <strong>{{ $ann->vendor ? $ann->vendor->name : '' }}</strong> (Skor Tahunan: {{ $ann->final_score }}):</p>
                                                            
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Jenis Aksi Management:</label>
                                                                <select name="action_type" class="form-control" required>
                                                                    <option value="rekualifikasi">1. Trigger Process Rekualifikasi (Form Rekualifikasi Baru)</option>
                                                                    <option value="terminated">2. Terminated (Berhentikan Kerjasama)</option>
                                                                    <option value="suspended">3. Suspended (Bekukan Sementara)</option>
                                                                    <option value="qualified_with_notes">4. Qualified Dengan Catatan (Pemantauan Ketat)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger font-weight-bold">Eksekusi Tindakan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @forelse
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data evaluasi tahunan. Klik tombol <strong>Generate Evaluasi Tahunan</strong> di atas untuk memproses data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Single Fetch QAD via AJAX
    $('.btn-fetch-single-qad').on('click', function() {
        let btn = $(this);
        let vendorId = btn.data('vendor-id');
        let row = btn.closest('tr');

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

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
                    row.find('input[type="checkbox"]').prop('checked', true);
                    toastr.success('Data QAD berhasil ditarik');
                }
            },
            error: function() {
                toastr.error('Gagal menarik data QAD');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Fetch QAD');
            }
        });
    });

    // Fetch All QAD
    $('#btnSyncAllQad').on('click', function() {
        $('.btn-fetch-single-qad').each(function() {
            $(this).trigger('click');
        });
    });
});
</script>
@endpush
