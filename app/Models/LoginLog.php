<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'nama',
        'email',
        'jabatan',
        'departemen',
        'divisi',
        'ip_address',
        'user_agent',
    ];
}
