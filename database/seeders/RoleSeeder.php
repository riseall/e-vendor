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
            Role::create(['name' => $role]);
        }

        // $adminIT = User::create([
        //     'name' => 'RIZAL NUGROHO',
        //     'username' => '03130',
        //     'email' => 'rizal.nugroho@phapros.co.id',
        //     'password' => bcrypt('12345678'),
        // ]);
        // $adminIT->assignRole('Admin IT');
    }
}
