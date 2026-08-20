<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    use WithoutModelEvents;

    private const TAX_RATE = 0.16;

    /**
     * Seed orders together with their order_items, order_status_history
     * and payment, so every order stays financially and chronologically
     * consistent with its children.
     */
    public function run(): void
    {
        $customerIds = DB::table('customers')->pluck('id')->all();
        $staffUserIds = DB::table('users')->pluck('id')->all();
        $products = DB::table('products')->select('id', 'sku', 'name', 'price')->get();

        foreach (range(1, 25) as $i) {
            $this->seedOrder($customerIds, $staffUserIds, $products);
        }
    }

    private function seedOrder(array $customerIds, array $staffUserIds, Collection $products): void
    {
        $finalStatus = fake()->randomElement([
            'pending', 'pending',
            'confirmed',
            'processing',
            'shipped',
            'completed', 'completed', 'completed',
            'cancelled',
        ]);

        $placedAt = Carbon::now()->subDays(random_int(1, 60))->subHours(random_int(0, 23));

        [$items, $subtotal] = $this->buildItems($products);

        $subtotal = round($subtotal, 2);
        $tax = round($subtotal * self::TAX_RATE, 2);
        $orderDiscount = fake()->boolean(10) ? round($subtotal * 0.05, 2) : 0;
        $total = round($subtotal + $tax - $orderDiscount, 2);

        $orderId = DB::table('orders')->insertGetId([
            'customer_id' => $customerIds[array_rand($customerIds)],
            'user_id' => fake()->boolean(70) ? $staffUserIds[array_rand($staffUserIds)] : null,
            'status' => $finalStatus,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $orderDiscount,
            'total' => $total,
            'currency' => 'USD',
            'notes' => fake()->boolean(20) ? fake()->sentence() : null,
            'created_at' => $placedAt,
            'updated_at' => $placedAt,
        ]);

        foreach ($items as $item) {
            DB::table('order_items')->insert(array_merge($item, [
                'order_id' => $orderId,
                'created_at' => $placedAt,
                'updated_at' => $placedAt,
            ]));
        }

        $lastStatusAt = $this->seedStatusHistory($orderId, $finalStatus, $placedAt, $staffUserIds);

        DB::table('orders')->where('id', $orderId)->update(['updated_at' => $lastStatusAt]);

        $this->seedPayment($orderId, $finalStatus, $total, $placedAt);
    }

    /**
     * @return array{0: array<int, array<string, mixed>>, 1: float}
     */
    private function buildItems(Collection $products): array
    {
        $items = [];
        $subtotal = 0;

        foreach ($products->random(random_int(1, 4)) as $product) {
            $quantity = random_int(1, 3);
            $price = (float) $product->price;
            $discount = fake()->boolean(15) ? round($price * 0.1, 2) : 0;
            $lineTotal = round(($price * $quantity) - $discount, 2);

            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $price,
                'quantity' => $quantity,
                'discount' => $discount,
                'total' => $lineTotal,
            ];

            $subtotal += $lineTotal;
        }

        return [$items, $subtotal];
    }

    private function seedStatusHistory(int $orderId, string $finalStatus, Carbon $placedAt, array $staffUserIds): Carbon
    {
        $timeline = match ($finalStatus) {
            'pending' => ['pending'],
            'confirmed' => ['pending', 'confirmed'],
            'processing' => ['pending', 'confirmed', 'processing'],
            'shipped' => ['pending', 'confirmed', 'processing', 'shipped'],
            'completed' => ['pending', 'confirmed', 'processing', 'shipped', 'completed'],
            'cancelled' => fake()->boolean(50) ? ['pending', 'cancelled'] : ['pending', 'confirmed', 'cancelled'],
        };

        $timestamp = $placedAt->copy();
        $lastUsed = $timestamp->copy();

        foreach ($timeline as $status) {
            DB::table('order_status_history')->insert([
                'order_id' => $orderId,
                'status' => $status,
                'changed_by' => fake()->boolean(80) ? $staffUserIds[array_rand($staffUserIds)] : null,
                'notes' => fake()->boolean(30) ? fake()->sentence() : null,
                'created_at' => $timestamp,
            ]);

            $lastUsed = $timestamp->copy();
            $timestamp = $timestamp->copy()->addHours(random_int(2, 48));
        }

        return $lastUsed;
    }

    private function seedPayment(int $orderId, string $finalStatus, float $total, Carbon $placedAt): void
    {
        if ($finalStatus === 'pending') {
            return;
        }

        if ($finalStatus === 'cancelled' && fake()->boolean(50)) {
            return;
        }

        $status = $finalStatus === 'cancelled' ? 'refunded' : 'approved';
        $paidAt = $placedAt->copy()->addHours(random_int(1, 6));

        DB::table('payments')->insert([
            'order_id' => $orderId,
            'provider' => fake()->randomElement(['stripe', 'paypal', 'mercadopago']),
            'transaction_id' => fake()->unique()->bothify('TXN-########'),
            'status' => $status,
            'amount' => $total,
            'currency' => 'USD',
            'paid_at' => $paidAt,
            'raw_response' => json_encode(['gateway_status' => $status, 'simulated' => true]),
            'created_at' => $paidAt,
            'updated_at' => $paidAt,
        ]);
    }
}
