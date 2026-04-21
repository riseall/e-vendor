<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorApplicationCategory extends Model
{
    protected $fillable = [
        'application_id',
        'category_id',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }

    public function getCategoryLabelAttribute()
    {
        return VendorApplication::CATEGORY_LABELS[$this->category_id] ?? '-';
    }
}
