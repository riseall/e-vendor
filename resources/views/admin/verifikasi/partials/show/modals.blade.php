    <div class="modal fade" id="modalPreviewDoc" tabindex="-1" role="dialog" aria-labelledby="modalPreviewDocLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content"
                style="border:none;border-radius:var(--radius-lg);box-shadow:0 20px 60px rgba(15,23,42,.15);overflow:hidden;">
                <div class="modal-header"
                    style="border-bottom:1px solid var(--border-light);background:var(--surface-1);padding:1rem 1.5rem;">
                    <h5 class="modal-title font-weight-bolder" id="modalPreviewDocLabel"
                        style="color:var(--text-primary);font-size:.95rem;">Preview Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body p-0" style="height:80vh;background:#f8fafc;">
                    <div id="previewContainer" class="h-100 d-flex align-items-center justify-content-center">
                        <div class="spinner spinner-primary spinner-lg"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border-light);background:var(--surface-1);">
                    <a href="#" target="_blank" class="btn-approve" id="btnDownloadDoc"
                        style="text-decoration:none;">
                        <i class="flaticon-download icon-xs"></i> Download
                    </a>
                    <button type="button" class="btn-undo" data-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalRejectItem" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:460px;" role="document">
            <form method="POST" action="#" class="modal-content" id="formRejectItem"
                style="border:none;border-radius:var(--radius-lg);box-shadow:0 20px 60px rgba(15,23,42,.15);overflow:hidden;">
                @csrf
                <div class="modal-header"
                    style="border-bottom:1px solid var(--border-light);background:var(--surface-1);padding:1rem 1.5rem;">
                    <div class="d-flex align-items-center">
                        <span
                            style="width:30px;height:30px;border-radius:99px;background:var(--brand-danger);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:.75rem;">
                            <i class="ki ki-close text-white" style="font-size:.6rem;"></i>
                        </span>
                        <h6 class="modal-title font-weight-bolder mb-0"
                            style="color:var(--text-primary);font-size:.9rem;">Tolak Item Verifikasi</h6>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body" style="padding:1.5rem;">
                    <div
                        style="background:var(--brand-danger-light);border:1px solid #fca5a5;border-radius:var(--radius-md);padding:1rem 1.25rem;margin-bottom:1.25rem;">
                        <div
                            style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);margin-bottom:.35rem;">
                            Item yang Ditolak</div>
                        <div style="font-weight:700;color:var(--brand-danger);font-size:.87rem;" id="rejectItemLabel">-
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label
                            style="font-weight:700;font-size:.83rem;color:var(--text-primary);margin-bottom:.5rem;display:block;">
                            Field/Dokumen yang Harus Direvisi <span style="color:var(--brand-danger);">*</span>
                        </label>
                        <div id="rejectFieldList" class="reject-field-list">
                        </div>

                        <label
                            style="font-weight:700;font-size:.83rem;color:var(--text-primary);margin-bottom:.5rem;display:block;">
                            Alasan Penolakan <span style="color:var(--brand-danger);">*</span>
                        </label>
                        <textarea name="note" rows="4" class="form-control" required
                            placeholder="Tuliskan alasan data tidak disetujui…"
                            style="border-color:var(--border-light);border-radius:var(--radius-sm);font-size:.83rem;resize:vertical;"></textarea>
                        <div style="color:var(--text-muted);font-size:.72rem;margin-top:.5rem;">
                            Alasan ini akan ditampilkan ke vendor sebagai catatan revisi.
                        </div>
                    </div>
                </div>
                <div class="modal-footer"
                    style="border-top:1px solid var(--border-light);background:var(--surface-1);gap:.5rem;">
                    <button type="button" class="btn-undo" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-reject"
                        style="background:var(--brand-danger);color:#fff;border-color:var(--brand-danger);">
                        <i class="ki ki-close icon-xs"></i> Tolak Item
                    </button>
                </div>
            </form>
        </div>
    </div>

