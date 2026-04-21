<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mst_anggota extends Model
{
    use HasFactory;

    protected $connection = 'db_master';
    protected $table = 'mst_anggota';
}
