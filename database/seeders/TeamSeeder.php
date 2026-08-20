<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's teams.
     */
    public function run(): void
    {
        $teams = ['Acme Corp', 'Northwind Traders', 'Globex Retail', 'Initech Supplies', 'Umbrella Goods'];

        foreach ($teams as $name) {
            DB::table('teams')->insert([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
