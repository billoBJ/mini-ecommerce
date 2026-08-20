<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamInvitationSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed pending and accepted team invitations.
     */
    public function run(): void
    {
        $teamIds = DB::table('teams')->pluck('id')->all();
        $userIds = DB::table('users')->pluck('id')->all();

        foreach (range(1, 8) as $i) {
            $accepted = fake()->boolean(30);

            DB::table('team_invitations')->insert([
                'team_id' => $teamIds[array_rand($teamIds)],
                'email' => fake()->unique()->safeEmail(),
                'role' => fake()->randomElement(['member', 'admin']),
                'invited_by' => $userIds[array_rand($userIds)],
                'expires_at' => now()->addDays(7),
                'accepted_at' => $accepted ? now()->subDays(random_int(1, 5)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
