{{-- Modal Import Nilai QA via Excel --}}
<div class="modal fade" id="modalImportQaExcel" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="modalImportQaExcelTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom py-4 px-6">
                <h5 class="modal-title font-weight-bolder text-dark" id="modalImportQaExcelTitle">
                    <i class="fas fa-file-excel text-success mr-2"></i>Upload Nilai Manual
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.evaluasi.import-qa') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-6">
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="font-weight-bold text-dark mb-1 font-size-sm">Tahun Evaluasi</label>
                            <select name="year" class="form-control selectpicker font-weight-bold" required>
                                @for ($y = date('Y'); $y >= 2024; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="font-weight-bold text-dark mb-1 font-size-sm">Bulan Evaluasi</label>
                            <select name="month" class="form-control selectpicker font-weight-bold" required>
                                @foreach ($months as $num => $name)
                                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>
                                        {{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark mb-1 font-size-sm">Pilih File Excel (.xlsx /
                            .csv)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="excelFileQa" name="file"
                                accept=".xlsx,.xls,.csv" required>
                            <label class="custom-file-label font-size-sm" for="excelFileQa">Pilih file excel...</label>
                        </div>
                        <span class="form-text text-muted font-size-xs mt-1">Format yang didukung: Microsoft Excel
                            (.xlsx, .xls) atau CSV.</span>
                    </div>

                    <div
                        class="p-3 rounded bg-light-primary border border-primary-light d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="font-weight-bolder text-primary font-size-sm mb-0">Belum punya template?</div>
                            <div class="text-muted font-size-xs">Unduh format template Excel untuk pengisian nilai evaluasi.</div>
                        </div>
                        <a href="{{ asset('storage/templates/template_evaluasi.xlsx') }}" download="template_evaluasi.xlsx"
                            class="btn btn-sm btn-primary font-weight-bolder px-3 text-nowrap ml-2">
                            <i class="fas fa-download mr-1"></i>Unduh Template
                        </a>
                    </div>

                    <div class="alert alert-custom alert-light-info p-3 mb-0" role="alert">
                        <div class="alert-icon"><i class="fas fa-info-circle text-info"></i></div>
                        <div class="alert-text font-size-xs">
                            <strong>Pencocokan & Nilai yang Diperbarui:</strong><br>
                            &bull; Pencocokan wajib via <code>supplier_code</code> (Kode Supplier QAD)<br>
                            &bull; <code>Complain Score</code><br>
                            &bull; <code>Incoming Material Score</code><br>
                            &bull; <code>Safety & Environment Score</code>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-3 px-6 d-flex justify-content-between">
                    <button type="button" class="btn btn-light-danger font-weight-bold btn-sm px-4"
                        data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success font-weight-bolder btn-sm px-6">
                        <i class="fas fa-upload mr-1"></i>Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Update custom file input label on selection
            $(document).on('change', '#excelFileQa', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName ||
                    'Pilih file excel...');
            });
        });
    </script>
@endpush
