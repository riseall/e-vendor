<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicationNumberToVendorApplicationsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('vendor_applications', 'application_number')) {
            Schema::table('vendor_applications', function (Blueprint $table) {
                $table->string('application_number', 30)->nullable()->unique()->after('user_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('vendor_applications', 'application_number')) {
            Schema::table('vendor_applications', function (Blueprint $table) {
                $table->dropUnique(['application_number']);
                $table->dropColumn('application_number');
            });
        }
    }
}
