@extends('layouts.app', ['title' => 'Detail Audit'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Detail Audit')
@section('page_title', 'Detail Audit Vendor')
@section('page_desc', 'Pantau progres audit, verifikasi hasil, dan ambil keputusan akhir.')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/admin/audit.css') }}">
@endpush

@php
    use App\Models\VendorAudit;
    use App\Models\VendorAuditFinding;
    use App\Models\VendorAuditCapa;

    $vendorName =
        optional(optional($application)->general)->nama_perusahaan ??
        (optional(optional($application)->user)->name ?? '—');

    $isOnDesk = $audit->isOnDesk();
@endphp

@section('content')
    <div id="au-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- Header audit --}}
    <div class="au-card">
        <div class="au-card-head">
            <div>
                <div class="au-card-title">
                    <span class="au-card-title-dot"></span>
                    Audit #{{ $audit->id }}
                    <span class="au-tag au-tag--{{ $audit->audit_type === 'on_desk' ? 'on-desk' : 'on-site' }} ml-2">
                        {{ strtoupper(str_replace('_', '-', $audit->audit_type)) }}
                    </span>
                </div>
                <div class="text-muted small mt-1">
                    No Permohonan: <strong>{{ $application->application_number }}</strong> ·
                    Vendor: <strong>{{ $vendorName }}</strong>
                </div>
            </div>
            <div class="d-flex align-items-center" style="gap:.5rem;">
                <span
                    class="au-status au-status--{{ $audit->status }}">{{ strtoupper(str_replace('_', ' ', $audit->status)) }}</span>
                @if ($audit->audit_letter_path)
                    <a href="{{ route('qa.audit.letter.download', $audit->id) }}" class="btn btn-sm btn-light-info">
                        <i class="flaticon2-download"></i> Surat Audit
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body px-4 pb-4">
            <div class="au-progress au-progress--large">
                <div class="au-progress-bar" style="width:{{ $progress }}%"></div>
                <span class="au-progress-text">{{ $progress }}% selesai</span>
            </div>

            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="au-info-label">QA Lead</div>
                    <div class="au-info-value">{{ optional($audit->qaLead)->name ?? '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="au-info-label">Dibuat</div>
                    <div class="au-info-value">{{ optional($audit->created_at)->format('d M Y H:i') ?? '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="au-info-label">Selesai</div>
                    <div class="au-info-value">{{ optional($audit->completed_at)->format('d M Y H:i') ?? '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="au-info-label">Risk Level</div>
                    <div class="au-info-value">
                        <span class="au-tag au-tag--{{ optional($application)->risk_level ?? 'muted' }}">
                            {{ strtoupper(optional($application)->risk_level ?? '—') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ ON DESK: Questionnaire verification ══════════ --}}
    @if ($isOnDesk)
        <div class="au-card mt-4">
            <div class="au-card-head">
                <div class="au-card-title">
                    <span class="au-card-title-dot"></span>
                    Verifikasi Questionnaire
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                @if (in_array($audit->status, [VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED, VendorAudit::STATUS_CAPA_REVISED], true))
                    <h6 class="au-section-title">Jawaban Vendor</h6>
                    @php $payload = $audit->questionnaire_payload ?? []; @endphp
                    @if (empty($payload))
                        <p class="text-muted">Vendor belum mengisi questionnaire.</p>
                    @else
                        <div class="au-qa-list">
                            @foreach ($payload as $qid => $answer)
                                <div class="au-qa-item">
                                    <div class="au-qa-q">QID #{{ $qid }}</div>
                                    <div class="au-qa-a">{{ is_array($answer) ? json_encode($answer) : $answer }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <hr>
                    <form method="POST" action="{{ route('qa.audit.questionnaire.verify', $audit->id) }}" class="au-form">
                        @csrf
                        <h6 class="au-section-title">Keputusan QA</h6>
                        <div class="form-group">
                            <label class="au-form-label">Verdict</label>
                            <select name="verdict" class="form-control" required>
                                <option value="approved">Approve (vendor → approved)</option>
                                <option value="need_revision">Need Revision (kembalikan ke vendor)</option>
                                <option value="rejected">Reject (vendor → rejected)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="au-form-label">Catatan Tambahan</label>
                            <textarea name="summary" class="form-control" rows="2" maxlength="1000"></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
                        </div>
                    </form>
                @elseif ($audit->status === VendorAudit::STATUS_NEED_REVISION)
                    <div class="alert alert-warning">
                        Questionnaire dikembalikan ke vendor untuk revisi.
                        @if ($audit->questionnaire_revision_notes)
                            <ul class="mt-2 mb-0">
                                @foreach ($audit->questionnaire_revision_notes as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @elseif ($audit->status === VendorAudit::STATUS_COMPLETED)
                    <div class="alert alert-success">
                        Questionnaire disetujui. Status vendor: <strong>APPROVED</strong>.
                    </div>
                @elseif ($audit->status === VendorAudit::STATUS_REJECTED)
                    <div class="alert alert-danger">Vendor ditolak.</div>
                @else
                    <p class="text-muted">Vendor sedang mengisi questionnaire. Halaman ini akan menampilkan jawaban setelah
                        vendor submit.</p>
                @endif
            </div>
        </div>
    @endif

    {{-- ══════════ ON SITE: Schedule + Findings + CAPA ══════════ --}}
    @if (!$isOnDesk)
        {{-- Schedule --}}
        <div class="au-card mt-4">
            <div class="au-card-head">
                <div class="au-card-title">
                    <span class="au-card-title-dot"></span>
                    Jadwal Audit
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                @if ($audit->proposed_schedules)
                    <h6 class="au-section-title">Proposed Schedule</h6>
                    <ul>
                        @foreach ($audit->proposed_schedules as $s)
                            <li>{{ \Carbon\Carbon::parse($s)->format('d M Y H:i') }}</li>
                        @endforeach
                    </ul>
                @endif

                @if ($audit->confirmed_schedule_at)
                    <h6 class="au-section-title mt-3">Jadwal Terkonfirmasi</h6>
                    <p>{{ $audit->confirmed_schedule_at->format('d M Y H:i') }} ·
                        Lokasi: <strong>{{ $audit->audit_location }}</strong>
                    </p>
                @endif

                @if ($audit->auditor_team)
                    <h6 class="au-section-title mt-3">Tim Auditor</h6>
                    <ul>
                        @foreach ($audit->auditor_team as $m)
                            <li>{{ $m['name'] ?? '—' }} <small class="text-muted">({{ $m['role'] ?? '—' }})</small></li>
                        @endforeach
                    </ul>
                @endif

                @if (in_array($audit->status, [VendorAudit::STATUS_SCHEDULE_PROPOSED, VendorAudit::STATUS_SCHEDULE_CONFIRMED], true))
                    @if ($audit->status === VendorAudit::STATUS_SCHEDULE_PROPOSED)
                        <form method="POST" action="{{ route('qa.audit.schedule.confirm', $audit->id) }}"
                            class="au-form mt-3">
                            @csrf
                            <div class="form-row align-items-end">
                                <div class="form-group col-md-6 mb-0">
                                    <label class="au-form-label">Konfirmasi Jadwal (vendor memilih)</label>
                                    <input type="datetime-local" name="confirmed_schedule_at" class="form-control" required>
                                </div>
                                <div class="form-group col-md-3 mb-0">
                                    <button type="submit" class="btn btn-primary">Konfirmasi</button>
                                </div>
                            </div>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('qa.audit.letter.generate', $audit->id) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-light-info">
                            <i class="flaticon2-file"></i> Generate Surat Pemberitahuan Audit
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Findings --}}
        <div class="au-card mt-4">
            <div class="au-card-head">
                <div class="au-card-title">
                    <span class="au-card-title-dot"></span>
                    Temuan Audit
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                @if ($findings->count() > 0)
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th>Deadline CAPA</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($findings as $f)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="au-tag au-tag--muted">{{ $f->category }}</span></td>
                                    <td>
                                        <div>{{ $f->description }}</div>
                                        @if ($f->evidence_reference)
                                            <small class="text-muted">Bukti: {{ $f->evidence_reference }}</small>
                                        @endif
                                    </td>
                                    <td>{{ optional($f->capa_deadline)->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        <span class="au-status au-status--{{ $f->status }}">
                                            {{ strtoupper($f->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if (in_array(
                        $audit->status,
                        [
                            VendorAudit::STATUS_SCHEDULE_CONFIRMED,
                            VendorAudit::STATUS_IN_PROGRESS,
                            VendorAudit::STATUS_FINDINGS_RECORDED,
                        ],
                        true))
                    <hr>
                    <h6 class="au-section-title">Tambah Temuan</h6>
                    <form method="POST" action="{{ route('qa.audit.findings.store', $audit->id) }}" class="au-form">
                        @csrf
                        <div id="au-findings-list"></div>
                        <button type="button" class="btn btn-sm btn-light-primary" id="au-add-finding">
                            <i class="flaticon2-add-1"></i> Tambah Temuan
                        </button>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Temuan & Kirim Notifikasi</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- CAPA verification --}}
        @if ($findings->count() > 0)
            <div class="au-card mt-4">
                <div class="au-card-head">
                    <div class="au-card-title">
                        <span class="au-card-title-dot"></span>
                        CAPA & Verifikasi
                        <span class="au-tag au-tag--muted ml-2">
                            {{ $capaProgress['closed'] }}/{{ $capaProgress['total'] }} selesai
                            ({{ $capaProgress['percent'] }}%)
                        </span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    @foreach ($findings as $f)
                        @php $capa = $f->latestCapa; @endphp
                        <div class="au-capa-item">
                            <div class="au-capa-head">
                                <strong>Temuan #{{ $loop->iteration }}:</strong> {{ $f->category }} —
                                {{ Str::limit($f->description, 80) }}
                            </div>

                            @if ($capa)
                                <div class="au-capa-body">
                                    <div><strong>Tindakan Korektif:</strong> {{ $capa->corrective_action ?: '—' }}</div>
                                    <div><strong>Tindakan Preventif:</strong> {{ $capa->preventive_action ?: '—' }}</div>

                                    @if ($capa->attachments)
                                        <div class="au-capa-attachments">
                                            <strong>Bukti:</strong>
                                            @foreach ($capa->attachments as $a)
                                                <a href="{{ asset('storage/' . $a['path']) }}"
                                                    target="_blank">{{ $a['name'] }}</a>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if (in_array($audit->status, [VendorAudit::STATUS_CAPA_SUBMITTED, VendorAudit::STATUS_CAPA_REVISED], true))
                                        <form method="POST" action="{{ route('qa.audit.capa.verify', $capa->id) }}"
                                            class="mt-2">
                                            @csrf
                                            <div class="form-row align-items-end">
                                                <div class="form-group col-md-3 mb-0">
                                                    <select name="verdict" class="form-control form-control-sm" required>
                                                        <option value="approved">Approve</option>
                                                        <option value="rejected">Reject (revisi)</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-7 mb-0">
                                                    <input type="text" name="qa_note"
                                                        class="form-control form-control-sm"
                                                        placeholder="Catatan QA (opsional)">
                                                </div>
                                                <div class="form-group col-md-2 mb-0">
                                                    <button type="submit"
                                                        class="btn btn-sm btn-primary btn-block">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        <div class="mt-2">
                                            <span class="au-status au-status--{{ $capa->qa_verdict }}">
                                                {{ strtoupper($capa->qa_verdict) }}
                                            </span>
                                            @if ($capa->qa_note)
                                                <small class="text-muted">— {{ $capa->qa_note }}</small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="au-capa-body text-muted">Vendor belum mengisi CAPA untuk temuan ini.</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    {{-- Final decision --}}
    @if (in_array($audit->status, [VendorAudit::STATUS_CAPA_SUBMITTED, VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED], true))
        <div class="au-card mt-4">
            <div class="au-card-head">
                <div class="au-card-title">
                    <span class="au-card-title-dot"></span>
                    Keputusan Akhir
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="POST" action="{{ route('qa.audit.finalize', $audit->id) }}" class="au-form">
                    @csrf
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-3 mb-0">
                            <label class="au-form-label">Keputusan</label>
                            <select name="decision" class="form-control" required>
                                <option value="approved">Approve Vendor</option>
                                <option value="rejected">Reject Vendor</option>
                            </select>
                        </div>
                        <div class="form-group col-md-7 mb-0">
                            <label class="au-form-label">Catatan</label>
                            <input type="text" name="summary" class="form-control" maxlength="1000">
                        </div>
                        <div class="form-group col-md-2 mb-0">
                            <button type="submit" class="btn btn-success btn-block">Finalisasi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Activity log --}}
    @php
        $logs = $audit->application->activityLogs()->latest()->limit(10)->get();
    @endphp
    @if ($logs->count() > 0)
        <div class="au-card mt-4">
            <div class="au-card-head">
                <div class="au-card-title">
                    <span class="au-card-title-dot"></span>
                    Activity Log
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <ul class="au-timeline">
                    @foreach ($logs as $log)
                        <li>
                            <strong>{{ $log->action }}</strong>
                            <span class="text-muted small">
                                · {{ optional($log->created_at)->format('d M Y H:i') }}
                                @if ($log->user)
                                    oleh {{ $log->user->name }}
                                @endif
                            </span>
                            <div class="small">
                                {{ $log->status_before ? $log->status_before . ' → ' : '' }}{{ $log->status_after }}
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(function() {
            // Finding repeater
            var $list = $('#au-findings-list');
            if ($list.length) {
                var idx = 0;

                function addFinding() {
                    var row = '<div class="au-repeater-card">' +
                        '<div class="form-row">' +
                        '<div class="form-group col-md-3 mb-2"><input type="text" name="findings[' + idx +
                        '][category]" class="form-control form-control-sm" placeholder="Kategori (Critical/Major/Minor)" required></div>' +
                        '<div class="form-group col-md-6 mb-2"><input type="text" name="findings[' + idx +
                        '][description]" class="form-control form-control-sm" placeholder="Deskripsi temuan" required></div>' +
                        '<div class="form-group col-md-2 mb-2"><input type="date" name="findings[' + idx +
                        '][capa_deadline]" class="form-control form-control-sm" required></div>' +
                        '<div class="form-group col-md-1 mb-2"><button type="button" class="btn btn-sm btn-light-danger au-remove-row btn-block"><i class="flaticon-delete"></i></button></div>' +
                        '</div>' +
                        '<div class="form-row"><div class="form-group col-md-12 mb-2"><input type="text" name="findings[' +
                        idx +
                        '][evidence_reference]" class="form-control form-control-sm" placeholder="Referensi bukti (opsional)"></div></div>' +
                        '</div>';
                    $list.append(row);
                    idx++;
                }
                $('#au-add-finding').on('click', addFinding);
                $list.on('click', '.au-remove-row', function() {
                    $(this).closest('.au-repeater-card').remove();
                });
                addFinding();
            }
        });
    </script>
@endpush
