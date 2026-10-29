@php
    $app = $application;
    $isVerifiedAdmin = $application->status === \App\Models\VendorApplication::STATUS_VERIFIED_ADMIN;
    $isFullyVerified = $application->status === \App\Models\VendorApplication::STATUS_VERIFIED;
    $canManageVerify = auth()->user() && auth()->user()->can('verifikasi-verify-manager');
@endphp

{{-- ══════════════════════════════════════════════════════════════════════════════
     APPROVAL STATUS BANNER & MANAGER ACTION BAR (LEVEL 2)
══════════════════════════════════════════════════════════════════════════════ --}}

{{-- Card Alur Approval Berjenjang --}}
<div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:12px; background: #ffffff;">
    <div class="card-body p-6">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-5 pb-4 border-bottom">
            <div>
                <h5 class="font-weight-bolder text-dark mb-1 d-flex align-items-center">
                    <i class="fas fa-layer-group text-primary mr-2 font-size-lg"></i>
                    Status Verifikasi Berjenjang
                </h5>
                <span class="text-muted font-size-sm">Pelacakan proses persetujuan verifikasi pendaftaran calon
                    vendor</span>
            </div>

            <div class="d-flex align-items-center mt-3 mt-md-0">
                @if ($isFullyVerified)
                    <span class="label label-lg label-light-success label-inline font-weight-bolder py-3 px-4">
                        <i class="fas fa-check-circle text-success mr-2"></i> Terverifikasi Penuh (Siap QA Risk
                        Assessment)
                    </span>
                @else
                    <span class="label label-lg label-light-warning label-inline font-weight-bolder py-3 px-4">
                        <i class="fas fa-user-clock text-warning mr-2"></i> Menunggu Verifikasi Manajer
                    </span>
                @endif
            </div>
        </div>

        <div class="row align-items-stretch">
            {{-- Step 1: Admin Verifikator --}}
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="p-4 rounded-lg h-100 d-flex flex-column justify-content-between"
                    style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-circle badge-success mr-2 font-weight-bolder"
                                    style="width: 24px; height: 24px; font-size: 0.75rem;">1</span>
                                <strong class="text-dark font-size-sm">Verifikasi Tingkat 1: Admin /
                                    Verifikator</strong>
                            </div>
                            <span class="label label-sm label-success label-inline font-weight-bolder">Selesai</span>
                        </div>
                        <div class="text-success font-size-xs font-weight-bolder mb-1">
                            <i class="fas fa-check-circle text-success mr-1"></i> Seluruh data berkas
                            ({{ $totalItems }} bagian) telah disetujui.
                        </div>
                        <div class="text-muted font-size-xs mb-3">
                            Verifikator:
                            <strong>{{ optional($application->verifier)->name ?: 'Tim Verifikator' }}</strong> &middot;
                            {{ optional($application->verified_at)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="pt-2">
                        <button class="btn btn-xs btn-outline-success font-weight-bolder" type="button"
                            data-toggle="collapse" data-target="#collapseCardChecklist" aria-expanded="false"
                            aria-controls="collapseCardChecklist">
                            <i class="fas fa-clipboard-check mr-1"></i> Lihat Checklist Berkas
                            ({{ $approvedItems }}/{{ $totalItems }})
                        </button>
                    </div>
                </div>
            </div>

            {{-- Step 2: Manager Pengadaan --}}
            <div class="col-md-6">
                <div class="p-4 rounded-lg h-100 d-flex flex-column justify-content-between"
                    style="background: {{ $isFullyVerified ? '#f0fdf4' : '#fffbeb' }}; border: 1px solid {{ $isFullyVerified ? '#bbf7d0' : '#fde68a' }};">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span
                                    class="badge badge-circle {{ $isFullyVerified ? 'badge-success' : 'badge-warning' }} mr-2 font-weight-bolder"
                                    style="width: 24px; height: 24px; font-size: 0.75rem;">2</span>
                                <strong class="text-dark font-size-sm">Verifikasi Tingkat 2: Manajer Pengadaan</strong>
                            </div>
                            @if ($isFullyVerified)
                                <span
                                    class="label label-sm label-success label-inline font-weight-bolder">Disetujui</span>
                            @else
                                <span class="label label-sm label-warning label-inline font-weight-bolder">Menunggu
                                    Keputusan</span>
                            @endif
                        </div>
                        @if ($isFullyVerified)
                            <div class="text-success font-size-xs font-weight-bolder mb-1">
                                <i class="fas fa-check-double text-success mr-1"></i> Disetujui oleh Manajer Pengadaan.
                            </div>
                            <div class="text-muted font-size-xs mb-2">
                                Manajer:
                                <strong>{{ optional($application->managerVerifier)->name ?: 'Manajer Pengadaan' }}</strong>
                                &middot; {{ optional($application->manager_verified_at)->format('d/m/Y H:i') }}
                            </div>
                            @if ($application->manager_note)
                                <div class="mt-2 p-2 rounded bg-white font-size-xs text-dark"
                                    style="border: 1px dashed #86efac;">
                                    <strong>Catatan Manajer:</strong> {{ $application->manager_note }}
                                </div>
                            @endif
                        @else
                            <div class="font-size-xs font-weight-bolder mb-1" style="color: #b45309;">
                                <i class="fas fa-clock mr-1" style="color: #b45309;"></i> Menunggu keputusan persetujuan
                                resume atau pengembalian revisi.
                            </div>
                            <div class="text-muted font-size-xs mb-2">
                                Manajer dapat menyetujui langsung untuk melanjutkan ke QA Risk Assessment atau meminta
                                revisi ke vendor.
                            </div>
                        @endif
                    </div>
                    <div class="pt-2">
                        @if ($isFullyVerified)
                            <span class="text-success font-size-xs font-weight-bold">
                                <i class="fas fa-shield-alt text-success mr-1"></i> Siap dilanjutkan ke QA Risk
                                Assessment
                            </span>
                        @else
                            <span class="text-muted font-size-xs font-weight-bold">
                                <i class="fas fa-info-circle text-muted mr-1"></i> Menunggu otorisasi verifikasi tingkat
                                2
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Collapsible Checklist Rincian Berkas dari Verifikator Admin --}}
        <div class="collapse mt-4" id="collapseCardChecklist">
            <div class="card card-custom border shadow-none" style="border-radius:10px; background:#f8fafc;">
                <div class="card-header py-2 px-4 border-bottom d-flex align-items-center justify-content-between"
                    style="min-height:42px;">
                    <span class="font-weight-bold font-size-xs text-dark">
                        <i class="fas fa-list-check mr-1 text-primary"></i> Rincian Checklist Verifikasi Per Card &
                        Berkas
                    </span>
                    <button type="button" class="btn btn-xs btn-link text-muted p-0 font-weight-bolder"
                        data-toggle="collapse" data-target="#collapseCardChecklist">
                        Tutup
                    </button>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 bg-white" style="font-size:0.8rem;">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:40px;" class="text-center">No</th>
                                    <th>Bagian / Berkas</th>
                                    <th>Status Admin</th>
                                    <th>Verifikator</th>
                                    <th>Waktu Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($itemIndex = 1)
                                @foreach ($verificationSections as $section)
                                    @foreach ($section['items'] as $item)
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted">{{ $itemIndex++ }}
                                            </td>
                                            <td class="font-weight-bold text-dark">{{ $item->item_label }}</td>
                                            <td>
                                                <span class="badge badge-light-success font-weight-bold"
                                                    style="font-size:0.7rem; padding:3px 6px;">
                                                    <i class="fas fa-check-circle text-success mr-1"></i> Disetujui
                                                </span>
                                            </td>
                                            <td class="text-muted">
                                                {{ optional($item->verifier)->name ?: 'Tim Verifikator' }}</td>
                                            <td class="text-muted">
                                                {{ optional($item->verified_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Bar untuk Manager Pengadaan --}}
        @if ($isVerifiedAdmin)
            <div class="mt-5 p-4 rounded-lg d-flex flex-wrap align-items-center justify-content-between"
                style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                <div class="d-flex align-items-center mb-3 mb-lg-0 pr-lg-4" style="max-width: 650px;">
                    <div class="btn btn-icon btn-light-primary btn-circle mr-3 flex-shrink-0"
                        style="width: 44px; height: 44px;">
                        <i class="fas fa-user-shield text-primary font-size-lg"></i>
                    </div>
                    <div>
                        <div class="font-weight-bolder font-size-sm text-dark mb-1">
                            Aksi Verifikasi Manajer Pengadaan
                        </div>
                        <div class="text-muted font-size-xs">
                            Silakan periksa resume data calon penyedia di bawah. Anda dapat langsung
                            <strong>menyetujui</strong> untuk meneruskan data ke penilaian risiko QA, atau
                            <strong>mengembalikan revisi</strong> ke vendor bila terdapat data yang belum memenuhi
                            syarat.
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    @can('verifikasi-verify-manager')
                        {{-- Tombol Setujui --}}
                        <form id="formManagerApprove" method="POST"
                            action="{{ route('verifikasi.manager.approve', $application) }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="manager_note" id="inputManagerApproveNote" value="">
                            <button type="button" id="btnManagerApprove"
                                class="btn btn-success font-weight-bolder px-5 py-3 shadow-sm d-flex align-items-center"
                                style="border-radius: 8px;">
                                <i class="fas fa-check-circle mr-2"></i> Setujui Permohonan
                            </button>
                        </form>

                        {{-- Tombol Minta Revisi --}}
                        <button type="button"
                            class="btn btn-outline-warning font-weight-bolder px-5 py-3 shadow-sm d-flex align-items-center"
                            data-toggle="modal" data-target="#modalManagerRevision">
                            <i class="fas fa-undo-alt mr-2"></i> Minta Revisi ke Vendor
                        </button>
                    @else
                        <div class="text-muted font-size-xs font-italic bg-white py-2 px-3 rounded border">
                            <i class="fas fa-lock mr-1 text-muted"></i> Hanya akun dengan wewenang Manajer Pengadaan yang
                            dapat melakukan persetujuan tingkat 2.
                        </div>
                    @endcan
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════════
     RESUME DETAIL CALON PENYEDIA (REUSE MODUL TAB-REGISTRATION)
══════════════════════════════════════════════════════════════════════════════ --}}
@include('admin.supplier.partials.show.tab-registration', [
    'app' => $application,
    'general' => $application->general,
])
