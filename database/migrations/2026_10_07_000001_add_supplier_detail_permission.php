<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AddSupplierDetailPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $perm = Permission::firstOrCreate([
                'name' => 'supplier-detail',
                'guard_name' => 'web',
            ]);

            // Assign ke Super Admin dan Admin IT
            foreach (['Super Admin', 'Admin IT'] as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role && !$role->hasPermissionTo($perm)) {
                    $role->givePermissionTo($perm);
                }
            }

            // Assign ke Procurement
            $procurement = Role::where('name', 'Procurement')->first();
            if ($procurement && !$procurement->hasPermissionTo($perm)) {
                $procurement->givePermissionTo($perm);
            }

            // Assign ke QA, Apoteker, Specialist (Supplier Detail & Rekualifikasi Initiate)
            $rekualifikasiInitiate = Permission::where('name', 'rekualifikasi-initiate')->first();

            foreach (['Quality Assurance', 'Apoteker', 'Specialist'] as $roleName) {
                $qaRole = Role::where('name', $roleName)->first();
                if ($qaRole) {
                    if (!$qaRole->hasPermissionTo($perm)) {
                        $qaRole->givePermissionTo($perm);
                    }
                    if ($rekualifikasiInitiate && !$qaRole->hasPermissionTo($rekualifikasiInitiate)) {
                        $qaRole->givePermissionTo($rekualifikasiInitiate);
                    }
                }
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Log or ignore if tables do not exist yet during fresh migration
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            $perm = Permission::where('name', 'supplier-detail')->first();
            if ($perm) {
                $perm->delete();
            }
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Ignore
        }
    }
}
