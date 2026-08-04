<?php

namespace App\Mail;

use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorRequalificationTriggered extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public VendorApplication $application;
    public string $applicationNumber;
    public string $reasonLabel;

    public function __construct(VendorApplication $application, string $applicationNumber, string $reasonLabel)
    {
        $this->application = $application;
        $this->applicationNumber = $applicationNumber;
        $this->reasonLabel = $reasonLabel;
    }

    public function build()
    {
        return $this->subject('Permintaan Rekualifikasi Vendor: ' . $this->applicationNumber)
            ->view('emails.vendor-requalification-triggered');
    }
}
