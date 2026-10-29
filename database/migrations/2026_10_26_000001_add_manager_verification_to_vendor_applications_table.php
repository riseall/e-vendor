<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddManagerVerificationToVendorApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ubah status enum menjadi varchar(30) agar mendukung status multi-level verified_admin
        try {
            DB::statement("ALTER TABLE vendor_applications MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'draft'");
        } catch (\Exception $e) {
            // Fallback jika bukan MySQL atau jika sudah varchar
        }

        Schema::table('vendor_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_applications', 'manager_verified_by')) {
                $table->foreignId('manager_verified_by')
                    ->nullable()
                    ->after('verified_by')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('vendor_applications', 'manager_verified_at')) {
                $table->timestamp('manager_verified_at')
                    ->nullable()
                    ->after('manager_verified_by');
            }

            if (!Schema::hasColumn('vendor_applications', 'manager_note')) {
                $table->text('manager_note')
                    ->nullable()
                    ->after('manager_verified_at');
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
            if (Schema::hasColumn('vendor_applications', 'manager_verified_by')) {
                $table->dropForeign(['manager_verified_by']);
                $table->dropColumn('manager_verified_by');
            }

            if (Schema::hasColumn('vendor_applications', 'manager_verified_at')) {
                $table->dropColumn('manager_verified_at');
            }

            if (Schema::hasColumn('vendor_applications', 'manager_note')) {
                $table->dropColumn('manager_note');
            }
        });
    }
}
