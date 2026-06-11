<?php

namespace App\Http\Controllers;

use App\Models\VendorApplication;
use App\Models\VendorApplicationVerificationItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProcurementVerificationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeProcurementAccess();

        $status = $request->input('status', 'all');
        $search = $request->input('q');

        $query = VendorApplication::with(['user', 'general', 'categories', 'verifier'])
            ->withCount([
                'verificationItems as verification_items_count',
                'verificationItems as verification_pending_count' => function ($itemQuery) {
                    $itemQuery->where('status', VendorApplicationVerificationItem::STATUS_PENDING);
                },
                'verificationItems as verification_approved_count' => function ($itemQuery) {
                    $itemQuery->where('status', VendorApplicationVerificationItem::STATUS_APPROVED);
                },
                'verificationItems as verification_rejected_count' => function ($itemQuery) {
                    $itemQuery->where('status', VendorApplicationVerificationItem::STATUS_REJECTED);
                },
            ])
            ->whereIn('status', [
                VendorApplication::STATUS_SUBMITTED,
                VendorApplication::STATUS_NEED_REVISION,
                VendorApplication::STATUS_VERIFIED,
            ])
            ->latest('submitted_at')
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($inner) use ($search) {
                $inner->where('application_number', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('general', function ($generalQuery) use ($search) {
                        $generalQuery->where('nama_perusahaan', 'like', '%' . $search . '%')
                            ->orWhere('npwp', 'like', '%' . $search . '%')
                            ->orWhere('nib', 'like', '%' . $search . '%');
                    });
            });
        }

        $applications = $query->paginate(10)->withQueryString();

        return view('admin.pengadaan.permohonan.index', [
            'applications' => $applications,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function show(VendorApplication $application)
    {
        $this->authorizeProcurementAccess();

        $application->load([
            'user',
            'general',
            'products',
            'documents',
            'categories',
            'verifier',
            'specBaku',
            'specVaria',
            'specTrans',
            'specKontraktor',
            'specPengujian',
            'specFacility',
            'specPelatihan',
            'specAgency',
            'verificationItems.verifier',
        ]);

        $this->syncVerificationItems($application);
        $application->load('verificationItems.verifier');

        return view('admin.pengadaan.permohonan.show', [
            'application' => $application,
            'deadline' => $this->verificationDeadline($application),
            'remainingBusinessDays' => $this->remainingBusinessDays($application),
            'verificationSections' => $this->verificationSections($application),
            'verificationItemRows' => $this->verificationItemRows($application),
            'specificCategoryHeaders' => $this->specificCategoryHeaders($application),
        ]);
    }

    public function verify(Request $request, VendorApplication $application)
    {
        $this->authorizeProcurementAccess();

        if ($application->status !== VendorApplication::STATUS_SUBMITTED) {
            throw ValidationException::withMessages([
                'application_id' => 'Hanya permohonan dengan status submitted yang dapat diverifikasi.',
            ]);
        }

        $application->load(['categories', 'verificationItems']);
        $this->syncVerificationItems($application);
        $items = $application->verificationItems()->get();

        if ($items->isEmpty() || !$items->every(function ($item) {
            return $item->status === VendorApplicationVerificationItem::STATUS_APPROVED;
        })) {
            throw ValidationException::withMessages([
                'application_id' => 'Semua item verifikasi harus disetujui terlebih dahulu.',
            ]);
        }

        $application->update([
            'status' => VendorApplication::STATUS_VERIFIED,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'admin_note' => $request->input('admin_note'),
            'revision_notes' => null,
            'auto_verified' => false,
        ]);

        return redirect()
            ->route('pengadaan.permohonan.show', $application)
            ->with('success', 'Permohonan berhasil diverifikasi lengkap.');
    }

    public function requestRevision(Request $request, VendorApplication $application)
    {
        $this->authorizeProcurementAccess();

        if (!in_array($application->status, [
            VendorApplication::STATUS_SUBMITTED,
            VendorApplication::STATUS_VERIFIED,
        ])) {
            throw ValidationException::withMessages([
                'application_id' => 'Permohonan ini tidak dapat diminta revisi dari status saat ini.',
            ]);
        }

        $data = $request->validate([
            'admin_note' => 'required|string|max:2000',
            'revision_fields' => 'nullable|array',
            'revision_fields.*' => 'nullable|string|max:255',
            'revision_notes' => 'nullable|array',
            'revision_notes.*' => 'nullable|string|max:1000',
        ]);

        $revisionNotes = $this->normalizeRevisionNotes(
            $request->input('revision_fields', []),
            $request->input('revision_notes', [])
        );

        $application->update([
            'status' => VendorApplication::STATUS_NEED_REVISION,
            'verified_at' => null,
            'verified_by' => Auth::id(),
            'admin_note' => $data['admin_note'],
            'revision_notes' => $revisionNotes,
            'auto_verified' => false,
        ]);

        return redirect()
            ->route('pengadaan.permohonan.show', $application)
            ->with('success', 'Permohonan dikembalikan ke vendor untuk revisi.');
    }

    public function approveItem(VendorApplication $application, VendorApplicationVerificationItem $item)
    {
        $this->authorizeProcurementAccess();
        $this->ensureItemBelongsToApplication($application, $item);
        $this->ensureApplicationCanBeVerified($application);

        $item->update([
            'status' => VendorApplicationVerificationItem::STATUS_APPROVED,
            'note' => null,
            'revision_fields' => null,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        $this->refreshApplicationVerificationStatus($application);

        return redirect()
            ->route('pengadaan.permohonan.show', $application)
            ->with('success', $item->item_label . ' disetujui.');
    }

    public function rejectItem(Request $request, VendorApplication $application, VendorApplicationVerificationItem $item)
    {
        $this->authorizeProcurementAccess();
        $this->ensureItemBelongsToApplication($application, $item);
        $this->ensureApplicationCanBeVerified($application);

        $data = $request->validate([
            'note' => 'required|string|max:1000',
            'revision_fields' => 'required|array|min:1',
            'revision_fields.*' => 'required|string|max:255',
            'revision_labels' => 'required|array|min:1',
            'revision_labels.*' => 'required|string|max:255',
        ]);

        $revisionFields = collect($data['revision_fields'])
            ->values()
            ->map(function ($field) use ($data) {
                return [
                    'field' => $field,
                    'label' => $data['revision_labels'][$field] ?? $field,
                ];
            })
            ->unique('field')
            ->values()
            ->all();

        $item->update([
            'status' => VendorApplicationVerificationItem::STATUS_REJECTED,
            'note' => $data['note'],
            'revision_fields' => $revisionFields,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        $this->refreshApplicationVerificationStatus($application);

        return redirect()
            ->route('pengadaan.permohonan.show', $application)
            ->with('success', $item->item_label . ' ditandai tidak setuju.');
    }

    private function authorizeProcurementAccess(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasAnyRole(['Super Admin', 'Admin IT', 'Procurement', 'Verifikator']),
            403
        );
    }

    private function ensureItemBelongsToApplication(VendorApplication $application, VendorApplicationVerificationItem $item): void
    {
        abort_unless((int) $item->application_id === (int) $application->id, 404);
    }

    private function ensureApplicationCanBeVerified(VendorApplication $application): void
    {
        if (!in_array($application->status, [
            VendorApplication::STATUS_SUBMITTED,
            VendorApplication::STATUS_NEED_REVISION,
            VendorApplication::STATUS_VERIFIED,
        ])) {
            throw ValidationException::withMessages([
                'application_id' => 'Permohonan ini tidak dapat diverifikasi dari status saat ini.',
            ]);
        }
    }

    private function syncVerificationItems(VendorApplication $application): void
    {
        $definitions = $this->verificationItemDefinitions($application);
        $keys = collect($definitions)->pluck('item_key')->all();

        $application->verificationItems()
            ->whereNotIn('item_key', $keys)
            ->delete();

        foreach ($definitions as $definition) {
            $item = VendorApplicationVerificationItem::firstOrNew([
                'application_id' => $application->id,
                'item_key' => $definition['item_key'],
            ]);

            $item->section_key = $definition['section_key'];
            $item->section_label = $definition['section_label'];
            $item->item_label = $definition['item_label'];

            if (!$item->exists) {
                $item->status = VendorApplicationVerificationItem::STATUS_PENDING;
            }

            $item->save();
        }
    }

    private function verificationSections(VendorApplication $application): array
    {
        $items = $application->verificationItems
            ->sortBy('id')
            ->groupBy('section_key');

        $sections = [];

        foreach ($this->sectionLabels() as $key => $label) {
            if (!$items->has($key)) {
                continue;
            }

            $sectionItems = $items->get($key)->values();
            $approved = $sectionItems->where('status', VendorApplicationVerificationItem::STATUS_APPROVED)->count();
            $rejected = $sectionItems->where('status', VendorApplicationVerificationItem::STATUS_REJECTED)->count();

            $sections[$key] = [
                'key' => $key,
                'label' => $label,
                'items' => $sectionItems,
                'approved' => $approved,
                'rejected' => $rejected,
                'pending' => $sectionItems->count() - $approved - $rejected,
            ];
        }

        return $sections;
    }

    private function verificationItemDefinitions(VendorApplication $application): array
    {
        $definitions = [
            [
                'section_key' => 'profile',
                'section_label' => 'Profil Perusahaan',
                'item_key' => 'profile_company',
                'item_label' => 'Data Umum Perusahaan',
            ],
            [
                'section_key' => 'profile',
                'section_label' => 'Profil Perusahaan',
                'item_key' => 'profile_pic',
                'item_label' => 'Narahubung / PIC',
            ],
            [
                'section_key' => 'structure',
                'section_label' => 'Struktur Perusahaan',
                'item_key' => 'company_structure',
                'item_label' => 'Struktur Organisasi',
            ],
            [
                'section_key' => 'legal',
                'section_label' => 'Data Legalitas',
                'item_key' => 'legal_nib_npwp',
                'item_label' => 'TDP/NIB & NPWP',
            ],
            [
                'section_key' => 'goods',
                'section_label' => 'Barang / Jasa',
                'item_key' => 'goods_products',
                'item_label' => 'Daftar Barang/Jasa',
            ],
            [
                'section_key' => 'certificate',
                'section_label' => 'Sertifikat',
                'item_key' => 'certificates',
                'item_label' => 'Sertifikat & Komitmen Mutu',
            ],
            [
                'section_key' => 'tax_finance',
                'section_label' => 'Pajak & Keuangan',
                'item_key' => 'tax_finance',
                'item_label' => 'Pajak dan Rekening Bank',
            ],
        ];

        foreach ($application->categories as $category) {
            foreach ($this->specificVerificationItems((int) $category->category_id) as $item) {
                $definitions[] = $item;
            }
        }

        return $definitions;
    }

    private function specificVerificationItems(int $categoryId): array
    {
        $base = [
            'section_key' => 'specific',
            'section_label' => 'Persyaratan Khusus',
        ];

        $items = [
            1 => [
                ['item_key' => 'specific_baku', 'item_label' => 'Bahan Baku, Bahan Kemas, Produk Jadi & Alkes'],
            ],
            2 => [
                ['item_key' => 'specific_varia', 'item_label' => 'Varia Teknik dan Umum, Reagen, Barang Investasi'],
            ],
            3 => [
                ['item_key' => 'specific_trans', 'item_label' => 'Jasa Transporter, Forwarder & PPJK'],
            ],
            4 => [
                ['item_key' => 'specific_kontraktor', 'item_label' => 'Jasa Kontraktor, Perbaikan & Pemeliharaan'],
            ],
            5 => [
                ['item_key' => 'specific_pengujian', 'item_label' => 'Jasa Pengujian Laboratorium, Kalibrasi, Radiasi & Sertifikasi'],
            ],
            6 => [
                ['item_key' => 'specific_facility', 'item_label' => 'Jasa Facility Service, Sewa, Security, Katering & MCU'],
            ],
            7 => [
                ['item_key' => 'specific_pelatihan', 'item_label' => 'Jasa Pelatihan,Konsultan, Notaris & Alih Daya Tenaga Kerja'],
            ],
            8 => [
                ['item_key' => 'specific_agency', 'item_label' => 'Jasa Agency Advertising'],
            ],
        ][$categoryId] ?? [];

        return collect($items)
            ->map(function ($item) use ($base) {
                return array_merge($base, $item);
            })
            ->all();
    }

    private function sectionLabels(): array
    {
        return [
            'profile' => 'Profil Perusahaan',
            'structure' => 'Struktur Perusahaan',
            'legal' => 'Data Legalitas',
            'goods' => 'Barang / Jasa',
            'certificate' => 'Sertifikat',
            'tax_finance' => 'Pajak & Keuangan',
            'specific' => 'Persyaratan Khusus',
        ];
    }

    private function categoryTitles(): array
    {
        return [
            1 => 'Bahan Baku, Bahan Kemas, Produk Jadi & Alkes',
            2 => 'Varia Teknik dan Umum, Reagen, Barang Investasi',
            3 => 'Jasa Transporter, Forwarder & PPJK',
            4 => 'Jasa Kontraktor, Perbaikan & Pemeliharaan',
            5 => 'Jasa Pengujian Laboratorium, Kalibrasi, Radiasi & Sertifikasi',
            6 => 'Jasa Facility Service, Sewa, Security, Katering & MCU',
            7 => 'Jasa Pelatihan,Konsultan, Notaris & Alih Daya Tenaga Kerja',
            8 => 'Jasa Agency Advertising',
        ];
    }

    private function specificCategoryHeaders(VendorApplication $application): array
    {
        $headers = [];
        $titles = $this->categoryTitles();

        foreach ($application->categories as $category) {
            $title = $titles[(int) $category->category_id] ?? $category->category_label;

            foreach ($this->specificVerificationItems((int) $category->category_id) as $item) {
                $headers[$item['item_key']] = $title;
            }
        }

        return $headers;
    }

    private function verificationItemRows(VendorApplication $application): array
    {
        $general = $application->general;

        return [
            'profile_company' => [
                ['field' => 'nama_perusahaan', 'label' => 'Nama Perusahaan', 'value' => data_get($general, 'nama_perusahaan')],
                ['field' => 'website', 'label' => 'Situs Resmi Perusahaan', 'value' => data_get($general, 'website')],
                ['field' => 'alamat_perusahaan', 'label' => 'Alamat Lengkap Perusahaan', 'value' => data_get($general, 'alamat_perusahaan')],
                ['field' => 'email_perusahaan', 'label' => 'Email Perusahaan', 'value' => data_get($general, 'email_perusahaan')],
                ['field' => 'telepon_perusahaan', 'label' => 'Nomor Telepon Perusahaan', 'value' => data_get($general, 'telepon_perusahaan')],
            ],
            'profile_pic' => [
                ['field' => 'pic_nama', 'label' => 'Nama PIC', 'value' => data_get($general, 'pic_nama')],
                ['field' => 'pic_email', 'label' => 'Email PIC', 'value' => data_get($general, 'pic_email')],
                ['field' => 'pic_telepon', 'label' => 'Nomor HP PIC', 'value' => data_get($general, 'pic_telepon')],
                [
                    'field' => 'other_companies',
                    'label' => 'Perusahaan Lain Milik Pimpinan',
                    'type' => 'other_company_table',
                    'value' => data_get($general, 'has_other_company') === 'yes' ? (data_get($general, 'other_companies', []) ?: []) : [],
                ],
            ],
            'company_structure' => [
                $this->documentRow($application, 'dok_struktur_org', 'Struktur Organisasi'),
                $this->documentRow($application, 'dok_company_profile', 'Company Profile'),
            ],
            'legal_nib_npwp' => [
                ['field' => 'nib', 'label' => 'Nomor NIB', 'value' => data_get($general, 'nib')],
                $this->documentRow($application, 'dok_nib', 'Dokumen NIB'),
                ['field' => 'npwp', 'label' => 'Nomor NPWP', 'value' => data_get($general, 'npwp')],
                $this->documentRow($application, 'dok_npwp', 'Dokumen NPWP'),
                $this->documentRow($application, 'dok_akte_pendirian', 'Akte Pendirian'),
                $this->documentRow($application, 'dok_akte_direksi', 'Akte Pengangkatan Direksi'),
                $this->documentRow($application, 'dok_ktp_pj', 'KTP Penanggung Jawab'),
                $this->documentRow($application, 'dok_pernyataan_keaslian', 'Surat Keaslian Dokumen'),
                $this->documentRow($application, 'dok_pakta_integritas', 'Pakta Integritas'),
                $this->documentRow($application, 'dok_bebas_perkara', 'Surat Pernyataan Bebas Perkara/Blacklist'),
            ],
            'goods_products' => $this->productRows($application),
            'certificates' => [
                ['label' => 'Sertifikat ISO', 'value' => $this->formatIsoCertificates($general)],
                ['label' => 'Komitmen Kualitas, Lingkungan & K3', 'value' => $this->yesNo(data_get($general, 'komitmen_kualitas'))],
                ['label' => 'Detail Komitmen', 'value' => data_get($general, 'komitmen_kualitas_detail')],
                ['label' => 'Sertifikasi Halal BPJPH', 'value' => $this->yesNo(data_get($general, 'sertifikat_halal'))],
                $this->documentRow($application, 'dok_sertifikat_halal', 'Dokumen Sertifikat Halal'),
            ],
            'tax_finance' => [
                ['field' => 'payment_term', 'label' => 'Payment Term', 'value' => $this->formatPaymentTerm($general)],
                ['field' => 'pemegang_rekening', 'label' => 'Pemegang Rekening', 'value' => data_get($general, 'pemegang_rekening')],
                ['field' => 'nomor_rekening', 'label' => 'Nomor Rekening', 'value' => data_get($general, 'nomor_rekening')],
                ['field' => 'nama_bank', 'label' => 'Nama Bank', 'value' => data_get($general, 'nama_bank')],
                ['field' => 'swift_code', 'label' => 'SWIFT Code', 'value' => data_get($general, 'swift_code')],
                ['field' => 'alamat_bank', 'label' => 'Alamat Bank', 'value' => data_get($general, 'alamat_bank')],
                ['field' => 'status_perusahaan', 'label' => 'Status Perusahaan', 'value' => data_get($general, 'status_perusahaan')],
                ['field' => 'status_pajak', 'label' => 'Status Pajak', 'value' => data_get($general, 'status_pajak')],
                $this->documentRow($application, 'dok_sppkp', 'Surat Pengukuhan PKP'),
                ['field' => 'jenis_modal', 'label' => 'Jenis Penanaman Modal', 'value' => data_get($general, 'jenis_modal')],
                ['field' => 'skala_perusahaan', 'label' => 'Skala Perusahaan', 'value' => data_get($general, 'skala_perusahaan')],
                ['field' => 'kbli', 'label' => 'KBLI', 'value' => data_get($general, 'kbli')],
            ],
        ] + $this->specificItemRows($application);
    }

    private function productRows(VendorApplication $application): array
    {
        $products = $application->products->map(function ($product) {
            $erp = $product->erp_product_id ?: $product->id;
            $productName = $product->product_name ?: 'Produk';

            return [
                'field' => 'products.' . $erp,
                'label' => trim($productName . ' (' . $erp . ')'),
                'product' => $productName,
                'erp' => $product->erp_product_id ?: '-',
                'manufaktur' => $product->manufaktur ?: '-',
                'rantai_pasok' => $product->rantai_pasok ?: '-',
                'surat' => $this->storageUrl($product->file_surat_path),
                'tkdn' => $product->has_tkdn === 'yes' ? ($product->tkdn_value ?: '-') : 'Tidak',
                'sni' => $product->has_sni === 'yes' ? ($product->sni_number ?: '-') : 'Tidak',
                'halal' => $product->has_halal === 'yes' ? ($product->halal_number ?: '-') : 'Tidak',
            ];
        })->values()->all();

        return [[
            'label' => 'Daftar Produk yang Disuplai',
            'type' => 'product_table',
            'value' => $products,
        ]];
    }

    private function specificItemRows(VendorApplication $application): array
    {
        $rows = [];

        if ($application->specBaku) {
            $spec = $application->specBaku;
            $rows['specific_baku'] = [
                ['type' => 'section_title', 'label' => 'Data Produsen/Agen'],
                ['field' => 'q1_is_manufacturer', 'label' => 'Pemasok sebagai produsen', 'value' => $this->yesNo($spec->q1_is_manufacturer)],
                ['field' => 'q1_manufacturer_name', 'label' => 'Nama perusahaan produsen', 'value' => $spec->q1_manufacturer_name],
                ['field' => 'q2_is_sole_agent', 'label' => 'Agen tunggal', 'value' => $this->yesNo($spec->q2_is_sole_agent)],
                $this->fileRow('q2_auth_letter', 'Surat penunjukan agen', $spec->q2_auth_letter),
                ['type' => 'section_title', 'label' => 'Gudang & Transportasi'],
                ['field' => 'q3_transportation', 'label' => 'Angkutan pengiriman', 'value' => $spec->q3_transportation],
                ['field' => 'q3_3pl_name', 'label' => 'Nama perusahaan 3PL', 'value' => $spec->q3_3pl_name],
                ['field' => 'q4_has_warehouse', 'label' => 'Memiliki gudang sendiri', 'value' => $this->yesNo($spec->q4_has_warehouse)],
                ['field' => 'q4_warehouse_address', 'label' => 'Alamat gudang', 'value' => $spec->q4_warehouse_address],
                ['field' => 'q4_warehouse_condition', 'label' => 'Kondisi gudang', 'value' => $spec->q4_warehouse_condition],
                ['type' => 'section_title', 'label' => 'Sertifikat CDOB/SIPA'],
                ['field' => 'q5_num', 'label' => 'No. Sertifikat CDOB', 'value' => $spec->q5_num],
                ['field' => 'q5_date', 'label' => 'Masa Berlaku CDOB', 'value' => $spec->q5_date],
                ['field' => 'q6_name', 'label' => 'Nama APJ', 'value' => $spec->q6_name],
                ['field' => 'q6_num', 'label' => 'No. SIPA', 'value' => $spec->q6_num],
                ['field' => 'q6_date', 'label' => 'Masa Berlaku SIPA', 'value' => $spec->q6_date],
                ['type' => 'section_title', 'label' => 'Peralatan & Import'],
                ['field' => 'q7_equipments', 'label' => 'Daftar Peralatan', 'value' => $this->formatList($spec->q7_equipments ?: [], ['jenis' => 'Jenis', 'jml' => 'Jumlah', 'kapasitas' => 'Kapasitas', 'merk' => 'Merk', 'tahun' => 'Tahun'])],
                ['field' => 'q8_is_import', 'label' => 'Barang import', 'value' => $this->yesNo($spec->q8_is_import)],
                ['field' => 'q8_country_name', 'label' => 'Negara import', 'value' => $spec->q8_country_name],
            ];
        }

        if ($application->specVaria) {
            $spec = $application->specVaria;
            $rows['specific_varia'] = [
                ['type' => 'section_title', 'label' => 'Agen/Izin Khusus'],
                ['field' => 'v1_is_sole_agent', 'label' => 'Agen tunggal', 'value' => $this->yesNo($spec->v1_is_sole_agent)],
                $this->fileRow('v1_auth_letter', 'Surat penunjukan agen', $spec->v1_auth_letter),
                ['field' => 'v2_has_special_license', 'label' => 'Memiliki izin khusus', 'value' => $this->yesNo($spec->v2_has_special_license)],
                $this->fileRow('v2_license_file', 'File izin khusus', $spec->v2_license_file),
                ['field' => 'v3_iso_b3', 'label' => 'ISO/Izin B3', 'value' => $this->yesNo($spec->v3_iso_b3)],
                ['type' => 'section_title', 'label' => 'Pengiriman & KIR'],
                ['field' => 'v4_driver_training', 'label' => 'Training driver', 'value' => $this->yesNo($spec->v4_driver_training)],
                ['field' => 'v5_valid_license', 'label' => 'Izin valid saat pengiriman', 'value' => $this->yesNo($spec->v5_valid_license)],
                ['field' => 'v6_kir', 'label' => 'Surat KIR', 'value' => $this->yesNo($spec->v6_kir)],
                $this->fileRow('v6_kir_file', 'File KIR', $spec->v6_kir_file),
            ];
        }

        if ($application->specTrans) {
            $spec = $application->specTrans;
            $rows['specific_trans'] = [
                ['type' => 'section_title', 'label' => 'Safety & Armada'],
                ['field' => 't1_k3_commitment', 'label' => 'Komitmen K3', 'value' => $this->yesNo($spec->t1_k3_commitment)],
                $this->fileRow('t1_safety_file', 'File safety', $spec->t1_safety_file),
                ['field' => 't4_is_insured', 'label' => 'Diasuransikan', 'value' => $this->yesNo($spec->t4_is_insured)],
                ['field' => 't4_insurance_pct', 'label' => 'Persentase asuransi', 'value' => $spec->t4_insurance_pct],
                ['field' => 't2_truck_type', 'label' => 'Tipe truk', 'value' => $spec->t2_truck_type],
                ['field' => 't3_has_logger', 'label' => 'Data logger', 'value' => $this->yesNo($spec->t3_has_logger)],
                ['type' => 'section_title', 'label' => 'Operasional & Layanan'],
                ['field' => 't5_own_fleet_outer_island', 'label' => 'Armada luar pulau milik sendiri', 'value' => $this->yesNo($spec->t5_own_fleet_outer_island)],
                ['field' => 't6_3pl_darat', 'label' => '3PL Darat', 'value' => $spec->t6_3pl_darat],
                ['field' => 't6_3pl_laut', 'label' => '3PL Laut', 'value' => $spec->t6_3pl_laut],
                ['field' => 't6_3pl_udara', 'label' => '3PL Udara', 'value' => $spec->t6_3pl_udara],
                ['field' => 't7_association', 'label' => 'Asosiasi', 'value' => $spec->t7_association],
                ['field' => 't8_customs_expert', 'label' => 'Ahli kepabeanan', 'value' => $spec->t8_customs_expert],
                $this->fileRow('t8_expert_cert', 'Sertifikat ahli', $spec->t8_expert_cert),
                ['field' => 't9_has_intl_affiliate', 'label' => 'Afiliasi internasional', 'value' => $this->yesNo($spec->t9_has_intl_affiliate)],
                ['field' => 't9_countries', 'label' => 'Negara afiliasi', 'value' => $spec->t9_countries],
                ['field' => 't10_other_services', 'label' => 'Layanan lainnya', 'value' => $spec->t10_other_services],
            ];
        }

        if ($application->specKontraktor) {
            $spec = $application->specKontraktor;
            $rows['specific_kontraktor'] = [
                ['type' => 'section_title', 'label' => 'SDM, K3, BPJS'],
                ['field' => 'k1_pro_staff', 'label' => 'Tenaga profesional', 'value' => $this->yesNo($spec->k1_pro_staff)],
                $this->fileRow('k1_cert_file', 'File sertifikasi tenaga ahli', $spec->k1_cert_file),
                ['field' => 'k2_safety_commitment', 'label' => 'Komitmen safety', 'value' => $this->yesNo($spec->k2_safety_commitment)],
                ['field' => 'k3_bpjs', 'label' => 'BPJS', 'value' => $this->yesNo($spec->k3_bpjs)],
                ['field' => 'k4_apd', 'label' => 'APD', 'value' => $this->yesNo($spec->k4_apd)],
                ['field' => 'k5_association', 'label' => 'Asosiasi', 'value' => $spec->k5_association],
                ['type' => 'section_title', 'label' => 'Peralatan Kerja'],
                ['field' => 'k6_equipments', 'label' => 'Daftar Peralatan', 'value' => $this->formatList($spec->k6_equipments ?: [], ['jenis' => 'Jenis', 'jml' => 'Jumlah', 'kapasitas' => 'Kapasitas', 'merk' => 'Merk', 'tahun' => 'Tahun'])],
            ];
        }

        if ($application->specPengujian) {
            $spec = $application->specPengujian;
            $rows['specific_pengujian'] = [
                ['type' => 'section_title', 'label' => 'Layanan & Scope'],
                ['field' => 'l1_services', 'label' => 'Layanan', 'value' => implode(', ', $spec->l1_services ?: [])],
                ['field' => 'l1_kalibrasi_scope', 'label' => 'Scope kalibrasi', 'value' => $spec->l1_kalibrasi_scope],
                ['type' => 'section_title', 'label' => 'Sertifikat Laboratorium'],
                ['field' => 'l2_selected_certs', 'label' => 'Sertifikat dipilih', 'value' => implode(', ', $spec->l2_selected_certs ?: [])],
                ['field' => 'l2_kan_no', 'label' => 'KAN', 'value' => trim(($spec->l2_kan_no ?: '-') . ' / ' . ($spec->l2_kan_date ?: '-'))],
                $this->fileRow('l2_kan_file', 'File KAN', $spec->l2_kan_file),
                ['field' => 'l2_cukb_no', 'label' => 'CUKB', 'value' => trim(($spec->l2_cukb_no ?: '-') . ' / ' . ($spec->l2_cukb_date ?: '-'))],
                $this->fileRow('l2_cukb_file', 'File CUKB', $spec->l2_cukb_file),
                ['field' => 'l2_iso17025_no', 'label' => 'ISO 17025', 'value' => trim(($spec->l2_iso17025_no ?: '-') . ' / ' . ($spec->l2_iso17025_date ?: '-'))],
                $this->fileRow('l2_iso17025_file', 'File ISO 17025', $spec->l2_iso17025_file),
                ['field' => 'l2_glp_no', 'label' => 'GLP', 'value' => trim(($spec->l2_glp_no ?: '-') . ' / ' . ($spec->l2_glp_date ?: '-'))],
                $this->fileRow('l2_glp_file', 'File GLP', $spec->l2_glp_file),
                ['field' => 'l2_bapeten_no', 'label' => 'BAPETEN', 'value' => trim(($spec->l2_bapeten_no ?: '-') . ' / ' . ($spec->l2_bapeten_date ?: '-'))],
                $this->fileRow('l2_bapeten_file', 'File BAPETEN', $spec->l2_bapeten_file),
                ['type' => 'section_title', 'label' => 'Principal/Agen'],
                ['field' => 'l3_is_agent', 'label' => 'Agen', 'value' => $this->yesNo($spec->l3_is_agent)],
                ['field' => 'l3_principal_name', 'label' => 'Nama Perusahaan Principal', 'value' => $spec->l3_principal_name],
            ];
        }

        if ($application->specFacility) {
            $spec = $application->specFacility;
            $rows['specific_facility'] = [
                ['type' => 'section_title', 'label' => 'Asosiasi, BPJS, Izin'],
                ['field' => 'f1_association', 'label' => 'Asosiasi', 'value' => $spec->f1_association],
                ['field' => 'f2_bpjs', 'label' => 'BPJS', 'value' => $this->yesNo($spec->f2_bpjs)],
                ['field' => 'f3_permenaker_ijin', 'label' => 'Izin Permenaker', 'value' => $spec->f3_permenaker_ijin],
                ['type' => 'section_title', 'label' => 'Sertifikasi & Operasional'],
                ['field' => 'f4_certs', 'label' => 'Sertifikasi', 'value' => $this->formatList($spec->f4_certs ?: [], ['type' => 'Tipe', 'name' => 'Nama', 'date' => 'Masa Berlaku'])],
                ['field' => 'f5_hygiene_guarantee', 'label' => 'Jaminan hygiene', 'value' => $this->yesNo($spec->f5_hygiene_guarantee)],
                ['field' => 'f6_sanitation_cert', 'label' => 'Sertifikat sanitasi', 'value' => $this->yesNo($spec->f6_sanitation_cert)],
                $this->fileRow('f6_file', 'File sanitasi', $spec->f6_file),
                ['field' => 'f7_kitchen_facility', 'label' => 'Fasilitas dapur', 'value' => $spec->f7_kitchen_facility],
                ['field' => 'f8_transport_facility', 'label' => 'Fasilitas transportasi', 'value' => $spec->f8_transport_facility],
            ];
        }

        if ($application->specPelatihan) {
            $spec = $application->specPelatihan;
            $rows['specific_pelatihan'] = [
                ['type' => 'section_title', 'label' => 'Asosiasi & Sertifikasi'],
                ['field' => 'g1_association', 'label' => 'Asosiasi', 'value' => $spec->g1_association],
                ['field' => 'g2_trainer_cert', 'label' => 'Sertifikat trainer', 'value' => $this->yesNo($spec->g2_trainer_cert)],
                ['field' => 'g2_cert_source', 'label' => 'Lembaga penerbit', 'value' => $spec->g2_cert_source],
                ['type' => 'section_title', 'label' => 'Izin & BPJS'],
                ['field' => 'g3_permits', 'label' => 'Izin', 'value' => $this->formatList($spec->g3_permits ?: [], ['desc' => 'Deskripsi', 'no' => 'Nomor', 'date' => 'Tanggal'])],
                ['field' => 'g4_labor_permit', 'label' => 'Izin operasional', 'value' => $spec->g4_labor_permit],
                ['field' => 'g5_bpjs', 'label' => 'BPJS', 'value' => $this->yesNo($spec->g5_bpjs)],
            ];
        }

        if ($application->specAgency) {
            $spec = $application->specAgency;
            $rows['specific_agency'] = [
                ['type' => 'section_title', 'label' => 'Asosiasi'],
                ['field' => 'h1_association', 'label' => 'Asosiasi', 'value' => $spec->h1_association],
                $this->fileRow('h1_association_file', 'Bukti asosiasi', $spec->h1_association_file),
                ['type' => 'section_title', 'label' => 'Spesialisasi & Pengalaman'],
                ['field' => 'h2_specialization', 'label' => 'Spesialisasi', 'value' => $spec->h2_specialization],
                ['field' => 'h3_project_experience', 'label' => 'Pengalaman proyek', 'value' => $spec->h3_project_experience],
            ];
        }

        return $rows;
    }

    private function allDocumentRows(VendorApplication $application): array
    {
        $labels = [
            'dok_nib' => 'NIB (Nomor Induk Berusaha)',
            'dok_npwp' => 'NPWP Perusahaan',
            'dok_company_profile' => 'Company Profile',
            'dok_struktur_org' => 'Struktur Organisasi',
            'dok_sertifikat_halal' => 'Sertifikat Halal (PBF)',
            'dok_akte_pendirian' => 'Akte Pendirian',
            'dok_akte_direksi' => 'Akte Pengangkatan Direksi',
            'dok_sppkp' => 'Surat Pengukuhan PKP',
            'dok_ktp_pj' => 'KTP Penanggung Jawab',
            'dok_pernyataan_keaslian' => 'Surat Keaslian Dokumen',
            'dok_pakta_integritas' => 'Pakta Integritas',
            'dok_bebas_perkara' => 'Surat Pernyataan Tidak Dalam Pengawasan Pengadilan dan atau Tidak Masuk Dalam Daftar Hitam',
        ];

        return collect($labels)
            ->map(function ($label, $field) use ($application) {
                return $this->documentRow($application, $field, $label);
            })
            ->values()
            ->all();
    }

    private function documentRow(VendorApplication $application, string $field, string $label): array
    {
        $document = $application->documents->firstWhere('field_name', $field);

        return [
            'field' => $field,
            'label' => $label,
            'value' => optional($document)->original_name,
            'url' => $document ? asset('storage/' . $document->file_path) : null,
        ];
    }

    private function fileRow(string $field, string $label, ?string $path): array
    {
        return [
            'field' => $field,
            'label' => $label,
            'value' => $this->fileName($path),
            'url' => $this->storageUrl($path),
        ];
    }

    private function storageUrl(?string $path): ?string
    {
        return $path ? asset('storage/' . $path) : null;
    }

    private function fileName(?string $path): ?string
    {
        return $path ? basename(str_replace('\\', '/', $path)) : null;
    }

    private function yesNo($value): ?string
    {
        if ($value === 'yes') {
            return 'Ya';
        }

        if ($value === 'no') {
            return 'Tidak';
        }

        return $value;
    }

    private function formatIsoCertificates($general): string
    {
        $certificates = collect((array) (data_get($general, 'iso_certificates', []) ?: []))
            ->map(function ($certificate) use ($general) {
                if ($certificate === 'other') {
                    return data_get($general, 'iso_other');
                }

                return $certificate;
            })
            ->filter()
            ->values();

        return $certificates->isNotEmpty() ? $certificates->implode(', ') : '-';
    }

    private function formatPaymentTerm($general): string
    {
        $paymentTerm = data_get($general, 'payment_term');

        if ($paymentTerm === 'other') {
            $other = data_get($general, 'payment_term_other');
            return $other ?: '-';
        }

        return $paymentTerm ?: '-';
    }

    private function formatList($items, array $labels): string
    {
        if (empty($items) || !is_array($items)) {
            return '-';
        }

        return collect($items)
            ->filter(function ($item) {
                return is_array($item) && collect($item)->filter()->isNotEmpty();
            })
            ->values()
            ->map(function ($item, $index) use ($labels) {
                $parts = [];

                foreach ($labels as $key => $label) {
                    $value = $item[$key] ?? null;
                    if ($value !== null && $value !== '') {
                        $parts[] = $label . ': ' . $value;
                    }
                }

                return ($index + 1) . '. ' . implode(', ', $parts);
            })
            ->implode("\n");
    }

    private function refreshApplicationVerificationStatus(VendorApplication $application): void
    {
        $items = $application->verificationItems()->get();
        $hasPendingItems = $items->contains(function ($item) {
            return $item->status === VendorApplicationVerificationItem::STATUS_PENDING;
        });
        $allItemsApproved = $items->isNotEmpty() && $items->every(function ($item) {
            return $item->status === VendorApplicationVerificationItem::STATUS_APPROVED;
        });
        $revisionNotes = $items
            ->where('status', VendorApplicationVerificationItem::STATUS_REJECTED)
            ->flatMap(function ($item) {
                $fields = $item->revision_fields ?: [[
                    'field' => $item->item_key,
                    'label' => $item->item_label,
                ]];

                return collect($fields)->map(function ($field) use ($item) {
                    return [
                        'field' => $field['field'] ?? $item->item_key,
                        'label' => $field['label'] ?? $item->item_label,
                        'item' => $item->item_label,
                        'note' => $item->note,
                    ];
                });
            })
            ->values()
            ->all();

        if ($allItemsApproved) {
            $application->update([
                'status' => VendorApplication::STATUS_VERIFIED,
                'verified_at' => now(),
                'verified_by' => Auth::id(),
                'admin_note' => 'Seluruh data permohonan sudah disetujui oleh pengadaan.',
                'revision_notes' => null,
                'auto_verified' => false,
            ]);

            return;
        }

        if ($hasPendingItems) {
            $application->update([
                'status' => VendorApplication::STATUS_SUBMITTED,
                'verified_at' => null,
                'verified_by' => null,
                'admin_note' => null,
                'revision_notes' => null,
                'auto_verified' => false,
            ]);

            return;
        }

        if (!empty($revisionNotes)) {
            $application->update([
                'status' => VendorApplication::STATUS_NEED_REVISION,
                'verified_at' => null,
                'verified_by' => Auth::id(),
                'admin_note' => 'Terdapat data yang tidak disetujui oleh pengadaan.',
                'revision_notes' => $revisionNotes,
                'auto_verified' => false,
            ]);

            return;
        }

        if ($application->status === VendorApplication::STATUS_NEED_REVISION) {
            $application->update([
                'status' => VendorApplication::STATUS_SUBMITTED,
                'verified_at' => null,
                'verified_by' => null,
                'admin_note' => null,
                'revision_notes' => null,
                'auto_verified' => false,
            ]);
        }
    }

    private function normalizeRevisionNotes(array $fields, array $notes): array
    {
        $items = [];

        foreach ($notes as $index => $note) {
            $field = trim((string) ($fields[$index] ?? ''));
            $note = trim((string) $note);

            if ($field === '' && $note === '') {
                continue;
            }

            $items[] = [
                'field' => $field ?: 'Umum',
                'note' => $note,
            ];
        }

        return $items;
    }

    private function verificationDeadline(VendorApplication $application): ?Carbon
    {
        if (!$application->submitted_at) {
            return null;
        }

        $date = $application->submitted_at->copy();
        $days = 0;

        while ($days < 10) {
            $date->addDay();

            if (!$date->isWeekend()) {
                $days++;
            }
        }

        return $date;
    }

    private function remainingBusinessDays(VendorApplication $application): ?int
    {
        $deadline = $this->verificationDeadline($application);

        if (!$deadline) {
            return null;
        }

        $today = now()->startOfDay();
        $target = $deadline->copy()->startOfDay();
        $sign = $today->lessThanOrEqualTo($target) ? 1 : -1;
        $cursor = $sign === 1 ? $today->copy() : $target->copy();
        $end = $sign === 1 ? $target : $today;
        $days = 0;

        while ($cursor->lt($end)) {
            $cursor->addDay();

            if (!$cursor->isWeekend()) {
                $days++;
            }
        }

        return $days * $sign;
    }
}
