<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTwoTierApprovalToVendorAnnualEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ubah status enum menjadi varchar(30) agar mendukung status 2-tier approval (draft, verified_manager, approved, rejected)
        try {
            DB::statement("ALTER TABLE vendor_annual_evaluations MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'draft'");
        } catch (\Exception $e) {
            // Fallback jika bukan MySQL
        }

        Schema::table('vendor_annual_evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_annual_evaluations', 'manager_approved_by')) {
                $table->unsignedBigInteger('manager_approved_by')->nullable()->after('notes');
                $table->foreign('manager_approved_by')->references('id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('vendor_annual_evaluations', 'manager_approved_at')) {
                $table->timestamp('manager_approved_at')->nullable()->after('manager_approved_by');
            }

            if (!Schema::hasColumn('vendor_annual_evaluations', 'manager_notes')) {
                $table->text('manager_notes')->nullable()->after('manager_approved_at');
            }

            if (!Schema::hasColumn('vendor_annual_evaluations', 'gm_approved_by')) {
                $table->unsignedBigInteger('gm_approved_by')->nullable()->after('manager_notes');
                $table->foreign('gm_approved_by')->references('id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('vendor_annual_evaluations', 'gm_approved_at')) {
                $table->timestamp('gm_approved_at')->nullable()->after('gm_approved_by');
            }

            if (!Schema::hasColumn('vendor_annual_evaluations', 'gm_notes')) {
                $table->text('gm_notes')->nullable()->after('gm_approved_at');
            }
        });

        // Daftarkan permission baru secara otomatis
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            foreach (['evaluasi-verify-manager', 'evaluasi-approve-gm'] as $perm) {
                \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }
            foreach (['Super Admin', 'Admin IT', 'Procurement'] as $roleName) {
                $r = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
                if ($r) {
                    $r->givePermissionTo(['evaluasi-verify-manager', 'evaluasi-approve-gm']);
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika tabel permission belum siap
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_annual_evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_annual_evaluations', 'gm_approved_by')) {
                $table->dropForeign(['gm_approved_by']);
                $table->dropColumn('gm_approved_by');
            }
            if (Schema::hasColumn('vendor_annual_evaluations', 'gm_approved_at')) {
                $table->dropColumn('gm_approved_at');
            }
            if (Schema::hasColumn('vendor_annual_evaluations', 'gm_notes')) {
                $table->dropColumn('gm_notes');
            }
            if (Schema::hasColumn('vendor_annual_evaluations', 'manager_approved_by')) {
                $table->dropForeign(['manager_approved_by']);
                $table->dropColumn('manager_approved_by');
            }
            if (Schema::hasColumn('vendor_annual_evaluations', 'manager_approved_at')) {
                $table->dropColumn('manager_approved_at');
            }
            if (Schema::hasColumn('vendor_annual_evaluations', 'manager_notes')) {
                $table->dropColumn('manager_notes');
            }
        });

        try {
            DB::statement("ALTER TABLE vendor_annual_evaluations MODIFY COLUMN status ENUM('draft', 'submitted', 'approved', 'rejected') NOT NULL DEFAULT 'draft'");
        } catch (\Exception $e) {
            // Fallback jika bukan MySQL
        }
    }
}
