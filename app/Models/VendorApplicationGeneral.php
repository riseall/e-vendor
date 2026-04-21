<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorApplicationGeneral extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'other_companies' => 'array',
        'iso_certificates' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }
}
