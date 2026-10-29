<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CreateManagerAndGmRolesAndPermissions extends Migration
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

            // 1. Daftarkan permission verifikasi-verify-manager
            $perm = Permission::firstOrCreate([
                'name' => 'verifikasi-verify-manager',
                'guard_name' => 'web',
            ]);

            // 2. Buat Role Manager dan General Manager jika belum ada
            $roleManager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
            $roleGm = Role::firstOrCreate(['name' => 'General Manager', 'guard_name' => 'web']);

            // 3. Hak akses untuk Role Manager
            $managerPermissions = [
                'verifikasi-list',
                'verifikasi-detail',
                'verifikasi-verify-manager',
                'evaluasi-list',
                'evaluasi-verify-manager',
                'supplier-list',
                'supplier-detail',
                'rekualifikasi-list',
                'rekualifikasi-initiate',
                'notify-procurement-verification',
                'notify-evaluasi-alert',
            ];
            $existingManagerPerms = Permission::whereIn('name', $managerPermissions)->get();
            $roleManager->syncPermissions($existingManagerPerms);

            // 4. Hak akses untuk Role General Manager
            $gmPermissions = [
                'verifikasi-list',
                'verifikasi-detail',
                'evaluasi-list',
                'evaluasi-approve-gm',
                'supplier-list',
                'supplier-detail',
                'rekualifikasi-list',
            ];
            $existingGmPerms = Permission::whereIn('name', $gmPermissions)->get();
            $roleGm->syncPermissions($existingGmPerms);

            // 5. Berikan juga ke Super Admin, Admin IT, dan Procurement
            foreach (['Super Admin', 'Admin IT', 'Procurement'] as $roleName) {
                $r = Role::where('name', $roleName)->first();
                if ($r) {
                    $r->givePermissionTo('verifikasi-verify-manager');
                }
            }
        } catch (\Exception $e) {
            // Log or ignore if tables not ready
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

            $perm = Permission::where('name', 'verifikasi-verify-manager')->first();
            if ($perm) {
                $perm->delete();
            }
        } catch (\Exception $e) {
            // Ignore
        }
    }
}
