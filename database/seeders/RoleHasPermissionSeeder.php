<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Grant each role its set of permissions.
     */
    public function run(): void
    {
        $roleIds = DB::table('roles')->pluck('id', 'name');
        $permissionIds = DB::table('permissions')->pluck('id', 'name');

        $map = [
            'admin' => $permissionIds->keys()->all(),
            'manager' => [
                'products.view', 'products.create', 'products.update',
                'orders.view', 'orders.update', 'orders.cancel',
                'customers.view', 'customers.update',
                'payments.view',
            ],
            'support' => [
                'orders.view', 'orders.update', 'customers.view', 'payments.view',
            ],
            'customer' => [],
        ];

        foreach ($map as $roleName => $permissionNames) {
            foreach ($permissionNames as $permissionName) {
                DB::table('role_has_permissions')->insert([
                    'role_id' => $roleIds[$roleName],
                    'permission_id' => $permissionIds[$permissionName],
                ]);
            }
        }
    }
}
