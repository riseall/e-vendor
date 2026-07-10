@extends('layouts.app', ['title' => 'Audit Vendor'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Audit Vendor')
@section('page_title', 'Audit Vendor - On Desk & On Site')
@section('page_desc',
    'Pantau proses audit vendor berdasarkan hasil risk assessment (Medium = On Desk, High = On
    Site).')

    @php
        $typeOptions = [
            'all' => 'Semua Tipe',
            'on_desk' => 'On Desk',
            'on_site' => 'On Site',
        ];
        $typeMap = [
            'on_desk' => ['class' => 'vnd-tag--on-desk', 'label' => 'ON DESK'],
            'on_site' => ['class' => 'vnd-tag--on-site', 'label' => 'ON SITE'],
        ];
    @endphp

@section('content')
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- Stat Cards --}}
    <div class="row mb-6">
        <div class="col-6 col-sm-3 mb-3">
            <div class="vnd-stat vnd-stat--primary">
                <div class="vnd-stat-icon"><i class="flaticon2-hourglass text-white"></i></div>
                <div>
                    <div class="vnd-stat-num">{{ $countByStatus['scheduled'] ?? 0 }}</div>
                    <div class="vnd-stat-lbl">Baru Terjadwal</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3 mb-3">
            <div class="vnd-stat vnd-stat--info">
                <div class="vnd-stat-icon"><i class="flaticon2-refresh text-white"></i></div>
                <div>
                    <div class="vnd-stat-num">{{ $countByStatus['in_progress'] ?? 0 }}</div>
                    <div class="vnd-stat-lbl">Berjalan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3 mb-3">
            <div class="vnd-stat vnd-stat--warning">
                <div class="vnd-stat-icon"><i class="flaticon-warning text-white"></i></div>
                <div>
                    <div class="vnd-stat-num">{{ $countByStatus['need_revision'] ?? 0 }}</div>
                    <div class="vnd-stat-lbl">Perlu Revisi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3 mb-3">
            <div class="vnd-stat vnd-stat--success">
                <div class="vnd-stat-icon"><i class="flaticon2-check-mark text-white"></i></div>
                <div>
                    <div class="vnd-stat-num">{{ $countByStatus['completed'] ?? 0 }}</div>
                    <div class="vnd-stat-lbl">Selesai</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="vnd-card">
        <div class="vnd-card-head">
            <div>
                <div class="vnd-card-title">
                    <span class="vnd-card-title-dot"></span>
                    Daftar Audit Vendor
                </div>
                <div class="mt-1">
                    <span class="vnd-tag vnd-tag--on-desk">On Desk: Medium Risk</span>
                    <span class="vnd-tag vnd-tag--on-site">On Site: High Risk</span>
                </div>
            </div>

            <form method="GET" action="{{ route('qa.audit.index') }}" class="vnd-filter">
                {{-- <div class="form-input" style="width:200px;">
                    <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm"
                        placeholder="No permohonan / vendor...">
                </div> --}}
                <select name="type" class="selectpicker" style="width:130px;">
                    @foreach ($typeOptions as $value => $label)
                        <option value="{{ $value }}" {{ $type === $value ? 'selected' : '' }}>{{ $label }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="selectpicker" style="width:160px;">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="vnd-btn-filter">
                    <i class="flaticon-search" style="font-size:.65rem;"></i> Filter
                </button>
                <a href="{{ route('qa.audit.index') }}" class="vnd-btn-reset">Reset</a>
            </form>
        </div>

        <div class="card-body p-0 px-4 pt-5 pb-6">
            <table id="tbl-audit" class="table tbl-vendor table-borderless" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Permohonan</th>
                        <th>Vendor</th>
                        <th>Tipe Audit</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Update</th>
                        <th class="text-right no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($audits as $audit)
                        @php
                            $vendorName =
                                optional(optional($audit->application)->general)->nama_perusahaan ??
                                (optional(optional($audit->application)->user)->name ?? '—');
                            $typeInfo = $typeMap[$audit->audit_type] ?? [
                                'class' => 'vnd-tag--muted',
                                'label' => strtoupper($audit->audit_type),
                            ];
                            $progress = $audit->progressPercent();
                        @endphp
                        <tr>
                            <td class="vnd-cell-muted">{{ $audits->firstItem() + $loop->index }}</td>
                            <td><span
                                    class="vnd-appnum">{{ optional($audit->application)->application_number ?? '—' }}</span>
                            </td>
                            <td>
                                <div class="vnd-vendor-name">{{ $vendorName }}</div>
                                <div class="vnd-vendor-email">
                                    {{ optional(optional($audit->application)->user)->email ?? '—' }}</div>
                            </td>
                            <td><span class="vnd-tag {{ $typeInfo['class'] }}">{{ $typeInfo['label'] }}</span></td>
                            <td>
                                <span class="vnd-status vnd-status--{{ $audit->status }}">
                                    {{ str_replace('_', ' ', strtoupper($audit->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="vnd-progress">
                                    <div class="vnd-progress-bar" style="width: {{ $progress }}%"></div>
                                    <span class="vnd-progress-text">{{ $progress }}%</span>
                                </div>
                            </td>
                            <td class="vnd-cell-muted">{{ optional($audit->updated_at)->diffForHumans() ?? '—' }}</td>
                            <td class="text-right">
                                <a href="{{ route('qa.audit.show', $audit->id) }}" class="vnd-btn-detail">
                                    <i class="flaticon-eye icon-sm text-primary"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="vnd-empty">
                                    <i class="flaticon2-search-1"></i>
                                    <div class="vnd-empty-title">Tidak ada audit</div>
                                    <div class="vnd-empty-sub">Audit akan muncul setelah risk assessment menghasilkan
                                        Medium/High.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // Flash toast
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

            $('#tbl-audit').DataTable({
                scrollY: '65vh',
                scrollCollapse: true,
                scrollX: true,
                paging: true,
                dom: '<"d-flex justify-content-between align-items-center mb-4"lf>rtip',
            });

            $('select[name="status"], select[name="type"]').on('change', function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush
