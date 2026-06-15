<?php

namespace App\Mail;

use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorApplicationAutoVerified extends Mailable implements ShouldQueue
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
        return $this->subject('Permohonan Vendor Otomatis Terverifikasi: ' . $this->applicationNumber)
            ->view('emails.vendor-application-auto-verified');
    }
}
