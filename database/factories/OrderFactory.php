<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'user_id' => $user->id,
            'total_amount' => 0,
            'total_price' => 0,
            'status' => 'created',
        ];
    }

    /**
     * Add Order Items after the Order is created
     */
    public function withItems(int $count = 3): self
    {
        return $this->afterCreating(function (Order $order) use ($count) {
            $totalAmount = 0;
            $totalPrice = 0;

            $products = Product::factory()->count($count)->create();

            foreach ($products as $product) {
                $qty = $this->faker->numberBetween(1, min(5, $product->qty));

                $lineTotal = $product->price * $qty;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price_at_order' => $product->price,
                    'qty' => $qty,
                    'line_total' => $lineTotal,
                ]);

                // Deduct stock
                $product->decrement('qty', $qty);

                $totalAmount +=  $qty;
                $totalPrice += $lineTotal;
            }

            $order->update([
                'total_amount' => $totalAmount,
                'total_price' => $totalPrice,
            ]);
        });
    }
}
