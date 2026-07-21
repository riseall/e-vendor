<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorAudit extends Model
{
    use HasFactory;

    protected $table = 'vendor_audits';

    // ─── Tipe audit ─────────────────────────────────────────────────────
    const TYPE_ON_DESK = 'on_desk';
    const TYPE_ON_SITE = 'on_site';

    // ─── Status audit ───────────────────────────────────────────────────
    const STATUS_SCHEDULED               = 'scheduled';
    const STATUS_QUESTIONNAIRE_PROGRESS  = 'questionnaire_in_progress';
    const STATUS_QUESTIONNAIRE_SUBMITTED = 'questionnaire_submitted';
    const STATUS_SCHEDULE_PROPOSED       = 'schedule_proposed';
    const STATUS_SCHEDULE_CONFIRMED      = 'schedule_confirmed';
    const STATUS_IN_PROGRESS             = 'in_progress';
    const STATUS_NEED_REVISION           = 'need_revision';
    const STATUS_COMPLETED               = 'completed';
    const STATUS_REJECTED                = 'rejected';

    protected $fillable = [
        'vendor_application_id',
        'vendor_qualification_id',
        'questionnaire_form_id',
        'audit_type',
        'status',
        'questionnaire_submitted_at',
        'questionnaire_payload',
        'questionnaire_revision_notes',
        'proposed_schedules',
        'confirmed_schedule_at',
        'audit_location',
        'auditor_team',
        'audit_agenda',
        'audit_letter_path',
        'audit_letter_sent_at',
        'preparation_checklist',
        'preparation_submitted_at',
        'summary',
        'completed_at',
        'audit_result_path',
        'audit_result_category',
        'qa_lead_id',
        'created_by',
    ];

    protected $casts = [
        'questionnaire_payload' => 'array',
        'questionnaire_revision_notes' => 'array',
        'proposed_schedules' => 'array',
        'auditor_team' => 'array',
        'preparation_checklist' => 'array',
        'questionnaire_submitted_at' => 'datetime',
        'confirmed_schedule_at' => 'datetime',
        'audit_letter_sent_at' => 'datetime',
        'preparation_submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(VendorApplication::class, 'vendor_application_id');
    }

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(VendorQualification::class, 'vendor_qualification_id');
    }

    public function questionnaireForm(): BelongsTo
    {
        return $this->belongsTo(VendorAuditQuestionnaireForm::class, 'questionnaire_form_id');
    }

    public function qaLead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'qa_lead_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOnDesk(): bool
    {
        return $this->audit_type === self::TYPE_ON_DESK;
    }

    public function isOnSite(): bool
    {
        return $this->audit_type === self::TYPE_ON_SITE;
    }

    public function auditorTeam()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function progressPercent(): int
    {
        $map = [
            self::STATUS_SCHEDULED               => 5,
            self::STATUS_QUESTIONNAIRE_PROGRESS  => 25,
            self::STATUS_QUESTIONNAIRE_SUBMITTED => 45,
            self::STATUS_SCHEDULE_CONFIRMED      => 50,
            self::STATUS_IN_PROGRESS             => 70,
            self::STATUS_NEED_REVISION           => 50,
            self::STATUS_COMPLETED               => 100,
            self::STATUS_REJECTED                => 100,
        ];

        return $map[$this->status] ?? 0;
    }
}
