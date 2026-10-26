<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AddNotificationPermissions extends Migration
{
    private const NOTIFICATION_PERMISSIONS = [
        'notify-procurement-verification',
        'notify-qa-risk-assessment',
        'notify-qa-audit',
        'notify-evaluasi-alert',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            foreach (self::NOTIFICATION_PERMISSIONS as $permName) {
                Permission::firstOrCreate([
                    'name' => $permName,
                    'guard_name' => 'web',
                ]);
            }

            // Assign notify-procurement-verification ke Verifikator dan Procurement
            foreach (['Verifikator', 'Procurement'] as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role && !$role->hasPermissionTo('notify-procurement-verification')) {
                    $role->givePermissionTo('notify-procurement-verification');
                }
            }

            // Assign notify-qa-risk-assessment dan notify-qa-audit ke QA, Apoteker, Specialist
            foreach (['Quality Assurance', 'Apoteker', 'Specialist'] as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    if (!$role->hasPermissionTo('notify-qa-risk-assessment')) {
                        $role->givePermissionTo('notify-qa-risk-assessment');
                    }
                    if (!$role->hasPermissionTo('notify-qa-audit')) {
                        $role->givePermissionTo('notify-qa-audit');
                    }
                }
            }

            // Assign notify-evaluasi-alert ke Procurement dan QA
            foreach (['Procurement', 'Quality Assurance'] as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role && !$role->hasPermissionTo('notify-evaluasi-alert')) {
                    $role->givePermissionTo('notify-evaluasi-alert');
                }
            }

            // Pastikan Super Admin dan Admin IT tidak ter-assign notify-* secara default agar inbox bersih
            foreach (['Super Admin', 'Admin IT'] as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    foreach (self::NOTIFICATION_PERMISSIONS as $permName) {
                        if ($role->hasPermissionTo($permName)) {
                            $role->revokePermissionTo($permName);
                        }
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

            foreach (self::NOTIFICATION_PERMISSIONS as $permName) {
                $perm = Permission::where('name', $permName)->first();
                if ($perm) {
                    $perm->delete();
                }
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Ignore
        }
    }
}
