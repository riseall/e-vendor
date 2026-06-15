<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Services\VendorApplicationNotificationService;
use App\Services\VendorApplicationDeadlineService;
use App\Services\VendorApplicationWorkflowService;
use Illuminate\Console\Command;

class AutoVerifyVendorApplications extends Command
{
    protected $signature = 'vendor-applications:auto-verify';

    protected $description = 'Automatically verify submitted vendor applications after 10 calendar days.';

    private VendorApplicationNotificationService $notificationService;
    private VendorApplicationDeadlineService $deadlineService;
    private VendorApplicationWorkflowService $workflowService;

    public function __construct(
        VendorApplicationNotificationService $notificationService,
        VendorApplicationDeadlineService $deadlineService,
        VendorApplicationWorkflowService $workflowService
    )
    {
        parent::__construct();

        $this->notificationService = $notificationService;
        $this->deadlineService = $deadlineService;
        $this->workflowService = $workflowService;
    }

    public function handle(): int
    {
        $applications = VendorApplication::where('status', VendorApplication::STATUS_SUBMITTED)
            ->whereNotNull('submitted_at')
            ->get();

        $verifiedCount = 0;

        foreach ($applications as $application) {
            if (!$this->deadlineService->isExpired($application)) {
                continue;
            }

            $previousStatus = $application->status;

            $this->workflowService->transition(
                $application,
                VendorApplication::STATUS_VERIFIED,
                'application_auto_verified',
                [
                'verified_at' => now(),
                'verified_by' => null,
                'auto_verified' => true,
                'admin_note' => 'Permohonan otomatis terverifikasi setelah melewati 10 hari kalender.',
                'revision_notes' => null,
                ]
            );
            $this->notificationService->statusChanged($application, $previousStatus);

            $verifiedCount++;
        }

        $this->info($verifiedCount . ' vendor application(s) auto-verified.');

        return self::SUCCESS;
    }
}
