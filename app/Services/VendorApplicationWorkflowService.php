<?php

namespace App\Services;

use App\Models\User;
use App\Models\VendorApplication;
use App\Models\VendorApplicationActivityLog;

class VendorApplicationWorkflowService
{
    public function transition(
        VendorApplication $application,
        string $status,
        string $action,
        array $attributes = [],
        ?User $actor = null,
        array $metadata = []
    ): VendorApplication {
        $previousStatus = $application->status;

        if ($status === VendorApplication::STATUS_APPROVED) {
            $attributes['requalification_reason'] = null;
        }

        $application->update(array_merge($attributes, ['status' => $status]));

        VendorApplicationActivityLog::create([
            'application_id' => $application->id,
            'user_id' => optional($actor)->id,
            'action' => $action,
            'status_before' => $previousStatus,
            'status_after' => $status,
            'ip_address' => request()->ip(),
            'metadata' => $metadata ?: null,
        ]);

        return $application->refresh();
    }

    public function record(
        VendorApplication $application,
        string $action,
        ?User $actor = null,
        array $metadata = []
    ): void {
        VendorApplicationActivityLog::create([
            'application_id' => $application->id,
            'user_id' => optional($actor)->id,
            'action' => $action,
            'status_before' => $application->status,
            'status_after' => $application->status,
            'ip_address' => request()->ip(),
            'metadata' => $metadata ?: null,
        ]);
    }
}
