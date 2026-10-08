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

    protected $appends = [
        'question_translations',
        'section_translations',
        'options_translations',
    ];

    /**
     * Accessor otomatis sesuai locale aktif (id / en).
     */
    public function getQuestionAttribute($value)
    {
        $translations = json_decode($value, true);
        if (!is_array($translations)) {
            return $value;
        }
        $locale = app()->getLocale();
        if (!empty($translations[$locale])) {
            return $translations[$locale];
        }
        return !empty($translations['id']) ? $translations['id'] : (reset($translations) ?: '');
    }

    public function getSectionAttribute($value)
    {
        $translations = json_decode($value, true);
        if (!is_array($translations)) {
            return $value;
        }
        $locale = app()->getLocale();
        if (!empty($translations[$locale])) {
            return $translations[$locale];
        }
        return !empty($translations['id']) ? $translations['id'] : (reset($translations) ?: '');
    }

    public function getOptionsAttribute($value)
    {
        $data = is_array($value) ? $value : json_decode($value, true);
        if (!is_array($data)) {
            return [];
        }
        // Struktur dwibahasa: ['id' => [...], 'en' => [...]]
        if (isset($data['id']) || isset($data['en'])) {
            $locale = app()->getLocale();
            if (!empty($data[$locale]) && is_array($data[$locale])) {
                return $data[$locale];
            }
            if (!empty($data['id']) && is_array($data['id'])) {
                return $data['id'];
            }
            $first = reset($data);
            return is_array($first) ? $first : [];
        }
        return $data;
    }

    /**
     * Raw translations untuk form edit modal.
     */
    public function getQuestionTranslationsAttribute(): array
    {
        $raw = isset($this->attributes['question']) ? $this->attributes['question'] : '';
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return [
                'id' => isset($decoded['id']) ? $decoded['id'] : '',
                'en' => isset($decoded['en']) ? $decoded['en'] : '',
            ];
        }
        return [
            'id' => (string) $raw,
            'en' => '',
        ];
    }

    public function getSectionTranslationsAttribute(): array
    {
        $raw = isset($this->attributes['section']) ? $this->attributes['section'] : '';
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return [
                'id' => isset($decoded['id']) ? $decoded['id'] : '',
                'en' => isset($decoded['en']) ? $decoded['en'] : '',
            ];
        }
        return [
            'id' => (string) $raw,
            'en' => '',
        ];
    }

    public function getOptionsTranslationsAttribute(): array
    {
        $raw = isset($this->attributes['options']) ? $this->attributes['options'] : null;
        $decoded = is_array($raw) ? $raw : json_decode($raw, true);
        if (is_array($decoded) && (isset($decoded['id']) || isset($decoded['en']))) {
            return [
                'id' => isset($decoded['id']) && is_array($decoded['id']) ? $decoded['id'] : [],
                'en' => isset($decoded['en']) && is_array($decoded['en']) ? $decoded['en'] : [],
            ];
        }
        return [
            'id' => is_array($decoded) ? $decoded : [],
            'en' => [],
        ];
    }

    /**
     * Resolusi nilai pilihan ganda yang tersimpan agar tetap terpilih
     * saat vendor atau admin berpindah bahasa antara ID dan EN.
     */
    public function resolveSelectedOption($val)
    {
        if (empty($val) || empty($this->options_translations['en'])) {
            return $val;
        }
        $raw = $this->options_translations;
        $idList = isset($raw['id']) && is_array($raw['id']) ? $raw['id'] : [];
        $enList = isset($raw['en']) && is_array($raw['en']) ? $raw['en'] : [];
        $locale = app()->getLocale();

        // Jika tersimpan dalam ID, tapi tampilan sekarang EN
        $idx = array_search($val, $idList, true);
        if ($idx !== false) {
            return ($locale === 'en' && isset($enList[$idx]) && $enList[$idx] !== '') ? $enList[$idx] : $val;
        }

        // Jika tersimpan dalam EN, tapi tampilan sekarang ID
        $idxEn = array_search($val, $enList, true);
        if ($idxEn !== false) {
            return ($locale === 'id' && isset($idList[$idxEn]) && $idList[$idxEn] !== '') ? $idList[$idxEn] : $val;
        }

        return $val;
    }

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
