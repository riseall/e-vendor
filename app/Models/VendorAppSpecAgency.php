<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAppSpecAgency extends Model
{
    use HasFactory;

    protected $table = 'vendor_app_spec_agency';

    protected $guarded = ['id'];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }
}
