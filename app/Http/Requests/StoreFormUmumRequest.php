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

                'status_perusahaan'  => 'required|string|max:20',
                'status_pajak'       => 'required|string|max:20',
                'skala_perusahaan'   => 'required|string|max:30',
                'jenis_modal'        => 'required|string|max:20',
                'kbli'               => 'required|string|max:50',
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

        // Products array validation
        if ($this->has('products') && is_array($this->input('products'))) {
            if (!$isDraft) {
                // Strict validation for submit
                $rules += [
                    'products' => 'required|array|min:1',
                    'products.*.erp_product_id' => 'required|string|max:255',
                    'products.*.product_name' => 'required|string|max:255',
                    'products.*.manufaktur' => 'required|string|max:255',
                    'products.*.rantai_pasok' => 'required|string|max:500',
                    'products.*.has_tkdn' => 'required|in:yes,no',
                    'products.*.tkdn_file' => 'nullable|' . VendorUploadPolicy::fileRule(),
                    'products.*.existing_tkdn_file' => 'nullable|string|max:255',
                    'products.*.has_sni' => 'required|in:yes,no',
                    'products.*.sni_file' => 'nullable|' . VendorUploadPolicy::fileRule(),
                    'products.*.existing_sni_file' => 'nullable|string|max:255',
                    'products.*.has_halal' => 'required|in:yes,no',
                    'products.*.halal_file' => 'nullable|' . VendorUploadPolicy::fileRule(),
                    'products.*.existing_halal_file' => 'nullable|string|max:255',
                    'products.*.file_surat' => 'nullable|' . VendorUploadPolicy::fileRule(),
                    'products.*.file_surat_path' => 'nullable|string|max:255',
                    'products.*.existing_file_surat' => 'nullable|string|max:255',
                ];
            } else {
                // Loose validation for draft
                $rules += [
                    'products' => 'nullable|array',
                    'products.*.erp_product_id' => 'nullable|string|max:255',
                    'products.*.product_name' => 'nullable|string|max:255',
                    'products.*.manufaktur' => 'nullable|string|max:255',
                    'products.*.rantai_pasok' => 'nullable|string|max:500',
                    'products.*.has_tkdn' => 'nullable|in:yes,no',
                    'products.*.tkdn_file' => 'nullable|' . VendorUploadPolicy::fileRule(),
                    'products.*.existing_tkdn_file' => 'nullable|string|max:255',
                    'products.*.has_sni' => 'nullable|in:yes,no',
                    'products.*.sni_file' => 'nullable|' . VendorUploadPolicy::fileRule(),
                    'products.*.existing_sni_file' => 'nullable|string|max:255',
                    'products.*.has_halal' => 'nullable|in:yes,no',
                    'products.*.halal_file' => 'nullable|' . VendorUploadPolicy::fileRule(),
                    'products.*.existing_halal_file' => 'nullable|string|max:255',
                    'products.*.file_surat' => 'nullable|' . VendorUploadPolicy::fileRule(),
                    'products.*.file_surat_path' => 'nullable|string|max:255',
                    'products.*.existing_file_surat' => 'nullable|string|max:255',
                ];
            }
        }

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

        $fileValidation = VendorUploadPolicy::fileRule();

        foreach ($documentFields as $field) {
            $rules[$field] = 'nullable|' . $fileValidation;
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

                    $hasUpload = $this->hasFile("products.{$index}.{$certificate}_file");
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

                $hasSuratUpload = $this->hasFile("products.{$index}.file_surat");
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
            'products.*.tkdn_file.file' => 'Dokumen TKDN harus berupa file.',
            'products.*.tkdn_file.mimes' => 'Dokumen TKDN harus berformat PDF, JPG, JPEG, atau PNG.',
            'products.*.tkdn_file.max' => 'Ukuran dokumen TKDN maksimal 5 MB.',
            'products.*.has_sni.required' => 'Status SNI harus dipilih.',
            'products.*.sni_file.file' => 'Dokumen SNI harus berupa file.',
            'products.*.sni_file.mimes' => 'Dokumen SNI harus berformat PDF, JPG, JPEG, atau PNG.',
            'products.*.sni_file.max' => 'Ukuran dokumen SNI maksimal 5 MB.',
            'products.*.has_halal.required' => 'Status halal harus dipilih.',
            'products.*.halal_file.file' => 'Dokumen halal harus berupa file.',
            'products.*.halal_file.mimes' => 'Dokumen halal harus berformat PDF, JPG, JPEG, atau PNG.',
            'products.*.halal_file.max' => 'Ukuran dokumen halal maksimal 5 MB.',
            'products.*.file_surat.required' => 'Dokumen surat produk wajib diunggah.',
            'products.*.file_surat.file' => 'Dokumen surat harus berupa file.',
            'products.*.file_surat.mimes' => 'Dokumen surat harus berformat PDF, JPG, JPEG, atau PNG.',
            'products.*.file_surat.max' => 'Ukuran dokumen surat maksimal 5 MB.',
            'dok_nib.file' => 'Dokumen NIB harus berupa file.',
            'dok_nib.mimes' => 'Dokumen NIB harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_nib.max' => 'Ukuran dokumen NIB maksimal 5 MB.',
            'dok_npwp.file' => 'Dokumen NPWP harus berupa file.',
            'dok_npwp.mimes' => 'Dokumen NPWP harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_npwp.max' => 'Ukuran dokumen NPWP maksimal 5 MB.',
            'dok_company_profile.file' => 'Dokumen profil perusahaan harus berupa file.',
            'dok_company_profile.mimes' => 'Dokumen profil perusahaan harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_company_profile.max' => 'Ukuran dokumen profil perusahaan maksimal 5 MB.',
            'dok_struktur_org.file' => 'Dokumen struktur organisasi harus berupa file.',
            'dok_struktur_org.mimes' => 'Dokumen struktur organisasi harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_struktur_org.max' => 'Ukuran dokumen struktur organisasi maksimal 5 MB.',
            'dok_sertifikat_halal.file' => 'Dokumen sertifikat halal harus berupa file.',
            'dok_sertifikat_halal.mimes' => 'Dokumen sertifikat halal harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_sertifikat_halal.max' => 'Ukuran dokumen sertifikat halal maksimal 5 MB.',
            'dok_akte_pendirian.file' => 'Dokumen akte pendirian harus berupa file.',
            'dok_akte_pendirian.mimes' => 'Dokumen akte pendirian harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_akte_pendirian.max' => 'Ukuran dokumen akte pendirian maksimal 5 MB.',
            'dok_akte_direksi.file' => 'Dokumen akte direksi harus berupa file.',
            'dok_akte_direksi.mimes' => 'Dokumen akte direksi harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_akte_direksi.max' => 'Ukuran dokumen akte direksi maksimal 5 MB.',
            'dok_sppkp.file' => 'Dokumen SPPKP harus berupa file.',
            'dok_sppkp.mimes' => 'Dokumen SPPKP harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_sppkp.max' => 'Ukuran dokumen SPPKP maksimal 5 MB.',
            'dok_ktp_pj.file' => 'Dokumen KTP Pejabat harus berupa file.',
            'dok_ktp_pj.mimes' => 'Dokumen KTP Pejabat harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_ktp_pj.max' => 'Ukuran dokumen KTP Pejabat maksimal 5 MB.',
            'dok_pernyataan_keaslian.file' => 'Dokumen pernyataan keaslian harus berupa file.',
            'dok_pernyataan_keaslian.mimes' => 'Dokumen pernyataan keaslian harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_pernyataan_keaslian.max' => 'Ukuran dokumen pernyataan keaslian maksimal 5 MB.',
            'dok_pakta_integritas.file' => 'Dokumen pakta integritas harus berupa file.',
            'dok_pakta_integritas.mimes' => 'Dokumen pakta integritas harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_pakta_integritas.max' => 'Ukuran dokumen pakta integritas maksimal 5 MB.',
            'dok_bebas_perkara.file' => 'Dokumen bebas perkara harus berupa file.',
            'dok_bebas_perkara.mimes' => 'Dokumen bebas perkara harus berformat PDF, JPG, JPEG, atau PNG.',
            'dok_bebas_perkara.max' => 'Ukuran dokumen bebas perkara maksimal 5 MB.',
        ];
    }
}
