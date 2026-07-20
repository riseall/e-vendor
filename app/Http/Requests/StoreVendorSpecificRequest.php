<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\VendorApplication;
use App\Services\VendorUploadPolicy;

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

        // Base structural validation rules
        $rules = [
            'action'         => 'required|in:draft,submit',
            'application_id' => 'required|integer|exists:vendor_applications,id',

            'q5_issue_date'       => 'nullable|date',
            'q5_valid_until'      => 'nullable|date|after_or_equal:q5_issue_date',
            'pbf_num'             => 'nullable|string|max:255',
            'pbf_issue_date'      => 'nullable|date',
            'q6_issue_date'       => 'nullable|date',
            'q6_valid_until'      => 'nullable|date|after_or_equal:q6_issue_date',

            'q7_equipments'     => 'nullable|array',
            'k6_equipments'     => 'nullable|array',
            'l1_services'       => 'nullable|array',
            'l2_selected_certs' => 'nullable|array',
            'f4_certs'          => 'nullable|array',
            'g3_permits'        => 'nullable|array',
        ];

        // Initialize file validation types cleanly (String path AJAX vs Physical File)
        $fileFields = [
            'q2_auth_letter',
            'q5_document',
            'v1_auth_letter',
            'v2_license_file',
            'v6_kir_file',
            't1_safety_file',
            't8_expert_cert',
            'k1_cert_file',
            'l2_kan_file',
            'l2_cukb_file',
            'l2_iso17025_file',
            'l2_glp_file',
            'l2_bapeten_file',
            'f6_file',
            'h1_association_file',
            'pbf_document',
            'q6_document'
        ];

        foreach ($fileFields as $field) {
            $rules[$field] = $this->hasFile($field) ? 'nullable|' . VendorUploadPolicy::fileRule() : 'nullable|string|max:255';
        }

        // Apply strict conditions if final submission
        if (!$isDraft) {
            $application = VendorApplication::find($this->application_id);
            if ($application) {
                $categories = $application->getCategoryIds();

                // Cat 1: Bahan Baku
                if (in_array(1, $categories)) {
                    $rules['q1_is_manufacturer']   = 'required|in:yes,no';
                    $rules['q1_manufacturer_name'] = 'required_if:q1_is_manufacturer,yes';
                    $rules['q2_is_sole_agent']     = 'required|in:yes,no';

                    // Safely append requirement logic conditionally based on toggle selection
                    if ($this->input('q2_is_sole_agent') === 'yes') {
                        $rules['q2_auth_letter']      .= '|required_without:existing_q2_auth_letter';
                    }
                    $rules['q3_transportation']    = 'required|in:owned,3pl';
                    $rules['q3_3pl_name']          = 'required_if:q3_transportation,3pl';
                    $rules['q4_has_warehouse']     = 'required|in:yes,no';
                    $rules['q5_document']         .= '|required_without:existing_q5_document';
                    $rules['q5_issue_date']        = 'required|date';
                    $rules['q5_valid_until']       = 'required|date|after_or_equal:q5_issue_date';
                }

                // Cat 2: Varia Teknik
                if (in_array(2, $categories)) {
                    $rules['v1_is_sole_agent']   = 'required|in:yes,no';
                    if ($this->input('v1_is_sole_agent') === 'yes') {
                        $rules['v1_auth_letter']     .= '|required_without:existing_v1_auth_letter';
                    }
                    $rules['v3_iso_b3']          = 'required|in:yes,no';
                    $rules['v4_driver_training'] = 'required|in:yes,no';
                    $rules['v6_kir']             = 'required|in:yes,no';
                    if ($this->input('v6_kir') === 'yes') {
                        $rules['v6_kir_file']        .= '|required_without:existing_v6_kir_file';
                    }
                }

                // Cat 3: Transporter
                if (in_array(3, $categories)) {
                    $rules['t1_k3_commitment']          = 'required|in:yes,no';
                    $rules['t2_truck_type']             = 'required|in:ac,non_ac';
                    $rules['t5_own_fleet_outer_island'] = 'required|in:yes,no';
                }

                // Cat 4: Kontraktor
                if (in_array(4, $categories)) {
                    $rules['k1_pro_staff']  = 'required|in:yes,no';
                    $rules['k3_bpjs']       = 'required|in:yes,no';
                    $rules['k6_equipments'] = 'required|array|min:1';
                }

                // Cat 5: Pengujian
                if (in_array(5, $categories)) {
                    $rules['l1_services'] = 'required|array|min:1';
                    $rules['l3_is_agent'] = 'required|in:yes,no';
                }

                // Cat 6: Facility Service
                if (in_array(6, $categories)) {
                    $rules['f2_bpjs']             = 'required|in:yes,no';
                    $rules['f7_kitchen_facility'] = 'nullable|in:sendiri,subkontrak';
                }

                // Cat 7: Pelatihan & Konsultan
                if (in_array(7, $categories)) {
                    $rules['g2_trainer_cert'] = 'required|in:yes,no';
                    $rules['g5_bpjs']         = 'required|in:yes,no';
                }

                // Cat 8: Agency
                if (in_array(8, $categories)) {
                    $rules['h2_specialization']     = 'required|string|max:255';
                    $rules['h3_project_experience'] = 'required|string';
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
            'string'           => 'Parameter data file tidak valid.',
            'max'              => 'Ukuran string path file melebihi batas yang ditentukan.',
        ];
    }

    public function withValidator($validator): void
    {
        if ($this->input('action') === 'draft') {
            return;
        }

        $validator->after(function ($validator) {
            VendorUploadPolicy::validateTotalSize($this, $validator);
        });
    }
}
