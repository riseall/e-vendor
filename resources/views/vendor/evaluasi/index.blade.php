@extends('layouts.app', ['title' => __('vendor_performance_evaluation')])

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-award text-primary mr-2"></i>{{ __('vendor_perf_eval_report') }}
            </h1>
            <p class="text-muted small mb-0">{{ __('annual_perf_eval_result_desc') }}</p>
        </div>
    </div>

    @if($evaluations->isEmpty())
        <div class="card card-custom shadow-sm text-center py-5">
            <div class="card-body">
                <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                <h5 class="font-weight-bold text-dark">{{ __('no_annual_eval_report_title') }}</h5>
                <p class="text-muted small">{{ __('no_annual_eval_report_desc') }}</p>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-4">
                <div class="card card-custom shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="card-title font-weight-bold mb-0"><i class="fas fa-history mr-2"></i>{{ __('select_evaluation_year') }}</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($evaluations as $item)
                            <a href="{{ route('vendor.evaluasi.index', ['year' => $item->year]) }}" 
                               class="list-group-item list-group-item-action d-flex align-items-center justify-content-between {{ $selectedEvaluation && $selectedEvaluation->id == $item->id ? 'active' : '' }}">
                                <span class="font-weight-bold">{{ __('year_num', ['year' => $item->year]) }}</span>
                                <div>
                                    <span class="badge {{ $item->category == 'BAIK' ? 'badge-success' : ($item->category == 'CUKUP' ? 'badge-warning' : 'badge-danger') }} mr-2">
                                        {{ $item->category == 'BAIK' ? __('eval_cat_good') : ($item->category == 'CUKUP' ? __('eval_cat_adequate') : __('eval_cat_poor')) }}
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
                                <i class="fas fa-file-alt mr-2"></i>{{ __('annual_eval_detail_year', ['year' => $selectedEvaluation->year]) }}
                            </h5>
                            <div>
                                <span class="badge badge-light font-weight-bold text-success px-3 py-2">
                                    <i class="fas fa-check-circle text-success mr-1"></i>{{ __('approved_by_procurement_gm') }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            {{-- Alert Khusus Kategori CUKUP dan KURANG --}}
                            @if($selectedEvaluation->category === 'CUKUP')
                                <div class="alert alert-custom alert-light-warning fade show mb-5 p-4 border border-warning shadow-xs" role="alert" style="border-radius: 10px;">
                                    <div class="alert-icon">
                                        <i class="fas fa-exclamation-circle text-warning fa-2x mr-2"></i>
                                    </div>
                                    <div class="alert-text">
                                        <h5 class="alert-heading font-weight-bolder text-warning mb-1">
                                            <i class="fas fa-info-circle mr-1"></i>{{ __('perf_improvement_appeal_title') }}
                                        </h5>
                                        <div class="text-dark-75 font-size-sm">
                                            {!! __('eval_appeal_adequate_desc', [
                                                'year' => $selectedEvaluation->year,
                                                'category' => __('eval_cat_adequate'),
                                                'score' => number_format($selectedEvaluation->final_score, 2),
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            @elseif($selectedEvaluation->category === 'KURANG')
                                <div class="alert alert-custom alert-light-danger fade show mb-5 p-4 border border-danger shadow-xs" role="alert" style="border-radius: 10px;">
                                    <div class="alert-icon">
                                        <i class="fas fa-exclamation-triangle text-danger fa-2x mr-2"></i>
                                    </div>
                                    <div class="alert-text">
                                        <h5 class="alert-heading font-weight-bolder text-danger mb-1">
                                            <i class="fas fa-radiation-alt mr-1"></i>{{ __('perf_warning_poor_title') }}
                                        </h5>
                                        <div class="text-dark-75 font-size-sm mb-2">
                                            {!! __('eval_warning_poor_desc', [
                                                'year' => $selectedEvaluation->year,
                                                'category' => __('eval_cat_poor'),
                                                'score' => number_format($selectedEvaluation->final_score, 2),
                                            ]) !!}
                                        </div>
                                        <div class="p-3 bg-white border border-danger rounded text-danger font-size-sm">
                                            <strong><i class="fas fa-bolt mr-1"></i>{{ __('mandatory_instruction_colon') }}</strong> {!! __('eval_cap_instruction_desc') !!}
                                            <div class="text-muted font-size-xs mt-1">
                                                {{ __('eval_poor_consequence_note') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- KPI Cards --}}
                            <div class="row mb-4">
                                <div class="col-sm-6 col-lg-6 mb-3">
                                    <div class="p-3 border rounded bg-light">
                                        <span class="text-muted small d-block mb-1">{{ __('annual_final_score') }}</span>
                                        <span class="h2 font-weight-bold text-primary mb-0">{{ number_format($selectedEvaluation->final_score, 2) }}</span>
                                        <span class="text-muted small"> / 100</span>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-6 mb-3">
                                    <div class="p-3 border rounded bg-light">
                                        <span class="text-muted small d-block mb-1">{{ __('evaluation_result_category') }}</span>
                                        @if($selectedEvaluation->category == 'BAIK')
                                            <span class="h2 font-weight-bold text-success mb-0">{{ __('eval_cat_good') }}</span>
                                            <p class="small text-muted mb-0 mt-1">{{ __('eval_desc_good') }}</p>
                                        @elseif($selectedEvaluation->category == 'CUKUP')
                                            <span class="h2 font-weight-bold text-warning mb-0">{{ __('eval_cat_adequate') }}</span>
                                            <p class="small text-muted mb-0 mt-1">{{ __('eval_desc_adequate') }}</p>
                                        @else
                                            <span class="h2 font-weight-bold text-danger mb-0">{{ __('eval_cat_poor') }}</span>
                                            <p class="small text-muted mb-0 mt-1">{{ __('eval_desc_poor') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Breakdown 6 Aspek --}}
                            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                                <i class="fas fa-chart-bar text-primary mr-1"></i>{{ __('score_breakdown_by_aspect') }}
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-light">
                                        <tr class="small text-uppercase font-weight-bold">
                                            <th>{{ __('no_num') }}</th>
                                            <th>{{ __('assessment_aspect') }}</th>
                                            <th class="text-center" width="140">{{ __('average_score') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>{{ __('aspect_delivery') }}</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->delivery_score_avg, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>{{ __('aspect_quality') }}</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->quality_score_avg, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>{{ __('aspect_quantity') }}</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->quantity_score_avg, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>{{ __('aspect_complain') }}</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->complain_score_avg, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>{{ __('aspect_incoming_material') }}</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->incoming_material_score_avg, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td>{{ __('aspect_safety_environment') }}</td>
                                            <td class="text-center font-weight-bold text-dark">{{ number_format($selectedEvaluation->safety_environment_score_avg, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            @if($selectedEvaluation->notes)
                                <div class="mt-3 p-3 bg-light-warning border rounded">
                                    <h6 class="font-weight-bold text-warning mb-1"><i class="fas fa-sticky-note mr-1"></i>{{ __('additional_notes_colon') }}</h6>
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
