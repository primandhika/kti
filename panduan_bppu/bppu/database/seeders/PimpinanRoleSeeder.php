<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PimpinanRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'pimpinan']);

        $permissions = [
            'view dashboard',
            'view statistics',
            'view reports',
            'view work units',
            'view financial data',
            'view sales data',
            'view inventory data',
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }

        $this->command->info('Role pimpinan dengan permissions berhasil dibuat!');
    }
}
