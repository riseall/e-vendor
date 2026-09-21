<?php

namespace App\Mail;

use App\Models\VendorAnnualEvaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorConsecutiveLowAlertMail extends Mailable implements ShouldQueue
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

        return $this->subject("[PERHATIAN] Evaluasi Vendor 2 Tahun Berturut-turut KURANG: {$vendorName} ({$year})")
            ->view('emails.vendor-consecutive-low-alert');
    }
}
