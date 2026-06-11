<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevisionSubmissionFieldsToVendorApplicationsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('vendor_applications')) {
            Schema::table('vendor_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('vendor_applications', 'revision_submitted_at')) {
                    $table->timestamp('revision_submitted_at')->nullable()->after('submitted_at');
                }

                if (!Schema::hasColumn('vendor_applications', 'revision_count')) {
                    $table->unsignedInteger('revision_count')->default(0)->after('revision_submitted_at');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('vendor_applications')) {
            Schema::table('vendor_applications', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_applications', 'revision_submitted_at')) {
                    $table->dropColumn('revision_submitted_at');
                }

                if (Schema::hasColumn('vendor_applications', 'revision_count')) {
                    $table->dropColumn('revision_count');
                }
            });
        }
    }
}
