<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAnnualEvaluation extends Model
{
    use HasFactory;

    const STATUS_DRAFT            = 'draft';
    const STATUS_VERIFIED_MANAGER  = 'verified_manager';
    const STATUS_APPROVED          = 'approved';
    const STATUS_REJECTED          = 'rejected';

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
        'manager_approved_by',
        'manager_approved_at',
        'manager_notes',
        'gm_approved_by',
        'gm_approved_at',
        'gm_notes',
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
        'manager_approved_at' => 'datetime',
        'gm_approved_at' => 'datetime',
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

    public function managerApprover()
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    public function gmApprover()
    {
        return $this->belongsTo(User::class, 'gm_approved_by');
    }

    public function isDraft(): bool
    {
        return empty($this->status) || $this->status === self::STATUS_DRAFT;
    }

    public function isVerifiedManager(): bool
    {
        return $this->status === self::STATUS_VERIFIED_MANAGER;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
