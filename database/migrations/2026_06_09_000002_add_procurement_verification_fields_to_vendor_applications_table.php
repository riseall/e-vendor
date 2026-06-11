<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcurementVerificationFieldsToVendorApplicationsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('vendor_applications', 'verified_by')) {
            Schema::table('vendor_applications', function (Blueprint $table) {
                $table->foreignId('verified_by')
                    ->nullable()
                    ->after('verified_at')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('vendor_applications', 'verified_by')) {
            Schema::table('vendor_applications', function (Blueprint $table) {
                $table->dropConstrainedForeignId('verified_by');
            });
        }
    }
}
