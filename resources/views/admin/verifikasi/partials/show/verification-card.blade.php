<div id="verificationMainCard" class="card card-custom"
        style="border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:none;">
        <div class="card-header border-bottom-0 pt-6 pb-0">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-check-mark" style="color:var(--brand-primary);"></i>
                </span>
                <h5 class="card-label font-weight-bolder" style="color:var(--text-primary);">Verifikasi Data Calon
                    Penyedia
                </h5>
            </div>
        </div>

        <div class="card-body pt-3">

            {{-- Tab Nav --}}
            <div class="tab-nav-wrap">
                <ul class="nav verif-tabs nav-tabs mb-0 flex-nowrap overflow-auto" role="tablist">
                    @foreach ($verificationSections as $section)
                        <li class="nav-item flex-shrink-0">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab"
                                href="#tab-{{ $section['key'] }}" role="tab">
                                {{ $section['label'] }}
                                @if ($section['rejected'] > 0)
                                    <span class="verif-tab-badge is-rejected">{{ $section['rejected'] }}</span>
                                @elseif ($section['pending'] > 0)
                                    <span class="verif-tab-badge is-pending">{{ $section['pending'] }}</span>
                                @else
                                    <span class="verif-tab-badge is-done">✓</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Tab Content --}}
            <div class="tab-content pt-7">
                @foreach ($verificationSections as $section)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $section['key'] }}"
                        role="tabpanel">

                        <div class="verification-item-list">
                            @php $lastSpecificHeader = null; @endphp
                            @foreach ($section['items'] as $item)
                                @php
                                    $meta = $itemStatus[$item->status] ?? $itemStatus['pending'];
                                    $rows = $verificationItemRows[$item->item_key] ?? [];
                                    $specificHeader =
                                        $section['key'] === 'specific'
                                            ? $specificCategoryHeaders[$item->item_key] ?? null
                                            : null;
                                    $showActionsInSpecificHeader = $section['key'] === 'specific' && $specificHeader;
                                    $panelClass = '';
                                    if ($item->status === 'approved') {
                                        $panelClass = 'is-approved';
                                    } elseif ($item->status === 'rejected') {
                                        $panelClass = 'is-rejected';
                                    }
                                @endphp

                                @if ($specificHeader && $specificHeader !== $lastSpecificHeader)
                                    <div class="specific-category-header">
                                        <span>{{ $specificHeader }}</span>

                                        @if ($showActionsInSpecificHeader)
                                            <div class="d-flex align-items-center flex-wrap justify-content-end"
                                                style="gap:.5rem;">
                                                <span
                                                    class="verif-status-badge {{ $item->status === 'approved' ? 'is-approved' : ($item->status === 'rejected' ? 'is-rejected' : 'is-pending') }}">
                                                    <i class="{{ $meta['icon'] }} icon-xs"></i>
                                                    {{ $meta['label'] }}
                                                </span>

                                                @if ($item->status === 'pending')
                                                    <form method="POST"
                                                        action="{{ route('verifikasi.items.approve', [$application, $item]) }}"
                                                        class="d-inline js-confirm-approve"
                                                        data-label="{{ $specificHeader }}">
                                                        @csrf
                                                        <button type="submit" class="btn-approve">
                                                            <i class="flaticon2-check-mark icon-xs"></i> Setuju
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn-reject js-reject-btn"
                                                        data-toggle="modal" data-target="#modalRejectItem"
                                                        data-action="{{ route('verifikasi.items.reject', [$application, $item]) }}"
                                                        data-label="{{ $specificHeader }}">
                                                        <i class="ki ki-close icon-xs"></i> Tidak Setuju
                                                    </button>
                                                @elseif ($item->status === 'approved')
                                                    <button type="button" class="btn-undo js-reject-btn"
                                                        data-toggle="modal" data-target="#modalRejectItem"
                                                        data-action="{{ route('verifikasi.items.reject', [$application, $item]) }}"
                                                        data-label="{{ $specificHeader }}"
                                                        title="Ubah menjadi Tidak Setuju">
                                                        <i class="ki ki-close icon-xs"></i> Tolak
                                                    </button>
                                                @elseif ($item->status === 'rejected')
                                                    <form method="POST"
                                                        action="{{ route('verifikasi.items.approve', [$application, $item]) }}"
                                                        class="d-inline js-confirm-approve"
                                                        data-label="{{ $specificHeader }}">
                                                        @csrf
                                                        <button type="submit" class="btn-undo"
                                                            title="Ubah menjadi Setuju">
                                                            <i class="flaticon2-check-mark icon-xs"></i> Setujui
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    @php $lastSpecificHeader = $specificHeader; @endphp
                                @endif

                                <div class="verification-panel mb-4 {{ $panelClass }}">

                                    {{-- Panel Head --}}
                                    <div class="verification-panel-head">
                                        <div class="min-w-0">
                                            <div class="panel-head-title">
                                                {{ $showActionsInSpecificHeader ? 'Detail Persyaratan' : $item->item_label }}
                                            </div>
                                            <div class="panel-head-meta">
                                                @if ($item->verifier)
                                                    <i class="flaticon2-user icon-xs"></i>
                                                    {{ $item->verifier->name }}
                                                    &middot;
                                                    {{ optional($item->verified_at)->format('d/m/Y H:i') }}
                                                @else
                                                    <i class="flaticon2-hourglass icon-xs"></i>
                                                    Belum ada verifikator
                                                @endif
                                            </div>
                                        </div>

                                        @if (!$showActionsInSpecificHeader)
                                            <div class="d-flex align-items-center flex-wrap justify-content-end"
                                                style="gap:.5rem;">
                                                {{-- Status badge --}}
                                                <span
                                                    class="verif-status-badge {{ $item->status === 'approved' ? 'is-approved' : ($item->status === 'rejected' ? 'is-rejected' : 'is-pending') }}">
                                                    <i class="{{ $meta['icon'] }} icon-xs"></i>
                                                    {{ $meta['label'] }}
                                                </span>

                                                {{-- Aksi --}}
                                                @if ($item->status === 'pending')
                                                    <form method="POST"
                                                        action="{{ route('verifikasi.items.approve', [$application, $item]) }}"
                                                        class="d-inline js-confirm-approve"
                                                        data-label="{{ $item->item_label }}">
                                                        @csrf
                                                        <button type="submit" class="btn-approve">
                                                            <i class="flaticon2-check-mark icon-xs"></i> Setuju
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn-reject js-reject-btn"
                                                        data-toggle="modal" data-target="#modalRejectItem"
                                                        data-action="{{ route('verifikasi.items.reject', [$application, $item]) }}"
                                                        data-label="{{ $item->item_label }}">
                                                        <i class="ki ki-close icon-xs"></i> Tidak Setuju
                                                    </button>
                                                @elseif ($item->status === 'approved')
                                                    <button type="button" class="btn-undo js-reject-btn"
                                                        data-toggle="modal" data-target="#modalRejectItem"
                                                        data-action="{{ route('verifikasi.items.reject', [$application, $item]) }}"
                                                        data-label="{{ $item->item_label }}"
                                                        title="Ubah menjadi Tidak Setuju">
                                                        <i class="ki ki-close icon-xs"></i> Tolak
                                                    </button>
                                                @elseif ($item->status === 'rejected')
                                                    <form method="POST"
                                                        action="{{ route('verifikasi.items.approve', [$application, $item]) }}"
                                                        class="d-inline js-confirm-approve"
                                                        data-label="{{ $item->item_label }}">
                                                        @csrf
                                                        <button type="submit" class="btn-undo"
                                                            title="Ubah menjadi Setuju">
                                                            <i class="flaticon2-check-mark icon-xs"></i> Setujui
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Rejection Note --}}
                                    @if ($item->note)
                                        <div class="rejection-note">
                                            <div class="rejection-note-icon">
                                                <i class="ki ki-close"></i>
                                            </div>
                                            <div class="rejection-note-text">
                                                <span style="font-weight:700;">
                                                    {{ $item->status === 'pending' ? 'Catatan Revisi Sebelumnya:' : 'Catatan Penolakan:' }}
                                                </span>
                                                {{ $item->note }}
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Field Grid --}}
                                    <div class="row px-4 pb-4 pt-2">
                                        @forelse ($rows as $row)
                                            @if (($row['type'] ?? null) === 'section_title')
                                                <div class="col-12 mt-4 mb-2">
                                                    <div class="font-weight-bold text-dark text-uppercase border-bottom pb-2">
                                                        {{ $row['label'] }}
                                                    </div>
                                                </div>
                                            @elseif (($row['type'] ?? null) === 'other_company_table')
                                                <div class="col-12 mb-4 js-revision-field"
                                                    data-field="{{ $row['field'] ?? $row['label'] }}"
                                                    data-label="{{ $row['label'] }}">
                                                    <label class="font-size-sm font-weight-bold text-muted">{{ $row['label'] }}</label>
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width:60px;">No</th>
                                                                <th style="width:260px;">Nama Perusahaan</th>
                                                                <th>Alamat</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse (($row['value'] ?? []) as $company)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td class="font-weight-bold text-dark">
                                                                        {{ $company['nama'] ?? '-' }}</td>
                                                                    <td>{{ $company['alamat'] ?? '-' }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="3"
                                                                        class="text-center text-muted py-3">
                                                                        Tidak ada perusahaan lain milik pimpinan.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @elseif (($row['type'] ?? null) === 'product_table')
                                                <div class="col-12 mb-4">
                                                    <label class="font-size-sm font-weight-bold text-muted">{{ $row['label'] }}</label>
                                                    <table class="table table-sm table-bordered table-hover mb-0" id="verificationProductTable">
                                                        <thead class="thead-light text-nowrap text-center align-middle">
                                                            <tr>
                                                                <th class="align-middle" style="min-width:250px;">Produk</th>
                                                                <th class="align-middle" style="min-width:100px;">Item PH</th>
                                                                <th class="align-middle" style="min-width:160px;">Manufaktur / Asal</th>
                                                                <th class="align-middle" style="min-width:100px;">GMP</th>
                                                                <th class="align-middle" style="min-width:120px;">Negara</th>
                                                                <th class="align-middle" style="min-width:140px;">Rantai Pasok</th>
                                                                <th class="align-middle" style="min-width:140px;">Surat Keagenan</th>
                                                                <th class="align-middle" style="min-width:90px;">TKDN</th>
                                                                <th class="align-middle" style="min-width:90px;">SNI</th>
                                                                <th class="align-middle" style="min-width:90px;">Halal</th>
                                                                <th class="align-middle" style="min-width:110px;">BSE/TSE</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse (($row['value'] ?? []) as $product)
                                                                <tr class="js-revision-field align-middle text-center"
                                                                    data-field="{{ $product['field'] ?? 'products.' . ($product['erp'] ?? $loop->iteration) }}"
                                                                    data-label="{{ $product['label'] ?? ($product['product'] ?? 'Produk') }}">
                                                                    <td class="font-weight-bold text-dark text-left align-middle" style="white-space: normal;">
                                                                        {{ $product['product'] ?? '-' }}</td>
                                                                    <td class="align-middle">{{ $product['erp'] ?? '-' }}</td>
                                                                    <td class="align-middle">{{ $product['manufaktur'] ?? '-' }}</td>
                                                                    <td class="align-middle">
                                                                        @if (!empty($product['gmp']))
                                                                            <x-preview-doc-button
                                                                                url="{{ $product['gmp'] }}"
                                                                                title="GMP - {{ $product['product'] ?? 'Produk' }}"
                                                                                compact />
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle">{{ $product['negara'] ?? '-' }}</td>
                                                                    <td class="align-middle">{{ $product['rantai_pasok'] ?? '-' }}</td>
                                                                    <td class="align-middle">
                                                                        @if (!empty($product['surat']))
                                                                            <x-preview-doc-button
                                                                                url="{{ $product['surat'] }}"
                                                                                title="Surat Keagenan - {{ $product['product'] ?? 'Produk' }}"
                                                                                compact />
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        @if (!empty($product['tkdn']))
                                                                            <x-preview-doc-button
                                                                                url="{{ $product['tkdn'] }}"
                                                                                title="Sertifikat TKDN - {{ $product['product'] ?? 'Produk' }}"
                                                                                compact />
                                                                        @else
                                                                            {{ !empty($product['has_tkdn']) ? '-' : 'Tidak' }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        @if (!empty($product['sni']))
                                                                            <x-preview-doc-button
                                                                                url="{{ $product['sni'] }}"
                                                                                title="Sertifikat SNI - {{ $product['product'] ?? 'Produk' }}"
                                                                                compact />
                                                                        @else
                                                                            {{ !empty($product['has_sni']) ? '-' : 'Tidak' }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        @if (!empty($product['halal']))
                                                                            <x-preview-doc-button
                                                                                url="{{ $product['halal'] }}"
                                                                                title="Sertifikat Halal - {{ $product['product'] ?? 'Produk' }}"
                                                                                compact />
                                                                        @else
                                                                            {{ !empty($product['has_halal']) ? '-' : 'Tidak' }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        @if (!empty($product['bse_tse']))
                                                                            <x-preview-doc-button
                                                                                url="{{ $product['bse_tse'] }}"
                                                                                title="Dokumen BSE/TSE - {{ $product['product'] ?? 'Produk' }}"
                                                                                compact />
                                                                        @else
                                                                            {{ !empty($product['has_bse_tse']) ? '-' : 'Tidak' }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="11"
                                                                        class="text-center text-muted py-5">
                                                                        Belum ada produk yang dipilih.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @elseif (($row['type'] ?? null) === 'iso_files_table')
                                                @php $isoFileService = app(\App\Services\VendorFileService::class); @endphp
                                                <div class="col-md-6 mb-4 js-revision-field"
                                                    data-field="{{ $row['field'] ?? 'iso_files' }}"
                                                    data-label="{{ $row['label'] }}">
                                                    <label class="font-size-sm font-weight-bold text-muted">{{ $row['label'] }}</label>
                                                    <div class="form-control form-control-solid h-auto" style="min-height: 38px;">
                                                        @if (empty($row['value']) || (is_object($row['value']) && $row['value']->isEmpty()))
                                                            <span class="text-muted font-size-sm">Tidak ada dokumen.</span>
                                                        @else
                                                            @php
                                                                $isoDocs =
                                                                    $row['value'] instanceof
                                                                    \Illuminate\Support\Collection
                                                                        ? $row['value']->all()
                                                                        : (array) $row['value'];
                                                            @endphp
                                                            <div class="d-flex flex-column" style="gap: .5rem;">
                                                                @foreach ($isoDocs as $doc)
                                                                    @php
                                                                        $docPath =
                                                                            $doc['file_path'] ??
                                                                            ($doc->file_path ?? null);
                                                                        $docName =
                                                                            $doc['original_name'] ??
                                                                            ($doc->original_name ?? 'Dokumen ISO');
                                                                        $docUrl =
                                                                            $doc['url'] ??
                                                                            ($docPath
                                                                                ? $isoFileService->url(
                                                                                    $application,
                                                                                    $docPath,
                                                                                )
                                                                                : null);
                                                                    @endphp
                                                                    @if ($docUrl)
                                                                        <x-preview-doc-button
                                                                            url="{{ $docUrl }}"
                                                                            title="{{ $docName }}"
                                                                            label="{{ $docName }}"
                                                                            class="btn btn-sm btn-light-primary text-left" />
                                                                    @else
                                                                        <div class="text-muted font-size-sm">
                                                                            <i class="flaticon2-document icon-sm mr-1"></i>
                                                                            {{ $docName }} (file tidak tersedia)
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-6 mb-4 js-revision-field"
                                                    data-field="{{ $row['field'] ?? $row['label'] }}"
                                                    data-label="{{ $row['label'] }}">
                                                    <div class="form-group mb-0">
                                                        <label class="font-size-sm font-weight-bold text-muted">{{ $row['label'] }}</label>
                                                        @if (!empty($row['url']))
                                                            <div class="mt-1">
                                                                <x-preview-doc-button url="{{ $row['url'] }}"
                                                                    title="{{ $row['label'] ?? 'Preview Dokumen' }}"
                                                                    label="Lihat Dokumen: {{ $row['value'] ?? ($row['label'] ?? 'Dokumen') }}"
                                                                    class="btn btn-sm btn-light-primary w-100 text-left" />
                                                            </div>
                                                        @else
                                                            @if (strlen($row['value'] ?? '') > 100)
                                                                <textarea class="form-control form-control-solid" rows="3" readonly disabled>{!! strip_tags($row['value'] ?? null ?: '-') !!}</textarea>
                                                            @else
                                                                <input type="text" class="form-control form-control-solid" value="{!! strip_tags($row['value'] ?? null ?: '-') !!}" readonly disabled>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @empty
                                            <div
                                                style="grid-column:1/-1; padding:2rem 1.5rem; color:var(--text-muted); font-size:.83rem; display:flex; align-items:center; gap:.5rem;">
                                                <i class="flaticon2-information"></i>
                                                Belum ada data tersimpan untuk item ini.
                                            </div>
                                        @endforelse
                                    </div>

                                </div>{{-- /verification-panel --}}
                            @endforeach
                        </div>{{-- /verification-item-list --}}
                    </div>{{-- /tab-pane --}}
                @endforeach
            </div>{{-- /tab-content --}}

        </div>
    </div>

    {{-- ───── Modal Tolak ───── --}}

@push('scripts')
    <script>
        $(document).ready(function() {
            if ($('#verificationProductTable').length) {
                $('#verificationProductTable').DataTable({
                    responsive: false,
                    scrollX: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, 'Semua']
                    ],
                    order: []
                });
            }
        });
    </script>
@endpush
