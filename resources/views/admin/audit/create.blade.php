@extends('layouts.app', ['title' => 'Setup Audit'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Setup Audit')
@section('page_title', 'Setup Audit Vendor')
@section('page_desc', 'Konfigurasi awal proses audit berdasarkan hasil risk assessment.')

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

    <div class="card card-custom mb-5">
        <div class="card-header border-0 pt-5">
            <div class="card-title align-items-start flex-column">
                <h3 class="card-label font-weight-bolder text-dark">
                    Informasi Vendor
                </h3>
                <div class="text-muted mt-2 font-weight-bold font-size-sm">
                    No Permohonan: <strong>{{ optional($application)->application_number ?? '—' }}</strong>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="text-muted font-weight-bold mb-1">Vendor</div>
                    <div class="font-weight-bolder text-dark font-size-h6">{{ $vendorName }}</div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-muted font-weight-bold mb-1">Risk Level</div>
                    <div class="font-weight-bolder text-dark font-size-h6">
                        @php
                            $risk = optional($application)->risk_level ?? 'muted';
                            $riskClass =
                                $risk === 'high'
                                    ? 'danger'
                                    : ($risk === 'medium'
                                        ? 'warning'
                                        : ($risk === 'low'
                                            ? 'success'
                                            : 'secondary'));
                        @endphp
                        <span class="label label-light-{{ $riskClass }} label-inline font-weight-bold">
                            {{ strtoupper($risk !== 'muted' ? $risk : '—') }}
                        </span>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-muted font-weight-bold mb-1">Total Score</div>
                    <div class="font-weight-bolder text-dark font-size-h6">{{ optional($application)->total_score ?? '—' }}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="text-muted font-weight-bold mb-1">Kategori</div>
                    <div>
                        @forelse ($categoryLabels as $cat)
                            <span
                                class="label label-light-primary label-inline font-weight-bold mr-1">{{ $cat }}</span>
                        @empty
                            <span class="text-muted">—</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom mb-5">
        <div class="card-header border-0 pt-5">
            <div class="card-title align-items-center">
                <h3 class="card-label font-weight-bolder text-dark mb-0">
                    Setup Audit
                </h3>
                @if ($qualification = optional($application)->qualification)
                    <span
                        class="label label-light-{{ $qualification->audit_type === 'on_desk' ? 'info' : 'primary' }} label-inline font-weight-bold ml-3">
                        {{ strtoupper(str_replace('_', '-', $qualification->audit_type)) }}
                    </span>
                @endif
            </div>
        </div>

        <div class="card-body">
            @php $auditType = optional(optional($application)->qualification)->audit_type; @endphp

            <form method="POST" action="{{ route('qa.audit.store', $application->id) }}">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <x-vendor-select name="qa_lead_id" label="QA Lead" :options="$qaUsers->pluck('name', 'id')->toArray()"
                            placeholder="— Pilih QA Lead —" />
                    </div>
                    <div class="col-md-6">
                        <x-vendor-input name="summary" label="Catatan Awal (opsional)" placeholder="Catatan internal QA"
                            labelClass="font-weight-bolder text-dark" />
                    </div>
                </div>

                @if ($auditType === 'on_site')
                    <hr>
                    <h6 class="font-weight-bolder text-dark mb-4 mt-5">Detail Audit On Site</h6>

                    <x-vendor-input name="audit_location" label="Lokasi Audit" placeholder="Alamat lengkap lokasi vendor"
                        :required="true" labelClass="font-weight-bolder text-dark" />

                    <x-vendor-input name="audit_agenda" label="Agenda Audit"
                        placeholder="Misal: Tour pabrik, review dokumen mutu, wawancara PIC" :required="true"
                        labelClass="font-weight-bolder text-dark" />

                    <div class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">Tim Auditor</label>
                        <div id="au-team-list"></div>
                        <button type="button" class="btn btn-sm btn-light-primary mt-2" id="au-add-team">
                            <i class="flaticon2-add-1"></i> Tambah Auditor
                        </button>
                    </div>

                    <x-vendor-input name="confirmed_schedule_at" type="text" label="Tanggal Pelaksanaan Audit"
                        :required="true" labelClass="font-weight-bolder text-dark" class="datepicker"
                        placeholder="Pilih Tanggal" autocomplete="off" rightIcon="far fa-calendar-alt" />
                @else
                    <hr>
                    <h6 class="font-weight-bolder text-dark mb-4 mt-5">Audit On Desk (Questionnaire)</h6>

                    @if ($questionnaireForms->isEmpty())
                        <div class="alert alert-warning small mb-0">
                            Belum ada master form questionnaire yang aktif.
                            Jalankan <code>php artisan db:seed --class=VendorAuditQuestionnaireKemasPrimerSeeder</code>
                            (atau seed form lain) untuk menambahkan.
                        </div>
                    @else
                        @php
                            $qFormOptions = [];
                            foreach ($questionnaireForms as $form) {
                                $qFormOptions[$form->id] = "[{$form->materialTypeLabel()}] {$form->name}";
                            }
                        @endphp
                        <x-vendor-select name="questionnaire_form_id" label="Pilih Checklist / Form Questionnaire"
                            :options="$qFormOptions" placeholder="— Pilih Form Checklist —" :required="true" />
                        <small class="form-text text-muted mt-2">
                            Vendor akan menerima questionnaire sesuai form yang dipilih (beserta pertanyaan
                            kondisional-nya).
                        </small>
                    @endif
                @endif

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('qa.risk-assessment.index') }}" class="btn btn-light mr-2">Batal</a>
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
                    var row = '<div class="d-flex mb-2 au-repeater-row" style="gap: 0.5rem;">' +
                        '<input type="text" name="auditor_team[' + idx +
                        '][name]" class="form-control" placeholder="Nama auditor" required>' +
                        '<input type="text" name="auditor_team[' + idx +
                        '][role]" class="form-control" placeholder="Role (Lead / Observer / dll)" required>' +
                        '<button type="button" class="btn btn-icon btn-light-danger au-remove-row"><i class="fas fa-trash-alt"></i></button>' +
                        '</div>';
                    $teamList.append(row);
                    idx++;
                }
                $('#au-add-team').on('click', addTeam);
                $teamList.on('click', '.au-remove-row', function() {
                    $(this).closest('.au-repeater-row').remove();
                });
                addTeam();
            }

            if ($.fn.datepicker) {
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    todayHighlight: true,
                    autoclose: true,
                    orientation: 'top left'
                });
            }
        });
    </script>
@endpush
