<!-- Modal Form Questionnaire (Create / Edit) -->
<div class="modal fade" id="modalFormQuestionnaire" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius:12px;">
            <div class="modal-header border-bottom py-4 px-6">
                <h5 class="modal-title font-weight-bolder text-dark" id="modalTitle">Tambah Form Kuesioner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="questionnaireForm" method="POST">
                @csrf
                <div id="methodPlaceholder"></div>
                <div class="modal-body p-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Kode Form <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="code" id="form_code" required
                                    placeholder="Contoh: bahan_baku_pemasok">
                                <small class="text-muted">Identifier unik untuk template form.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Tipe Material <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" name="material_type" id="form_material_type" required>
                                    <option value="bahan_baku">Bahan Baku</option>
                                    <option value="bahan_kemas">Bahan Kemas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark">Nama Form <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="form_name" required
                            placeholder="Contoh: Daftar Periksa Pemasok Bahan Baku">
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark">Nomor Dokumen</label>
                        <input type="text" class="form-control" name="document_number" id="form_document_number"
                            placeholder="Contoh: QA-FM-I2.02-017">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Urutan Tampil <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="order" id="form_order" value="0"
                                    min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-group mb-0 pt-3">
                                <label class="checkbox checkbox-lg checkbox-outline checkbox-success font-weight-bold">
                                    <input type="checkbox" name="is_active" id="form_is_active" value="1"
                                        checked />
                                    <span></span>&nbsp; Aktifkan Form
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-6">
                    <button type="submit" class="btn btn-primary font-weight-bold px-6" id="btnSubmit">
                        <i class="flaticon2-check-mark mr-1" style="font-size:.8rem;"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
