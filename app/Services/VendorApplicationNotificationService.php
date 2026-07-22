<?php

namespace App\Services;

use App\Mail\VendorApplicationApproved;
use App\Mail\VendorApplicationAutoVerified;
use App\Mail\VendorApplicationNeedsRevision;
use App\Mail\VendorApplicationRevisionSubmittedToProcurement;
use App\Mail\VendorApplicationSubmittedToProcurement;
use App\Mail\VendorApplicationSubmittedToVendor;
use App\Mail\VendorApplicationVerified;
use App\Mail\VendorApplicationVerificationReminder;
use App\Mail\VendorRiskAssessmentHighRisk;
use App\Mail\VendorRiskAssessmentLowRisk;
use App\Mail\VendorRiskAssessmentMediumRisk;
use Carbon\Carbon;
use App\Models\User;
use App\Models\VendorApplication;
use App\Models\VendorQualification;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VendorApplicationNotificationService
{
    public function submitted(
        VendorApplication $application,
        bool $isRevisionSubmit = false
    ): void {
        $application->loadMissing(['user', 'general']);
        $number = $this->applicationNumber($application);
        $deadline = app(ProcurementVerificationService::class)->verificationDeadline($application);
        $procurementMail = $isRevisionSubmit
            ? new VendorApplicationRevisionSubmittedToProcurement($application, $number, $deadline)
            : new VendorApplicationSubmittedToProcurement($application, $number, $deadline);

        $this->queue(
            $this->submissionReviewerEmails(),
            $procurementMail,
            $application,
            $isRevisionSubmit ? 'revision_submitted_procurement' : 'submitted_procurement'
        );

        $this->queue(
            $application->user ? [$application->user->email] : [],
            new VendorApplicationSubmittedToVendor($application, $number),
            $application,
            'submitted_vendor'
        );
    }

    public function verificationReminder(
        VendorApplication $application,
        Carbon $deadline,
        int $daysRemaining
    ): void {
        $application->loadMissing(['user', 'general']);

        $this->queue(
            $this->procurementEmails(),
            new VendorApplicationVerificationReminder(
                $application,
                $this->applicationNumber($application),
                $deadline,
                $daysRemaining
            ),
            $application,
            'verification_reminder_h' . $daysRemaining
        );
    }

    private function submissionReviewerEmails(): array
    {
        return collect($this->roleEmails([
            'Super Admin',
            'Admin IT',
            'Procurement',
            'Verifikator',
            'Quality Assurance',
        ]))
            ->merge(config('mail.procurement_recipients', []))
            ->merge(config('mail.qa_recipients', []))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function statusChanged(
        VendorApplication $application,
        string $previousStatus
    ): void {
        $application->refresh()->loadMissing(['user', 'general']);

        if ($previousStatus === $application->status) {
            return;
        }

        if ($application->status === VendorApplication::STATUS_NEED_REVISION) {
            $this->queue(
                $application->user ? [$application->user->email] : [],
                new VendorApplicationNeedsRevision($application, $this->applicationNumber($application)),
                $application,
                'need_revision'
            );
            return;
        }

        if ($application->status !== VendorApplication::STATUS_VERIFIED) {
            return;
        }

        if ($application->auto_verified) {
            $mail = new VendorApplicationAutoVerified($application, $this->applicationNumber($application));
            $recipients = collect($this->procurementEmails())
                ->merge($application->user ? [$application->user->email] : [])
                ->filter()
                ->unique()
                ->values()
                ->all();

            $this->queue($recipients, $mail, $application, 'verified_auto');
            return;
        }

        $this->queue(
            $application->user ? [$application->user->email] : [],
            new VendorApplicationVerified($application, $this->applicationNumber($application)),
            $application,
            'verified_manual'
        );
    }

    // Notif email Risk Assessment
    public function riskAssessmentResult(
        VendorApplication $application,
        VendorQualification $qualification
    ): void {
        $application->loadMissing(['user', 'general']);

        $vendorRecipients = $application->user ? [$application->user->email] : [];
        $notificationRecipients = $this->riskAssessmentNotificationEmails();
        $applicationNumber = $this->applicationNumber($application);

        if ($qualification->risk_level === 'low') {
            $this->queue(
                $vendorRecipients,
                new VendorRiskAssessmentLowRisk($application, $qualification, $applicationNumber),
                $application,
                'risk_assessment_low_vendor'
            );

            $this->queue(
                $vendorRecipients,
                new VendorApplicationApproved($application, $qualification, $applicationNumber),
                $application,
                'application_approved_vendor'
            );

            return;
        }

        if ($qualification->risk_level === 'medium') {
            $this->queue(
                $vendorRecipients,
                new VendorRiskAssessmentMediumRisk($application, $qualification, $applicationNumber),
                $application,
                'risk_assessment_medium_vendor'
            );

            return;
        }

        if ($qualification->risk_level === 'high') {
            $this->queue(
                $vendorRecipients,
                new VendorRiskAssessmentHighRisk($application, $qualification, $applicationNumber),
                $application,
                'risk_assessment_high_vendor'
            );
        }
    }

    private function riskAssessmentNotificationEmails(): array
    {
        return collect($this->roleEmails(['Super Admin', 'Admin IT', 'Procurement', 'Quality Assurance']))
            // ->merge(config('mail.akuntansi', []))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function auditQuestionnaireSubmitted(\App\Models\VendorAudit $audit, bool $isRevision): void
    {
        $application = $audit->application;
        $application->loadMissing(['user', 'general']);
        $number = $this->applicationNumber($application);

        $this->queue(
            $this->qaNotificationEmails(),
            new \App\Mail\VendorAuditQuestionnaireSubmittedToQa($application, $audit, $number, $isRevision),
            $application,
            'audit_questionnaire_submitted'
        );
    }

    public function auditQuestionnaireVerified(\App\Models\VendorAudit $audit): void
    {
        $application = $audit->application;
        $application->loadMissing(['user', 'general']);
        $number = $this->applicationNumber($application);

        $vendorRecipients = $application->user ? [$application->user->email] : [];
        $qaRecipients = $this->qaNotificationEmails();

        $this->queue(
            $vendorRecipients,
            new \App\Mail\VendorAuditOnDeskNotification($application, $audit, $number),
            $application,
            'audit_questionnaire_verified_vendor'
        );

        $this->queue(
            $qaRecipients,
            new \App\Mail\VendorAuditOnDeskNotification($application, $audit, $number),
            $application,
            'audit_questionnaire_verified_qa'
        );
    }

    public function auditOnSiteScheduled(\App\Models\VendorAudit $audit): void
    {
        $application = $audit->application;
        $application->loadMissing(['user', 'general']);
        $number = $this->applicationNumber($application);

        $vendorRecipients = $application->user ? [$application->user->email] : [];
        $qaRecipients = $this->qaNotificationEmails();

        $this->queue(
            $vendorRecipients,
            new \App\Mail\VendorAuditOnSiteScheduledNotification($application, $audit, $number),
            $application,
            'audit_onsite_scheduled_vendor'
        );

        $this->queue(
            $qaRecipients,
            new \App\Mail\VendorAuditOnSiteScheduledNotification($application, $audit, $number),
            $application,
            'audit_onsite_scheduled_qa'
        );
    }

    public function auditOnSiteResult(\App\Models\VendorAudit $audit): void
    {
        $application = $audit->application;
        $application->loadMissing(['user', 'general']);
        $number = $this->applicationNumber($application);

        $vendorRecipients = $application->user ? [$application->user->email] : [];
        $qaRecipients = $this->qaNotificationEmails();

        $this->queue(
            $vendorRecipients,
            new \App\Mail\VendorAuditResultNotification($application, $audit, $number),
            $application,
            'audit_onsite_result_vendor'
        );

        $this->queue(
            $qaRecipients,
            new \App\Mail\VendorAuditResultNotification($application, $audit, $number),
            $application,
            'audit_onsite_result_qa'
        );
    }

    private function qaNotificationEmails(): array
    {
        return collect($this->roleEmails(['Super Admin', 'Admin IT', 'Quality Assurance']))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function procurementEmails(): array
    {
        return collect($this->roleEmails(['Super Admin', 'Admin IT', 'Procurement', 'Verifikator']))
            ->merge(config('mail.procurement_recipients', []))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function roleEmails(array $roles): array
    {
        return User::whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })
            ->where('is_active', true)
            ->pluck('email')
            ->filter()
            ->all();
    }

    private function queue(
        array $recipients,
        Mailable $mail,
        VendorApplication $application,
        string $trigger
    ): void {
        $recipients = collect($recipients)->filter()->unique()->values()->all();

        if (empty($recipients)) {
            Log::warning('Vendor email notification skipped because recipient is empty.', [
                'application_id' => $application->id,
                'trigger' => $trigger,
            ]);
            return;
        }

        try {
            Mail::to($recipients)->queue($mail);
        } catch (\Throwable $e) {
            Log::warning('Vendor email notification failed.', [
                'application_id' => $application->id,
                'trigger' => $trigger,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function applicationNumber(VendorApplication $application): string
    {
        return $application->application_number ?: 'EV-' . str_pad((string) $application->id, 6, '0', STR_PAD_LEFT);
    }
}
