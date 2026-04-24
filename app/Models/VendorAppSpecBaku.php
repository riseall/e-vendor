<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAppSpecBaku extends Model
{
    use HasFactory;

    protected $table = 'vendor_app_spec_baku';

    protected $guarded = ['id'];

    protected $casts = [
        'q7_equipments' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }
}
