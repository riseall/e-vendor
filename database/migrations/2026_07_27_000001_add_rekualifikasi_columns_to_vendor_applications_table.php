<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRekualifikasiColumnsToVendorApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_applications', 'type')) {
                $table->enum('type', ['initial', 'rekualifikasi'])
                    ->default('initial')
                    ->after('user_id');
            }

            if (!Schema::hasColumn('vendor_applications', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('valid_until')
                    ->constrained('vendor_applications')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('vendor_applications', 'requalification_reason')) {
                $table->string('requalification_reason')
                    ->nullable()
                    ->after('parent_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_applications', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_applications', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }

            if (Schema::hasColumn('vendor_applications', 'requalification_reason')) {
                $table->dropColumn('requalification_reason');
            }

            if (Schema::hasColumn('vendor_applications', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
}
