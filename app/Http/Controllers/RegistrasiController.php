<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormUmumRequest;
use App\Http\Requests\StoreVendorSpecificRequest;
use App\Models\VendorApplication;
use App\Models\VendorApplicationCategory;
use App\Models\User;
use App\Mail\VendorApplicationSubmittedToProcurement;
use App\Mail\VendorApplicationSubmittedToVendor;
use App\Services\SupplierItemService;
use App\Services\VendorRegistrationService;
use App\Services\VendorSpecificService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class RegistrasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Cek apakah ada draft/submitted terbaru untuk user ini.
        $application = VendorApplication::where('user_id', $user->id)
            ->whereIn('status', [
                VendorApplication::STATUS_DRAFT,
                VendorApplication::STATUS_SUBMITTED,
            ])
            ->with([
                'general',
                'products',
                'categories',
                'documents',
                'specBaku',
                'specVaria',
                'specTrans',
                'specKontraktor',
                'specPengujian',
                'specFacility',
                'specPelatihan',
                'specAgency'
            ])
            ->latest()
            ->first();

        $hasDraft    = $application ? true : false;
        $draftStep   = $application ? $application->current_step : 1;
        $applicationId = $application ? $application->id : null;
        $applicationStatus = $application ? $application->status : null;

        $isReadOnly = false;
        $draft = [];
        $uploadedDocs = [];

        if ($application) {
            $draft['categories'] = $application->getCategoryIds();
            $draft['general'] = $application->general;
            $draft['products'] = $application->products->mapWithKeys(function ($item) {
                return [$item->erp_product_id => $item->toArray()];
            })->toArray();

            $specificData = $this->getSpecificDataArray($application);
            $draft = array_merge($draft, $specificData);

            $uploadedDocs = $application->documents->keyBy('field_name')->map(function ($doc) {
                return [
                    'original_name' => $doc->original_name,
                    'url' => asset('storage/' . $doc->file_path),
                ];
            })->toArray();

            if ($application->status == VendorApplication::STATUS_SUBMITTED) {
                $isReadOnly = true;
            }
        }

        return view('admin.registrasi.reg', compact(
            'hasDraft',
            'draftStep',
            'applicationId',
            'applicationStatus',
            'draft',
            'isReadOnly',
            'uploadedDocs'
        ));
    }

    private function getSpecificDataArray($application)
    {
        $data = [];
        $categoryIds = $application->getCategoryIds();

        // Map tabel ke relasi
        $map = [
            1 => 'specBaku',
            2 => 'specVaria',
            3 => 'specTrans',
            4 => 'specKontraktor',
            5 => 'specPengujian',
            6 => 'specFacility',
            7 => 'specPelatihan',
            8 => 'specAgency',
        ];

        foreach ($categoryIds as $catId) {
            if (isset($map[$catId])) {
                $relation = $map[$catId];
                if ($application->$relation) {
                    // Merge data agar bisa dipanggil $draft['q1_is_manufacturer']
                    $data = array_merge($data, $application->$relation->toArray());
                }
            }
        }

        return $data;
    }

    public function saveDraft(Request $request)
    {
        $request->validate([
            'categories' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();
            $user = Auth::user();

            $hasSubmittedApplication = VendorApplication::where('user_id', $user->id)
                ->where('status', VendorApplication::STATUS_SUBMITTED)
                ->exists();

            if ($hasSubmittedApplication) {
                throw ValidationException::withMessages([
                    'application_id' => 'Permohonan yang sudah dikirim tidak dapat diubah.',
                ]);
            }

            // Gunakan updateOrCreate agar tidak duplikat data saat klik draft berkali-kali
            $application = VendorApplication::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'status'  => VendorApplication::STATUS_DRAFT
                ],
                [
                    'current_step' => 1,
                    'updated_at'   => now()
                ]
            );

            // Sync Kategori
            VendorApplicationCategory::where('application_id', $application->id)->delete();
            foreach ($request->categories as $catId) {
                VendorApplicationCategory::create([
                    'application_id' => $application->id,
                    'category_id'    => $catId
                ]);
            }

            DB::commit();

            return response()->json([
                'status'         => 'success',
                'message'        => 'Draft kategori berhasil disimpan.',
                'application_id' => $application->id // Kunci agar step 2 bisa jalan
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function saveUmum(StoreFormUmumRequest $request, VendorRegistrationService $service)
    {
        try {
            $user = Auth::user();

            // Pastikan aplikasi memang milik user yang login
            $application = VendorApplication::where('id', $request->application_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $result = $service->saveFormUmum(
                $request->validated(),
                $application->id,
                $request->action
            );

            return response()->json([
                'status'  => 'success',
                'message' => $request->action === 'submit' ? 'Data berhasil divalidasi.' : 'Draft berhasil diperbarui.',
                'application_id' => $result['application_id']
            ], 200);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses data.',
                'debug'   => $e->getMessage() // Hapus 'debug' saat production
            ], 500);
        }
    }

    public function saveSpecificStep(StoreVendorSpecificRequest $request, VendorSpecificService $specificService)
    {
        try {
            $result = $specificService->saveSpecificData(
                $request->all(),
                $request->application_id,
                $request->action
            );

            return response()->json([
                'status'  => 'success',
                'message' => $request->action === 'submit' ? 'Data spesifik berhasil divalidasi.' : 'Draft Spesifik berhasil disimpan.',
                'application_id' => $result['application_id']
            ], 200);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses data spesifik.',
                'debug'   => $e->getMessage()
            ], 500);
        }
    }

    public function submit(Request $request)
    {
        $request->validate([
            'application_id' => 'required|integer|exists:vendor_applications,id',
        ]);

        $application = VendorApplication::where('id', $request->application_id)
            ->where('user_id', Auth::id())
            ->with([
                'user',
                'general',
                'products',
                'categories',
                'documents',
                'specBaku',
                'specVaria',
                'specTrans',
                'specKontraktor',
                'specPengujian',
                'specFacility',
                'specPelatihan',
                'specAgency',
            ])
            ->firstOrFail();

        if ($application->status === VendorApplication::STATUS_SUBMITTED) {
            if (!$application->application_number) {
                $application->update([
                    'application_number' => $this->generateApplicationNumber($application, $application->submitted_at ?: now()),
                ]);
                $application->refresh();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Permohonan sudah pernah dikirim.',
                'application_id' => $application->id,
                'application_number' => $this->applicationNumber($application),
                'redirect' => route('registrasi.tracking', $this->applicationNumber($application)),
            ]);
        }

        if ($application->status !== VendorApplication::STATUS_DRAFT) {
            throw ValidationException::withMessages([
                'application_id' => 'Permohonan ini tidak dapat dikirim dari status saat ini.',
            ]);
        }

        $this->validateFinalSubmission($application);

        DB::transaction(function () use ($application) {
            $submittedAt = now();

            $application->update([
                'application_number' => $application->application_number ?: $this->generateApplicationNumber($application, $submittedAt),
                'status' => VendorApplication::STATUS_SUBMITTED,
                'submitted_at' => $submittedAt,
            ]);
        });

        $application->refresh()->load([
            'user',
            'general',
            'categories',
            'products',
        ]);

        $this->sendSubmissionEmails($application);

        return response()->json([
            'status' => 'success',
            'message' => 'Permohonan berhasil dikirim.',
            'application_id' => $application->id,
            'application_number' => $this->applicationNumber($application),
            'redirect' => route('registrasi.tracking', $this->applicationNumber($application)),
        ]);
    }

    public function success(VendorApplication $application)
    {
        abort_unless($application->user_id === Auth::id(), 403);

        $application->load(['general', 'categories', 'products']);

        return view('admin.registrasi.success', [
            'application' => $application,
            'applicationNumber' => $this->applicationNumber($application),
        ]);
    }

    public function tracking(?string $applicationNumber = null)
    {
        $query = VendorApplication::where('user_id', Auth::id())
            ->with(['user', 'general', 'categories']);

        if ($applicationNumber) {
            $application = $query->where('application_number', $applicationNumber)->firstOrFail();
        } else {
            $application = $query->where('status', '!=', VendorApplication::STATUS_DRAFT)
                ->latest('submitted_at')
                ->latest()
                ->firstOrFail();
        }

        return view('admin.registrasi.tracking', [
            'application' => $application,
            'applicationNumber' => $this->applicationNumber($application),
            'statusSteps' => $this->trackingStatusSteps($application),
        ]);
    }

    private function validateFinalSubmission(VendorApplication $application): void
    {
        $errors = [];

        if ($application->categories->isEmpty()) {
            $errors['categories'] = 'Minimal satu kategori vendor harus dipilih.';
        }

        if (!$application->general) {
            $errors['general'] = 'Formulir umum belum disimpan.';
        } else {
            $this->addMissingFields($errors, $application->general, [
                'nama_perusahaan' => 'Nama perusahaan',
                'alamat_perusahaan' => 'Alamat perusahaan',
                'website' => 'Website',
                'email_perusahaan' => 'Email perusahaan',
                'telepon_perusahaan' => 'Telepon perusahaan',
                'nib' => 'NIB',
                'npwp' => 'NPWP',
                'pic_nama' => 'Nama PIC',
                'pic_email' => 'Email PIC',
                'pic_telepon' => 'Telepon PIC',
                'payment_term' => 'Payment term',
                'pemegang_rekening' => 'Pemegang rekening',
                'nomor_rekening' => 'Nomor rekening',
                'nama_bank' => 'Nama bank',
                'alamat_bank' => 'Alamat bank',
                'swift_code' => 'Swift code',
                'iso_certificates' => 'Sertifikat ISO',
                'komitmen_kualitas' => 'Komitmen kualitas',
                'sertifikat_halal' => 'Sertifikat halal',
                'lead_time' => 'Jangka waktu pengiriman',
                'customer_list' => 'Daftar pelanggan',
                'status_perusahaan' => 'Status perusahaan',
                'status_pajak' => 'Status pajak',
                'skala_perusahaan' => 'Skala perusahaan',
                'jenis_modal' => 'Jenis modal',
                'kbli' => 'KBLI',
            ]);

            if ($application->general->has_other_company === 'yes' && empty($application->general->other_companies)) {
                $errors['other_companies'] = 'Daftar perusahaan lain wajib diisi.';
            }
        }

        $documents = $application->documents->keyBy('field_name');
        foreach ($this->requiredDocumentFields() as $field => $label) {
            if (!$documents->has($field)) {
                $errors[$field] = $label . ' wajib diunggah.';
            }
        }

        $categoryIds = $application->getCategoryIds();
        if (in_array(1, $categoryIds) && $application->products->isEmpty()) {
            $errors['products'] = 'Minimal satu produk harus ditambahkan untuk kategori bahan baku.';
        }

        foreach ($application->products as $index => $product) {
            $this->addMissingFields($errors, $product, [
                'erp_product_id' => 'Kode produk #' . ($index + 1),
                'product_name' => 'Nama produk #' . ($index + 1),
                'manufaktur' => 'Manufaktur produk #' . ($index + 1),
                'rantai_pasok' => 'Rantai pasok produk #' . ($index + 1),
                'has_tkdn' => 'Status TKDN produk #' . ($index + 1),
                'has_sni' => 'Status SNI produk #' . ($index + 1),
                'has_halal' => 'Status halal produk #' . ($index + 1),
                'file_surat_path' => 'Dokumen surat produk #' . ($index + 1),
            ], 'products.' . $index . '.');
        }

        $this->validateSpecificRequirements($application, $categoryIds, $errors);

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function addMissingFields(array &$errors, $source, array $fields, string $prefix = ''): void
    {
        foreach ($fields as $field => $label) {
            $value = data_get($source, $field);
            if ($value === null || $value === '' || $value === []) {
                $errors[$prefix . $field] = $label . ' wajib diisi.';
            }
        }
    }

    private function validateSpecificRequirements(VendorApplication $application, array $categoryIds, array &$errors): void
    {
        if (in_array(1, $categoryIds)) {
            $specific = $application->specBaku;
            $this->requireSpecific($errors, $specific, [
                'q1_is_manufacturer' => 'Kategori bahan baku: status produsen',
                'q2_is_sole_agent' => 'Kategori bahan baku: status agen tunggal',
                'q3_transportation' => 'Kategori bahan baku: angkutan pengiriman',
                'q4_has_warehouse' => 'Kategori bahan baku: status gudang',
            ]);
        }

        if (in_array(2, $categoryIds)) {
            $specific = $application->specVaria;
            $this->requireSpecific($errors, $specific, [
                'v1_is_sole_agent' => 'Kategori varia teknik: status agen tunggal',
                'v3_iso_b3' => 'Kategori varia teknik: ISO/ijin B3',
                'v4_driver_training' => 'Kategori varia teknik: training driver',
                'v6_kir' => 'Kategori varia teknik: status KIR',
            ]);
        }

        if (in_array(3, $categoryIds)) {
            $specific = $application->specTrans;
            $this->requireSpecific($errors, $specific, [
                't1_k3_commitment' => 'Kategori transporter: komitmen K3',
                't2_truck_type' => 'Kategori transporter: tipe truk',
                't5_own_fleet_outer_island' => 'Kategori transporter: armada luar pulau',
            ]);
        }

        if (in_array(4, $categoryIds)) {
            $specific = $application->specKontraktor;
            $this->requireSpecific($errors, $specific, [
                'k1_pro_staff' => 'Kategori kontraktor: tenaga profesional',
                'k3_bpjs' => 'Kategori kontraktor: BPJS',
                'k6_equipments' => 'Kategori kontraktor: daftar peralatan',
            ]);
        }

        if (in_array(5, $categoryIds)) {
            $specific = $application->specPengujian;
            $this->requireSpecific($errors, $specific, [
                'l1_services' => 'Kategori pengujian: layanan',
                'l3_is_agent' => 'Kategori pengujian: status agen',
            ]);
        }

        if (in_array(6, $categoryIds)) {
            $specific = $application->specFacility;
            $this->requireSpecific($errors, $specific, [
                'f2_bpjs' => 'Kategori facility: BPJS',
            ]);
        }

        if (in_array(7, $categoryIds)) {
            $specific = $application->specPelatihan;
            $this->requireSpecific($errors, $specific, [
                'g2_trainer_cert' => 'Kategori pelatihan: sertifikat trainer',
                'g5_bpjs' => 'Kategori pelatihan: BPJS',
            ]);
        }

        if (in_array(8, $categoryIds)) {
            $specific = $application->specAgency;
            $this->requireSpecific($errors, $specific, [
                'h2_specialization' => 'Kategori agency: spesialisasi',
                'h3_project_experience' => 'Kategori agency: pengalaman proyek',
            ]);
        }
    }

    private function requireSpecific(array &$errors, $specific, array $fields): void
    {
        if (!$specific) {
            $firstLabel = reset($fields);
            $errors['specific'] = $firstLabel . ' belum disimpan.';
            return;
        }

        $this->addMissingFields($errors, $specific, $fields);
    }

    private function requiredDocumentFields(): array
    {
        return [
            // 'dok_nib' => 'Dokumen NIB',
            // 'dok_npwp' => 'Dokumen NPWP',
            // 'dok_company_profile' => 'Dokumen profil perusahaan',
            // 'dok_struktur_org' => 'Dokumen struktur organisasi',
            // 'dok_sertifikat_halal' => 'Dokumen sertifikat halal',
            // 'dok_akte_pendirian' => 'Dokumen akte pendirian',
            // 'dok_akte_direksi' => 'Dokumen akte direksi',
            // 'dok_sppkp' => 'Dokumen SPPKP',
            // 'dok_ktp_pj' => 'Dokumen KTP pejabat',
            // 'dok_pernyataan_keaslian' => 'Dokumen pernyataan keaslian',
            // 'dok_pakta_integritas' => 'Dokumen pakta integritas',
            // 'dok_bebas_perkara' => 'Dokumen bebas perkara',
        ];
    }

    private function sendSubmissionEmails(VendorApplication $application): void
    {
        try {
            $procurementEmails = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['Procurement', 'Verifikator']);
            })
                ->where('is_active', true)
                ->pluck('email')
                ->filter()
                ->unique()
                ->values();

            if ($procurementEmails->isNotEmpty()) {
                Mail::to($procurementEmails->all())
                    ->send(new VendorApplicationSubmittedToProcurement($application, $this->applicationNumber($application)));
            }

            if ($application->user && $application->user->email) {
                Mail::to($application->user->email)
                    ->send(new VendorApplicationSubmittedToVendor($application, $this->applicationNumber($application)));
            }
        } catch (\Throwable $e) {
            Log::warning('Vendor application submitted, but email notification failed.', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function applicationNumber(VendorApplication $application): string
    {
        if ($application->application_number) {
            return $application->application_number;
        }

        $date = optional($application->submitted_at ?: $application->created_at)->format('Ymd') ?: now()->format('Ymd');

        return 'EV-' . $date . '-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT);
    }

    private function generateApplicationNumber(VendorApplication $application, $date): string
    {
        return 'EV-' . $date->format('Ymd') . '-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT);
    }

    private function trackingStatusSteps(VendorApplication $application): array
    {
        $status = $application->status;

        return [
            [
                'key' => VendorApplication::STATUS_SUBMITTED,
                'title' => 'Permohonan Dikirim',
                'description' => 'Data vendor sudah dikirim ke sistem E-Vendor.',
                'date' => $application->submitted_at,
                'state' => in_array($status, [
                    VendorApplication::STATUS_SUBMITTED,
                    VendorApplication::STATUS_NEED_REVISION,
                    VendorApplication::STATUS_VERIFIED,
                    VendorApplication::STATUS_APPROVED,
                    VendorApplication::STATUS_REJECTED,
                ]) ? 'done' : 'pending',
            ],
            [
                'key' => 'review',
                'title' => 'Verifikasi Pengadaan',
                'description' => $status === VendorApplication::STATUS_NEED_REVISION
                    ? 'Tim pengadaan meminta revisi data permohonan.'
                    : 'Tim pengadaan memeriksa kelengkapan data dan dokumen.',
                'date' => $application->verified_at,
                'state' => in_array($status, [
                    VendorApplication::STATUS_VERIFIED,
                    VendorApplication::STATUS_APPROVED,
                    VendorApplication::STATUS_REJECTED,
                ]) ? 'done' : ($status === VendorApplication::STATUS_NEED_REVISION ? 'warning' : 'active'),
            ],
            [
                'key' => VendorApplication::STATUS_APPROVED,
                'title' => 'Hasil Permohonan',
                'description' => $status === VendorApplication::STATUS_REJECTED
                    ? 'Permohonan tidak disetujui.'
                    : 'Hasil akhir akan tampil setelah proses verifikasi selesai.',
                'date' => null,
                'state' => $status === VendorApplication::STATUS_APPROVED
                    ? 'done'
                    : ($status === VendorApplication::STATUS_REJECTED ? 'danger' : 'pending'),
            ],
        ];
    }



    public function searchProducts(Request $request, SupplierItemService $qad)
    {
        $keyword = $request->keyword;
        $page    = $request->page ?? 1;

        // Proteksi karakter minimal
        if (strlen($keyword) < 3) {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        $data = $qad->searchItems($keyword, $page, 20);

        return response()->json([
            'results' => collect($data['items'])->map(function ($item) {
                return [
                    'id'           => $item['id'],
                    // 'text'         => $item['id'] . ' - ' . $item['desc'],
                    'text'         => $item['desc'],
                    'product_name' => $item['desc']
                ];
            }),
            'pagination' => ['more' => $data['hasMore']]
        ]);
    }
}
