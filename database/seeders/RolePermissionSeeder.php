<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage tournaments',
            'manage teams',
            'manage players',
            'manage users',
            'approve players',
            'configure draft',
            'control draft',
            'undo latest pick',
            'make draft pick',
            'view public draft',
            'manage system',
            'manage api clients',
            'view system health',
            'revoke api tokens',
            'view all audit logs',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $captain = Role::findOrCreate('captain', 'web');
        $player = Role::findOrCreate('player', 'web');
        $superAdmin = Role::findOrCreate('super_admin', 'web');

        $admin->syncPermissions($permissions);
        $captain->syncPermissions(['make draft pick', 'view public draft']);
        $player->syncPermissions(['view public draft']);
        $superAdmin->syncPermissions($permissions);
    }
}
