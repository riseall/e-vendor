    <div id="verificationMainCard" class="card card-custom"
        style="border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:none;">
        <div class="card-header border-bottom-0 pt-6 pb-0">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-check-mark" style="color:var(--brand-primary);"></i>
                </span>
                <h5 class="card-label font-weight-bolder" style="color:var(--text-primary);">Verifikasi Data Calon Penyedia
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
                                    <div class="verification-field-grid">
                                        @forelse ($rows as $row)
                                            @if (($row['type'] ?? null) === 'section_title')
                                                <div class="verification-subsection-title">
                                                    {{ $row['label'] }}
                                                </div>
                                            @elseif (($row['type'] ?? null) === 'other_company_table')
                                                <div class="verification-table-wrap p-4 js-revision-field"
                                                    data-field="{{ $row['field'] ?? $row['label'] }}"
                                                    data-label="{{ $row['label'] }}">
                                                    <div class="font-weight-bold text-dark mb-3">{{ $row['label'] }}</div>
                                                    <table class="table table-sm table-bordered table-hover mb-0">
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
                                                                        class="text-center text-muted py-5">
                                                                        Tidak ada perusahaan lain milik pimpinan.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @elseif (($row['type'] ?? null) === 'product_table')
                                                <div class="verification-table-wrap p-4">
                                                    <div class="font-weight-bold text-dark mb-3">{{ $row['label'] }}</div>
                                                    <table class="table table-sm table-bordered table-hover mb-0">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width:220px;">Produk</th>
                                                                <th style="width:100px;">Kode ERP</th>
                                                                <th style="width:160px;">Manufaktur / Asal</th>
                                                                <th style="width:140px;">Rantai Pasok</th>
                                                                <th style="width:120px;">Surat Keagenan</th>
                                                                <th style="width:100px;">TKDN</th>
                                                                <th style="width:110px;">SNI</th>
                                                                <th style="width:110px;">Halal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse (($row['value'] ?? []) as $product)
                                                                <tr class="js-revision-field"
                                                                    data-field="{{ $product['field'] ?? 'products.' . ($product['erp'] ?? $loop->iteration) }}"
                                                                    data-label="{{ $product['label'] ?? ($product['product'] ?? 'Produk') }}">
                                                                    <td class="font-weight-bold text-dark">
                                                                        {{ $product['product'] ?? '-' }}</td>
                                                                    <td>{{ $product['erp'] ?? '-' }}</td>
                                                                    <td>{{ $product['manufaktur'] ?? '-' }}</td>
                                                                    <td>{{ $product['rantai_pasok'] ?? '-' }}</td>
                                                                    <td>
                                                                        @if (!empty($product['surat']))
                                                                            <button type="button"
                                                                                class="btn btn-xs btn-light-primary btn-preview-doc"
                                                                                data-url="{{ $product['surat'] }}"
                                                                                data-title="Surat Keagenan - {{ $product['product'] ?? 'Produk' }}">
                                                                                <i class="flaticon2-document icon-xs"></i>
                                                                                Lihat
                                                                            </button>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $product['tkdn'] ?? '-' }}</td>
                                                                    <td>{{ $product['sni'] ?? '-' }}</td>
                                                                    <td>{{ $product['halal'] ?? '-' }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="8"
                                                                        class="text-center text-muted py-5">
                                                                        Belum ada produk yang dipilih.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="verification-field js-revision-field"
                                                    data-field="{{ $row['field'] ?? $row['label'] }}"
                                                    data-label="{{ $row['label'] }}">
                                                    <div class="verification-field-label">{{ $row['label'] }}</div>
                                                    <div class="verification-field-value">
                                                        @if (!empty($row['url']))
                                                            <button type="button" class="btn-preview-doc btn-preview-doc"
                                                                data-url="{{ $row['url'] }}"
                                                                data-title="{{ $row['label'] ?? 'Preview Dokumen' }}">
                                                                <i class="flaticon2-document icon-xs"></i>
                                                                {{ $row['file_label'] ?? 'Lihat Dok.' }}
                                                            </button>
                                                        @endif
                                                        {!! nl2br(e($row['value'] ?? null ?: '-')) !!}
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
