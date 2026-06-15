<?php

namespace App\Services;

use App\Mail\VendorApplicationAutoVerified;
use App\Mail\VendorApplicationNeedsRevision;
use App\Mail\VendorApplicationRevisionSubmittedToProcurement;
use App\Mail\VendorApplicationSubmittedToProcurement;
use App\Mail\VendorApplicationSubmittedToVendor;
use App\Mail\VendorApplicationVerified;
use App\Mail\VendorApplicationVerificationReminder;
use Carbon\Carbon;
use App\Models\User;
use App\Models\VendorApplication;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VendorApplicationNotificationService
{
    public function submitted(
        VendorApplication $application,
        bool $isRevisionSubmit = false
    ): void
    {
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

    private function procurementEmails(): array
    {
        return collect($this->roleEmails(['Procurement', 'Verifikator']))
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
