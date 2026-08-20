<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed customers: some linked to a registered user, some guest checkouts.
     */
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id')->all();
        $linkedUserIds = collect($userIds)->shuffle()->take(6);

        foreach ($linkedUserIds as $userId) {
            $this->insertCustomer($userId);
        }

        foreach (range(1, 9) as $i) {
            $this->insertCustomer(null);
        }
    }

    private function insertCustomer(?int $userId): void
    {
        DB::table('customers')->insert([
            'user_id' => $userId,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional(0.7)->phoneNumber(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->optional(0.3)->secondaryAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->randomElement(['USA', 'Mexico', 'Colombia', 'Argentina', 'Spain']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
