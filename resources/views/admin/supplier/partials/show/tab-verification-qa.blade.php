@php
    $qualification = $app->qualification;
    $riskLevel = $qualification
        ? strtolower($qualification->risk_level)
        : ($app->risk_level
            ? strtolower($app->risk_level)
            : null);
    $audits = $app->audits;
@endphp

<div class="row">
    {{-- Kolom Kiri: Verifikasi Procurement & Risk Assessment --}}
    <div class="col-lg-6 mb-6">
        {{-- Card 1: Hasil Verifikasi Procurement --}}
        <div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:10px;">
            <div class="card-header border-bottom py-3 px-5 d-flex align-items-center">
                <div class="card-title m-0">
                    <span class="card-icon mr-2 text-primary">
                        <i class="fas fa-clipboard-check text-primary"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                        Tahap 1: Verifikasi Berkas
                    </h5>
                </div>
            </div>
            <div class="card-body p-5">
                <table class="table table-sm table-borderless m-0" style="font-size:0.875rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted font-weight-bold" style="width: 40%;">Status Verifikasi</td>
                            <td>
                                <span class="badge badge-success font-weight-bold">
                                    <i class="fas fa-check-circle mr-1 icon-sm text-white"></i> Approved
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal Pengajuan</td>
                            <td class="text-dark font-weight-bold">
                                {{ $app->submitted_at ? $app->submitted_at->format('d F Y, H:i') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Petugas Verifikator</td>
                            <td class="text-dark">
                                <i class="fas fa-user-check mr-1 text-muted"></i>
                                {{ optional($app->verifier)->name ?: 'Sistem / Verifikator Tim Pengadaan' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal Verifikasi</td>
                            <td class="text-dark">
                                {{ $app->verified_at ? $app->verified_at->format('d F Y, H:i') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal Disetujui</td>
                            <td class="text-dark font-weight-bolder text-success">
                                {{ $app->approved_at ? $app->approved_at->format('d F Y, H:i') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Jumlah Revisi</td>
                            <td>
                                <span class="badge badge-light-secondary font-weight-bold">
                                    {{ $app->revision_count ?: 0 }} Kali Revisi
                                </span>
                            </td>
                        </tr>
                        @if ($app->admin_note)
                            <tr>
                                <td class="text-muted font-weight-bold align-top">Catatan Verifikasi</td>
                                <td class="text-dark">
                                    <div class="p-3 bg-light rounded font-size-sm">
                                        {{ $app->admin_note }}
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                @if ($app->revision_notes && is_array($app->revision_notes) && count($app->revision_notes) > 0)
                    <div class="border-top mt-4 pt-3">
                        <div class="font-size-xs text-muted font-weight-bold text-uppercase mb-2">Riwayat Catatan
                            Revisi:</div>
                        <ul class="list-unstyled mb-0" style="font-size:0.82rem;">
                            @foreach ($app->revision_notes as $rev)
                                <li class="mb-2 p-2 bg-light-warning rounded text-dark">
                                    <i class="fas fa-exclamation-circle text-warning mr-1"></i>
                                    {{ is_array($rev) ? $rev['note'] ?? json_encode($rev) : $rev }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 2: QA Risk Assessment --}}
        <div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:10px;">
            <div class="card-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between">
                <div class="card-title m-0">
                    <span class="card-icon mr-2 text-primary">
                        <i class="fas fa-shield-alt text-primary"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                        Tahap 2: Risk Assessment
                    </h5>
                </div>
            </div>
            <div class="card-body p-5">
                @if ($qualification)
                    <div class="d-flex align-items-center justify-content-between p-4 bg-light rounded mb-4 flex-wrap"
                        style="gap:10px;">
                        <div>
                            <div class="text-muted font-size-xs font-weight-bold text-uppercase">Tingkat Risiko (RPN)
                            </div>
                            <div class="mt-1">
                                @if ($riskLevel === 'low')
                                    <span class="badge badge-success font-weight-bolder py-2 px-3"
                                        style="font-size:0.95rem;">
                                        <i class="fas fa-shield-alt mr-1 text-white"></i> LOW RISK
                                    </span>
                                @elseif ($riskLevel === 'medium')
                                    <span class="badge badge-warning font-weight-bolder py-2 px-3"
                                        style="font-size:0.95rem;">
                                        <i class="fas fa-exclamation-triangle mr-1 text-white"></i> MEDIUM RISK
                                    </span>
                                @elseif ($riskLevel === 'high')
                                    <span class="badge badge-danger font-weight-bolder py-2 px-3"
                                        style="font-size:0.95rem;">
                                        <i class="fas fa-times-circle mr-1 text-white"></i> HIGH RISK
                                    </span>
                                @else
                                    <span class="badge badge-secondary font-weight-bolder py-2 px-3">-</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="text-muted font-size-xs font-weight-bold text-uppercase">Total Skor RPN</div>
                            <div class="font-size-h3 font-weight-bolder text-primary">
                                {{ number_format($qualification->total_score, 0, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-muted font-size-xs font-weight-bold text-uppercase">Rekomendasi Jalur</div>
                            <div class="mt-1">
                                @if ($qualification->audit_type === 'on_site')
                                    <span class="badge badge-light-danger font-weight-bold">
                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i> On-Site Audit
                                    </span>
                                @elseif ($qualification->audit_type === 'on_desk')
                                    <span class="badge badge-light-warning font-weight-bold">
                                        <i class="fas fa-file-alt text-warning mr-1"></i> On-Desk Audit (Kuesioner)
                                    </span>
                                @else
                                    <span class="badge badge-light-success font-weight-bold">
                                        <i class="fas fa-check text-success mr-1"></i> Bebas Audit (Langsung Qualified)
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Breakdown Parameter RPN --}}
                    <div class="text-muted font-size-xs font-weight-bold text-uppercase mb-2">Parameter Matriks
                        Penilaian Risiko:</div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-3" style="font-size:0.82rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th>Parameter Kualifikasi QA</th>
                                    <th class="text-center" style="width: 90px;">Sub Skor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Safety & Efficacy (Keamanan & Khasiat)</td>
                                    <td class="text-center font-weight-bold">
                                        {{ $qualification->score_safety_efficacy ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Availability & Traceability (Ketersediaan)</td>
                                    <td class="text-center font-weight-bold">
                                        {{ $qualification->score_availability ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Detectability (Keterdeteksian Risiko)</td>
                                    <td class="text-center font-weight-bold">
                                        {{ $qualification->score_detectability ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Probability (Probabilitas Kegagalan)</td>
                                    <td class="text-center font-weight-bold">
                                        {{ $qualification->score_probability ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if ($qualification->notes)
                        <div class="mt-3">
                            <span class="text-muted font-size-xs font-weight-bold text-uppercase d-block mb-1">Catatan
                                Apoteker / QA Evaluator:</span>
                            <div class="p-3 bg-light rounded text-dark font-size-sm">
                                {{ $qualification->notes }}
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-clipboard-list icon-2x d-block mb-2 text-muted"></i>
                        Data Risk Assessment belum diisi untuk permohonan ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Pelaksanaan Audit QA (Stage 5) --}}
    <div class="col-lg-6 mb-6">
        <div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:10px;">
            <div class="card-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between">
                <div class="card-title m-0">
                    <span class="card-icon mr-2 text-primary">
                        <i class="fas fa-search-plus text-primary"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                        Tahap 3: Riwayat Pelaksanaan Audit QA ({{ $audits->count() }})
                    </h5>
                </div>
            </div>
            <div class="card-body p-5">
                @forelse ($audits as $audit)
                    @php
                        $isOnDesk = $audit->isOnDesk();
                        $statusClass = 'badge-light-secondary';
                        if ($audit->status === \App\Models\VendorAudit::STATUS_COMPLETED) {
                            $statusClass = 'badge-success';
                        } elseif ($audit->status === \App\Models\VendorAudit::STATUS_REJECTED) {
                            $statusClass = 'badge-danger';
                        } elseif (
                            strpos($audit->status, 'progress') !== false ||
                            strpos($audit->status, 'submitted') !== false
                        ) {
                            $statusClass = 'badge-warning';
                        }
                    @endphp

                    <div class="border rounded p-4 mb-4 {{ $loop->first ? 'border-primary' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap" style="gap:6px;">
                            <div>
                                <span
                                    class="badge {{ $isOnDesk ? 'badge-light-warning' : 'badge-light-danger' }} font-weight-bold mr-1">
                                    <i
                                        class="{{ $isOnDesk ? 'fas fa-laptop-code' : 'fas fa-map-marker-alt' }} mr-1"></i>
                                    {{ $isOnDesk ? 'Audit On-Desk (Kuesioner)' : 'Audit On-Site (Lapangan)' }}
                                </span>
                                <span class="badge {{ $statusClass }} font-weight-bold">
                                    {{ ucfirst(str_replace('_', ' ', $audit->status)) }}
                                </span>
                            </div>
                            <span class="text-muted font-size-xs">
                                Dibuat: {{ $audit->created_at ? $audit->created_at->format('d/m/Y') : '-' }}
                            </span>
                        </div>

                        <table class="table table-sm table-borderless m-0" style="font-size:0.85rem;">
                            <tbody>
                                @if ($isOnDesk)
                                    <tr>
                                        <td class="text-muted font-weight-bold" style="width: 35%;">Form Kuesioner
                                        </td>
                                        <td class="text-dark font-weight-bold">
                                            {{ optional($audit->questionnaireForm)->title ?: 'Kuesioner Audit Standar' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted font-weight-bold">Tanggal Submit</td>
                                        <td class="text-dark">
                                            {{ $audit->questionnaire_submitted_at ? $audit->questionnaire_submitted_at->format('d F Y, H:i') : 'Belum Submit' }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="text-muted font-weight-bold" style="width: 35%;">Jadwal On-Site
                                        </td>
                                        <td class="text-dark font-weight-bold">
                                            {{ $audit->confirmed_schedule_at ? \Carbon\Carbon::parse($audit->confirmed_schedule_at)->format('d F Y') : 'Belum Terjadwal' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted font-weight-bold">Lokasi Pabrik/Gudang</td>
                                        <td class="text-dark">{{ $audit->audit_location ?: '-' }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="text-muted font-weight-bold">QA Lead / Auditor</td>
                                    <td class="text-dark">
                                        <i class="fas fa-user-shield mr-1 text-muted"></i>
                                        {{ optional($audit->qaLead)->name ?: (optional($audit->creator)->name ?: '-') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-weight-bold">Hasil / Kategori</td>
                                    <td>
                                        @if ($audit->audit_result_category)
                                            <span class="badge badge-light-primary font-weight-bolder">
                                                Kategori {{ $audit->audit_result_category }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($audit->summary)
                                    <tr>
                                        <td class="text-muted font-weight-bold align-top">Ringkasan Temuan</td>
                                        <td class="text-dark font-size-xs">
                                            <div class="p-2 bg-white rounded border">
                                                {{ $audit->summary }}
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @if ($audit->audit_result_path)
                                    <tr>
                                        <td class="text-muted font-weight-bold align-middle">Laporan Temuan</td>
                                        <td>
                                            <x-preview-doc-button :url="asset('storage/' . $audit->audit_result_path)" label="Lihat Dokumen Hasil Audit"
                                                :compact="false" />
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @empty
                    <div class="text-center py-8 text-muted">
                        <i class="fas fa-check-double icon-3x d-block mb-3 text-success"></i>
                        <h6 class="font-weight-bold text-dark mb-1">Tidak Ada Kewajiban Audit</h6>
                        <p class="font-size-sm text-muted mb-0">
                            Vendor ini dikategorikan <strong>Low Risk</strong> atau lolos verifikasi langsung tanpa
                            memerlukan audit lanjutan (On-Desk / On-Site).
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
