@extends('layouts.app', ['title' => 'Evaluasi Kinerja Vendor'])

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-award text-primary mr-2"></i>Laporan Evaluasi Kinerja Vendor
            </h1>
            <p class="text-muted small mb-0">Hasil Penilaian Kinerja Tahunan oleh PT Phapros Tbk</p>
        </div>
    </div>

    @if($evaluations->isEmpty())
        <div class="card card-custom shadow-sm text-center py-5">
            <div class="card-body">
                <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                <h5 class="font-weight-bold text-dark">Belum Ada Laporan Evaluasi Tahunan</h5>
                <p class="text-muted small">Laporan evaluasi kinerja tahunan akan tampil di sini setelah diverifikasi dan disetujui oleh Manajemen PT Phapros Tbk.</p>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-4">
                <div class="card card-custom shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="card-title font-weight-bold mb-0"><i class="fas fa-history mr-2"></i>Pilih Tahun Evaluasi</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($evaluations as $item)
                            <a href="{{ route('vendor.evaluasi.index', ['year' => $item->year]) }}" 
                               class="list-group-item list-group-item-action d-flex align-items-center justify-content-between {{ $selectedEvaluation && $selectedEvaluation->id == $item->id ? 'active' : '' }}">
                                <span class="font-weight-bold">Tahun {{ $item->year }}</span>
                                <div>
                                    <span class="badge {{ $item->category == 'BAIK' ? 'badge-success' : ($item->category == 'CUKUP' ? 'badge-warning' : 'badge-danger') }} mr-2">
                                        {{ $item->category }}
                                    </span>
                                    <span class="font-weight-bold">{{ number_format($item->final_score, 2) }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                @if($selectedEvaluation)
                    <div class="card card-custom shadow-sm mb-4">
                        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                            <h5 class="card-title font-weight-bold mb-0 text-white">
                                <i class="fas fa-file-alt mr-2"></i>Detail Evaluasi Tahunan {{ $selectedEvaluation->year }}
                            </h5>
                            <div>
                                <span class="badge badge-light font-weight-bold text-primary px-3 py-2">
                                    Approved
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            {{-- KPI Cards --}}
                            <div class="row mb-4">
                                <div class="col-sm-6 col-lg-6 mb-3">
                                    <div class="p-3 border rounded bg-light">
                                        <span class="text-muted small d-block mb-1">Skor Akhir Tahunan</span>
                                        <span class="h2 font-weight-bold text-primary mb-0">{{ number_format($selectedEvaluation->final_score, 2) }}</span>
                                        <span class="text-muted small"> / 100</span>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-6 mb-3">
                                    <div class="p-3 border rounded bg-light">
                                        <span class="text-muted small d-block mb-1">Kategori Hasil Evaluasi</span>
                                        @if($selectedEvaluation->category == 'BAIK')
                                            <span class="h2 font-weight-bold text-success mb-0">BAIK</span>
                                            <p class="small text-muted mb-0 mt-1">Kinerja sangat baik, dipertahankan.</p>
                                        @elseif($selectedEvaluation->category == 'CUKUP')
                                            <span class="h2 font-weight-bold text-warning mb-0">CUKUP</span>
                                            <p class="small text-muted mb-0 mt-1">Diperlukan peningkatan kinerja.</p>
                                        @else
                                            <span class="h2 font-weight-bold text-danger mb-0">KURANG</span>
                                            <p class="small text-muted mb-0 mt-1">Diperlukan tindakan perbaikan khusus.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Breakdown 6 Aspek --}}
                            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                                <i class="fas fa-chart-bar text-primary mr-1"></i>Rincian Skor per Aspek Penilaian
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-light">
                                        <tr class="small text-uppercase font-weight-bold">
                                            <th>No</th>
                                            <th>Aspek Penilaian</th>
                                            <th class="text-center" width="140">Rata-rata Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Delivery (Pengiriman Tepat Waktu)</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->delivery_score_avg, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Quality (Kualitas Released QAD)</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->quality_score_avg, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Quantity (Kesesuaian Jumlah PO)</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->quantity_score_avg, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Complain (Rekap Komplain Kualitas)</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->complain_score_avg, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>Incoming Material (Kondisi Material Masuk)</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->incoming_material_score_avg, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td>Safety & Environment (Kepatuhan K3 & Lingkungan)</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->safety_environment_score_avg, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            @if($selectedEvaluation->notes)
                                <div class="mt-3 p-3 bg-light-warning border rounded">
                                    <h6 class="font-weight-bold text-warning mb-1"><i class="fas fa-sticky-note mr-1"></i>Catatan Tambahan:</h6>
                                    <p class="small mb-0 text-dark">{{ $selectedEvaluation->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
