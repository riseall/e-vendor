<?php

namespace App\Mail;

use App\Models\VendorAnnualEvaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorAnnualEvaluationApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $annual;

    public function __construct(VendorAnnualEvaluation $annual)
    {
        $this->annual = $annual;
    }

    public function build()
    {
        $year = $this->annual->year;

        return $this->subject(__('email_subject_annual_evaluation', ['year' => $year]))
            ->view('emails.vendor-annual-evaluation-approved');
    }
}
