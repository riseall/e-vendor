<?php

namespace App\Mail;

use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorApplicationSubmittedToVendor extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public VendorApplication $application;
    public string $applicationNumber;

    public function __construct(VendorApplication $application, string $applicationNumber)
    {
        $this->application = $application;
        $this->applicationNumber = $applicationNumber;
    }

    public function build()
    {
        $isRekualifikasi = $this->application->isRekualifikasi();
        $subjectPrefix = $isRekualifikasi 
            ? __('email_subject_requalification_submitted') 
            : __('email_subject_vendor_submitted');

        return $this->subject($subjectPrefix . ': ' . $this->applicationNumber)
            ->view('emails.vendor-application-submitted-vendor');
    }
}
