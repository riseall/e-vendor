<?php

namespace App\Mail;

use App\Models\VendorApplication;
use App\Models\VendorAudit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorAuditOnSiteScheduledNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public VendorApplication $application;
    public VendorAudit $audit;
    public string $applicationNumber;

    public function __construct(
        VendorApplication $application,
        VendorAudit $audit,
        string $applicationNumber
    ) {
        $this->application = $application;
        $this->audit = $audit;
        $this->applicationNumber = $applicationNumber;
    }

    public function build()
    {
        $companyName = optional($this->application->general)->nama_perusahaan ?? '';
        return $this->subject(__('email_subject_audit_onsite_scheduled') . ($companyName ? ' - ' . $companyName : ''))
            ->view('emails.vendor-audit-onsite-scheduled-notification');
    }
}
