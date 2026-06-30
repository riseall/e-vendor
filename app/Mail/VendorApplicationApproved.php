<?php

namespace App\Mail;

use App\Models\VendorApplication;
use App\Models\VendorQualification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorApplicationApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public VendorApplication $application;
    public VendorQualification $qualification;
    public string $applicationNumber;

    public function __construct(
        VendorApplication $application,
        VendorQualification $qualification,
        string $applicationNumber
    ) {
        $this->application = $application;
        $this->qualification = $qualification;
        $this->applicationNumber = $applicationNumber;
    }

    public function build()
    {
        return $this->subject('Permohonan Vendor Disetujui: ' . $this->applicationNumber)
            ->view('emails.vendor-application-approved');
    }
}
