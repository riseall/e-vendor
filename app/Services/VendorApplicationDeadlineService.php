<?php

namespace App\Services;

use App\Models\VendorApplication;
use Carbon\Carbon;

class VendorApplicationDeadlineService
{
    public const VERIFICATION_DAYS = 10;

    public function deadline(VendorApplication $application): ?Carbon
    {
        return $application->submitted_at
            ? $application->submitted_at->copy()->addDays(self::VERIFICATION_DAYS)
            : null;
    }

    public function isExpired(VendorApplication $application, ?Carbon $now = null): bool
    {
        $deadline = $this->deadline($application);

        return $deadline !== null && ($now ?: now())->greaterThanOrEqualTo($deadline);
    }

    public function remainingDays(VendorApplication $application, ?Carbon $today = null): ?int
    {
        $deadline = $this->deadline($application);
        if (!$deadline) {
            return null;
        }

        $today = ($today ?: today())->startOfDay();
        $deadline = $deadline->copy()->startOfDay();

        return $today->diffInDays($deadline, false);
    }
}
