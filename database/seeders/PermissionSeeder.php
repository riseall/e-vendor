<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // User Management
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            // Master Role & Permission
            'role-list',
            'role-create',
            'role-edit',
            'role-permission-assign',

            // Verifikasi Pendaftaran
            'verifikasi-list',
            'verifikasi-detail',
            'verifikasi-approve',
            'verifikasi-reject',

            // QA Risk Assessment
            'qa-risk-list',
            'qa-risk-create',
            'qa-risk-store',

            // Audit Vendor
            'audit-list',
            'audit-create',
            'audit-verify',
            'audit-result',

            // Rekualifikasi Vendor
            'rekualifikasi-list',
            'rekualifikasi-initiate',

            // Master Questionnaire
            'questionnaire-list',
            'questionnaire-create',
            'questionnaire-edit',

            // Evaluasi Vendor
            'evaluasi-list',
            'evaluasi-create',
            'evaluasi-approve',
            'evaluasi-settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web',
            ]);
        }

        // Assign all permissions to Super Admin and Admin IT
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        $adminIT = Role::where('name', 'Admin IT')->first();
        if ($adminIT) {
            $adminIT->syncPermissions(Permission::all());
        }

        // Assign module permissions to specific roles
        $verifikator = Role::where('name', 'Verifikator')->first();
        if ($verifikator) {
            $verifikator->syncPermissions([
                'verifikasi-list',
                'verifikasi-detail',
                'verifikasi-approve',
                'verifikasi-reject',
            ]);
        }

        $qa = Role::where('name', 'Quality Assurance')->first();
        if ($qa) {
            $qa->syncPermissions([
                'qa-risk-list',
                'qa-risk-create',
                'qa-risk-store',
                'audit-list',
                'audit-create',
                'audit-verify',
                'audit-result',
                'evaluasi-list',
                'evaluasi-create',
            ]);
        }

        $procurement = Role::where('name', 'Procurement')->first();
        if ($procurement) {
            $procurement->syncPermissions([
                'verifikasi-list',
                'verifikasi-detail',
                'rekualifikasi-list',
                'rekualifikasi-initiate',
                'evaluasi-list',
                'evaluasi-create',
                'evaluasi-approve',
            ]);
        }
    }
}
