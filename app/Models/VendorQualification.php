<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorQualification extends Model
{
    use HasFactory;

    protected $table = 'vendor_qualifications';

    protected $fillable = [
        'vendor_application_id',
        'score_safety_efficacy_doc',
        'score_safety_efficacy_attr',
        'score_safety_efficacy',
        'score_availability_trace',
        'score_availability_type',
        'score_availability',
        'score_detectability_country',
        'score_detectability_warning',
        'score_detectability',
        'score_probability_function',
        'score_probability',
        'total_score',
        'risk_level',
        'audit_type',
        'qa_pharmacist_id',
        'qa_manager_id',
        'notes'
    ];

    // Otomatisasi rumus matematika sebelum data disimpan ke database
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->total_score = (
                (int) $model->score_safety_efficacy + (int) $model->score_availability
            ) * (
                (int) $model->score_detectability + (int) $model->score_probability
            );

            $lowThreshold = (int) config('risk_assessment.low_threshold', 88);
            $highThreshold = (int) config('risk_assessment.high_threshold', 164);

            if ($model->total_score <= $lowThreshold) {
                $model->risk_level = 'low';
                $model->audit_type = null;
            } elseif ($model->total_score <= $highThreshold) {
                $model->risk_level = 'medium';
                $model->audit_type = 'on_desk';
            } else {
                $model->risk_level = 'high';
                $model->audit_type = 'on_site';
            }
        });
    }

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'vendor_application_id');
    }
}
