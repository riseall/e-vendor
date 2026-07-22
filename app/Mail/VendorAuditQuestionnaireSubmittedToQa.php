<?php

namespace App\Mail;

use App\Models\VendorApplication;
use App\Models\VendorAudit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorAuditQuestionnaireSubmittedToQa extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public VendorApplication $application;
    public VendorAudit $audit;
    public string $applicationNumber;
    public bool $isRevision;

    public function __construct(
        VendorApplication $application,
        VendorAudit $audit,
        string $applicationNumber,
        bool $isRevision = false
    ) {
        $this->application = $application;
        $this->audit = $audit;
        $this->applicationNumber = $applicationNumber;
        $this->isRevision = $isRevision;
    }

    public function build()
    {
        $statusText = $this->isRevision ? 'Revisi Kuesioner Dikirim' : 'Kuesioner Baru Dikirim';
        
        return $this->subject("Notifikasi QA: {$statusText} dari Vendor {$this->application->general->nama_perusahaan}")
            ->view('emails.vendor-audit-questionnaire-submitted-to-qa');
    }
}
