<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'users.list', 'users.create', 'users.edit', 'users.delete',
            'roles.list', 'roles.create', 'roles.edit', 'roles.delete',
            'teams.list', 'teams.create', 'teams.edit', 'teams.delete',
            'settings.view',
        ];

        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm, 'guard_name' => 'web']);
        }

        $superAdmin = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'users.list', 'users.create', 'users.edit',
            'roles.list',
            'teams.list', 'teams.create', 'teams.edit',
            'settings.view',
        ]);

        Role::create(['name' => 'user', 'guard_name' => 'web']);
    }
}
