<!-- Modal Import Excel Pertanyaan -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius:12px;">
            <div class="modal-header border-bottom py-4 px-6">
                <h5 class="modal-title font-weight-bolder text-dark">Import Pertanyaan Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form action="{{ route('questionnaire-form.questions.import', $form->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-6">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark mb-2">Upload File Excel <span class="text-danger">*</span></label>
                        <div class="custom-file custom-file-sm">
                            <input type="file" name="file" class="custom-file-input" id="import_excel_file" accept=".xlsx, .xls, .csv" required>
                            <label class="custom-file-label text-truncated" for="import_excel_file">Pilih file excel...</label>
                        </div>
                        <small class="form-text text-muted mt-2">Format file harus berupa `.xlsx`, `.xls`, atau `.csv` sesuai template.</small>
                    </div>
                    <div class="mt-4 pt-2 border-top">
                        <a href="{{ Storage::url('templates/template_questions.xlsx') }}"
                            class="btn btn-sm btn-light-info font-weight-bold">
                            <i class="flaticon2-download mr-1" style="font-size:.8rem;"></i> Download Template Excel
                        </a>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-6">
                    <button type="button" class="btn btn-light-secondary font-weight-bold"
                        data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-6">
                        <i class="flaticon2-file mr-1" style="font-size:.8rem;"></i> Import Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
