<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Return a list of products
     *
     * @return void
     */
    public function index(Request $request): JsonResponse
    {
        $pagination = (int) $request->query('page_size', 20);
        $orderBy = (string) $request->query('order_by', 'sku');

        $productList = Product::query()
            ->select(Product::FRONTFACING_LIST_ATTRIBUTES)
            ->orderBy($orderBy)
            ->paginate($pagination);

        return response()->json($productList);
    }

    /**
     * Show more about a specific product
     *
     * @param Product $product
     * @return void
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => (string) $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'description' => $product->description,
                'price' => $product->price,
                'qty' => $product->qty,
                'in_stock' => $product->qty > 0,
            ],
        ]);
    }
}
