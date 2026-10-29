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
            'verifikasi-verify-manager',

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
            'evaluasi-verify-manager',
            'evaluasi-approve-gm',
            'evaluasi-settings',

            // Supplier Terekomendasi
            'supplier-list',
            'supplier-edit-qad',
            'supplier-detail',

            // Notifikasi Email
            'notify-procurement-verification',
            'notify-procurement-manager',
            'notify-qa-risk-assessment',
            'notify-qa-audit',
            'notify-evaluasi-alert',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web',
            ]);
        }

        // Assign operational permissions to Super Admin and Admin IT (excluding notify-* by default to prevent inbox spamming)
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::where('name', 'not like', 'notify-%')->get());
        }

        $adminIT = Role::where('name', 'Admin IT')->first();
        if ($adminIT) {
            $adminIT->syncPermissions(Permission::where('name', 'not like', 'notify-%')->get());
        }

        // Assign module permissions to specific roles
        $verifikator = Role::where('name', 'Verifikator')->first();
        if ($verifikator) {
            $verifikator->syncPermissions([
                'verifikasi-list',
                'verifikasi-detail',
                'verifikasi-approve',
                'verifikasi-reject',
                'notify-procurement-verification',
            ]);
        }

        $qaPermissions = [
            'qa-risk-list',
            'qa-risk-create',
            'qa-risk-store',
            'audit-list',
            'audit-create',
            'audit-verify',
            'audit-result',
            'rekualifikasi-initiate',
            'evaluasi-list',
            'evaluasi-create',
            'supplier-list',
            'supplier-edit-qad',
            'supplier-detail',
            'notify-qa-risk-assessment',
            'notify-qa-audit',
            'notify-evaluasi-alert',
        ];

        $qa = Role::where('name', 'Quality Assurance')->first();
        if ($qa) {
            $qa->syncPermissions($qaPermissions);
        }

        $apoteker = Role::where('name', 'Apoteker')->first();
        if ($apoteker) {
            $apoteker->syncPermissions($qaPermissions);
        }

        $specialist = Role::where('name', 'Specialist')->first();
        if ($specialist) {
            $specialist->syncPermissions($qaPermissions);
        }

        $procurement = Role::where('name', 'Procurement')->first();
        if ($procurement) {
            $procurement->syncPermissions([
                'verifikasi-list',
                'verifikasi-detail',
                'verifikasi-verify-manager',
                'rekualifikasi-list',
                'rekualifikasi-initiate',
                'evaluasi-list',
                'evaluasi-create',
                'evaluasi-approve',
                'evaluasi-verify-manager',
                'evaluasi-approve-gm',
                'supplier-list',
                'supplier-edit-qad',
                'supplier-detail',
                'notify-procurement-verification',
                'notify-evaluasi-alert',
            ]);
        }

        $manager = Role::where('name', 'Manager')->first();
        if ($manager) {
            $manager->syncPermissions([
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
            ]);
        }

        $gm = Role::where('name', 'General Manager')->first();
        if ($gm) {
            $gm->syncPermissions([
                'verifikasi-list',
                'verifikasi-detail',
                'evaluasi-list',
                'evaluasi-approve-gm',
                'supplier-list',
                'supplier-detail',
                'rekualifikasi-list',
            ]);
        }
    }
}
