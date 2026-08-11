<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'month',
        'year',
        'delivery_score',
        'quality_score',
        'quantity_score',
        'complain_score',
        'incoming_material_score',
        'safety_environment_score',
        'total_score',
        'category',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'delivery_score' => 'float',
        'quality_score' => 'float',
        'quantity_score' => 'float',
        'complain_score' => 'float',
        'incoming_material_score' => 'float',
        'safety_environment_score' => 'float',
        'total_score' => 'float',
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Hitung total skor terbobot dan tentukan kategori.
     */
    public function calculateTotalAndCategory($settings = null)
    {
        if (!$settings) {
            $settings = EvaluationSetting::getSettings();
        }

        $deliveryWeight = $settings->weight_delivery / 100;
        $qualityWeight  = $settings->weight_quality / 100;
        $quantityWeight = $settings->weight_quantity / 100;
        $complainWeight = $settings->weight_complain / 100;
        $incomingWeight = $settings->weight_incoming_material / 100;
        $safetyWeight   = $settings->weight_safety_environment / 100;

        $weightedTotal = ($this->delivery_score * $deliveryWeight)
                       + ($this->quality_score * $qualityWeight)
                       + ($this->quantity_score * $quantityWeight)
                       + ($this->complain_score * $complainWeight)
                       + ($this->incoming_material_score * $incomingWeight)
                       + ($this->safety_environment_score * $safetyWeight);

        $this->total_score = round($weightedTotal, 2);

        if ($this->total_score >= $settings->threshold_baik) {
            $this->category = 'BAIK';
        } elseif ($this->total_score >= $settings->threshold_cukup) {
            $this->category = 'CUKUP';
        } else {
            $this->category = 'KURANG';
        }

        return $this;
    }
}
