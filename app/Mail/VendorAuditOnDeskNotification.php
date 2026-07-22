<?php

namespace App\Mail;

use App\Models\VendorApplication;
use App\Models\VendorAudit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorAuditOnDeskNotification extends Mailable implements ShouldQueue
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
        $statusText = 'Direvisi';
        if ($this->audit->status === VendorAudit::STATUS_COMPLETED) {
            $statusText = 'Disetujui';
        } elseif ($this->audit->status === VendorAudit::STATUS_REJECTED) {
            $statusText = 'Ditolak';
        }

        return $this->subject("Hasil Verifikasi Kuesioner Vendor: {$statusText}")
            ->view('emails.vendor-audit-ondesk-notification');
    }
}
