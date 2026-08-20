<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 1, 500);

        return [
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('???-####')),
            'description' => fake()->optional()->sentence(),
            'price' => $price,
            'cost' => round($price * fake()->randomFloat(2, 0.4, 0.75), 2),
            'stock' => fake()->numberBetween(0, 100),
            'active' => true,
        ];
    }
}
