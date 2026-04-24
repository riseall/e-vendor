<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAppSpecFacility extends Model
{
    use HasFactory;

    protected $table = 'vendor_app_spec_facility';

    protected $guarded = ['id'];

    protected $casts = [
        'f4_certs' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }
}
