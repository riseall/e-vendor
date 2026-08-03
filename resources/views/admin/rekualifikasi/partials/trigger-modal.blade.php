<!-- Modal Trigger Rekualifikasi Admin -->
@if (auth()->user()->role !== 'supplier')
    <div class="modal fade" id="modalTriggerRekualifikasiAdmin" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="modalTriggerTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius:12px;">
                <form id="formTriggerRekualifikasiModal" action="" method="POST">
                    @csrf
                    <div class="modal-header border-bottom py-4 px-6">
                        <h5 class="modal-title font-weight-bolder text-dark" id="modalTriggerTitle">
                            <i class="flaticon2-reload text-warning mr-2" style="font-size:1rem;"></i> Picu Rekualifikasi Vendor
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body p-6">
                        <div class="form-group mb-4">
                            <label class="font-weight-bolder text-dark">Pilih Vendor (Berstatus Approved): <span
                                    class="text-danger">*</span></label>
                            <select name="vendor_app_id" id="select_vendor_trigger"
                                class="form-control form-control-solid selectpicker" data-live-search="true" required>
                                <option value="">-- Pilih Vendor --</option>
                                @foreach ($approvedVendors as $v)
                                    @php
                                        $name = optional($v->general)->nama_perusahaan ?: (optional($v->user)->name ?: 'Vendor');
                                    @endphp
                                    <option value="{{ $v->id }}">
                                        {{ $name }} ({{ $v->application_number ?: 'ID: ' . $v->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label class="font-weight-bolder text-dark">Alasan / Pemicu Rekualifikasi: <span
                                    class="text-danger">*</span></label>
                            <select name="reason" class="form-control form-control-solid" required>
                                <option value="qa_trigger">Permintaan Tim Pengadaan / QA (QA Trigger)</option>
                                <option value="expired_period">Masa Berlaku Kadaluarsa (&le; 60 Hari)</option>
                                <option value="cdob_expiry">Masa Berlaku Sertifikat CDOB Kadaluarsa</option>
                                <option value="eval_score_drop">Penurunan Skor Evaluasi Kinerja</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-3 px-6">
                        <button type="button" class="btn btn-light-secondary font-weight-bold"
                            data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning font-weight-bold px-6">
                            <i class="flaticon2-reload mr-1" style="font-size:.8rem;"></i> Picu Rekualifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
