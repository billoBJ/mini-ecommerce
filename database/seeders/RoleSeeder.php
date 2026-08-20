<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's roles.
     */
    public function run(): void
    {
        $roles = ['admin', 'manager', 'support', 'customer'];

        foreach ($roles as $name) {
            DB::table('roles')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
