<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;
    protected function setUp(): void
    {
        parent::setUp();

        // Create a user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!')
        ]);

        // Create API token
        $this->token = $this->user->createToken('test-user')->plainTextToken;
    }

    public function test_fails_if_product_stock_is_insufficient()
    {
        $product = Product::factory()->create([
            'qty' => 1,
            'price' => 50
        ]);

        $payload = [
            'items' => [
                ['id' => $product->id, 'qty' => 5]
            ]
        ];

        $response = $this->postJson('/api/orders/create', $payload, [
            'Authorization' => "Bearer {$this->token}"
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment([
                     'stock' => ["Insufficient stock for {$product->name}. Available: 1"]
                 ]);
    }

    public function test_creates_order_successfully()
    {
        $product1 = Product::factory()->create(['qty' => 10, 'price' => 100]);
        $product2 = Product::factory()->create(['qty' => 5, 'price' => 50]);

        $payload = [
            'items' => [
                ['id' => $product1->id, 'qty' => 2],
                ['id' => $product2->id, 'qty' => 1],
            ]
        ];

        $response = $this->postJson('/api/orders/create', $payload, [
            'Authorization' => "Bearer {$this->token}"
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'order_id',
                     'total_amount',
                     'total_price',
                 ]);

        $this->assertDatabaseHas('orders', [
            'id' => $response['order_id'],
            'total_amount' => 3,
            'total_price' => 250,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product1->id,
            'qty' => 2,
            'line_total' => 200,
        ]);
    }

    public function test_user_cannot_view_others_order()
    {
        $otherUser = User::factory()->create();
        $order = Order::factory()->for($otherUser)->create();

        $response = $this->getJson("/api/orders/show/{$order->id}", [
            'Authorization' => "Bearer {$this->token}"
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'message' => 'You did not make the requested order.'
                 ]);
    }
}
