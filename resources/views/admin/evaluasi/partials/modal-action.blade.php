{{-- Modal Dinamis Tindakan Peringatan Vendor (Single Reusable Modal) --}}
<div class="modal fade text-left" id="modalActionAlert" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius:12px;">
            <div class="modal-header border-bottom py-4 px-6">
                <h5 class="modal-title font-weight-bolder text-dark" id="modalActionAlertLabel">
                    <i class="fas fa-exclamation-triangle text-danger mr-2"></i>Tindakan Peringatan Vendor
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="formTriggerAction" action="" method="POST">
                @csrf
                <div class="modal-body p-6">
                    <div class="alert alert-custom alert-light-danger fade show mb-4 p-4" role="alert">
                        <div class="alert-icon"><i class="fas fa-exclamation-circle text-danger"></i></div>
                        <div class="alert-text font-size-sm">
                            Pilih tindakan khusus untuk vendor <strong id="modalVendorNameTarget">-</strong> 
                            (Skor Tahunan: <strong id="modalVendorScoreTarget">-</strong>).
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bolder text-dark">
                            Jenis Aksi Management: <span class="text-danger">*</span>
                        </label>
                        <select name="action_type" class="form-control selectpicker" required>
                            <option value="rekualifikasi">1. Trigger Proses Rekualifikasi (Form Rekualifikasi Baru)</option>
                            <option value="terminated">2. Terminated (Berhentikan Kerjasama)</option>
                            <option value="suspended">3. Suspended (Bekukan Sementara)</option>
                            <option value="qualified_with_notes">4. Qualified Dengan Catatan (Pemantauan Ketat)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-6">
                    <button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger font-weight-bolder px-6">
                        <i class="fas fa-bolt mr-1"></i> Eksekusi Tindakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
