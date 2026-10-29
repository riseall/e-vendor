    <div class="modal fade" id="modalRejectItem" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalRejectItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:480px; width:94vw;" role="document">
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
                <div class="modal-body" style="padding:1.25rem 1.5rem; max-height:calc(100vh - 160px); overflow-y:auto;">
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
                    @can('verifikasi-reject')
                    <button type="submit" class="btn-reject"
                        style="background:var(--brand-danger);color:#fff;border-color:var(--brand-danger);">
                        <i class="ki ki-close icon-xs"></i> Tolak Item
                    </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Minta Revisi oleh Manajer Pengadaan --}}
    <div class="modal fade" id="modalManagerRevision" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:540px; width:94vw;" role="document">
            <form method="POST" action="{{ route('verifikasi.manager.revisi', $application) }}" class="modal-content" id="formManagerRevision"
                style="border:none;border-radius:var(--radius-lg);box-shadow:0 20px 60px rgba(15,23,42,.15);overflow:hidden;">
                @csrf
                <div class="modal-header"
                    style="border-bottom:1px solid var(--border-light);background:var(--surface-1);padding:1rem 1.5rem;">
                    <div class="d-flex align-items-center">
                        <span
                            style="width:32px;height:32px;border-radius:99px;background:#f59e0b;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:.75rem;">
                            <i class="fas fa-undo-alt text-white" style="font-size:.8rem;"></i>
                        </span>
                        <div>
                            <h6 class="modal-title font-weight-bolder mb-0"
                                style="color:var(--text-primary);font-size:.92rem;">Kembalikan Revisi ke Vendor</h6>
                            <span class="text-muted font-size-xs">Keputusan Verifikasi Tingkat 2 Manajer Pengadaan</span>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="fas fa-times font-size-sm"></i>
                    </button>
                </div>
                <div class="modal-body" style="padding:1.25rem 1.5rem; max-height:calc(100vh - 160px); overflow-y:auto;">
                    <div class="alert alert-custom alert-light-warning mb-4 py-3 px-4" role="alert">
                        <div class="alert-icon"><i class="fas fa-exclamation-circle text-warning"></i></div>
                        <div class="alert-text font-size-xs">
                            Permohonan akan dikembalikan ke status <strong>Perlu Revisi</strong>. Vendor akan menerima instruksi catatan revisi ini dan dapat memperbarui data profilnya.
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold font-size-sm text-dark mb-2 d-block">
                            Instruksi / Catatan Revisi untuk Vendor <span class="text-danger">*</span>
                        </label>
                        <textarea name="manager_note" rows="4" class="form-control" required
                            placeholder="Tuliskan dengan jelas poin-poin data atau dokumen yang harus diperbaiki oleh vendor…"
                            style="border-color:var(--border-light);border-radius:var(--radius-sm);font-size:.85rem;resize:vertical;"></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold font-size-sm text-dark mb-2 d-block">
                            Bagian Data yang Diminta Revisi (Opsional):
                        </label>
                        <div class="p-3 rounded" style="background:#f8fafc; border:1px solid #e2e8f0; max-height:180px; overflow-y:auto;">
                            @foreach ($verificationSections as $section)
                                @foreach ($section['items'] as $item)
                                    <div class="d-flex align-items-center mb-2">
                                        <label class="checkbox checkbox-primary mb-0 font-size-xs text-dark font-weight-bold">
                                            <input type="checkbox" name="revision_fields[]" value="{{ $item->item_key }}">
                                            <span></span>
                                            {{ $item->item_label }}
                                        </label>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                        <small class="text-muted mt-1 d-block font-size-xs">
                            Jika tidak ada bagian khusus yang dicentang, seluruh permohonan akan ditandai perlu perbaikan sesuai catatan manajer.
                        </small>
                    </div>
                </div>
                <div class="modal-footer"
                    style="border-top:1px solid var(--border-light);background:var(--surface-1);gap:.5rem;">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
                    @can('verifikasi-verify-manager')
                    <button type="submit" class="btn btn-warning font-weight-bolder">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Revisi ke Vendor
                    </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>

