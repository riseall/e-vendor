<?php

namespace App\Mail;

use App\Models\VendorApplication;
use App\Models\VendorQualification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorRiskAssessmentMediumRisk extends Mailable implements ShouldQueue
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
        return $this->subject(__('email_subject_risk_medium') . ': ' . $this->applicationNumber)
            ->view('emails.vendor-risk-assessment-medium-risk');
    }
}
