@extends('layouts.app', ['title' => 'Pengaturan Evaluasi Vendor'])

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-cog text-primary mr-2"></i>Pengaturan Evaluasi Vendor
            </h1>
            <p class="text-muted small mb-0">Konfigurasi Bobot 6 Aspek Penilaian & Threshold Kategori Evaluasi</p>
        </div>
        <div>
            <a href="{{ route('admin.evaluasi.index') }}" class="btn btn-secondary btn-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Evaluasi
            </a>
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

    <form action="{{ route('admin.evaluasi.settings.update') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Card Bobot 6 Aspek --}}
            <div class="col-lg-7">
                <div class="card card-custom shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title font-weight-bold text-dark mb-0">
                            <i class="fas fa-balance-scale text-primary mr-2"></i>Bobot Persentase 6 Aspek Evaluasi (%)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-6 col-form-label font-weight-bold">1. Delivery (Pengiriman PO Tepat Waktu)</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="weight_delivery" class="form-control font-weight-bold text-center weight-input" value="{{ $settings->weight_delivery }}" required>
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <span class="form-text text-muted small">Sumber data: PO Receipt QAD</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-6 col-form-label font-weight-bold">2. Quality (Kualitas Released QAD)</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="weight_quality" class="form-control font-weight-bold text-center weight-input" value="{{ $settings->weight_quality }}" required>
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <span class="form-text text-muted small">Sumber data: Release / Reject QAD</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-6 col-form-label font-weight-bold">3. Quantity (Kesesuaian Jumlah PO)</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="weight_quantity" class="form-control font-weight-bold text-center weight-input" value="{{ $settings->weight_quantity }}" required>
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <span class="form-text text-muted small">Sumber data: PO Receipt QAD</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-6 col-form-label font-weight-bold">4. Complain (Rekap Komplain QA)</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="weight_complain" class="form-control font-weight-bold text-center weight-input" value="{{ $settings->weight_complain }}" required>
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <span class="form-text text-muted small">Diinput manual oleh Tim QA</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-6 col-form-label font-weight-bold">5. Incoming Material (Material Masuk Gudang)</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="weight_incoming_material" class="form-control font-weight-bold text-center weight-input" value="{{ $settings->weight_incoming_material }}" required>
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <span class="form-text text-muted small">Diinput manual oleh Tim Gudang / QA</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-6 col-form-label font-weight-bold">6. Safety & Environment (Keamanan & Lingkungan)</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="weight_safety_environment" class="form-control font-weight-bold text-center weight-input" value="{{ $settings->weight_safety_environment }}" required>
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <span class="form-text text-muted small">Diinput manual dari logbook / KIC</span>
                            </div>
                        </div>

                        <div class="border-top pt-3 d-flex align-items-center justify-content-between">
                            <span class="font-weight-bold h6 mb-0">Total Bobot Persentase:</span>
                            <span class="font-weight-bold h5 mb-0 text-primary" id="totalWeightBadge">100.00%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Threshold Kategori --}}
            <div class="col-lg-5">
                <div class="card card-custom shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title font-weight-bold text-dark mb-0">
                            <i class="fas fa-layer-group text-warning mr-2"></i>Batas Threshold Kategori Skor
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Batas Minimal Kategori <span class="badge badge-success">BAIK</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">&ge;</span></div>
                                <input type="number" step="0.01" min="0" max="100" name="threshold_baik" class="form-control font-weight-bold text-center" value="{{ $settings->threshold_baik }}" required>
                                <div class="input-group-append"><span class="input-group-text">Poin</span></div>
                            </div>
                            <span class="form-text text-muted small">Skor $\ge$ angka ini dikategorikan <strong>BAIK</strong> (Vendor Dipertahankan).</span>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Batas Minimal Kategori <span class="badge badge-warning">CUKUP</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">&ge;</span></div>
                                <input type="number" step="0.01" min="0" max="100" name="threshold_cukup" class="form-control font-weight-bold text-center" value="{{ $settings->threshold_cukup }}" required>
                                <div class="input-group-append"><span class="input-group-text">Poin</span></div>
                            </div>
                            <span class="form-text text-muted small">Skor di bawah threshold BAIK dan $\ge$ angka ini dikategorikan <strong>CUKUP</strong>. Skor di bawah angka ini dikategorikan <strong>KURANG</strong>.</span>
                        </div>

                        <div class="alert alert-light-info border mt-4">
                            <h6 class="font-weight-bold mb-1"><i class="fas fa-info-circle mr-1"></i>Informasi Catatan:</h6>
                            <p class="small text-muted mb-0">Setiap kali bobot atau threshold diubah, kalkulasi evaluasi bulanan & tahunan selanjutnya akan otomatis mengikuti pengaturan terbaru ini.</p>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary font-weight-bold">
                            <i class="fas fa-save mr-1"></i>Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function calcTotalWeight() {
        let total = 0;
        $('.weight-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#totalWeightBadge').text(total.toFixed(2) + '%');
        if (Math.abs(total - 100.0) < 0.01) {
            $('#totalWeightBadge').removeClass('text-danger').addClass('text-primary');
        } else {
            $('#totalWeightBadge').removeClass('text-primary').addClass('text-danger');
        }
    }

    $('.weight-input').on('input', function() {
        calcTotalWeight();
    });
    calcTotalWeight();
});
</script>
@endpush
