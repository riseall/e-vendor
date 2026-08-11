<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'weight_delivery',
        'weight_quality',
        'weight_quantity',
        'weight_complain',
        'weight_incoming_material',
        'weight_safety_environment',
        'threshold_baik',
        'threshold_cukup',
    ];

    protected $casts = [
        'weight_delivery' => 'float',
        'weight_quality' => 'float',
        'weight_quantity' => 'float',
        'weight_complain' => 'float',
        'weight_incoming_material' => 'float',
        'weight_safety_environment' => 'float',
        'threshold_baik' => 'float',
        'threshold_cukup' => 'float',
    ];

    /**
     * Get or create default settings singleton.
     */
    public static function getSettings()
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'weight_delivery' => 20.00,
                'weight_quality' => 20.00,
                'weight_quantity' => 20.00,
                'weight_complain' => 15.00,
                'weight_incoming_material' => 15.00,
                'weight_safety_environment' => 10.00,
                'threshold_baik' => 80.00,
                'threshold_cukup' => 60.00,
            ]);
        }
        return $setting;
    }
}
