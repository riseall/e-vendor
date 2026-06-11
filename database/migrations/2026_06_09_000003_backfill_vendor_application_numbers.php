<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillVendorApplicationNumbers extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('vendor_applications', 'application_number')) {
            return;
        }

        DB::table('vendor_applications')
            ->whereNull('application_number')
            ->whereNotNull('submitted_at')
            ->orderBy('id')
            ->get(['id', 'submitted_at', 'created_at'])
            ->each(function ($application) {
                $date = $application->submitted_at ?: $application->created_at;
                $datePart = date('Ymd', strtotime($date));

                DB::table('vendor_applications')
                    ->where('id', $application->id)
                    ->update([
                        'application_number' => 'EV-' . $datePart . '-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT),
                    ]);
            });
    }

    public function down()
    {
        // Keep generated application numbers for audit consistency.
    }
}
