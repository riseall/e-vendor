<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAppSpecKontraktor extends Model
{
    use HasFactory;

    protected $table = 'vendor_app_spec_kontraktor';

    protected $guarded = ['id'];

    protected $casts = [
        'k6_equipments' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }
}
