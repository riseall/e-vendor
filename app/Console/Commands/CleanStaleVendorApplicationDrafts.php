<?php

namespace App\Console\Commands;

use App\Models\VendorApplication;
use App\Services\VendorApplicationWorkflowService;
use Illuminate\Console\Command;

class CleanStaleVendorApplicationDrafts extends Command
{
    protected $signature = 'vendor-applications:clean-drafts';
    protected $description = 'Soft delete vendor application drafts inactive for more than 90 days.';

    public function handle(VendorApplicationWorkflowService $workflowService): int
    {
        $deleted = 0;

        VendorApplication::where('status', VendorApplication::STATUS_DRAFT)
            ->where('updated_at', '<', now()->subDays(90))
            ->chunkById(100, function ($applications) use ($workflowService, &$deleted) {
                foreach ($applications as $application) {
                    $workflowService->record($application, 'stale_draft_soft_deleted', null, [
                        'last_updated_at' => optional($application->updated_at)->toIso8601String(),
                    ]);
                    $application->delete();
                    $deleted++;
                }
            });

        $this->info($deleted . ' stale draft(s) soft deleted.');

        return self::SUCCESS;
    }
}
