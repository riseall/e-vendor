<!-- Modal Question Form (Create / Edit) -->
<div class="modal fade" id="modalQuestion" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdrop" aria-hidden="true">
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
                        <div class="col-md-3">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Bobot Nilai <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="weight" id="q_weight"
                                    value="1" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">No Urut <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="order" id="q_order"
                                    value="0" min="0" required>
                            </div>
                        </div>
                    </div>

                    {{-- Language Tabs --}}
                    <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-2x nav-tabs-primary mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bolder py-2 px-4" data-toggle="tab" href="#tab_lang_id">
                                <i class="fas fa-flag text-danger mr-2"></i> Bahasa Indonesia <span class="text-danger">*</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bolder py-2 px-4" data-toggle="tab" href="#tab_lang_en">
                                <i class="fas fa-globe text-primary mr-2"></i> English <span class="badge badge-light-primary font-weight-bold font-size-xs ml-1">Opsional</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- Tab Bahasa Indonesia (Utama) --}}
                        <div class="tab-pane fade show active" id="tab_lang_id" role="tabpanel">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Section / Bagian (ID) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="section" id="q_section" required
                                    placeholder="Contoh: I. Sistem Manajemen Mutu">
                                <small class="form-text text-muted">Mengelompokkan pertanyaan ke dalam bab tertentu.</small>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Pertanyaan (ID) <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="question" id="q_question" rows="3" required
                                    placeholder="Tuliskan pertanyaan kuesioner dalam Bahasa Indonesia..."></textarea>
                            </div>

                            <div class="form-group mb-4" id="optionsGroup_id" style="display: none;">
                                <label class="font-weight-bold text-dark">Pilihan Jawaban (ID) (Satu baris per opsi) <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="options" id="q_options" rows="3"
                                    placeholder="Ketik opsi jawaban&#10;Contoh:&#10;Sangat Baik&#10;Cukup&#10;Kurang Baik"></textarea>
                            </div>
                        </div>

                        {{-- Tab English (Opsional) --}}
                        <div class="tab-pane fade" id="tab_lang_en" role="tabpanel">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Section Name (EN)</label>
                                <input type="text" class="form-control" name="section_en" id="q_section_en"
                                    placeholder="e.g.: I. Quality Management System">
                                <small class="form-text text-muted">Terjemahan bab dalam Bahasa Inggris (opsional).</small>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Question (EN)</label>
                                <textarea class="form-control" name="question_en" id="q_question_en" rows="3"
                                    placeholder="Write question in English..."></textarea>
                            </div>

                            <div class="form-group mb-4" id="optionsGroup_en" style="display: none;">
                                <label class="font-weight-bold text-dark">Answer Options (EN) (One line per option)</label>
                                <textarea class="form-control" name="options_en" id="q_options_en" rows="3"
                                    placeholder="Type answer options in English&#10;e.g.:&#10;Excellent&#10;Good&#10;Poor"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row pt-2 mt-2 border-top">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <span class="switch switch-outline switch-icon switch-danger mr-3">
                                    <label>
                                        <input type="checkbox" name="is_required" id="q_is_required" value="1" checked />
                                        <span></span>
                                    </label>
                                </span>
                                <div>
                                    <span class="font-weight-bold text-dark d-block">Wajib Diisi</span>
                                    <small class="text-muted">Vendor wajib menjawab pertanyaan ini</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <span class="switch switch-outline switch-icon switch-success mr-3">
                                    <label>
                                        <input type="checkbox" name="is_active" id="q_is_active" value="1" checked />
                                        <span></span>
                                    </label>
                                </span>
                                <div>
                                    <span class="font-weight-bold text-dark d-block">Aktifkan Pertanyaan</span>
                                    <small class="text-muted">Tampilkan pada kuesioner vendor</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-6">
                    <button type="button" class="btn btn-light-danger font-weight-bold"
                        data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-6" id="btnSubmit">
                        <i class="flaticon2-check-mark mr-1" style="font-size:.8rem;"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
