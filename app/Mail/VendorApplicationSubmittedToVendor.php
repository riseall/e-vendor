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
        $subject = ($isRekualifikasi ? 'Konfirmasi Permohonan Rekualifikasi Vendor: ' : 'Konfirmasi Permohonan Vendor: ') . $this->applicationNumber;

        return $this->subject($subject)
            ->view('emails.vendor-application-submitted-vendor');
    }
}
