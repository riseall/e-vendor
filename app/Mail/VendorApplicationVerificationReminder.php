<?php

namespace App\Mail;

use App\Models\VendorApplication;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorApplicationVerificationReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public VendorApplication $application;
    public string $applicationNumber;
    public Carbon $deadline;
    public int $daysRemaining;

    public function __construct(
        VendorApplication $application,
        string $applicationNumber,
        Carbon $deadline,
        int $daysRemaining
    ) {
        $this->application = $application;
        $this->applicationNumber = $applicationNumber;
        $this->deadline = $deadline;
        $this->daysRemaining = $daysRemaining;
    }

    public function build()
    {
        return $this->subject(
            'Reminder H-' . $this->daysRemaining . ' Verifikasi Vendor: ' . $this->applicationNumber
        )->view('emails.vendor-application-verification-reminder');
    }
}
