<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasPermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Grant a couple of one-off direct permissions, bypassing roles entirely.
     */
    public function run(): void
    {
        $permissionIds = DB::table('permissions')->pluck('id', 'name');
        $userIds = DB::table('users')->orderBy('id')->pluck('id')->all();
        $lastTwo = array_slice($userIds, -2);

        DB::table('model_has_permissions')->insert([
            [
                'permission_id' => $permissionIds['orders.view'],
                'model_type' => User::class,
                'model_id' => $lastTwo[0],
            ],
            [
                'permission_id' => $permissionIds['products.view'],
                'model_type' => User::class,
                'model_id' => $lastTwo[1],
            ],
        ]);
    }
}
