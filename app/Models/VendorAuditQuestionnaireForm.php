<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorAuditQuestionnaireForm extends Model
{
    use HasFactory;

    protected $table = 'vendor_audit_questionnaire_forms';

    const MATERIAL_BAHAN_BAKU  = 'bahan_baku';
    const MATERIAL_BAHAN_KEMAS = 'bahan_kemas';
    const MATERIAL_PRODUK_JADI = 'produk_jadi';
    const MATERIAL_ALKES       = 'alkes';

    public const MATERIAL_LABELS = [
        self::MATERIAL_BAHAN_BAKU  => 'Bahan Baku',
        self::MATERIAL_BAHAN_KEMAS => 'Bahan Kemas',
        self::MATERIAL_PRODUK_JADI => 'Produk Jadi',
        self::MATERIAL_ALKES       => 'Alkes',
    ];

    protected $fillable = [
        'code', 'name', 'material_type', 'document_number',
        'description', 'is_active', 'order', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(VendorAuditQuestionTemplate::class, 'form_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('id');
    }

    public function materialTypeLabel(): string
    {
        return self::MATERIAL_LABELS[$this->material_type] ?? '-';
    }

    /**
     * Daftar form aktif, opsional difilter by material_type.
     */
    public static function optionsForMaterial(?string $materialType = null)
    {
        $q = self::query()->where('is_active', true)->orderBy('order')->orderBy('name');
        if ($materialType !== null && $materialType !== '') {
            $q->where('material_type', $materialType);
        }
        return $q->get();
    }
}
