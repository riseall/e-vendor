<?php

namespace App\Mail;

use App\Models\VendorApplication;
use App\Models\VendorAudit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorAuditResultNotification extends Mailable implements ShouldQueue
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
        $statusText = __('recommended');
        if ($this->audit->audit_result_category === 'tdk_rekomendasi') {
            $statusText = __('not_recommended');
        } elseif ($this->audit->audit_result_category === 'on_hold') {
            $statusText = __('on_hold');
        }

        return $this->subject(__('email_subject_audit_result') . ': ' . $statusText)
            ->view('emails.vendor-audit-result-notification');
    }
}
