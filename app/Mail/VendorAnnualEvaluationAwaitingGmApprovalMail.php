<?php

namespace App\Mail;

use App\Models\VendorAnnualEvaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorAnnualEvaluationAwaitingGmApprovalMail extends Mailable implements ShouldQueue
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
        $vendorName = optional($this->annual->vendor)->name ?: 'Vendor';

        return $this->subject("[E-VENDOR] Permohonan Pengesahan Evaluasi Tahunan Vendor: {$vendorName} ({$year})")
            ->view('emails.vendor-annual-evaluation-awaiting-gm-approval');
    }
}

