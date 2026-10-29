<?php

namespace App\Mail;

use App\Models\VendorApplication;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorApplicationAwaitingManagerApproval extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public VendorApplication $application;
    public string $applicationNumber;
    public ?Carbon $deadline;
    public ?string $verifierName;

    public function __construct(
        VendorApplication $application,
        string $applicationNumber,
        ?Carbon $deadline = null,
        ?string $verifierName = null
    ) {
        $this->application = $application;
        $this->applicationNumber = $applicationNumber;
        $this->deadline = $deadline;
        $this->verifierName = $verifierName;
    }

    public function build()
    {
        $subject = __('email_subject_awaiting_manager_approval', [
            'number' => $this->applicationNumber,
        ]);

        return $this->subject($subject)
            ->view('emails.vendor-application-awaiting-manager-approval');
    }
}

