<div id="verificationMainCard" class="card card-custom"
    style="border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:none;">
    <div class="card-body p-6">

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
                        @foreach ($section['items'] as $item)
                            @php
                                $meta = $itemStatus[$item->status] ?? $itemStatus['pending'];
                                $rows = $verificationItemRows[$item->item_key] ?? [];
                                $specificHeader =
                                    $section['key'] === 'specific'
                                        ? $specificCategoryHeaders[$item->item_key] ?? null
                                        : null;
                                $panelTitle = $specificHeader ?: $item->item_label;
                                $panelClass = '';
                                if ($item->status === 'approved') {
                                    $panelClass = 'is-approved';
                                } elseif ($item->status === 'rejected') {
                                    $panelClass = 'is-rejected';
                                }
                            @endphp

                            <div class="verification-panel mb-4 {{ $panelClass }}">

                                {{-- Panel Head --}}
                                <div class="verification-panel-head">
                                    <div class="min-w-0">
                                        <div class="panel-head-title">
                                            {{ $panelTitle }}
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
                                            @can('verifikasi-approve')
                                                <form method="POST"
                                                    action="{{ route('verifikasi.items.approve', [$application, $item]) }}"
                                                    class="d-inline js-confirm-approve" data-label="{{ $panelTitle }}">
                                                    @csrf
                                                    <button type="submit" class="btn-approve">
                                                        <i class="flaticon2-check-mark icon-xs text-white"></i> Setuju
                                                    </button>
                                                </form>
                                            @endcan
                                            @can('verifikasi-reject')
                                                <button type="button" class="btn-reject js-reject-btn" data-toggle="modal"
                                                    data-target="#modalRejectItem"
                                                    data-action="{{ route('verifikasi.items.reject', [$application, $item]) }}"
                                                    data-label="{{ $panelTitle }}">
                                                    <i class="flaticon2-delete icon-xs"></i> Tidak Setuju
                                                </button>
                                            @endcan
                                        @elseif ($item->status === 'approved')
                                            @can('verifikasi-reject')
                                                <button type="button" class="btn-undo js-reject-btn" data-toggle="modal"
                                                    data-target="#modalRejectItem"
                                                    data-action="{{ route('verifikasi.items.reject', [$application, $item]) }}"
                                                    data-label="{{ $panelTitle }}" title="Ubah menjadi Tidak Setuju">
                                                    <i class="flaticon2-delete icon-xs"></i> Tolak
                                                </button>
                                            @endcan
                                        @elseif ($item->status === 'rejected')
                                            @can('verifikasi-approve')
                                                <form method="POST"
                                                    action="{{ route('verifikasi.items.approve', [$application, $item]) }}"
                                                    class="d-inline js-confirm-approve" data-label="{{ $panelTitle }}">
                                                    @csrf
                                                    <button type="submit" class="btn-undo" title="Ubah menjadi Setuju">
                                                        <i class="flaticon2-check-mark icon-xs"></i> Setujui
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </div>

                                {{-- Rejection Note --}}
                                @if ($item->note)
                                    <div class="rejection-note">
                                        <div class="rejection-note-icon">
                                            <i class="flaticon2-delete"></i>
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
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="verif-section-divider">
                                                    <i class="flaticon2-layers icon-sm mr-2 text-primary"></i>
                                                    <span>{{ $row['label'] }}</span>
                                                </div>
                                            </div>
                                        @elseif (($row['type'] ?? null) === 'other_company_table')
                                            <div class="col-12 mb-4 js-revision-field"
                                                data-field="{{ $row['field'] ?? $row['label'] }}"
                                                data-label="{{ $row['label'] }}">
                                                <label
                                                    class="font-size-sm font-weight-bold text-muted">{{ $row['label'] }}</label>
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
                                                                <td colspan="3" class="text-center text-muted py-3">
                                                                    Tidak ada perusahaan lain milik pimpinan.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @elseif (($row['type'] ?? null) === 'product_table')
                                            <div class="col-12 mb-4">
                                                {{-- Title Sesuai Gambar --}}
                                                <div class="form-section-title mb-4">
                                                    {{ __('supplied_products_list') }}
                                                </div>

                                                <table class="table table-hover mb-0 w-100"
                                                    id="verificationProductTable">
                                                    <thead class="verif-table-head">
                                                        <tr>
                                                            <th style="width: 28%; min-width: 180px;">
                                                                {{ __('product') }}</th>
                                                            <th style="width: 22%; min-width: 150px;">
                                                                {{ __('manufacturer') }} / {{ __('origin') }}</th>
                                                            <th style="width: 18%; min-width: 130px;">
                                                                {{ __('supply_chain') }}</th>
                                                            <th style="width: 32%; min-width: 280px;" class="text-left">
                                                                {{ __('documents_and_certifications') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse (($row['value'] ?? []) as $product)
                                                            <tr class="js-revision-field align-middle"
                                                                data-field="{{ $product['field'] ?? 'products.' . ($product['erp'] ?? $loop->iteration) }}"
                                                                data-label="{{ $product['label'] ?? ($product['product'] ?? 'Produk') }}">
                                                                {{-- Kolom 1: Product --}}
                                                                <td class="align-middle">
                                                                    <div class="font-weight-bolder text-dark font-size-xs"
                                                                        style="letter-spacing: 0.2px; line-height: 1.4;">
                                                                        {{ strtoupper($product['product'] ?? '-') }}
                                                                    </div>
                                                                </td>

                                                                {{-- Kolom 2: Manufacturer / Origin --}}
                                                                <td class="align-middle">
                                                                    <div
                                                                        class="font-weight-bold text-dark font-size-xs mb-1">
                                                                        {{ strtoupper($product['manufaktur'] ?? '-') }}
                                                                    </div>
                                                                    @if (!empty($product['negara']) && $product['negara'] !== '-')
                                                                        <div class="text-muted font-size-xs d-flex align-items-center"
                                                                            style="gap: 4px; color: #7e8299;">
                                                                            <i class="fas fa-map-marker-alt"
                                                                                style="font-size: 0.72rem; color: #7e8299;"></i>
                                                                            <span>{{ $product['negara'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                </td>

                                                                {{-- Kolom 3: Supply Chain --}}
                                                                <td class="align-middle">
                                                                    @php
                                                                        $supplyChainMap = [
                                                                            'Manufaktur' => __('manufacturer'),
                                                                            'Distributor Pertama' => __(
                                                                                'first_distributor',
                                                                            ),
                                                                            'Distributor Kedua' => __(
                                                                                'second_distributor',
                                                                            ),
                                                                            'Trader' => __('trader'),
                                                                            'Repacker' => __('repacker'),
                                                                        ];
                                                                        $supplyChainDisplay =
                                                                            $supplyChainMap[
                                                                                $product['rantai_pasok'] ?? ''
                                                                            ] ??
                                                                            ($product['rantai_pasok'] ?? '-');
                                                                    @endphp
                                                                    <div
                                                                        class="text-dark font-size-sm font-weight-medium">
                                                                        {{ $supplyChainDisplay }}
                                                                    </div>
                                                                </td>

                                                                {{-- Kolom 4: Documents & Certifications (2x3 Grid) --}}
                                                                <td class="align-middle">
                                                                    @php
                                                                        $allDocs = [
                                                                            [
                                                                                'key' => 'surat',
                                                                                'label' => 'Agency Letter',
                                                                                'icon' => 'fas fa-file-alt',
                                                                                'class' => 'btn-light-primary',
                                                                                'title' =>
                                                                                    'Surat Keagenan (Agency Letter)',
                                                                            ],
                                                                            [
                                                                                'key' => 'gmp',
                                                                                'label' => 'GMP',
                                                                                'icon' => 'fas fa-microscope',
                                                                                'class' => 'btn-light-info',
                                                                                'title' => 'Sertifikat GMP',
                                                                            ],
                                                                            [
                                                                                'key' => 'tkdn',
                                                                                'label' => 'TKDN',
                                                                                'icon' => 'fas fa-cogs',
                                                                                'class' => 'btn-light-dark',
                                                                                'title' => 'Sertifikat TKDN',
                                                                            ],
                                                                            [
                                                                                'key' => 'sni',
                                                                                'label' => 'SNI',
                                                                                'icon' => 'fas fa-award',
                                                                                'class' => 'btn-light-warning',
                                                                                'title' => 'Sertifikat SNI',
                                                                            ],
                                                                            [
                                                                                'key' => 'halal',
                                                                                'label' => 'Halal',
                                                                                'icon' => 'fas fa-leaf',
                                                                                'class' => 'btn-light-success',
                                                                                'title' => 'Sertifikat Halal',
                                                                            ],
                                                                            [
                                                                                'key' => 'bse_tse',
                                                                                'label' => 'BSE/TSE',
                                                                                'icon' => 'fas fa-heartbeat',
                                                                                'class' => 'btn-light-danger',
                                                                                'title' => 'Dokumen BSE/TSE',
                                                                            ],
                                                                        ];
                                                                    @endphp
                                                                    <div class="verif-doc-grid">
                                                                        @foreach ($allDocs as $doc)
                                                                            @php
                                                                                $hasDoc = !empty($product[$doc['key']]);
                                                                                $url = $hasDoc
                                                                                    ? $product[$doc['key']]
                                                                                    : null;
                                                                            @endphp
                                                                            @if ($hasDoc)
                                                                                <button type="button"
                                                                                    class="btn btn-xs {{ $doc['class'] }} font-weight-bold btn-preview-doc"
                                                                                    data-url="{{ $url }}"
                                                                                    data-title="{{ $doc['title'] }} - {{ $product['product'] ?? 'Produk' }}"
                                                                                    title="Lihat {{ $doc['title'] }}">
                                                                                    <i
                                                                                        class="{{ $doc['icon'] }} mr-1" style="font-size: 0.72rem;"></i>{{ $doc['label'] }}
                                                                                </button>
                                                                            @else
                                                                                <button type="button"
                                                                                    class="btn btn-xs {{ $doc['class'] }} font-weight-bold btn-preview-doc disabled"
                                                                                    disabled
                                                                                    title="{{ $doc['title'] }} (Belum dilampirkan)">
                                                                                    <i
                                                                                        class="{{ $doc['icon'] }} mr-1" style="font-size: 0.72rem;"></i>{{ $doc['label'] }}
                                                                                </button>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4"
                                                                    class="text-center text-muted py-5">
                                                                    Belum ada produk yang dipilih.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @elseif (($row['type'] ?? null) === 'iso_files_table')
                                            <div class="col-md-6 mb-3 js-revision-field"
                                                data-field="{{ $row['field'] ?? 'iso_files' }}"
                                                data-label="{{ $row['label'] }}">
                                                <div class="verif-field-box">
                                                    <div class="verif-field-label">
                                                        {{ $row['label'] }}
                                                    </div>
                                                    <div class="verif-field-value">
                                                        @if (empty($row['value']) || (is_object($row['value']) && $row['value']->isEmpty()))
                                                            <div class="verif-empty-doc">
                                                                <i class="far fa-file-alt text-muted mr-1"></i>
                                                                <span class="text-muted font-size-sm">Tidak
                                                                    dilampirkan</span>
                                                            </div>
                                                        @else
                                                            @php
                                                                $isoDocs =
                                                                    $row['value'] instanceof
                                                                    \Illuminate\Support\Collection
                                                                        ? $row['value']->all()
                                                                        : (array) $row['value'];
                                                            @endphp
                                                            <div class="d-flex flex-wrap mt-1" style="gap: 0.5rem;">
                                                                @foreach ($isoDocs as $doc)
                                                                    @php
                                                                        $docName = data_get(
                                                                            $doc,
                                                                            'original_name',
                                                                            'Dokumen ISO',
                                                                        );
                                                                        $docUrl = data_get($doc, 'url');
                                                                    @endphp
                                                                    @if ($docUrl)
                                                                        <button type="button"
                                                                            class="btn btn-xs btn-light-primary font-weight-bold btn-preview-doc"
                                                                            data-url="{{ $docUrl }}"
                                                                            data-title="{{ $docName }}"
                                                                            title="Lihat {{ $docName }}"
                                                                            style="padding: 0.35rem 0.85rem; border-radius: 6px; font-size: 0.8rem; gap: 6px;">
                                                                            <i class="far fa-eye"
                                                                                style="font-size: 0.82rem;"></i>
                                                                            <span>{{ $docName }}</span>
                                                                        </button>
                                                                    @else
                                                                        <span class="text-muted font-size-sm">
                                                                            <i class="far fa-file-alt mr-1"></i>
                                                                            {{ $docName }} (tidak tersedia)
                                                                        </span>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-6 mb-3 js-revision-field"
                                                data-field="{{ $row['field'] ?? $row['label'] }}"
                                                data-label="{{ $row['label'] }}">
                                                <div class="verif-field-box">
                                                    <div class="verif-field-label">
                                                        {{ $row['label'] }}
                                                    </div>
                                                    <div class="verif-field-value">
                                                        @if (!empty($row['url']))
                                                            <div class="mt-1">
                                                                <button type="button"
                                                                    class="btn btn-xs btn-light-primary font-weight-bold btn-preview-doc"
                                                                    data-url="{{ $row['url'] }}"
                                                                    data-title="{{ $row['label'] ?? 'Dokumen' }}"
                                                                    title="Lihat {{ $row['label'] ?? 'Dokumen' }}"
                                                                    style="padding: 0.35rem 0.85rem; border-radius: 6px; font-size: 0.8rem; gap: 6px;">
                                                                    <i class="far fa-eye"
                                                                        style="font-size: 0.82rem;"></i>
                                                                    <span>Lihat Dokumen</span>
                                                                </button>
                                                            </div>
                                                        @elseif (
                                                            !empty($row['is_document']) ||
                                                                \Illuminate\Support\Str::startsWith($row['field'] ?? '', 'dok_') ||
                                                                \Illuminate\Support\Str::contains($row['field'] ?? '', ['_document', '_file', '_cert']))
                                                            <div class="verif-empty-doc">
                                                                <i class="far fa-file-alt text-muted mr-1"></i>
                                                                <span class="text-muted font-size-sm">Tidak
                                                                    dilampirkan</span>
                                                            </div>
                                                        @elseif (!empty($row['value']) && $row['value'] !== '-')
                                                            @if (strlen($row['value']) > 100)
                                                                <div class="verif-text-multiline">
                                                                    {!! nl2br(e(strip_tags($row['value']))) !!}</div>
                                                            @else
                                                                <div class="verif-text-single">{!! strip_tags($row['value']) !!}
                                                                </div>
                                                            @endif
                                                        @else
                                                            <span
                                                                class="text-muted font-size-sm font-weight-bold">—</span>
                                                        @endif
                                                    </div>
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
                    scrollX: false,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, 'Semua']
                    ],
                    order: [],
                    columnDefs: [{
                        targets: 3,
                        orderable: false,
                        searchable: false
                    }]
                });
            }
        });
    </script>
@endpush
