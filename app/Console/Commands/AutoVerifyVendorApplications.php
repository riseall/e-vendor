<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Services\VendorApplicationNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoVerifyVendorApplications extends Command
{
    protected $signature = 'vendor-applications:auto-verify';

    protected $description = 'Automatically verify submitted vendor applications after 10 business days.';

    private VendorApplicationNotificationService $notificationService;

    public function __construct(VendorApplicationNotificationService $notificationService)
    {
        parent::__construct();

        $this->notificationService = $notificationService;
    }

    public function handle(): int
    {
        $applications = VendorApplication::where('status', VendorApplication::STATUS_SUBMITTED)
            ->whereNotNull('submitted_at')
            ->get();

        $verifiedCount = 0;

        foreach ($applications as $application) {
            if (!$this->isPastDeadline($application->submitted_at)) {
                continue;
            }

            $previousStatus = $application->status;

            $application->update([
                'status' => VendorApplication::STATUS_VERIFIED,
                'verified_at' => now(),
                'verified_by' => null,
                'auto_verified' => true,
                'admin_note' => 'Permohonan otomatis terverifikasi setelah melewati 10 hari kerja.',
                'revision_notes' => null,
            ]);
            $this->notificationService->statusChanged($application, $previousStatus);

            $verifiedCount++;
        }

        $this->info($verifiedCount . ' vendor application(s) auto-verified.');

        return self::SUCCESS;
    }

    private function isPastDeadline(Carbon $submittedAt): bool
    {
        $date = $submittedAt->copy();
        $days = 0;

        while ($days < 10) {
            $date->addDay();

            if (!$date->isWeekend()) {
                $days++;
            }
        }

        return now()->startOfDay()->greaterThan($date->startOfDay());
    }
}
