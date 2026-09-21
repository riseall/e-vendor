@extends('layouts.app', ['title' => 'Pengaturan Evaluasi Vendor'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Pengaturan Bobot & Threshold')
@section('page_title', 'Pengaturan Evaluasi Vendor')
@section('page_desc', 'Konfigurasi proporsi 6 aspek penilaian kinerja dan batas ambang kategori vendor.')

@section('content')
    {{-- Flash container untuk SweetAlert Toast --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    <form action="{{ route('admin.evaluasi.settings.update') }}" method="POST" id="formEvaluationSettings">
        @csrf
        <div class="row">
            {{-- Card Bobot 6 Aspek --}}
            <div class="col-lg-7 mb-4">
                <div class="card card-custom shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-4 d-flex align-items-center justify-content-between">
                        <div class="card-title mb-0">
                            <h5 class="font-weight-bolder text-dark mb-0">
                                <i class="fas fa-balance-scale text-primary mr-2"></i>Bobot 6 Aspek Penilaian
                            </h5>
                        </div>
                        <span class="badge badge-light-primary font-weight-bolder font-size-sm px-3 py-2"
                            id="totalWeightTopBadge">
                            Total: 100%
                        </span>
                    </div>
                    <div class="card-body p-6">

                        {{-- Kelompok 1: Sumber Data QAD ERP --}}
                        <div class="mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-uppercase font-weight-bolder font-size-xs text-primary letter-spacing-1">
                                    <i class="fas fa-server mr-1"></i> Aspek Otomatis dari QAD ERP
                                </span>
                                <span class="badge badge-light-primary font-weight-bold font-size-xs" id="badgeQadTotal">
                                    Subtotal QAD: 60%
                                </span>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-sm-7">
                                    <label class="font-weight-bold text-dark mb-0">1. Delivery</label>
                                    <div class="text-muted font-size-xs">Ditarik otomatis dari PO Receipt QAD</div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="weight_delivery"
                                            class="form-control font-weight-bolder text-center weight-input qad-weight"
                                            value="{{ $settings->weight_delivery }}" required>
                                        <div class="input-group-append"><span
                                                class="input-group-text bg-light font-weight-bolder">%</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-sm-7">
                                    <label class="font-weight-bold text-dark mb-0">2. Quality</label>
                                    <div class="text-muted font-size-xs">Ditarik dari data Released / Reject QAD</div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="weight_quality"
                                            class="form-control font-weight-bolder text-center weight-input qad-weight"
                                            value="{{ $settings->weight_quality }}" required>
                                        <div class="input-group-append"><span
                                                class="input-group-text bg-light font-weight-bolder">%</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-sm-7">
                                    <label class="font-weight-bold text-dark mb-0">3. Quantity</label>
                                    <div class="text-muted font-size-xs">Ditarik dari kesesuaian order vs receipt QAD</div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="weight_quantity"
                                            class="form-control font-weight-bolder text-center weight-input qad-weight"
                                            value="{{ $settings->weight_quantity }}" required>
                                        <div class="input-group-append"><span
                                                class="input-group-text bg-light font-weight-bolder">%</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kelompok 2: Penilaian Manual QA --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-uppercase font-weight-bolder font-size-xs text-warning letter-spacing-1">
                                    <i class="fas fa-clipboard-check mr-1"></i> Aspek Input Manual
                                </span>
                                <span class="badge badge-light-warning font-weight-bold font-size-xs text-warning"
                                    id="badgeQaTotal">
                                    Subtotal Manual: 40%
                                </span>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-sm-7">
                                    <label class="font-weight-bold text-dark mb-0">4. Complain</label>
                                    <div class="text-muted font-size-xs">Diinput berkala</div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="weight_complain"
                                            class="form-control font-weight-bolder text-center weight-input qa-weight"
                                            value="{{ $settings->weight_complain }}" required>
                                        <div class="input-group-append"><span
                                                class="input-group-text bg-light font-weight-bolder">%</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-sm-7">
                                    <label class="font-weight-bold text-dark mb-0">5. Incoming Material</label>
                                    <div class="text-muted font-size-xs">Diinput berkala</div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="weight_incoming_material"
                                            class="form-control font-weight-bolder text-center weight-input qa-weight"
                                            value="{{ $settings->weight_incoming_material }}" required>
                                        <div class="input-group-append"><span
                                                class="input-group-text bg-light font-weight-bolder">%</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-sm-7">
                                    <label class="font-weight-bold text-dark mb-0">6. Safety & Env</label>
                                    <div class="text-muted font-size-xs">Diinput berkala</div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="weight_safety_environment"
                                            class="form-control font-weight-bolder text-center weight-input qa-weight"
                                            value="{{ $settings->weight_safety_environment }}" required>
                                        <div class="input-group-append"><span
                                                class="input-group-text bg-light font-weight-bolder">%</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total & Live Indicator --}}
                        <div class="p-4 rounded bg-light" id="totalWeightContainer" style="border: 1px solid #ebedf3;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="font-weight-bolder text-dark">Total Akumulasi Bobot:</span>
                                <span class="h5 font-weight-bolder text-primary mb-0" id="totalWeightBadge">100.00%</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                <div class="progress-bar bg-primary" id="progressBarQad" role="progressbar"
                                    style="width: 60%" title="QAD ERP"></div>
                                <div class="progress-bar bg-warning" id="progressBarQa" role="progressbar"
                                    style="width: 40%" title="Manual Input"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 font-size-xs text-muted">
                                <span><i class="fas fa-square text-primary mr-1"></i>QAD ERP</span>
                                <span><i class="fas fa-square text-warning mr-1"></i>Manual Input</span>
                                <span id="weightStatusText" class="font-weight-bold text-success"><i
                                        class="fas fa-check-circle mr-1"></i>Valid (Pas 100%)</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Card Threshold Kategori --}}
            <div class="col-lg-5 mb-4">
                <div class="card card-custom shadow-sm border-0 h-100 d-flex flex-column justify-content-between"
                    style="border-radius: 12px;">
                    <div>
                        <div class="card-header bg-white border-bottom py-4">
                            <h5 class="card-title font-weight-bolder text-dark mb-0">
                                <i class="fas fa-layer-group text-warning mr-2"></i>Batas Threshold Kategori
                            </h5>
                        </div>
                        <div class="card-body p-6">

                            {{-- Visual Tier Reference --}}
                            <div class="mb-4 p-3 rounded" style="background-color: #f8f9fa; border: 1px dashed #e4e6ef;">
                                <div class="font-weight-bolder font-size-xs text-muted text-uppercase mb-2">Klasifikasi
                                    Evaluasi:</div>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge badge-success font-weight-bold px-2 py-1">BAIK</span>
                                    <span class="font-size-sm font-weight-bold text-dark">&ge; <span
                                            id="labelBaikThreshold">{{ $settings->threshold_baik }}</span> Poin</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge badge-warning font-weight-bold px-2 py-1 text-white">CUKUP</span>
                                    <span class="font-size-sm font-weight-bold text-dark">&ge; <span
                                            id="labelCukupThreshold">{{ $settings->threshold_cukup }}</span> Poin</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge badge-danger font-weight-bold px-2 py-1">KURANG</span>
                                    <span class="font-size-sm font-weight-bold text-dark">&lt; <span
                                            id="labelKurangThreshold">{{ $settings->threshold_cukup }}</span> Poin</span>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark mb-1">
                                    Batas Minimal Kategori <span
                                        class="badge badge-success font-weight-bold px-2 py-1 ml-1">BAIK</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text bg-light font-weight-bolder">&ge;</span></div>
                                    <input type="number" step="0.01" min="0" max="100"
                                        name="threshold_baik" id="inputThresholdBaik"
                                        class="form-control font-weight-bolder text-center"
                                        value="{{ $settings->threshold_baik }}" required>
                                    <div class="input-group-append"><span
                                            class="input-group-text bg-light font-weight-bold">Poin</span></div>
                                </div>
                                <span class="form-text text-muted font-size-xs mt-1">Vendor mendapatkan predikat
                                    <strong>BAIK</strong> jika skor akhir &ge; nilai ini.</span>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark mb-1">
                                    Batas Minimal Kategori <span
                                        class="badge badge-warning font-weight-bold px-2 py-1 ml-1 text-white">CUKUP</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text bg-light font-weight-bolder">&ge;</span></div>
                                    <input type="number" step="0.01" min="0" max="100"
                                        name="threshold_cukup" id="inputThresholdCukup"
                                        class="form-control font-weight-bolder text-center"
                                        value="{{ $settings->threshold_cukup }}" required>
                                    <div class="input-group-append"><span
                                            class="input-group-text bg-light font-weight-bold">Poin</span></div>
                                </div>
                                <span class="form-text text-muted font-size-xs mt-1">Skor di bawah batas Baik dan &ge;
                                    nilai ini masuk kategori <strong>CUKUP</strong>.</span>
                            </div>

                            {{-- Informational Banner --}}
                            <div class="alert alert-custom alert-light-info fade show mb-0 p-3" role="alert"
                                style="border-radius: 8px;">
                                <div class="alert-icon"><i class="fas fa-info-circle text-info"></i></div>
                                <div class="alert-text font-size-xs">
                                    <strong>Catatan Sistem:</strong><br>
                                    Perubahan bobot & threshold berlaku otomatis pada kalkulasi evaluasi bulanan dan
                                    pengesahan evaluasi tahunan selanjutnya.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-4 px-6 text-right">
                        <a href="{{ route('admin.evaluasi.index') }}"
                            class="btn btn-light-danger font-weight-bolder px-8 mr-3">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary font-weight-bolder px-8" id="btnSaveSettings">
                            <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Flash toast matching admin/audit
            (function() {
                var $el = $('#vnd-flash');
                if (!$el.length || typeof Swal === 'undefined') return;
                var msgs = [{
                        key: 'success',
                        icon: 'success',
                        title: 'Sukses'
                    },
                    {
                        key: 'error',
                        icon: 'error',
                        title: 'Gagal'
                    },
                    {
                        key: 'warning',
                        icon: 'warning',
                        title: 'Peringatan'
                    },
                    {
                        key: 'info',
                        icon: 'info',
                        title: 'Informasi'
                    },
                ];
                msgs.forEach(function(m) {
                    var v = $el.data(m.key);
                    if (v) {
                        Swal.fire({
                            icon: m.icon,
                            title: m.title,
                            html: v,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true
                        });
                    }
                });
            })();

            /* Realtime Weight & Subtotal Calculation */
            function calcTotalWeight() {
                let total = 0;
                let qadTotal = 0;
                let qaTotal = 0;

                $('.qad-weight').each(function() {
                    let val = parseFloat($(this).val()) || 0;
                    qadTotal += val;
                    total += val;
                });

                $('.qa-weight').each(function() {
                    let val = parseFloat($(this).val()) || 0;
                    qaTotal += val;
                    total += val;
                });

                $('#badgeQadTotal').text('Subtotal QAD: ' + qadTotal.toFixed(1) + '%');
                $('#badgeQaTotal').text('Subtotal Manual: ' + qaTotal.toFixed(1) + '%');
                $('#totalWeightBadge').text(total.toFixed(2) + '%');
                $('#totalWeightTopBadge').text('Total: ' + total.toFixed(2) + '%');

                // Progress bar distribution
                let totalCap = total > 0 ? total : 100;
                let qadPct = (qadTotal / totalCap) * 100;
                let qaPct = (qaTotal / totalCap) * 100;
                $('#progressBarQad').css('width', qadPct + '%');
                $('#progressBarQa').css('width', qaPct + '%');

                // Validation status text & styling
                if (Math.abs(total - 100.0) < 0.01) {
                    $('#totalWeightBadge').removeClass('text-danger text-warning').addClass('text-success');
                    $('#totalWeightTopBadge').removeClass('badge-light-danger badge-light-warning').addClass(
                        'badge-light-primary');
                    $('#weightStatusText').html('<i class="fas fa-check-circle mr-1"></i>Valid (Pas 100%)')
                        .removeClass('text-danger text-warning').addClass('text-success');
                    $('#btnSaveSettings').prop('disabled', false);
                } else {
                    let diff = (100.0 - total).toFixed(2);
                    let diffMsg = diff > 0 ? 'Kurang ' + diff + '%' : 'Kelebihan ' + Math.abs(diff) + '%';
                    $('#totalWeightBadge').removeClass('text-success').addClass('text-danger');
                    $('#totalWeightTopBadge').removeClass('badge-light-primary').addClass('badge-light-danger');
                    $('#weightStatusText').html('<i class="fas fa-exclamation-triangle mr-1"></i>' + diffMsg +
                        ' (Harus 100%)').removeClass('text-success').addClass('text-danger');
                }
            }

            $('.weight-input').on('input change', function() {
                calcTotalWeight();
            });

            // Threshold sync to visual guide
            $('#inputThresholdBaik').on('input change', function() {
                let val = $(this).val() || '0';
                $('#labelBaikThreshold').text(val);
            });

            $('#inputThresholdCukup').on('input change', function() {
                let val = $(this).val() || '0';
                $('#labelCukupThreshold').text(val);
                $('#labelKurangThreshold').text(val);
            });

            calcTotalWeight();
        });
    </script>
@endpush
