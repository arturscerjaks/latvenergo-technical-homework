<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Create an order, unless quantity is insufficient
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1']
        ]);

        /**
         * Should use transactions since writing to multiple tables,
         * and at any one point it might fail due to insufficient stock or something else
         *
         * Locks should be used since multiple users might order the same thing at roughly
         * the same time, just so two people don't both order the last product
         */
        return DB::transaction(function () use ($validated, $user) {

            // Get the product IDs
            $productIds = collect($validated['items'])
                ->pluck('id')
                ->unique()
                ->values();

            // Get the products from DB in an array, lock the table
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get(['id', 'name', 'sku', 'qty', 'price'])
                ->keyBy('id');

            // Check if order can even be created or if any are out of stock
            foreach ($validated['items'] as $item) {
                /** @var Product $product */
                $product = $products[$item['id']];

                if ($product->qty < $item['qty']) {
                    // Fail validation
                    throw ValidationException::withMessages([
                        'stock' => [
                            "Insufficient stock for {$product->name}. Available: {$product->qty}"
                        ]
                    ]);
                }
            }

            // Create Order so OrderItems can use it as a parent
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => 0,
                'total_amount' => 0
            ]);

            $totalPrice = 0;
            $totalAmount = 0;

            // Create order items + deduct stock
            foreach ($validated['items'] as $item) {
                /** @var Product $product */
                $product = $products[$item['id']];
                $lineTotal = $product->price * $item['qty'];

                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price_at_order' => $product->price,
                    'qty' => $item['qty'],
                    'line_total' => $lineTotal
                ]);

                // Deduct inventory
                $product->decrement('qty', $item['qty']);

                $totalPrice += $lineTotal;
                $totalAmount += $item['qty'];
            }

            // Update the order totals
            $order->update([
                'total_price' => $totalPrice,
                'total_amount' => $totalAmount
            ]);

            // Finally, return a response
            return response()->json([
                'message' => 'Order created successfully.',
                'order_id' => $order->id,
                'total_price' => $totalPrice,
                'total_amount' => $totalAmount,
            ], 201);
        });
    }

    /**
     * Show more about a specific order, retrieve its order items
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
{
    $requestUser = request()->user();

    // Eager load relationships
    $order->load(['orderItems', 'user']);

    // For now let only the initial order creator view their orders
    if ($order->user_id !== $requestUser?->id) {
        return response()->json([
            'message' => 'You did not make the requested order.'
        ], 403);
    }

    return response()->json([
        'data' => [
            'id' => $order->id,
            'status' => $order->status,
            'total_amount' => $order->total_amount,
            'total_price' => $order->total_price,
            'created_at' => $order->created_at,
            'user_id' => (string) $order->user_id,
            'items' => $order->orderItems->map(fn ($item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'price_at_order' => $item->price_at_order,
                'qty' => $item->qty,
                'line_total' => $item->line_total,
            ]),
        ]
    ]);
}
}
