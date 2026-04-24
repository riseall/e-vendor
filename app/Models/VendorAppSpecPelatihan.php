<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAppSpecPelatihan extends Model
{
    use HasFactory;

    protected $table = 'vendor_app_spec_pelatihan';

    protected $guarded = ['id'];

    protected $casts = [
        'g3_permits' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }
}
