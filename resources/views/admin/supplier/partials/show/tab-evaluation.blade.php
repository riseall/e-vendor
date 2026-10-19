@php
    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $now = now();
    $validityText = 'Tanpa Batas Masa Berlaku';
    $validityBadge = 'badge-light-success text-success';
    if ($app->valid_until) {
        $validityText = 'Berlaku Hingga ' . $app->valid_until->format('d F Y');
        if ($app->valid_until->isPast()) {
            $validityBadge = 'badge-danger';
            $validityText .= ' (Sudah Kadaluarsa)';
        } elseif ($now->diffInDays($app->valid_until, false) <= 60) {
            $validityBadge = 'badge-warning';
            $validityText .= ' (Masa Tenggang <= 60 Hari)';
        } else {
            $validityBadge = 'badge-success';
        }
    }
@endphp

{{-- Bagian 1: Status Validitas & Siklus Rekualifikasi --}}
<div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:10px;">
    <div class="card-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between">
        <div class="card-title m-0">
            <span class="card-icon mr-2 text-primary">
                <i class="fas fa-sync-alt text-primary"></i>
            </span>
            <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                Siklus Status Supplier & Rekualifikasi
            </h5>
        </div>
        <div>
            <span class="badge {{ $validityBadge }} font-weight-bolder px-3 py-2" style="font-size:0.85rem;">
                <i class="fas fa-calendar-check mr-1"></i> {{ $validityText }}
            </span>
        </div>
    </div>
    <div class="card-body p-5">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <table class="table table-sm table-borderless m-0" style="font-size:0.875rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted font-weight-bold" style="width: 40%;">Tipe Permohonan</td>
                            <td>
                                @if ($app->isRekualifikasi())
                                    <span class="badge badge-warning font-weight-bold">
                                        <i class="fas fa-sync-alt mr-1"></i> Rekualifikasi Berkala
                                    </span>
                                @else
                                    <span class="badge badge-light-primary font-weight-bold">
                                        <i class="fas fa-user-plus mr-1"></i> Registrasi Awal (Initial)
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @if ($app->isRekualifikasi() && $app->requalification_reason)
                            @php
                                $reasonLabels = \App\Models\VendorApplication::REASON_LABELS;
                                $reasonText =
                                    $reasonLabels[$app->requalification_reason] ?? $app->requalification_reason;
                            @endphp
                            <tr>
                                <td class="text-muted font-weight-bold">Alasan Rekualifikasi</td>
                                <td class="text-dark font-weight-bold">{{ $reasonText }}</td>
                            </tr>
                        @endif
                        @if ($app->parent)
                            <tr>
                                <td class="text-muted font-weight-bold">Permohonan Induk</td>
                                <td>
                                    <a href="{{ route('supplier.show', $app->parent_id) }}"
                                        class="font-weight-bold text-primary">
                                        {{ $app->parent->application_number }}
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="col-md-6 border-left-md">
                <div class="text-muted font-size-xs font-weight-bold text-uppercase mb-2">Riwayat Siklus Rekualifikasi
                    Lainnya:</div>
                @if ($app->rekualifikasiChildren->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered m-0" style="font-size:0.82rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nomor Berkas</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($app->rekualifikasiChildren as $child)
                                    <tr>
                                        <td class="font-weight-bold">{{ $child->application_number }}</td>
                                        <td>
                                            <span
                                                class="badge badge-light-{{ $child->status === 'approved' ? 'success' : 'primary' }} font-weight-bold">
                                                {{ ucfirst($child->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $child->created_at ? $child->created_at->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('supplier.show', $child->id) }}"
                                                class="btn btn-xs btn-light-primary font-weight-bold py-0 px-2">
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-muted font-size-sm">
                        <i class="fas fa-info-circle mr-1 text-muted"></i>
                        Belum ada riwayat permohonan rekualifikasi turunan untuk vendor ini.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Bagian 2: Evaluasi Kinerja Tahunan --}}
<div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:10px;">
    <div class="card-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between">
        <div class="card-title m-0">
            <span class="card-icon mr-2 text-primary">
                <i class="fas fa-award text-primary"></i>
            </span>
            <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                Evaluasi Kinerja Tahunan & Keputusan ({{ $annualEvaluations->count() }})
            </h5>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-head-custom table-vertical-center table-hover mb-0" style="font-size:0.85rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="pl-5" style="width: 80px;">Tahun</th>
                        <th class="text-center">Delivery</th>
                        <th class="text-center">Quality</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Complain</th>
                        <th class="text-center">Skor Akhir</th>
                        <th class="text-center">Grade</th>
                        <th>Approval</th>
                        <th class="pr-5">Keputusan / Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($annualEvaluations as $annual)
                        @php
                            $gradeColor = 'secondary';
                            if ($annual->category === 'A') {
                                $gradeColor = 'success';
                            } elseif ($annual->category === 'B') {
                                $gradeColor = 'primary';
                            } elseif ($annual->category === 'C') {
                                $gradeColor = 'warning';
                            } elseif ($annual->category === 'D') {
                                $gradeColor = 'danger';
                            }
                        @endphp
                        <tr>
                            <td class="pl-5 font-weight-bolder text-dark font-size-md">
                                {{ $annual->year }}
                            </td>
                            <td class="text-center font-weight-bold">
                                {{ number_format($annual->delivery_score_avg, 1) }}</td>
                            <td class="text-center font-weight-bold">{{ number_format($annual->quality_score_avg, 1) }}
                            </td>
                            <td class="text-center font-weight-bold">
                                {{ number_format($annual->quantity_score_avg, 1) }}</td>
                            <td class="text-center font-weight-bold">
                                {{ number_format($annual->complain_score_avg, 1) }}</td>
                            <td class="text-center font-weight-bolder font-size-md text-primary">
                                {{ number_format($annual->final_score, 1) }}
                            </td>
                            <td class="text-center">
                                <span
                                    class="badge badge-{{ $gradeColor }} font-weight-bolder px-3 py-1 font-size-sm">
                                    Grade {{ $annual->category }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <span
                                        class="badge badge-light-{{ $annual->manager_approved_at ? 'success' : 'secondary' }} font-weight-bold"
                                        style="font-size:0.75rem;">
                                        Manager: {{ $annual->manager_approved_at ? 'Approved' : 'Pending' }}
                                    </span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="badge badge-light-{{ $annual->gm_approved_at ? 'success' : 'secondary' }} font-weight-bold"
                                        style="font-size:0.75rem;">
                                        GM: {{ $annual->gm_approved_at ? 'Approved' : 'Pending' }}
                                    </span>
                                </div>
                            </td>
                            <td class="pr-5">
                                @if ($annual->decision_status)
                                    <span class="badge badge-outline-secondary font-weight-bold mb-1">
                                        {{ ucfirst(str_replace('_', ' ', $annual->decision_status)) }}
                                    </span>
                                @endif
                                <div class="text-muted font-size-xs text-truncate" style="max-width: 200px;"
                                    title="{{ $annual->notes }}">
                                    {{ $annual->notes ?: '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-6">
                                <i class="fas fa-chart-pie icon-2x d-block mb-2 text-muted"></i>
                                Belum ada laporan Evaluasi Tahunan yang disahkan untuk vendor ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Bagian 3: Riwayat Evaluasi Bulanan (Progress Berjalan) --}}
<div class="card card-custom gutter-b shadow-sm border-0 mb-6" style="border-radius:10px;">
    <div class="card-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between">
        <div class="card-title m-0">
            <span class="card-icon mr-2 text-primary">
                <i class="fas fa-calendar-alt text-primary"></i>
            </span>
            <h5 class="card-label font-weight-bolder m-0 text-dark font-size-md">
                Riwayat Evaluasi Bulanan ({{ $monthlyEvaluations->count() }} Periode)
            </h5>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table table-head-custom table-vertical-center table-hover mb-0" style="font-size:0.85rem;">
                <thead class="bg-light sticky-top">
                    <tr>
                        <th class="pl-5" style="width: 130px;">Periode</th>
                        <th class="text-center">Delivery</th>
                        <th class="text-center">Quality</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Complain</th>
                        <th class="text-center">Material</th>
                        <th class="text-center">Safety</th>
                        <th class="text-center">Total Skor</th>
                        <th class="text-center">Kategori</th>
                        <th class="pr-5">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($monthlyEvaluations as $monthly)
                        @php
                            $mMonth = $months[$monthly->month] ?? $monthly->month;
                            $mGradeColor = 'secondary';
                            if ($monthly->category === 'A') {
                                $mGradeColor = 'success';
                            } elseif ($monthly->category === 'B') {
                                $mGradeColor = 'primary';
                            } elseif ($monthly->category === 'C') {
                                $mGradeColor = 'warning';
                            } elseif ($monthly->category === 'D') {
                                $mGradeColor = 'danger';
                            }
                        @endphp
                        <tr>
                            <td class="pl-5 font-weight-bolder text-dark">
                                {{ $mMonth }} {{ $monthly->year }}
                            </td>
                            <td class="text-center font-weight-bold">{{ number_format($monthly->delivery_score, 1) }}
                            </td>
                            <td class="text-center font-weight-bold">{{ number_format($monthly->quality_score, 1) }}
                            </td>
                            <td class="text-center font-weight-bold">{{ number_format($monthly->quantity_score, 1) }}
                            </td>
                            <td class="text-center font-weight-bold">{{ number_format($monthly->complain_score, 1) }}
                            </td>
                            <td class="text-center font-weight-bold">
                                {{ number_format($monthly->incoming_material_score, 1) }}</td>
                            <td class="text-center font-weight-bold">
                                {{ number_format($monthly->safety_environment_score, 1) }}</td>
                            <td class="text-center font-weight-bolder text-primary">
                                {{ number_format($monthly->total_score, 1) }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-{{ $mGradeColor }} font-weight-bold px-2 py-1">
                                    {{ $monthly->category ?: '-' }}
                                </span>
                            </td>
                            <td class="pr-5 text-muted font-size-xs text-truncate" style="max-width: 180px;"
                                title="{{ $monthly->notes }}">
                                {{ $monthly->notes ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-6">
                                <i class="fas fa-calendar-times icon-2x d-block mb-2 text-muted"></i>
                                Belum ada input data evaluasi bulanan untuk vendor ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
