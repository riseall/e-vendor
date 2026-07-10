@extends('layouts.app', ['title' => 'Setup Audit'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Setup Audit')
@section('page_title', 'Setup Audit Vendor')
@section('page_desc', 'Konfigurasi awal proses audit berdasarkan hasil risk assessment.')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/admin/audit.css') }}">
@endpush

@section('content')
    <div id="au-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    @php
        $application = $application ?? ($activeAudit->application ?? null);
        $vendorName =
            optional(optional($application)->general)->nama_perusahaan ??
            (optional(optional($application)->user)->name ?? '—');
        $categoryLabels = optional($application)
            ->categories->pluck('category_id')
            ->map(fn($id) => \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id)
            ->filter()
            ->values();
    @endphp

    @if ($activeAudit)
        <div class="alert alert-info">
            <strong>Audit sudah dibuat.</strong> ID Audit: #{{ $activeAudit->id }}.
            <a href="{{ route('qa.audit.show', $activeAudit->id) }}" class="btn btn-sm btn-primary ml-2">Buka Audit</a>
        </div>
    @endif

    <div class="au-card">
        <div class="au-card-head">
            <div>
                <div class="au-card-title">
                    <span class="au-card-title-dot"></span>
                    Informasi Vendor
                </div>
                <div class="mt-1 text-muted" style="font-size:.85rem;">
                    No Permohonan: <strong>{{ optional($application)->application_number ?? '—' }}</strong>
                </div>
            </div>
        </div>

        <div class="card-body px-4 pb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="au-info-label">Vendor</div>
                    <div class="au-info-value">{{ $vendorName }}</div>
                </div>
                <div class="col-md-4">
                    <div class="au-info-label">Risk Level</div>
                    <div class="au-info-value">
                        <span class="au-tag au-tag--{{ optional($application)->risk_level ?? 'muted' }}">
                            {{ strtoupper(optional($application)->risk_level ?? '—') }}
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="au-info-label">RPN Score</div>
                    <div class="au-info-value">{{ optional($application)->risk_rpn ?? '—' }}</div>
                </div>
                <div class="col-md-12 mt-3">
                    <div class="au-info-label">Kategori</div>
                    <div>
                        @forelse ($categoryLabels as $cat)
                            <span class="au-tag au-tag--muted mr-1">{{ $cat }}</span>
                        @empty
                            <span class="text-muted">—</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="au-card mt-4">
        <div class="au-card-head">
            <div class="au-card-title">
                <span class="au-card-title-dot"></span>
                Setup Audit
                @if ($qualification = optional($application)->qualification)
                    <span
                        class="au-tag au-tag--{{ $qualification->audit_type === 'on_desk' ? 'on-desk' : 'on-site' }} ml-2">
                        {{ strtoupper(str_replace('_', '-', $qualification->audit_type)) }}
                    </span>
                @endif
            </div>
        </div>

        <div class="card-body px-4 pb-5">
            @php $auditType = optional(optional($application)->qualification)->audit_type; @endphp

            <form method="POST" action="{{ route('qa.audit.store', $application->id) }}" class="au-form">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="au-form-label">QA Lead</label>
                            <select name="qa_lead_id" class="form-control">
                                <option value="">— Pilih QA Lead —</option>
                                @foreach ($qaUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="au-form-label">Catatan Awal (opsional)</label>
                            <input type="text" name="summary" maxlength="1000" class="form-control"
                                placeholder="Catatan internal QA">
                        </div>
                    </div>
                </div>

                @if ($auditType === 'on_site')
                    <hr>
                    <h6 class="au-section-title">Detail Audit On Site</h6>

                    <div class="form-group">
                        <label class="au-form-label">Lokasi Audit</label>
                        <input type="text" name="audit_location" maxlength="255"
                            class="form-control @error('audit_location') is-invalid @enderror"
                            placeholder="Alamat lengkap lokasi vendor" required>
                        @error('audit_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="au-form-label">Agenda Audit</label>
                        <input type="text" name="audit_agenda" maxlength="500"
                            class="form-control @error('audit_agenda') is-invalid @enderror"
                            placeholder="Misal: Tour pabrik, review dokumen mutu, wawancara PIC" required>
                        @error('audit_agenda')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="au-form-label">Tim Auditor</label>
                        <div id="au-team-list"></div>
                        <button type="button" class="btn btn-sm btn-light-primary mt-2" id="au-add-team">
                            <i class="flaticon2-add-1"></i> Tambah Auditor
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="au-form-label">Proposed Schedule (Beberapa Pilihan Tanggal)</label>
                        <div id="au-schedule-list"></div>
                        <button type="button" class="btn btn-sm btn-light-primary mt-2" id="au-add-schedule">
                            <i class="flaticon2-add-1"></i> Tambah Tanggal
                        </button>
                        @error('proposed_schedules')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <hr>
                    <h6 class="au-section-title">Audit On Desk (Questionnaire)</h6>
                    <p class="text-muted small">
                        Sistem akan otomatis mengirim questionnaire dinamis ke vendor berdasarkan kategori yang dipilih
                        vendor
                        ({{ $templates->count() }} template tersedia). Vendor dapat menyimpan draft dan submit setelah
                        lengkap.
                    </p>
                @endif

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('qa.audit.index') }}" class="btn btn-light mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="flaticon2-check-mark"></i> Mulai Audit
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // Team repeater
            var $teamList = $('#au-team-list');
            if ($teamList.length) {
                var idx = 0;

                function addTeam() {
                    var row = '<div class="au-repeater-row">' +
                        '<input type="text" name="auditor_team[' + idx +
                        '][name]" class="form-control form-control-sm" placeholder="Nama auditor" required>' +
                        '<input type="text" name="auditor_team[' + idx +
                        '][role]" class="form-control form-control-sm" placeholder="Role (Lead / Observer / dll)" required>' +
                        '<button type="button" class="btn btn-sm btn-light-danger au-remove-row"><i class="flaticon-delete"></i></button>' +
                        '</div>';
                    $teamList.append(row);
                    idx++;
                }
                $('#au-add-team').on('click', addTeam);
                $teamList.on('click', '.au-remove-row', function() {
                    $(this).closest('.au-repeater-row').remove();
                });
                addTeam();

                // Schedule repeater
                var sIdx = 0;

                function addSchedule() {
                    var row = '<div class="au-repeater-row">' +
                        '<input type="datetime-local" name="proposed_schedules[' + sIdx +
                        ']" class="form-control form-control-sm" required>' +
                        '<button type="button" class="btn btn-sm btn-light-danger au-remove-row"><i class="flaticon-delete"></i></button>' +
                        '</div>';
                    $('#au-schedule-list').append(row);
                    sIdx++;
                }
                $('#au-add-schedule').on('click', addSchedule);
                $('#au-schedule-list').on('click', '.au-remove-row', function() {
                    $(this).closest('.au-repeater-row').remove();
                });
                addSchedule();
            }
        });
    </script>
@endpush
