<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormUmumRequest;
use App\Http\Requests\StoreVendorSpecificRequest;
use App\Models\VendorApplication;
use App\Models\VendorApplicationCategory;
use App\Services\SupplierItemService;
use App\Services\VendorApplicationNotificationService;
use App\Services\VendorRegistrationService;
use App\Services\VendorRegistrationViewService;
use App\Services\VendorSpecificService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrasiController extends Controller
{
    public function index(VendorRegistrationViewService $viewService)
    {
        $viewData = $viewService->viewData(
            $viewService->currentApplication(Auth::user())
        );

        view()->share('revisionNotes', $viewData['revisionNotes']);

        return view(
            $viewData['isProfileMode'] ? 'admin.registrasi.profile' : 'admin.registrasi.reg',
            $viewData
        );
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

            $application = VendorApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    VendorApplication::STATUS_DRAFT,
                    VendorApplication::STATUS_NEED_REVISION,
                ])
                ->latest()
                ->first();

            if ($application) {
                $application->update([
                    'current_step' => 1,
                    'updated_at' => now(),
                ]);
            } else {
                $application = VendorApplication::create([
                    'user_id' => $user->id,
                    'status' => VendorApplication::STATUS_DRAFT,
                    'current_step' => 1,
                ]);
            }

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
            VendorApplication::where('id', $request->application_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

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

    public function submit(
        Request $request,
        VendorApplicationNotificationService $notificationService
    )
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

        if (!in_array($application->status, [
            VendorApplication::STATUS_DRAFT,
            VendorApplication::STATUS_NEED_REVISION,
        ])) {
            throw ValidationException::withMessages([
                'application_id' => 'Permohonan ini tidak dapat dikirim dari status saat ini.',
            ]);
        }

        $this->validateFinalSubmission($application);
        $isRevisionSubmit = $application->status === VendorApplication::STATUS_NEED_REVISION;

        DB::transaction(function () use ($application, $isRevisionSubmit) {
            $submittedAt = now();

            $application->update([
                'application_number' => $application->application_number ?: $this->generateApplicationNumber($application, $submittedAt),
                'status' => VendorApplication::STATUS_SUBMITTED,
                'submitted_at' => $application->submitted_at ?: $submittedAt,
                'revision_submitted_at' => $isRevisionSubmit ? $submittedAt : $application->revision_submitted_at,
                'revision_count' => $isRevisionSubmit ? ((int) $application->revision_count + 1) : $application->revision_count,
                'verified_at' => null,
                'admin_note' => null,
                'revision_notes' => null,
                'auto_verified' => false,
            ]);

            if ($isRevisionSubmit) {
                $application->verificationItems()
                    ->where('status', 'rejected')
                    ->update([
                        'status' => 'pending',
                        'verified_by' => null,
                        'verified_at' => null,
                    ]);
            }
        });

        $application->refresh()->load([
            'user',
            'general',
            'categories',
            'products',
        ]);

        $notificationService->submitted($application, $isRevisionSubmit);

        return response()->json([
            'status' => 'success',
            'message' => $isRevisionSubmit
                ? 'Revisi permohonan berhasil dikirim.'
                : 'Permohonan berhasil dikirim.',
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

            foreach (
                [
                    'tkdn' => 'Dokumen sertifikat TKDN',
                    'sni' => 'Dokumen sertifikat SNI',
                    'halal' => 'Dokumen sertifikat halal',
                ] as $certificate => $label
            ) {
                if ($product->{'has_' . $certificate} === 'yes' && empty($product->{$certificate . '_file_path'})) {
                    $errors['products.' . $index . '.' . $certificate . '_file'] =
                        $label . ' produk #' . ($index + 1) . ' wajib diunggah.';
                }
            }
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
        return [];
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
