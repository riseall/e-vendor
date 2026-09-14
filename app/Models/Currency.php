<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Accessor for name attribute compatibility with description.
     */
    public function getNameAttribute()
    {
        return $this->description;
    }
}
