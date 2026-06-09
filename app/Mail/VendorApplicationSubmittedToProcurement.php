<?php

namespace App\Mail;

use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorApplicationSubmittedToProcurement extends Mailable
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
        return $this->subject('Permohonan Vendor Baru: ' . $this->applicationNumber)
            ->view('emails.vendor-application-submitted-procurement');
    }
}
