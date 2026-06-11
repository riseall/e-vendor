<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevisionFieldsToVendorApplicationVerificationItemsTable extends Migration
{
    public function up()
    {
        if (
            Schema::hasTable('vendor_application_verification_items') &&
            !Schema::hasColumn('vendor_application_verification_items', 'revision_fields')
        ) {
            Schema::table('vendor_application_verification_items', function (Blueprint $table) {
                $table->json('revision_fields')->nullable()->after('note');
            });
        }
    }

    public function down()
    {
        if (
            Schema::hasTable('vendor_application_verification_items') &&
            Schema::hasColumn('vendor_application_verification_items', 'revision_fields')
        ) {
            Schema::table('vendor_application_verification_items', function (Blueprint $table) {
                $table->dropColumn('revision_fields');
            });
        }
    }
}
