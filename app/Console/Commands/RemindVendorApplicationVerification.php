<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Services\VendorApplicationDeadlineService;
use App\Services\VendorApplicationNotificationService;
use App\Services\VendorApplicationWorkflowService;
use Illuminate\Console\Command;

class RemindVendorApplicationVerification extends Command
{
    protected $signature = 'vendor-applications:remind-verification';
    protected $description = 'Send H-3 and H-1 verification deadline reminders.';

    public function handle(
        VendorApplicationDeadlineService $deadlineService,
        VendorApplicationNotificationService $notificationService,
        VendorApplicationWorkflowService $workflowService
    ): int {
        $sent = 0;

        VendorApplication::where('status', VendorApplication::STATUS_SUBMITTED)
            ->whereNotNull('submitted_at')
            ->with(['user', 'general'])
            ->chunkById(100, function ($applications) use (
                $deadlineService,
                $notificationService,
                $workflowService,
                &$sent
            ) {
                foreach ($applications as $application) {
                    $daysRemaining = $deadlineService->remainingDays($application);
                    if (!in_array($daysRemaining, [3, 1], true)) {
                        continue;
                    }

                    $action = 'verification_reminder_h' . $daysRemaining;
                    $alreadySent = $application->activityLogs()
                        ->where('action', $action)
                        ->whereDate('created_at', today())
                        ->exists();

                    if ($alreadySent) {
                        continue;
                    }

                    $notificationService->verificationReminder(
                        $application,
                        $deadlineService->deadline($application),
                        $daysRemaining
                    );
                    $workflowService->record($application, $action, null, [
                        'deadline' => $deadlineService->deadline($application)->toIso8601String(),
                    ]);
                    $sent++;
                }
            });

        $this->info($sent . ' verification reminder(s) queued.');

        return self::SUCCESS;
    }
}
