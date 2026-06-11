<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevisionNotesToVendorApplicationsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('vendor_applications', 'revision_notes')) {
            Schema::table('vendor_applications', function (Blueprint $table) {
                $table->json('revision_notes')->nullable()->after('admin_note');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('vendor_applications', 'revision_notes')) {
            Schema::table('vendor_applications', function (Blueprint $table) {
                $table->dropColumn('revision_notes');
            });
        }
    }
}
