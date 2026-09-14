{{-- Modal Detail Rekualifikasi Vendor --}}
<div class="modal fade text-left" id="modalDetailRekualifikasi" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius:14px; overflow:hidden;">
            {{-- Modal Header --}}
            <div class="modal-header border-bottom py-4 px-6 bg-light">
                <div class="d-flex align-items-center">
                    <div class="btn btn-icon btn-light-primary btn-circle mr-3 flex-shrink-0"
                        style="width:40px; height:40px;">
                        <i class="fas fa-redo text-primary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bolder text-dark mb-0">Detail Rekualifikasi Vendor</h5>
                        <span class="text-muted font-size-xs">Informasi lengkap permohonan evaluasi ulang vendor</span>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times font-size-sm text-muted" aria-hidden="true"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-6">
                {{-- Banner Ringkasan Status --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap p-4 mb-5 border rounded"
                    style="background: #f8fafc; border-color: #e2e8f0 !important;">
                    <div>
                        <span
                            class="text-muted font-size-xs font-weight-bold text-uppercase letter-spacing-05 d-block mb-1">
                            Nomor Permohonan
                        </span>
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <span class="font-weight-bolder text-dark font-size-h5" id="dtl_appnum">-</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap mt-2 mt-sm-0" style="gap: 8px;">
                        <span id="dtl_reason_badge" class="vnd-tag vnd-tag--muted">-</span>
                        <span id="dtl_status_badge" class="vnd-status vnd-status--info">
                            <i id="dtl_status_icon" class="fas fa-info-circle" style="font-size: .65rem;"></i>
                            <span id="dtl_status_text">-</span>
                        </span>
                    </div>
                </div>

                {{-- Two Columns Grid: Data Vendor & Data Rekualifikasi --}}
                <div class="row">
                    {{-- Column 1: Profil Perusahaan / Vendor --}}
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="card card-custom card-stretch border"
                            style="border-radius:10px; border-color: #edf2f7 !important;">
                            <div class="card-header border-bottom py-3 px-4 min-h-40px bg-light-secondary">
                                <div class="card-title my-0">
                                    <h6 class="font-weight-bolder text-dark font-size-sm mb-0">
                                        <i class="fas fa-building text-primary mr-2" style="font-size: 0.8rem;"></i>
                                        Data Vendor
                                    </h6>
                                </div>
                            </div>
                            <div class="card-body p-4 font-size-sm">
                                <div class="mb-3">
                                    <span class="text-muted font-size-xs d-block">Nama Perusahaan:</span>
                                    <strong class="text-dark font-size-sm" id="dtl_vendor_name">-</strong>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted font-size-xs d-block">Email Perusahaan:</span>
                                    <span class="text-dark" id="dtl_vendor_email">-</span>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted font-size-xs d-block">Kontak Person / PIC:</span>
                                    <span class="text-dark font-weight-bold" id="dtl_pic_name">-</span>
                                </div>
                                <div>
                                    <span class="text-muted font-size-xs d-block">Nomor Telepon:</span>
                                    <span class="text-dark" id="dtl_phone">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Column 2: Data Rekualifikasi --}}
                    <div class="col-md-6">
                        <div class="card card-custom card-stretch border"
                            style="border-radius:10px; border-color: #edf2f7 !important;">
                            <div class="card-header border-bottom py-3 px-4 min-h-40px bg-light-secondary">
                                <div class="card-title my-0">
                                    <h6 class="font-weight-bolder text-dark font-size-sm mb-0">
                                        <i class="fas fa-redo text-warning mr-2" style="font-size: 0.8rem;"></i> Info
                                        Rekualifikasi
                                    </h6>
                                </div>
                            </div>
                            <div class="card-body p-4 font-size-sm">
                                <div class="mb-3">
                                    <span class="text-muted font-size-xs d-block">Pemicu / Alasan:</span>
                                    <strong class="text-dark font-size-sm" id="dtl_reason_label">-</strong>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted font-size-xs d-block">Tanggal Diajukan / Dibuat:</span>
                                    <span class="text-dark" id="dtl_created_at">-</span>
                                </div>
                                <div>
                                    <span class="text-muted font-size-xs d-block">Pembaruan Terakhir:</span>
                                    <span class="text-dark" id="dtl_updated_at">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-top py-3 px-6 bg-light d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-light-dark font-weight-bold" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <a id="dtl_btn_tracking" href="#" class="btn btn-light-primary font-weight-bold">
                        <i class="fas fa-route mr-1"></i> Timeline Tracking
                    </a>
                    @canany(['verifikasi-list', 'verifikasi-detail'])
                        <a id="dtl_btn_verify" href="#" class="btn btn-primary font-weight-bold">
                            <i class="fas fa-clipboard-check mr-1"></i> Verifikasi Berkas
                        </a>
                    @endcanany
                </div>
            </div>
        </div>
    </div>
</div>
