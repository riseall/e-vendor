<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VendorApplicationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'field_name',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }

    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }
}
