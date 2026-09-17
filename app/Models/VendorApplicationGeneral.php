<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorApplicationGeneral extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'other_companies'    => 'array',
        'iso_certificates'   => 'array',
        // Enkripsi data sensitif (Native Laravel 8)
        'email_perusahaan'   => 'encrypted',
        'telepon_perusahaan' => 'encrypted',
        'nib'                => 'encrypted',
        'npwp'               => 'encrypted',
        'pic_email'          => 'encrypted',
        'pic_telepon'        => 'encrypted',
        'pemegang_rekening'  => 'encrypted',
        'nomor_rekening'     => 'encrypted',
        'nama_bank'          => 'encrypted',
        'alamat_bank'        => 'encrypted',
        'swift_code'         => 'encrypted',
        'qad_supplier_code'  => 'encrypted',
        'supplier_type'      => 'encrypted',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }
}
