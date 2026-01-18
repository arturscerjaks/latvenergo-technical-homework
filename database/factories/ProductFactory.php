<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
        return [
            'name' => fake()->unique()->words(2, true),
            'sku' => 'SKU-' . fake()->unique()->numerify('#####'),
            'description' => fake()->sentence(12),
            'price' => fake()->randomFloat(2, 1, 100),
            'qty' => fake()->numberBetween(5, 50)
        ];
    }

    /**
     * Out of stock product
     */
    public function outOfStock(): static
    {
        return $this->state(fn () => [
            'qty' => 0,
        ]);
    }
}
