<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAppSpecTrans extends Model
{
    use HasFactory;

    protected $table = 'vendor_app_spec_trans';

    protected $guarded = ['id'];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }
}
