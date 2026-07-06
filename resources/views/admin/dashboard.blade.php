@extends('layouts.app', ['title' => 'Dashboard'])

@php
    // Status maps resolved in controller, not in view.
@endphp

@section('content')
    {{-- Welcome card --}}
    <div class="card welcome-card p-6 py-8 shadow-sm">
        <p class="mb-1">{{ Auth::user()->roles->pluck('name')->implode(', ') }}</p>
        <h2 class="mb-1">{{ __('welcome') }}, <b>{{ ucwords(strtolower(Auth::user()->name)) }}!</b></h2>
        <p class="mb-0"><i class="far fa-calendar-alt mr-2 text-white"></i> {{ date('l') }}, {{ date('d F Y') }}</p>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         1. SUPPLIER OVERVIEW KPI
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="row mt-6">
        <div class="col-xl col-md-6 col-sm-6 mb-3">
            <div class="vnd-stat vnd-stat--primary">
                <div class="vnd-stat-icon"><i class="fa fa-building text-white"></i></div>
                <div class="flex-grow-1">
                    <div class="vnd-stat-num">{{ number_format($totalSupplier) }}</div>
                    <div class="vnd-stat-lbl">Total Supplier</div>
                    <div class="vnd-cell-muted">Seluruh supplier terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 col-sm-6 mb-3">
            <div class="vnd-stat vnd-stat--success">
                <div class="vnd-stat-icon"><i class="fa fa-check-circle text-white"></i></div>
                <div class="flex-grow-1">
                    <div class="vnd-stat-num">{{ number_format($approvedSupplier) }}</div>
                    <div class="vnd-stat-lbl">Approved Supplier</div>
                    <div class="vnd-cell-muted">Telah disetujui</div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <div class="vnd-stat vnd-stat--info">
                <div class="vnd-stat-icon"><i class="fa fa-hourglass-half text-white"></i></div>
                <div class="flex-grow-1">
                    <div class="vnd-stat-num">{{ number_format($underQualification) }}</div>
                    <div class="vnd-stat-lbl">Under Qualification</div>
                    <div class="vnd-cell-muted">Sedang proses kualifikasi</div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <div class="vnd-stat vnd-stat--danger">
                <div class="vnd-stat-icon"><i class="fa fa-ban text-white"></i></div>
                <div class="flex-grow-1">
                    <div class="vnd-stat-num">{{ number_format($suspendedSupplier) }}</div>
                    <div class="vnd-stat-lbl">Suspended Supplier</div>
                    <div class="vnd-cell-muted">Dibekukan / on-hold</div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <div class="vnd-stat vnd-stat--warning">
                <div class="vnd-stat-icon"><i class="fa fa-sync-alt text-white"></i></div>
                <div class="flex-grow-1">
                    <div class="vnd-stat-num">{{ number_format($requalificationDue) }}</div>
                    <div class="vnd-stat-lbl">Requalification Due</div>
                    <div class="vnd-cell-muted">Evaluasi ulang</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         2. SUPPLIER AUDIT MONITORING
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="vnd-card mb-4 mt-6">
        <div class="vnd-card-head">
            <div class="vnd-card-title">
                <span class="vnd-card-title-dot"></span>
                <i class="fa fa-clipboard-list text-primary"></i>
                Supplier Audit Monitoring
            </div>
            <a href="{{ route('qa.audit.index') }}" class="vnd-btn-detail vnd-btn-detail--secondary">
                View All <i class="fa fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <div class="card-body p-0 px-4 pt-5 pb-6">
                <table class="table tbl-vendor mb-0">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Supplier Type</th>
                            <th>Audit Type</th>
                            <th>Last Audit</th>
                            <th>Next Audit</th>
                            <th>Status</th>
                            <th>Auditor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($audits as $audit)
                            <tr>
                                <td><span class="vnd-vendor-name">{{ $audit->vendor_name }}</span></td>
                                <td>
                                    @forelse ($audit->category_chips as $cat)
                                        <span class="vnd-cat-chip">{{ $cat }}</span>
                                    @empty
                                        <span class="vnd-cell-muted">—</span>
                                    @endforelse
                                </td>
                                <td>{{ $audit->audit_type_label }}</td>
                                <td><span class="vnd-cell-muted">{{ $audit->last_audit_date ?: '—' }}</span></td>
                                <td><span class="vnd-cell-muted">{{ $audit->next_audit_date ?: '—' }}</span></td>
                                <td>
                                    <span class="vnd-status {{ $audit->status_cls }}">{{ $audit->status_label }}</span>
                                </td>
                                <td><span class="vnd-cell-muted">{{ $audit->auditor_display }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="vnd-empty">
                                        <div class="vnd-empty-icon"><i class="fa fa-inbox"></i></div>
                                        <div class="vnd-empty-title">Belum ada data audit</div>
                                        <div class="vnd-empty-sub">Audit supplier akan muncul di sini setelah dijadwalkan.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         3. TWO-COLUMN: Document Expiry + Performance / Risk
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="vnd-card h-100">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        Document Expiry Monitoring
                    </div>
                </div>
                <div class="table-responsive">
                    <div class="card-body p-0 px-4 pt-5 pb-6">
                        <table class="table tbl-vendor mb-0">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Document Type</th>
                                    <th>Issue Date</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documents as $doc)
                                    <tr>
                                        <td><span class="vnd-vendor-name">{{ $doc->supplier }}</span></td>
                                        <td><span class="vnd-cell-muted">{{ $doc->doc_type }}</span></td>
                                        <td><span class="vnd-cell-muted">{{ $doc->issue_date }}</span></td>
                                        <td><span class="vnd-cell-muted">{{ $doc->expiry_date }}</span></td>
                                        <td>
                                            <span
                                                class="vnd-status {{ $doc->status_cls }}">{{ $doc->status_label }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="vnd-empty">
                                                <div class="vnd-empty-icon"><i class="fa fa-file-alt"></i></div>
                                                <div class="vnd-empty-title">Belum ada dokumen</div>
                                                <div class="vnd-empty-sub">Dokumen vendor akan muncul di sini.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            {{-- Supplier Performance --}}
            <div class="vnd-card mb-4">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        Supplier Performance
                    </div>
                    <a href="#" class="vnd-icon-link"><i class="fa fa-chart-line"></i></a>
                </div>
                <div class="card-body p-0 px-4 pt-5 pb-6">
                    @forelse ($topPerformers as $i => $perf)
                        <div class="d-flex align-items-center {{ $loop->last ? '' : 'mb-3' }}">
                            <span class="vnd-avatar mr-3">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="vnd-vendor-name">{{ $perf->name }}</span>
                                    <span class="vnd-score vnd-score--blue">{{ $perf->pct }}%</span>
                                </div>
                                <div class="vnd-progress-track">
                                    <div class="vnd-progress-fill" style="width: {{ $perf->pct }}%;"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="vnd-empty">
                            <div class="vnd-empty-icon"><i class="fa fa-chart-bar"></i></div>
                            <div class="vnd-empty-title">Belum ada data performa</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Supplier Risk Assessment (QA) --}}
            <div class="vnd-card">
                <div class="vnd-card-head">
                    <div class="vnd-card-title">
                        <span class="vnd-card-title-dot"></span>
                        Supplier Risk Assessment
                    </div>
                </div>
                <div class="card-body p-0 px-4 pt-4 pb-5">
                    <div id="supplier-risk-chart" class="d-flex justify-content-center"></div>
                </div>
            </div>
        </div>
    </div>
@endSection

@push('scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> --}}

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Mengambil data dari variabel PHP/Blade
            const riskHigh = {{ $riskCounts['high'] ?? 0 }};
            const riskMedium = {{ $riskCounts['medium'] ?? 0 }};
            const riskLow = {{ $riskCounts['low'] ?? 0 }};
            const riskTotal = {{ $riskTotal ?? 0 }};

            const options = {
                series: [riskHigh, riskMedium, riskLow],
                labels: ['High Risk', 'Medium Risk', 'Low Risk'],
                chart: {
                    type: 'donut',
                    height: 320,
                    fontFamily: 'inherit', // Mengikuti font bawaan website Anda
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                // Warna modern (Merah, Amber/Kuning, Hijau)
                colors: ['#EF4444', '#F59E0B', '#10B981'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%', // Membuat ring lebih tipis agar terlihat modern
                            labels: {
                                show: true,
                                name: {
                                    fontSize: '13px',
                                    fontWeight: 500,
                                    color: '#6B7280', // Warna abu-abu elegan
                                    offsetY: -10
                                },
                                value: {
                                    fontSize: '28px',
                                    fontWeight: 700,
                                    color: '#111827', // Warna gelap modern
                                    offsetY: 5,
                                    formatter: function(val) {
                                        return val;
                                    }
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Total Suppliers',
                                    fontSize: '13px',
                                    fontWeight: 500,
                                    color: '#6B7280',
                                    formatter: function(w) {
                                        return riskTotal;
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false // Dimatikan agar desain terlihat bersih tanpa teks menumpuk di chart
                },
                stroke: {
                    show: true,
                    colors: ['#ffffff'], // Garis pemisah putih antar slice
                    width: 3
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    markers: {
                        radius: 12 // Membuat dot legend membulat sempurna
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 5
                    }
                },
                tooltip: {
                    theme: 'light',
                    fillSeriesColor: false,
                    y: {
                        formatter: function(value) {
                            // Menambahkan styling persentase di tooltip
                            let pct = riskTotal > 0 ? Math.round((value / riskTotal) * 100) : 0;
                            return value + " Supplier (" + pct + "%)";
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#supplier-risk-chart"), options);
            chart.render();
        });
    </script>
@endpush
