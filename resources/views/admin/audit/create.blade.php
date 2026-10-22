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
                            $qualification = optional($application)->qualification;
                            $risk = $qualification->risk_level ?? (optional($application)->risk_level ?? 'muted');
                            $riskClass =
                                $risk === 'very_high'
                                    ? 'danger'
                                    : ($risk === 'high'
                                        ? 'danger'
                                        : ($risk === 'medium'
                                            ? 'warning'
                                            : ($risk === 'low'
                                                ? 'success'
                                                : 'secondary')));
                        @endphp
                        <span class="label label-light-{{ $riskClass }} label-inline font-weight-bold">
                            {{ strtoupper(str_replace('_', ' ', $risk !== 'muted' ? $risk : '—')) }}
                            @if ($qualification && $qualification->re_audit_period_years)
                                ({{ $qualification->re_audit_period_years }} Thn)
                            @endif
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
            @if ($errors->any())
                <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
                    <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="alert-text">{{ $errors->first() }}</div>
                    <div class="alert-close">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                </div>
            @endif

            @php $auditType = optional(optional($application)->qualification)->audit_type; @endphp

            <form method="POST" action="{{ route('qa.audit.store', $application->id) }}">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <x-vendor-input name="qa_lead" label="QA Lead" placeholder="Nama QA Lead" :value="old('qa_lead', Auth::user()->name ?? '')"
                            labelClass="font-weight-bolder text-dark" />
                    </div>
                    <div class="col-md-6">
                        <x-vendor-input name="summary" label="Catatan Awal (opsional)" placeholder="Catatan internal QA"
                            labelClass="font-weight-bolder text-dark" />
                    </div>
                </div>

                @if ($auditType === 'on_site')
                    <hr>
                    <h6 class="font-weight-bolder text-dark mb-4 mt-5">Detail Audit On Site</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <x-vendor-input name="confirmed_schedule_at" type="text" label="Tanggal Pelaksanaan Audit"
                                :required="true" labelClass="font-weight-bolder text-dark" class="datepicker"
                                placeholder="Pilih Tanggal" autocomplete="off" rightIcon="far fa-calendar-alt" />
                        </div>
                        <div class="col-md-6">
                            <x-vendor-input name="audit_location" label="Lokasi Audit"
                                placeholder="Alamat lengkap lokasi vendor" :required="true"
                                labelClass="font-weight-bolder text-dark" />
                        </div>
                    </div>

                    <x-vendor-input name="audit_agenda" label="Agenda Audit"
                        placeholder="Misal: Tour pabrik, review dokumen mutu, wawancara PIC" :required="true"
                        labelClass="font-weight-bolder text-dark" />

                    <div class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">
                            Tim Auditor <span class="text-danger">*</span>
                        </label>
                        <div class="table-responsive">
                            <table class="table table-bordered table-vertical-center mb-2" id="au-team-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 55%;" class="font-weight-bold">Nama Auditor <span
                                                class="text-danger">*</span></th>
                                        <th style="width: 35%;" class="font-weight-bold">Role / Peran <span
                                                class="text-danger">*</span></th>
                                        <th style="width: 10%;" class="text-center font-weight-bold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="au-team-list">
                                    @php
                                        $oldTeam = old('auditor_team', [['name' => '', 'role' => '']]);
                                    @endphp
                                    @foreach ($oldTeam as $idx => $member)
                                        <tr class="au-repeater-row">
                                            <td>
                                                <input type="text" name="auditor_team[{{ $idx }}][name]"
                                                    class="form-control" placeholder="Nama lengkap auditor"
                                                    value="{{ $member['name'] ?? '' }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="auditor_team[{{ $idx }}][role]"
                                                    class="form-control"
                                                    placeholder="Role (e.g. Lead / Observer / Anggota)"
                                                    value="{{ $member['role'] ?? '' }}" required>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button type="button"
                                                    class="btn btn-icon btn-light-danger btn-sm au-remove-row"
                                                    title="Hapus baris" {{ count($oldTeam) <= 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold" id="au-add-team">
                            <i class="fas fa-plus icon-sm"></i> Tambah Auditor
                        </button>
                    </div>
                @else
                    <hr>
                    <h6 class="font-weight-bolder text-dark mb-4 mt-5">Audit On Desk (Questionnaire)</h6>

                    @if ($questionnaireForms->isEmpty())
                        <div class="alert alert-warning small mb-0">
                            Belum ada master form questionnaire yang aktif.
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
                    @can('audit-create')
                        <button type="submit" class="btn btn-primary font-weight-bold">
                            <i class="fas fa-check icon-sm"></i> Mulai Audit
                        </button>
                    @endcan
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
                var idx = $teamList.find('.au-repeater-row').length;

                function updateRemoveButtons() {
                    var rows = $teamList.find('.au-repeater-row');
                    rows.find('.au-remove-row').prop('disabled', rows.length <= 1);
                }

                function addTeam() {
                    var row = '<tr class="au-repeater-row">' +
                        '<td><input type="text" name="auditor_team[' + idx +
                        '][name]" class="form-control" placeholder="Nama lengkap auditor" required></td>' +
                        '<td><input type="text" name="auditor_team[' + idx +
                        '][role]" class="form-control" placeholder="Role (e.g. Lead / Observer / Anggota)" required></td>' +
                        '<td class="text-center align-middle">' +
                        '<button type="button" class="btn btn-icon btn-light-danger btn-sm au-remove-row" title="Hapus baris"><i class="fas fa-trash-alt"></i></button>' +
                        '</td>' +
                        '</tr>';
                    $teamList.append(row);
                    idx++;
                    updateRemoveButtons();
                }

                $('#au-add-team').on('click', addTeam);

                $teamList.on('click', '.au-remove-row', function() {
                    if ($teamList.find('.au-repeater-row').length > 1) {
                        $(this).closest('.au-repeater-row').remove();
                        updateRemoveButtons();
                    }
                });

                updateRemoveButtons();
            }

            if ($.fn.datepicker) {
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    todayHighlight: true,
                    autoclose: true,
                    orientation: 'bottom left',
                    startDate: '+1d'
                });
            }
        });
    </script>
@endpush
