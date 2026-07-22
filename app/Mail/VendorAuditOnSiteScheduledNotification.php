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
        return $this->subject('Penjadwalan Audit On-Site Vendor - ' . ($this->application->general->nama_perusahaan ?? ''))
            ->view('emails.vendor-audit-onsite-scheduled-notification');
    }
}
