<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TeamSeeder::class,
            TeamUserSeeder::class,
            TeamInvitationSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            RoleHasPermissionSeeder::class,
            ModelHasRoleSeeder::class,
            ModelHasPermissionSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            OrderSeeder::class,
            WebhookSeeder::class,
        ]);
    }
}
