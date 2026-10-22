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
        'assessment_type',
        'score_safety_efficacy_doc',
        'score_complaint_side_effect',
        'score_safety_efficacy_attr',
        'score_safety_efficacy',
        'score_availability_trace',
        'score_availability_type',
        'score_pareto',
        'score_purchase_volume',
        'score_nie_status',
        'score_availability',
        'score_detectability_country',
        'score_detectability_warning',
        'score_audit_year',
        'score_audit_result',
        'score_audit_response',
        'score_complaint_count',
        'score_detectability',
        'score_probability_function',
        'score_material_grade',
        'score_facility_variation',
        'score_material_purpose',
        'material_function_label',
        'score_probability',
        'total_score',
        'risk_level',
        'audit_type',
        're_audit_period_years',
        'needs_quality_agreement',
        'qa_pharmacist_id',
        'qa_manager_id',
        'notes'
    ];

    protected $casts = [
        'needs_quality_agreement' => 'boolean',
        're_audit_period_years'   => 'integer',
        'total_score'             => 'integer',
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

            if ($model->assessment_type === 'existing') {
                $lowThreshold      = (int) config('risk_assessment.existing.low_threshold', 84);
                $mediumThreshold   = (int) config('risk_assessment.existing.medium_threshold', 168);
                $highThreshold     = (int) config('risk_assessment.existing.high_threshold', 336);

                if ($model->total_score <= $lowThreshold) {
                    $model->risk_level              = 'low';
                    $model->audit_type              = 'on_desk';
                    $model->re_audit_period_years   = 7;
                    $model->needs_quality_agreement = false;
                } elseif ($model->total_score <= $mediumThreshold) {
                    $model->risk_level              = 'medium';
                    $model->audit_type              = 'on_desk';
                    $model->re_audit_period_years   = 5;
                    $model->needs_quality_agreement = false;
                } elseif ($model->total_score <= $highThreshold) {
                    $model->risk_level              = 'high';
                    $model->audit_type              = 'on_site';
                    $model->re_audit_period_years   = 3;
                    $model->needs_quality_agreement = false;
                } else {
                    $model->risk_level              = 'very_high';
                    $model->audit_type              = 'on_site';
                    $model->re_audit_period_years   = 2;
                    $model->needs_quality_agreement = true;
                }
            } else {
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
            }
        });
    }

    public function application()
    {
        return $this->belongsTo(VendorApplication::class, 'vendor_application_id');
    }
}
