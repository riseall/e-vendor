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
                'other_companies'    => 'required_if:has_other_company,yes|array|min:1',

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
                'komitmen_kualitas_detail' => 'required_if:komitmen_kualitas,yes|string|max:255',

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
                $validator->errors()->add(
                    'iso_other',
                    'Sertifikat ISO lainnya wajib diisi.'
                );
            }

            foreach ((array) $this->input('products', []) as $index => $product) {
                foreach (
                    [
                        'tkdn' => 'Dokumen sertifikat TKDN',
                        'sni' => 'Dokumen sertifikat SNI',
                        'halal' => 'Dokumen sertifikat halal',
                    ] as $certificate => $label
                ) {
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
                            $label . ' wajib diunggah.'
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
                        'Dokumen surat produk wajib diunggah.'
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
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'nama_perusahaan.string' => 'Nama perusahaan harus berupa teks.',
            'nama_perusahaan.max' => 'Nama perusahaan maksimal 255 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'jenis_perusahaan.required' => 'Jenis perusahaan wajib diisi.',
            'tahun_berdiri.required' => 'Tahun berdiri wajib diisi.',
            'tahun_berdiri.digits' => 'Tahun berdiri harus berupa 4 digit.',
            'jumlah_karyawan.required' => 'Jumlah karyawan wajib diisi.',
            'jumlah_karyawan.integer' => 'Jumlah karyawan harus berupa angka.',
            'products.required' => 'Minimal 1 produk harus ditambahkan.',
            'products.min' => 'Minimal 1 produk harus ditambahkan.',
            'products.*.product_name.required' => 'Nama produk wajib diisi.',
            'products.*.manufaktur.required' => 'Manufaktur produk wajib diisi.',
            'products.*.rantai_pasok.required' => 'Rantai pasok produk wajib diisi.',
            'products.*.has_tkdn.required' => 'Status TKDN harus dipilih.',
            'products.*.tkdn_file.string' => 'Dokumen TKDN harus berupa path yang valid.',
            'products.*.tkdn_file.max' => 'Ukuran parameter dokumen TKDN tidak valid.',
            'products.*.has_sni.required' => 'Status SNI harus dipilih.',
            'products.*.sni_file.string' => 'Dokumen SNI harus berupa path yang valid.',
            'products.*.sni_file.max' => 'Ukuran parameter dokumen SNI tidak valid.',
            'products.*.has_halal.required' => 'Status halal harus dipilih.',
            'products.*.halal_file.string' => 'Dokumen halal harus berupa path yang valid.',
            'products.*.halal_file.max' => 'Ukuran parameter dokumen halal tidak valid.',
            'products.*.file_surat.required' => 'Dokumen surat produk wajib diunggah.',
            'products.*.file_surat.string' => 'Dokumen surat harus berupa path yang valid.',
            'products.*.file_surat.max' => 'Ukuran parameter dokumen surat tidak valid.',
        ];
    }
}
