<?php

namespace App\Http\Requests;

use App\Models\VendorApplicationProduct;
use App\Services\VendorUploadPolicy;
use Illuminate\Foundation\Http\FormRequest;

class StoreFormUmumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $action = $this->input('action');
        $isDraft = $action === 'draft';

        // Common rules
        $rules = [
            'action' => 'required|in:draft,submit',
            'application_id' => 'required|integer|exists:vendor_applications,id',
        ];

        // Basic company information
        if (!$isDraft) {
            // Strict validation for submit
            $rules += [
                'nama_perusahaan'    => 'required|string|max:255',
                'alamat_perusahaan'  => 'required|string',
                'website'            => 'required|url|max:255',
                'email_perusahaan'   => 'required|email|max:255',
                'telepon_perusahaan' => 'required|string|max:20',
                'nib'                => 'required|string|max:50',
                'npwp'               => 'required|string|max:50',
                'pic_nama'           => 'required|string|max:255',
                'pic_email'          => 'required|email|max:255',
                'pic_telepon'        => 'required|string|max:20',
                'has_other_company'  => 'required|in:yes,no',
                'other_companies'    => 'nullable|required_if:has_other_company,yes|array|min:1',

                'payment_term'       => 'required|string|max:20',
                'payment_term_other' => 'required_if:payment_term,other|nullable|string|max:20',
                'pemegang_rekening'  => 'required|string|max:255',
                'nomor_rekening'     => 'required|string|max:50',
                'nama_bank'          => 'required|string|max:255',
                'alamat_bank'        => 'required|string|max:255',
                'swift_code'         => 'required|string|max:11',

                'iso_certificates'   => 'required|array|min:1',
                'iso_other'          => 'nullable|string|max:255',
                'komitmen_kualitas' => 'required|in:yes,no',
                'komitmen_kualitas_detail' => 'nullable|required_if:komitmen_kualitas,yes|string|max:255',

                'lead_time'          => 'required|string|max:100',
                'customer_list'      => 'required|string|max:255',

                'status_perusahaan'  => 'nullable|string|max:20',
                'status_pajak'       => 'nullable|string|max:20',
                'skala_perusahaan'   => 'nullable|string|max:30',
                'jenis_modal'        => 'nullable|string|max:20',
                'kbli'               => 'nullable|string|max:50',
            ];
        } else {
            // Loose validation for draft
            $rules += [
                'nama_perusahaan'    => 'nullable|string|max:255',
                'alamat_perusahaan'  => 'nullable|string',
                'website'            => 'nullable|url|max:255',
                'email_perusahaan'   => 'nullable|email|max:255',
                'telepon_perusahaan' => 'nullable|string|max:20',
                'nib'                => 'nullable|string|max:50',
                'npwp'               => 'nullable|string|max:50',
                'pic_nama'           => 'nullable|string|max:255',
                'pic_email'          => 'nullable|email|max:255',
                'pic_telepon'        => 'nullable|string|max:20',
                'has_other_company'  => 'nullable|in:yes,no',
                'other_companies'    => 'nullable|array',

                'payment_term'       => 'nullable|string|max:20',
                'payment_term_other' => 'nullable|string|max:20',
                'pemegang_rekening'  => 'nullable|string|max:255',
                'nomor_rekening'     => 'nullable|string|max:50',
                'nama_bank'          => 'nullable|string|max:255',
                'alamat_bank'        => 'nullable|string|max:255',
                'swift_code'         => 'nullable|string|max:11',

                'iso_certificates'   => 'nullable|array',
                'iso_other'          => 'nullable|string|max:255',
                'komitmen_kualitas' => 'nullable|in:yes,no',
                'komitmen_kualitas_detail' => 'nullable|string|max:255',

                'lead_time'          => 'nullable|string|max:100',
                'customer_list'      => 'nullable|string|max:255',

                'status_perusahaan'  => 'nullable|string|max:20',
                'status_pajak'       => 'nullable|string|max:20',
                'skala_perusahaan'   => 'nullable|string|max:30',
                'jenis_modal'        => 'nullable|string|max:20',
                'kbli'               => 'nullable|string|max:50',
            ];
        }

        // Unroll products validation to prevent wildcard limitations with hasFile()
        if ($this->has('products') && is_array($this->input('products'))) {
            foreach (array_keys($this->input('products')) as $index) {
                if (!$isDraft) {
                    $rules["products.{$index}.erp_product_id"] = 'required|string|max:255';
                    $rules["products.{$index}.product_name"] = 'required|string|max:255';
                    $rules["products.{$index}.manufaktur"] = 'required|string|max:255';
                    $rules["products.{$index}.negara"] = 'required|string|max:100';
                    $rules["products.{$index}.rantai_pasok"] = 'required|string|max:500';
                    $rules["products.{$index}.has_tkdn"] = 'required|in:yes,no';
                    $rules["products.{$index}.has_sni"] = 'required|in:yes,no';
                    $rules["products.{$index}.has_halal"] = 'required|in:yes,no';
                    $rules["products.{$index}.has_bse_tse"] = 'required|in:yes,no';
                } else {
                    $rules["products.{$index}.erp_product_id"] = 'nullable|string|max:255';
                    $rules["products.{$index}.product_name"] = 'nullable|string|max:255';
                    $rules["products.{$index}.manufaktur"] = 'nullable|string|max:255';
                    $rules["products.{$index}.negara"] = 'nullable|string|max:100';
                    $rules["products.{$index}.rantai_pasok"] = 'nullable|string|max:500';
                    $rules["products.{$index}.has_tkdn"] = 'nullable|in:yes,no';
                    $rules["products.{$index}.has_sni"] = 'nullable|in:yes,no';
                    $rules["products.{$index}.has_halal"] = 'nullable|in:yes,no';
                    $rules["products.{$index}.has_bse_tse"] = 'nullable|in:yes,no';
                }

                // Dynamically evaluate each file field index for AJAX string or physical file
                foreach (['gmp_file', 'tkdn_file', 'sni_file', 'halal_file', 'bse_tse_file', 'file_surat'] as $fileField) {
                    $rules["products.{$index}.{$fileField}"] = $this->hasFile("products.{$index}.{$fileField}")
                        ? 'nullable|' . VendorUploadPolicy::fileRule()
                        : 'nullable|string|max:255';
                }

                $rules["products.{$index}.existing_gmp_file"] = 'nullable|string|max:255';
                $rules["products.{$index}.existing_tkdn_file"] = 'nullable|string|max:255';
                $rules["products.{$index}.existing_sni_file"] = 'nullable|string|max:255';
                $rules["products.{$index}.existing_halal_file"] = 'nullable|string|max:255';
                $rules["products.{$index}.existing_bse_tse_file"] = 'nullable|string|max:255';
                $rules["products.{$index}.file_surat_path"] = 'nullable|string|max:255';
                $rules["products.{$index}.existing_file_surat"] = 'nullable|string|max:255';
            }
        }

        $rules['products'] = !$isDraft ? 'required|array|min:1' : 'nullable|array';

        // General documents validation (file uploads)
        $documentFields = [
            'dok_nib',
            'dok_npwp',
            'dok_company_profile',
            'dok_struktur_org',
            'dok_sertifikat_halal',
            'dok_akte_pendirian',
            'dok_akte_direksi',
            'dok_sppkp',
            'dok_ktp_pj',
            'dok_pernyataan_keaslian',
            'dok_pakta_integritas',
            'dok_bebas_perkara',
        ];

        foreach ($documentFields as $field) {
            $rules[$field] = $this->hasFile($field) ? 'nullable|' . VendorUploadPolicy::fileRule() : 'nullable|string|max:255';
        }

        // ISO certificates (multiple files, one per selected ISO)
        // $rules['iso_files']   = $this->hasFile('iso_files') ? 'nullable|array' : 'nullable|string';
        // $rules['iso_files.*'] = $this->hasFile('iso_files.*') ? 'nullable|' . VendorUploadPolicy::fileRule() : 'nullable|string|max:255';

        if ($this->hasFile('iso_files') || is_array($this->input('iso_files'))) {
            $rules['iso_files']   = 'nullable|array';
            $rules['iso_files.*'] = $this->hasFile('iso_files.*')
                ? 'nullable|' . VendorUploadPolicy::fileRule()
                : 'nullable|string|max:255';
        } else {
            $rules['iso_files']   = 'nullable|string';
        }

        $rules['existing_iso_files'] = 'nullable|array';
        $rules['existing_iso_files.*'] = 'nullable|string|max:255';
        $rules['existing_iso_files_present'] = 'nullable';

        return $rules;
    }

    public function withValidator($validator): void
    {
        if ($this->input('action') === 'draft') {
            return;
        }

        $validator->after(function ($validator) {
            VendorUploadPolicy::validateTotalSize($this, $validator);

            if (
                in_array('other', (array) $this->input('iso_certificates', []), true)
                && blank($this->input('iso_other'))
            ) {
                $validator->errors()->add('iso_other', __('other_iso_certificate_required'));
            }

            $certErrors = [
                'tkdn'    => 'tkdn_file_required',
                'sni'     => 'sni_file_required',
                'halal'   => 'halal_file_required',
                'bse_tse' => 'bse_tse_file_required',
            ];

            foreach ((array) $this->input('products', []) as $index => $product) {
                foreach ($certErrors as $certificate => $errorKey) {
                    if (($product['has_' . $certificate] ?? 'no') !== 'yes') {
                        continue;
                    }

                    // CHECK BOTH: Check physical file OR AJAX uploaded string path
                    $hasUpload = $this->hasFile("products.{$index}.{$certificate}_file") || !empty($product[$certificate . '_file']);
                    $hasExisting = !empty($product["existing_{$certificate}_file"]);

                    if (!$hasExisting && !empty($product['erp_product_id'])) {
                        $pathColumn = $certificate . '_file_path';
                        $hasExisting = VendorApplicationProduct::query()
                            ->where('application_id', $this->input('application_id'))
                            ->where('erp_product_id', $product['erp_product_id'])
                            ->whereNotNull($pathColumn)
                            ->exists();
                    }

                    if (!$hasUpload && !$hasExisting) {
                        $validator->errors()->add(
                            "products.{$index}.{$certificate}_file",
                            __($errorKey)
                        );
                    }
                }

                // CHECK BOTH for product letter
                $hasSuratUpload = $this->hasFile("products.{$index}.file_surat") || !empty($product['file_surat']);
                $hasSuratExisting = !empty($product['existing_file_surat']);

                if (!$hasSuratExisting && !empty($product['erp_product_id'])) {
                    $hasSuratExisting = VendorApplicationProduct::query()
                        ->where('application_id', $this->input('application_id'))
                        ->where('erp_product_id', $product['erp_product_id'])
                        ->whereNotNull('file_surat_path')
                        ->exists();
                }

                if (!$hasSuratUpload && !$hasSuratExisting) {
                    $validator->errors()->add(
                        "products.{$index}.file_surat",
                        __('product_letter_doc_required')
                    );
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        if (!in_array('other', (array) $this->input('iso_certificates', []), true)) {
            $this->merge(['iso_other' => null]);
        }

        $website = $this->input('website');
        if (!empty($website) && is_string($website) && !preg_match('~^https?://~i', $website)) {
            $this->merge(['website' => 'https://' . $website]);
        }
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'products.required'                    => __('min_one_product_required'),
            'products.min'                         => __('min_one_product_required'),
            'products.*.product_name.required'     => __('product_name_required'),
            'products.*.manufaktur.required'       => __('manufacturer_origin_required'),
            'products.*.negara.required'           => __('product_country_required'),
            'products.*.rantai_pasok.required'     => __('product_supply_chain_required'),
            'products.*.has_tkdn.required'         => __('tkdn_status_required'),
            'products.*.has_sni.required'          => __('sni_status_required'),
            'products.*.has_halal.required'        => __('halal_status_required'),
            'products.*.has_bse_tse.required'      => __('bse_tse_status_required'),
            'products.*.file_surat.required'       => __('product_letter_doc_required'),
            'website.url'                          => __('website_url_invalid'),
            'payment_term_other.required_if'       => __('other_payment_term_required'),
            'komitmen_kualitas_detail.required_if' => __('quality_commitment_detail_required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nama_perusahaan'          => __('company_name'),
            'alamat_perusahaan'        => __('full_company_address'),
            'website'                  => __('official_website'),
            'email_perusahaan'         => __('company_email'),
            'telepon_perusahaan'       => __('company_phone_number'),
            'nib'                      => __('nib_number'),
            'npwp'                     => __('npwp_number'),
            'pic_nama'                 => __('name'),
            'pic_email'                => __('email'),
            'pic_telepon'              => __('mobile_phone_number'),
            'has_other_company'        => __('other_companies_owned'),
            'other_companies'          => __('other_companies'),
            'other_companies.*.nama'   => __('company_name'),
            'other_companies.*.alamat' => __('address'),
            'payment_term'             => __('preferred_payment_term'),
            'payment_term_other'       => __('other'),
            'pemegang_rekening'        => __('account_holder'),
            'nomor_rekening'           => __('account_number'),
            'nama_bank'                => __('bank_name'),
            'alamat_bank'              => __('bank_address'),
            'swift_code'               => __('swift_code'),
            'iso_certificates'         => __('iso_certificate_owned'),
            'iso_other'                => __('other'),
            'komitmen_kualitas'        => __('commitment'),
            'komitmen_kualitas_detail' => __('commitment'),
            'lead_time'                => __('delivery_lead_time'),
            'customer_list'            => __('pharmaceutical_customer_list'),
            'products'                 => __('product'),
            'products.*.product_name'  => __('product_name'),
            'products.*.manufaktur'    => __('manufacturer_origin'),
            'products.*.negara'        => __('country'),
            'products.*.rantai_pasok'  => __('supply_chain'),
            'products.*.has_tkdn'      => 'TKDN',
            'products.*.has_sni'       => 'SNI',
            'products.*.has_halal'     => __('halal'),
            'products.*.has_bse_tse'   => 'BSE/TSE',
            'products.*.file_surat'    => __('agency_letter'),
            'products.*.tkdn_file'     => __('tkdn_certificate_doc'),
            'products.*.sni_file'      => __('sni_certificate_doc'),
            'products.*.halal_file'    => __('halal_certificate_doc'),
            'products.*.bse_tse_file'  => __('bse_tse_certificate_doc'),
        ];
    }
}
