<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Super Admin',
            'Admin IT',
            'Verifikator',
            'Procurement',
            'Quality Assurance',
            'Apoteker',
            'Specialist',
            'Supplier'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $adminIT = User::firstOrCreate(
            ['username' => '03130'],
            [
                'name' => 'RIZAL NUGROHO',
                'email' => 'rizal.nugroho@phapros.co.id',
                'password' => bcrypt('12345678'),
            ]
        );
        if (!$adminIT->hasRole('Admin IT')) {
            $adminIT->assignRole('Admin IT');
        }
    }
}
