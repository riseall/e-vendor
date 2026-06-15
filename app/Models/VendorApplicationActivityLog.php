<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorApplicationActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'action',
        'status_before',
        'status_after',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'application_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
