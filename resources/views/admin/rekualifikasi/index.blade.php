@extends('layouts.app', ['title' => 'Daftar Rekualifikasi Vendor'])

@section('breadcrumb', 'Vendor')
@section('step', 'Rekualifikasi')
@section('page_title', 'Daftar Rekualifikasi Vendor')
@section('page_desc', 'Daftar permohonan evaluasi ulang / rekualifikasi vendor.')

@section('content')
    <div class="card card-custom shadow-sm">
        <div class="card-header border-0 pt-5 flex-wrap">
            <h3 class="card-title align-items-start flex-column mb-3 mb-sm-0">
                <span class="card-label font-weight-bolder text-dark">Riwayat & Manajemen Rekualifikasi</span>
                <span class="text-muted mt-1 font-size-sm">Daftar rekualifikasi mandiri vendor dan trigger otomatis oleh
                    Pengadaan / QA</span>
            </h3>
            <div class="card-toolbar">
                @if (auth()->user()->role === 'supplier')
                    <form action="{{ route('rekualifikasi.initiate') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning font-weight-bolder px-4 py-2">
                            <i class="flaticon2-reload mr-1" style="font-size:0.8rem;"></i> Picu Rekualifikasi Mandiri (Edit
                            Profil)
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-warning font-weight-bolder px-4 py-2" data-toggle="modal"
                        data-target="#modalTriggerRekualifikasiAdmin">
                        <i class="flaticon2-reload mr-1" style="font-size:0.8rem;"></i> Picu Rekualifikasi Vendor
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body py-3">
            @if (session('success'))
                <div class="alert alert-custom alert-light-success fade show mb-5" role="alert">
                    <div class="alert-icon"><i class="flaticon2-check-mark text-success"></i></div>
                    <div class="alert-text">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
                    <div class="alert-icon"><i class="flaticon-warning text-danger"></i></div>
                    <div class="alert-text">{{ session('error') }}</div>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-custom alert-light-info fade show mb-5" role="alert">
                    <div class="alert-icon"><i class="flaticon-info text-info"></i></div>
                    <div class="alert-text">{{ session('info') }}</div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-head-custom table-vertical-center" id="tbl_rekualifikasi">
                    <thead>
                        <tr class="text-left text-uppercase">
                            <th style="min-width: 120px">No. Permohonan</th>
                            <th style="min-width: 170px">Perusahaan / Vendor</th>
                            <th style="min-width: 160px">Pemicu Rekualifikasi</th>
                            <th style="min-width: 110px">Status</th>
                            <th style="min-width: 120px">Tanggal Dibuat</th>
                            <th class="text-right" style="min-width: 120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $app)
                            @php
                                $vName =
                                    optional($app->general)->nama_perusahaan ?:
                                    (optional($app->user)->name ?:
                                    'Vendor');
                            @endphp
                            <tr>
                                <td>
                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                        {{ $app->application_number ?: 'Draft' }}
                                    </span>
                                    <span class="text-muted font-size-sm">ID: {{ $app->id }}</span>
                                </td>
                                <td>
                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                        {{ $vName }}
                                    </span>
                                    <span class="text-muted font-size-xs d-block">
                                        {{ optional($app->user)->email }}
                                    </span>
                                </td>
                                <td>
                                    <span class="label label-light-warning label-inline font-weight-bold py-2 px-3">
                                        @switch($app->requalification_reason)
                                            @case('vendor_initiative')
                                                Inisiatif Vendor (Edit Profil)
                                            @break

                                            @case('expired_period')
                                                Kadaluarsa Periode
                                            @break

                                            @case('cdob_expiry')
                                                Sertifikat CDOB Expired
                                            @break

                                            @case('eval_score_drop')
                                                Penurunan Skor Evaluasi
                                            @break

                                            @case('qa_trigger')
                                                Trigger Manual QA
                                            @break

                                            @default
                                                {{ $app->requalification_reason ?: 'Rekualifikasi' }}
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $stMap = [
                                            'draft' => 'label-light-warning',
                                            'submitted' => 'label-light-info',
                                            'verified' => 'label-light-primary',
                                            'approved' => 'label-light-success',
                                            'rejected' => 'label-light-danger',
                                            'need_revision' => 'label-light-danger',
                                        ];
                                        $stClass = $stMap[$app->status] ?? 'label-light-secondary';
                                    @endphp
                                    <span class="label {{ $stClass }} label-inline font-weight-bold">
                                        {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark-75 d-block font-size-sm">
                                        {{ $app->created_at ? $app->created_at->format('d M Y H:i') : '-' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="d-inline-flex justify-content-end" style="gap:4px;">
                                        @if (auth()->user()->role === 'supplier' && $app->status === 'draft')
                                            <a href="{{ route('registrasi.index') }}"
                                                class="btn btn-sm btn-light-warning font-weight-bold">
                                                <i class="flaticon2-edit"></i> Edit Form
                                            </a>
                                        @else
                                            <a href="{{ route('registrasi.tracking', $app->application_number ?: $app->id) }}"
                                                class="btn btn-sm btn-light-primary font-weight-bold">
                                                <i class="flaticon2-search"></i> Detail
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <div class="py-4">
                                            <i class="flaticon2-reload text-muted font-size-h1 d-block mb-2"></i>
                                            <div class="font-weight-bold">Belum ada data rekualifikasi</div>
                                            <div class="font-size-sm text-muted">Permohonan rekualifikasi akan tampil di sini
                                                setelah dipicu.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap my-3">
                    {{ $applications->links() }}
                </div>
            </div>
        </div>

        {{-- Modal Trigger Rekualifikasi Admin --}}
        @if (auth()->user()->role !== 'supplier')
            <div class="modal fade" id="modalTriggerRekualifikasiAdmin" data-backdrop="static" tabindex="-1" role="dialog"
                aria-labelledby="staticBackdrop" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="formTriggerRekualifikasiModal" action="" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title font-weight-bolder" id="modalTriggerTitle">
                                    <i class="flaticon2-reload text-warning mr-2"></i> Picu Rekualifikasi Vendor
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i aria-hidden="true" class="ki ki-close"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bolder">Pilih Vendor (Berstatus Approved): <span
                                            class="text-danger">*</span></label>
                                    <select name="vendor_app_id" id="select_vendor_trigger"
                                        class="form-control form-control-solid selectpicker" data-live-search="true" required>
                                        <option value="">-- Pilih Vendor --</option>
                                        @foreach ($approvedVendors as $v)
                                            @php
                                                $name =
                                                    optional($v->general)->nama_perusahaan ?:
                                                    (optional($v->user)->name ?:
                                                    'Vendor');
                                            @endphp
                                            <option value="{{ $v->id }}">
                                                {{ $name }} ({{ $v->application_number ?: 'ID: ' . $v->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="font-weight-bolder">Alasan / Pemicu Rekualifikasi: <span
                                            class="text-danger">*</span></label>
                                    <select name="reason" class="form-control form-control-solid" required>
                                        <option value="qa_trigger">Permintaan Tim Pengadaan / QA (QA Trigger)</option>
                                        <option value="expired_period">Masa Berlaku Kadaluarsa (<= 60 Hari)</option>
                                        <option value="cdob_expiry">Masa Berlaku Sertifikat CDOB Kadaluarsa</option>
                                        <option value="eval_score_drop">Penurunan Skor Evaluasi Kinerja</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary font-weight-bold"
                                    data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning font-weight-bold">
                                    <i class="flaticon2-reload mr-1"></i> Picu Rekualifikasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endsection

    @push('scripts')
        <script>
            $(function() {
                var routeTemplate = "{{ route('rekualifikasi.trigger', ':id') }}";

                $('#select_vendor_trigger').on('change', function() {
                    var val = $(this).val();
                    if (val) {
                        var actionUrl = routeTemplate.replace(':id', val);
                        $('#formTriggerRekualifikasiModal').attr('action', actionUrl);
                    } else {
                        $('#formTriggerRekualifikasiModal').attr('action', '');
                    }
                });

                $('#formTriggerRekualifikasiModal').on('submit', function(e) {
                    var action = $(this).attr('action');
                    if (!action || action === '') {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Pilih Vendor',
                                'Silakan pilih vendor berstatus Approved terlebih dahulu.', 'warning');
                        } else {
                            alert('Silakan pilih vendor terlebih dahulu.');
                        }
                    }
                });
            });
        </script>
    @endpush
