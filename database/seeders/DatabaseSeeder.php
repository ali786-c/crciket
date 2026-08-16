<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $admin = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@cricketdraft.test',
            'password' => 'password',
        ]);

        $admin->assignRole('admin');

        $superAdmin = User::factory()->create([
            'name' => 'Platform Super Admin',
            'email' => 'superadmin@cricketdraft.test',
            'password' => 'password',
        ]);
        $superAdmin->assignRole('super_admin');
    }
}
