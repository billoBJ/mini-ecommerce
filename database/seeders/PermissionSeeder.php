<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's permissions.
     */
    public function run(): void
    {
        $permissions = [
            'products.view', 'products.create', 'products.update', 'products.delete',
            'orders.view', 'orders.update', 'orders.cancel',
            'customers.view', 'customers.update',
            'payments.view', 'payments.refund',
            'teams.manage',
        ];

        foreach ($permissions as $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
