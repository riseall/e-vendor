<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorAnnualEvaluationAwaitingVerificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $year;
    public $vendorCount;

    public function __construct(int $year, int $vendorCount)
    {
        $this->year = $year;
        $this->vendorCount = $vendorCount;
    }

    public function build()
    {
        return $this->subject("[E-VENDOR] Permohonan Verifikasi Evaluasi Tahunan Vendor ({$this->year})")
            ->view('emails.vendor-annual-evaluation-awaiting-verification');
    }
}

