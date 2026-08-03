<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'parent_id',
        'requalification_reason',
        'application_number',
        'status',
        'current_step',
        'submitted_at',
        'revision_submitted_at',
        'revision_count',
        'verified_at',
        'verified_by',
        'admin_note',
        'revision_notes',
        'auto_verified',
        'approved_by',
        'approved_at',
        'valid_until',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'revision_submitted_at' => 'datetime',
        'revision_count' => 'integer',
        'verified_at' => 'datetime',
        'revision_notes' => 'array',
        'auto_verified' => 'boolean',
        'approved_at' => 'datetime',
        'valid_until' => 'date',
        'parent_id' => 'integer',
    ];

    // Type constants
    const TYPE_INITIAL       = 'initial';
    const TYPE_REKUALIFIKASI = 'rekualifikasi';

    // Requalification Reason constants
    const REASON_VENDOR_INITIATIVE = 'vendor_initiative';
    const REASON_EXPIRED_PERIOD    = 'expired_period';
    const REASON_CDOB_EXPIRY       = 'cdob_expiry';
    const REASON_EVALUATION_DROP   = 'eval_score_drop';
    const REASON_QA_TRIGGER        = 'qa_trigger';

    const REASON_LABELS = [
        self::REASON_VENDOR_INITIATIVE => 'Inisiatif Vendor (Edit Profil)',
        self::REASON_EXPIRED_PERIOD    => 'Masa Berlaku Kadaluarsa (<= 60 Hari)',
        self::REASON_CDOB_EXPIRY       => 'Masa Berlaku Sertifikat CDOB Kadaluarsa',
        self::REASON_EVALUATION_DROP   => 'Penurunan Skor Evaluasi Kinerja',
        self::REASON_QA_TRIGGER        => 'Permintaan Tim Pengadaan / QA',
    ];

    // Status constants
    const STATUS_DRAFT     = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_NEED_REVISION = 'need_revision';
    const STATUS_VERIFIED  = 'verified';
    const STATUS_RISK_ASSESSED = 'risk_assessed';
    const STATUS_AUDIT_REQUIRED = 'audit_required';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';

    // Nama kategori
    const CATEGORY_LABELS = [
        1 => 'Bahan Baku, Bahan Kemas & Produk Jadi Farmasi/Alkes',
        2 => 'Varia Teknik, Umum, Reagen & Barang Investasi',
        3 => 'Jasa Transporter, Forwarder & PPJK',
        4 => 'Jasa Kontraktor, Perbaikan & Pemeliharaan',
        5 => 'Jasa Pengujian, Kalibrasi, Radiasi & Sertifikasi',
        6 => 'Jasa Facility Service, Sewa, Security, Katering & MCU',
        7 => 'Jasa Pelatihan, Konsultan, Notaris & Alih Daya',
        8 => 'Jasa Agency Advertising',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(VendorApplication::class, 'parent_id');
    }

    public function rekualifikasiChildren()
    {
        return $this->hasMany(VendorApplication::class, 'parent_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function categories()
    {
        return $this->hasMany(VendorApplicationCategory::class, 'application_id');
    }

    public function general()
    {
        return $this->hasOne(VendorApplicationGeneral::class, 'application_id');
    }

    public function products()
    {
        return $this->hasMany(VendorApplicationProduct::class, 'application_id');
    }

    public function documents()
    {
        return $this->hasMany(VendorApplicationDocument::class, 'application_id');
    }

    public function verificationItems()
    {
        return $this->hasMany(VendorApplicationVerificationItem::class, 'application_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(VendorApplicationActivityLog::class, 'application_id');
    }

    public function qualification()
    {
        return $this->hasOne(VendorQualification::class, 'vendor_application_id')->latestOfMany();
    }

    public function audits()
    {
        return $this->hasMany(VendorAudit::class, 'vendor_application_id')->latest();
    }

    public function activeAudit()
    {
        return $this->hasOne(VendorAudit::class, 'vendor_application_id')
            ->whereNotIn('status', [VendorAudit::STATUS_COMPLETED, VendorAudit::STATUS_REJECTED])
            ->latestOfMany();
    }

    public function fileVersions()
    {
        return $this->hasMany(VendorApplicationFile::class, 'application_id');
    }

    // Helpers
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isRekualifikasi(): bool
    {
        return $this->type === self::TYPE_REKUALIFIKASI;
    }

    public function isInitial(): bool
    {
        return $this->type === self::TYPE_INITIAL;
    }

    public function getCategoryIds(): array
    {
        return $this->categories->pluck('category_id')->toArray();
    }

    public function getDocumentByField(string $fieldName): ?VendorApplicationDocument
    {
        return $this->documents->firstWhere('field_name', $fieldName);
    }

    // Relasi dengan specific table
    public function specBaku()
    {
        return $this->hasOne(VendorAppSpecBaku::class, 'application_id');
    }
    public function specVaria()
    {
        return $this->hasOne(VendorAppSpecVaria::class, 'application_id');
    }
    public function specTrans()
    {
        return $this->hasOne(VendorAppSpecTrans::class, 'application_id');
    }
    public function specKontraktor()
    {
        return $this->hasOne(VendorAppSpecKontraktor::class, 'application_id');
    }
    public function specPengujian()
    {
        return $this->hasOne(VendorAppSpecPengujian::class, 'application_id');
    }
    public function specFacility()
    {
        return $this->hasOne(VendorAppSpecFacility::class, 'application_id');
    }
    public function specPelatihan()
    {
        return $this->hasOne(VendorAppSpecPelatihan::class, 'application_id');
    }
    public function specAgency()
    {
        return $this->hasOne(VendorAppSpecAgency::class, 'application_id');
    }

    /**
     * Accessor for risk_level directly from single source of truth (VendorQualification).
     */
    public function getRiskLevelAttribute()
    {
        if (in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SUBMITTED, self::STATUS_NEED_REVISION, self::STATUS_VERIFIED], true)) {
            return null;
        }

        return $this->qualification ? $this->qualification->risk_level : null;
    }

    /**
     * Accessor for total_score directly from single source of truth (VendorQualification).
     */
    public function getTotalScoreAttribute()
    {
        if (in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SUBMITTED, self::STATUS_NEED_REVISION, self::STATUS_VERIFIED], true)) {
            return null;
        }

        return $this->qualification ? $this->qualification->total_score : null;
    }

    /**
     * Legacy accessor alias for risk_rpn.
     */
    public function getRiskRpnAttribute()
    {
        return $this->total_score;
    }
}
