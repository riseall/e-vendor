<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorAuditQuestionTemplate extends Model
{
    use HasFactory;

    protected $table = 'vendor_audit_question_templates';

    const TYPE_YES_NO          = 'yes_no';
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_TEXT            = 'text';
    const TYPE_DOCUMENT        = 'document';

    protected $fillable = [
        'form_id',
        'category_id',
        'section',
        'question',
        'answer_type',
        'options',
        'weight',
        'is_required',
        'depends_on_question_code',
        'is_active',
        'order',
        'created_by',
    ];

    protected $casts = [
        'options'     => 'array',
        'is_active'   => 'boolean',
        'is_required' => 'boolean',
        'order'       => 'integer',
        'weight'      => 'integer',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(VendorAuditQuestionnaireForm::class, 'form_id');
    }

    /**
     * Ambil pertanyaan untuk 1 form. Dipakai setelah auditor pilih form.
     */
    public static function forForm(int $formId)
    {
        return self::query()
            ->where('is_active', true)
            ->where('form_id', $formId)
            ->orderBy('order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Backward-compat. Lama: cari pertanyaan by category_id vendor.
     * Sekarang controller auditor akan pass form_id, jadi ini fallback.
     */
    public static function forCategories(array $categoryIds)
    {
        if (empty($categoryIds)) {
            return collect();
        }

        return self::query()
            ->where('is_active', true)
            ->whereIn('category_id', $categoryIds)
            ->orderBy('order')
            ->orderBy('id')
            ->get();
    }
}
