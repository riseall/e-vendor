<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAppSpecPengujian extends Model
{
    use HasFactory;

    protected $table = 'vendor_app_spec_pengujian';

    protected $guarded = ['id'];

    protected $casts = [
        'l1_services'       => 'array',
        'l2_selected_certs' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }
}
