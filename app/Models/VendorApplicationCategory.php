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
        $key = 'cat_title_' . $this->category_id;
        $trans = __($key);
        if ($trans !== $key) {
            return $trans;
        }
        return VendorApplication::CATEGORY_LABELS[$this->category_id] ?? '-';
    }
}
