<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoVerifiedToVendorApplicationsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('vendor_applications', 'auto_verified')) {
            Schema::table('vendor_applications', function (Blueprint $table) {
                $table->boolean('auto_verified')->default(false)->after('verified_by');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('vendor_applications', 'auto_verified')) {
            Schema::table('vendor_applications', function (Blueprint $table) {
                $table->dropColumn('auto_verified');
            });
        }
    }
}
