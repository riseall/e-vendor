<?php

namespace App\Mail;

use App\Models\VendorApplication;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorApplicationSubmittedToProcurement extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public VendorApplication $application;
    public string $applicationNumber;
    public ?Carbon $deadline;

    public function __construct(
        VendorApplication $application,
        string $applicationNumber,
        ?Carbon $deadline = null
    )
    {
        $this->application = $application;
        $this->applicationNumber = $applicationNumber;
        $this->deadline = $deadline;
    }

    public function build()
    {
        return $this->subject('Permohonan Vendor Baru: ' . $this->applicationNumber)
            ->view('emails.vendor-application-submitted-procurement');
    }
}
