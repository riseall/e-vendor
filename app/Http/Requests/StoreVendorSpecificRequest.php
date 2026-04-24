<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\VendorApplication;

class StoreVendorSpecificRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $action = $this->input('action');
        $isDraft = $action === 'draft';

        // Common rules (wajib ada meskipun draft)
        $rules = [
            'action'         => 'required|in:draft,submit',
            'application_id' => 'required|integer|exists:vendor_applications,id',

            // Aturan tipe file (kalau diisi, harus valid file-nya)
            'q2_auth_letter'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'v1_auth_letter'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'v2_license_file'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'v6_kir_file'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            't1_safety_file'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            't8_expert_cert'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'k1_cert_file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'l2_kan_file'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'l2_cukb_file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'l2_iso17025_file'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'l2_glp_file'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'l2_bapeten_file'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'f6_file'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'h1_association_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

            // Aturan array
            'q7_equipments'     => 'nullable|array',
            'k6_equipments'     => 'nullable|array',
            'l1_services'       => 'nullable|array',
            'l2_selected_certs' => 'nullable|array',
            'f4_certs'          => 'nullable|array',
            'g3_permits'        => 'nullable|array',
        ];

        // Validasi Ketat JIKA Action = Submit
        if (!$isDraft) {
            $application = VendorApplication::find($this->application_id);
            if ($application) {
                $categories = $application->getCategoryIds();

                // Cat 1: Bahan Baku
                if (in_array(1, $categories)) {
                    $rules += [
                        'q1_is_manufacturer'   => 'required|in:yes,no',
                        'q1_manufacturer_name' => 'required_if:q1_is_manufacturer,yes',
                        'q2_is_sole_agent'     => 'required|in:yes,no',
                        'q2_auth_letter'       => 'required_if:q2_is_sole_agent,yes|required_without:existing_q2_auth_letter',
                        'q3_transportation'    => 'required|in:owned,3pl',
                        'q3_3pl_name'          => 'required_if:q3_transportation,3pl',
                        'q4_has_warehouse'     => 'required|in:yes,no',
                    ];
                }

                // Cat 2: Varia Teknik
                if (in_array(2, $categories)) {
                    $rules += [
                        'v1_is_sole_agent'   => 'required|in:yes,no',
                        'v1_auth_letter'     => 'required_if:v1_is_sole_agent,yes|required_without:existing_v1_auth_letter',
                        'v3_iso_b3'          => 'required|in:yes,no',
                        'v4_driver_training' => 'required|in:yes,no',
                        'v6_kir'             => 'required|in:yes,no',
                        'v6_kir_file'        => 'required_if:v6_kir,yes|required_without:existing_v6_kir_file',
                    ];
                }

                // Cat 3: Transporter
                if (in_array(3, $categories)) {
                    $rules += [
                        't1_k3_commitment'          => 'required|in:yes,no',
                        't2_truck_type'             => 'required|in:ac,non_ac',
                        't5_own_fleet_outer_island' => 'required|in:yes,no',
                    ];
                }

                // Cat 4: Kontraktor
                if (in_array(4, $categories)) {
                    $rules += [
                        'k1_pro_staff'  => 'required|in:yes,no',
                        'k3_bpjs'       => 'required|in:yes,no',
                        'k6_equipments' => 'required|array|min:1',
                    ];
                }

                // Cat 5: Pengujian
                if (in_array(5, $categories)) {
                    $rules += [
                        'l1_services' => 'required|array|min:1',
                        'l3_is_agent' => 'required|in:yes,no',
                    ];
                }

                // Cat 6: Facility Service
                if (in_array(6, $categories)) {
                    $rules += [
                        'f2_bpjs'             => 'required|in:yes,no',
                        'f7_kitchen_facility' => 'nullable|in:sendiri,subkontrak',
                    ];
                }

                // Cat 7: Pelatihan & Konsultan
                if (in_array(7, $categories)) {
                    $rules += [
                        'g2_trainer_cert' => 'required|in:yes,no',
                        'g5_bpjs'         => 'required|in:yes,no',
                    ];
                }

                // Cat 8: Agency
                if (in_array(8, $categories)) {
                    $rules += [
                        'h2_specialization'     => 'required|string|max:255',
                        'h3_project_experience' => 'required|string',
                    ];
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'required_if'      => 'Field ini wajib diisi berdasarkan pilihan Anda sebelumnya.',
            'required_without' => 'File dokumen wajib diunggah.',
            'mimes'            => 'Format file harus berupa PDF, JPG, JPEG, atau PNG.',
            'max'              => 'Ukuran file maksimal adalah 2MB.',
        ];
    }
}
