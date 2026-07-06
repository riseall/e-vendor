@extends('layouts.app', ['title' => 'Verifikasi Pengadaan'])

@section('breadcrumb', 'Pengadaan')
@section('step', 'Verifikasi')
@section('page_title', 'Verifikasi Permohonan Vendor')
@section('page_desc', 'Kelola permohonan vendor yang sudah dikirim dan tentukan apakah data lengkap atau perlu revisi.')

@push('style')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    {{-- @include('admin.verifikasi.partials.index.styles') --}}
@endpush

@section('content')
    {{-- Stat Cards --}}
    <div class="row mb-6" style="gap:0;">
        <div class="col-12 col-sm-6 col-xl-3 mb-4 mb-xl-0 pr-xl-3">
            <div class="vnd-stat vnd-stat--primary">
                <div class="vnd-stat-icon">
                    <i class="flaticon2-layers-1 text-white"></i>
                </div>
                <div>
                    <div class="vnd-stat-num">{{ $totalApplicationsAll }}</div>
                    <div class="vnd-stat-lbl">Total Permohonan</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-4 mb-xl-0 px-xl-2">
            <div class="vnd-stat vnd-stat--info">
                <div class="vnd-stat-icon">
                    <i class="flaticon2-hourglass text-white"></i>
                </div>
                <div>
                    <div class="vnd-stat-num">{{ $totalSubmittedAll ?? '—' }}</div>
                    <div class="vnd-stat-lbl">Menunggu Verifikasi</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-4 mb-sm-0 px-xl-2">
            <div class="vnd-stat vnd-stat--warning">
                <div class="vnd-stat-icon">
                    <i class="flaticon-warning text-white"></i>
                </div>
                <div>
                    <div class="vnd-stat-num">{{ $totalNeedRevisionAll }}</div>
                    <div class="vnd-stat-lbl">Perlu Revisi</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pl-xl-3">
            <div class="vnd-stat vnd-stat--success">
                <div class="vnd-stat-icon">
                    <i class="flaticon2-check-mark text-white"></i>
                </div>
                <div>
                    <div class="vnd-stat-num">{{ $totalVerifiedAll ?? '—' }}</div>
                    <div class="vnd-stat-lbl">Terverifikasi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="vnd-card">

        {{-- Card Head: judul + filter --}}
        <div class="vnd-card-head">
            <div class="vnd-card-title">
                <span class="vnd-card-title-dot"></span>
                Daftar Permohonan
            </div>

            <form method="GET" action="{{ route('verifikasi.index') }}" class="vnd-filter" id="filterForm">
                {{-- Search --}}
                <div class="vnd-filter-item" style="width:210px;">
                    <input type="text" name="q" id="filterQ" value="{{ $search }}"
                        class="form-control form-control-sm" placeholder="Nomor, PIC, perusahaan, NPWP...">
                </div>

                {{-- Status --}}
                <select name="status" id="filterStatus" class="form-control form-control-sm" style="width:160px;">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                {{-- Aksi --}}
                <button type="submit" class="vnd-btn-filter">
                    <i class="flaticon-search" style="font-size:.7rem;"></i> Filter
                </button>
                <a href="{{ route('verifikasi.index') }}" class="vnd-btn-reset">
                    Reset
                </a>
            </form>
        </div>

        {{-- Table --}}
        <div class="card-body p-0 px-4 pt-5 pb-6">
            <table id="tbl-vendor" class="table tbl-vendor table-borderless" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Permohonan</th>
                        <th>PIC/Narahubung</th>
                        <th>Perusahaan</th>
                        <th>Status</th>
                        <th>Tgl Submit</th>
                        <th>Deadline 10 Hari</th>
                        <th class="text-right no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        @include('admin.verifikasi.partials.index.application-row')
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="vnd-empty">
                                    <div class="vnd-empty-icon">
                                        <i class="flaticon2-search-1"></i>
                                    </div>
                                    <div class="vnd-empty-title">Tidak ada permohonan</div>
                                    <div class="vnd-empty-sub">Coba ubah filter pencarian.</div>
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
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    @include('admin.verifikasi.partials.index.scripts')
@endpush
