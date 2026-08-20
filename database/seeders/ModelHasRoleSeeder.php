<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasRoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Assign one role to each seeded user.
     */
    public function run(): void
    {
        $roleIds = DB::table('roles')->pluck('id', 'name');
        $userIds = DB::table('users')->orderBy('id')->pluck('id')->all();

        $assignments = [
            'admin' => array_slice($userIds, 0, 1),
            'manager' => array_slice($userIds, 1, 2),
            'support' => array_slice($userIds, 3, 2),
            'customer' => array_slice($userIds, 5),
        ];

        foreach ($assignments as $roleName => $ids) {
            foreach ($ids as $userId) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleIds[$roleName],
                    'model_type' => User::class,
                    'model_id' => $userId,
                ]);
            }
        }
    }
}
