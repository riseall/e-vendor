<!-- Modal Question Form (Create / Edit) -->
<div class="modal fade" id="modalQuestion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius:12px;">
            <div class="modal-header border-bottom py-4 px-6">
                <h5 class="modal-title font-weight-bolder text-dark" id="modalTitle">Tambah Pertanyaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="questionForm" method="POST">
                @csrf
                <div id="methodPlaceholder"></div>
                <div class="modal-body p-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Section / Bagian Kuesioner <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="section" id="q_section" required
                                    placeholder="Contoh: I. Sistem Manajemen Mutu">
                                <small class="form-text text-muted">Mengelompokkan pertanyaan ke dalam bab tertentu.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Tipe Jawaban <span class="text-danger">*</span></label>
                                <select class="form-control" name="answer_type" id="q_answer_type" required
                                    onchange="toggleOptionsField()">
                                    <option value="yes_no">Yes / No</option>
                                    <option value="multiple_choice">Pilihan Ganda (Multiple Choice)</option>
                                    <option value="text">Teks Bebas</option>
                                    <option value="document">Unggah Dokumen Bukti</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="question" id="q_question" rows="4" required
                            placeholder="Tuliskan pertanyaan kuesioner di sini..."></textarea>
                    </div>

                    <div class="form-group mb-4" id="optionsGroup" style="display: none;">
                        <label class="font-weight-bold text-dark">Pilihan Jawaban (Satu baris per opsi) <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="options" id="q_options" rows="4"
                            placeholder="Ketik opsi jawaban&#10;Contoh:&#10;Sangat Baik&#10;Cukup&#10;Kurang Baik"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Bobot Nilai <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="weight" id="q_weight"
                                    value="1" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">No Urut Tampil <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="order" id="q_order"
                                    value="0" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="checkbox checkbox-lg checkbox-outline checkbox-danger font-weight-bold">
                                    <input type="checkbox" name="is_required" id="q_is_required" value="1" checked />
                                    <span></span>&nbsp; Wajib Diisi (Required)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="checkbox checkbox-lg checkbox-outline checkbox-success font-weight-bold">
                                    <input type="checkbox" name="is_active" id="q_is_active" value="1" checked />
                                    <span></span>&nbsp; Aktifkan Pertanyaan
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-6">
                    <button type="button" class="btn btn-light-secondary font-weight-bold"
                        data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-6" id="btnSubmit">
                        <i class="flaticon2-check-mark mr-1" style="font-size:.8rem;"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
