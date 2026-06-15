<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorApplicationFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'owner_type',
        'owner_id',
        'field_name',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'is_current',
        'uploaded_by',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'file_size' => 'integer',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
