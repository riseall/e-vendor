<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'current_step',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Status constants
    const STATUS_DRAFT     = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_VERIFIED  = 'verified';
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


    // Helpers
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getCategoryIds(): array
    {
        return $this->categories->pluck('category_id')->toArray();
    }

    public function getDocumentByField(string $fieldName): ?VendorApplicationDocument
    {
        return $this->documents->firstWhere('field_name', $fieldName);
    }
}
