<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', Rule::exists(Product::class, 'id')],
            'quantity' => ['required', 'numeric'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->available_stock < $data['quantity']) {
            return response(['message' => 'Failed to order this product due to unavailability of the stock'], 400);
        }

        $product->orders()->create($data);

        return response(['message' => 'You have sucessfully ordered this product.'], 201);
    }
}
