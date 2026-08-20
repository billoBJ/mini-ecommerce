<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed team memberships and back-fill each user's current team.
     */
    public function run(): void
    {
        $teamIds = DB::table('teams')->pluck('id')->all();
        $userIds = DB::table('users')->pluck('id')->all();

        $currentTeamByUser = [];

        foreach ($teamIds as $teamId) {
            $members = collect($userIds)->shuffle()->take(random_int(3, 5))->values();

            foreach ($members as $index => $userId) {
                DB::table('team_user')->insert([
                    'team_id' => $teamId,
                    'user_id' => $userId,
                    'role' => $index === 0 ? 'owner' : 'member',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $currentTeamByUser[$userId] ??= $teamId;
            }
        }

        foreach ($currentTeamByUser as $userId => $teamId) {
            DB::table('users')->where('id', $userId)->update(['current_team_id' => $teamId]);
        }
    }
}
