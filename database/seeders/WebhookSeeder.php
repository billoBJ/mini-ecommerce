<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebhookSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed sample incoming webhook payloads, processed and pending.
     */
    public function run(): void
    {
        $events = [
            'payment.succeeded', 'payment.failed', 'payment.refunded',
            'order.shipped', 'order.cancelled', 'charge.dispute.created',
        ];

        foreach (range(1, 12) as $i) {
            $processed = fake()->boolean(75);

            DB::table('webhooks')->insert([
                'event' => fake()->randomElement($events),
                'payload' => json_encode([
                    'id' => fake()->uuid(),
                    'received_at' => now()->toIso8601String(),
                    'data' => ['amount' => fake()->randomFloat(2, 5, 500), 'currency' => 'USD'],
                ]),
                'processed' => $processed,
                'processed_at' => $processed ? now()->subMinutes(random_int(1, 500)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
