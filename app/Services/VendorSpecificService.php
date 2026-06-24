<?php

namespace App\Services;

use App\Models\VendorApplication;
use App\Models\VendorAppSpecBaku;
use App\Models\VendorAppSpecVaria;
use App\Models\VendorAppSpecTrans;
use App\Models\VendorAppSpecKontraktor;
use App\Models\VendorAppSpecPengujian;
use App\Models\VendorAppSpecFacility;
use App\Models\VendorAppSpecPelatihan;
use App\Models\VendorAppSpecAgency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VendorSpecificService
{
    private VendorFileService $fileService;

    public function __construct(VendorFileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function saveSpecificData(array $data, int $applicationId, string $action): array
    {
        return DB::transaction(function () use ($data, $applicationId, $action) {
            $application = VendorApplication::findOrFail($applicationId);

            if (!in_array($application->status, [
                VendorApplication::STATUS_DRAFT,
                VendorApplication::STATUS_NEED_REVISION,
            ])) {
                throw ValidationException::withMessages([
                    'application_id' => 'Permohonan yang sudah dikirim tidak dapat diubah.',
                ]);
            }

            $application->update([
                'status' => $application->status,
                'current_step' => max((int) $application->current_step, 9)
            ]);

            $categories = $application->getCategoryIds();

            // 1. Bahan Baku
            if (in_array(1, $categories)) {
                $bakuData = collect($data)->only([
                    'q1_is_manufacturer',
                    'q1_manufacturer_name',
                    'q2_is_sole_agent',
                    'q3_transportation',
                    'q3_3pl_name',
                    'q4_has_warehouse',
                    'q4_warehouse_address',
                    'q4_warehouse_condition',
                    'q5_num',
                    'q5_date',
                    'q5_issue_date',
                    'q5_valid_until',
                    'q6_name',
                    'q6_num',
                    'q6_date',
                    'q8_is_import',
                    'q8_country_name',
                ])->toArray();
                $bakuData['q7_equipments'] = $data['q7_equipments'] ?? [];
                $bakuData['q2_auth_letter'] = $this->handleSpecificFile($application, 'q2_auth_letter', $data);
                $bakuData['q5_document'] = $this->handleSpecificFile($application, 'q5_document', $data);

                VendorAppSpecBaku::updateOrCreate(['application_id' => $applicationId], $bakuData);
            }

            // 2. Varia Teknik
            if (in_array(2, $categories)) {
                $variaData = collect($data)->only(['v1_is_sole_agent', 'v2_has_special_license', 'v3_iso_b3', 'v4_driver_training', 'v5_valid_license', 'v6_kir'])->toArray();
                $variaData['v1_auth_letter'] = $this->handleSpecificFile($application, 'v1_auth_letter', $data);
                $variaData['v2_license_file'] = $this->handleSpecificFile($application, 'v2_license_file', $data);
                $variaData['v6_kir_file'] = $this->handleSpecificFile($application, 'v6_kir_file', $data);

                VendorAppSpecVaria::updateOrCreate(['application_id' => $applicationId], $variaData);
            }

            // 3. Transporter
            if (in_array(3, $categories)) {
                $transData = collect($data)->only(['t1_k3_commitment', 't4_is_insured', 't4_insurance_pct', 't2_truck_type', 't3_has_logger', 't5_own_fleet_outer_island', 't6_3pl_darat', 't6_3pl_laut', 't6_3pl_udara', 't7_association', 't8_customs_expert', 't9_has_intl_affiliate', 't9_countries', 't10_other_services'])->toArray();
                $transData['t1_safety_file'] = $this->handleSpecificFile($application, 't1_safety_file', $data);
                $transData['t8_expert_cert'] = $this->handleSpecificFile($application, 't8_expert_cert', $data);

                VendorAppSpecTrans::updateOrCreate(['application_id' => $applicationId], $transData);
            }

            // 4. Kontraktor
            if (in_array(4, $categories)) {
                $kontrakData = collect($data)->only(['k1_pro_staff', 'k2_safety_commitment', 'k3_bpjs', 'k4_apd', 'k5_association'])->toArray();
                $kontrakData['k6_equipments'] = $data['k6_equipments'] ?? [];
                $kontrakData['k1_cert_file'] = $this->handleSpecificFile($application, 'k1_cert_file', $data);

                VendorAppSpecKontraktor::updateOrCreate(['application_id' => $applicationId], $kontrakData);
            }

            // 5. Pengujian / Lab
            if (in_array(5, $categories)) {
                $labData = collect($data)->only(['l1_kalibrasi_scope', 'l2_kan_no', 'l2_kan_date', 'l2_cukb_no', 'l2_cukb_date', 'l2_iso17025_no', 'l2_iso17025_date', 'l2_glp_no', 'l2_glp_date', 'l2_bapeten_no', 'l2_bapeten_date', 'l3_is_agent', 'l3_principal_name'])->toArray();
                $labData['l1_services'] = $data['l1_services'] ?? [];
                $labData['l2_selected_certs'] = $data['l2_selected_certs'] ?? [];

                foreach (['kan', 'cukb', 'iso17025', 'glp', 'bapeten'] as $cert) {
                    $labData["l2_{$cert}_file"] = $this->handleSpecificFile($application, "l2_{$cert}_file", $data);
                }

                VendorAppSpecPengujian::updateOrCreate(['application_id' => $applicationId], $labData);
            }

            // 6. Facility
            if (in_array(6, $categories)) {
                $facData = collect($data)->only(['f1_association', 'f2_bpjs', 'f3_permenaker_ijin', 'f5_hygiene_guarantee', 'f6_sanitation_cert', 'f7_kitchen_facility', 'f8_transport_facility'])->toArray();
                $facData['f4_certs'] = $data['f4_certs'] ?? [];
                $facData['f6_file'] = $this->handleSpecificFile($application, 'f6_file', $data);

                VendorAppSpecFacility::updateOrCreate(['application_id' => $applicationId], $facData);
            }

            // 7. Pelatihan
            if (in_array(7, $categories)) {
                $trainData = collect($data)->only(['g1_association', 'g2_trainer_cert', 'g2_cert_source', 'g4_labor_permit', 'g5_bpjs'])->toArray();
                $trainData['g3_permits'] = $data['g3_permits'] ?? [];

                VendorAppSpecPelatihan::updateOrCreate(['application_id' => $applicationId], $trainData);
            }

            // 8. Agency
            if (in_array(8, $categories)) {
                $agencyData = collect($data)->only(['h1_association', 'h2_specialization', 'h3_project_experience'])->toArray();
                $agencyData['h1_association_file'] = $this->handleSpecificFile($application, 'h1_association_file', $data);

                VendorAppSpecAgency::updateOrCreate(['application_id' => $applicationId], $agencyData);
            }

            return ['application_id' => $applicationId];
        });
    }

    /**
     * Helper untuk menangani file, meniru gaya storeFile bawaan Bos
     */
    private function handleSpecificFile(
        VendorApplication $application,
        string $field,
        array $data
    ): ?string {
        if (request()->hasFile($field)) {
            return $this->fileService->store(
                $application,
                request()->file($field),
                'vendor_docs/specific',
                'specific',
                $field,
                null,
                Auth::user()
            );
        }

        // Jika tidak upload file baru, cek apakah ada file lama
        return $data['existing_' . $field] ?? null;
    }
}
