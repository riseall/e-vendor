<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAnnualEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'year',
        'delivery_score_avg',
        'quality_score_avg',
        'quantity_score_avg',
        'complain_score_avg',
        'incoming_material_score_avg',
        'safety_environment_score_avg',
        'final_score',
        'category',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'has_score_drop_alert',
        'has_consecutive_low_alert',
        'decision_status',
    ];

    protected $casts = [
        'delivery_score_avg' => 'float',
        'quality_score_avg' => 'float',
        'quantity_score_avg' => 'float',
        'complain_score_avg' => 'float',
        'incoming_material_score_avg' => 'float',
        'safety_environment_score_avg' => 'float',
        'final_score' => 'float',
        'has_score_drop_alert' => 'boolean',
        'has_consecutive_low_alert' => 'boolean',
        'approved_at' => 'datetime',
        'year' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
