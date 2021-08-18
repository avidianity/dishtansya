<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function authenticate()
    {
        $email = $this->faker->email;
        $password = $this->faker->password;

        $user = User::create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return auth()->attempt(['email' => $email, 'password' => $password]);
    }

    /**
     * Test order with successful response
     *
     * @return void
     */
    public function test_order_with_successful_response()
    {
        $token = $this->authenticate();

        $product = Product::factory()->create(['available_stock' => 50]);

        $response = $this->post(
            '/api/order',
            [
                'product_id' => $product->id,
                'quantity' => 1,
            ],
            [
                'Authorization' => sprintf('Bearer %s', $token),
            ]
        );

        $response->assertStatus(201)
            ->assertExactJson(['message' => 'You have sucessfully ordered this product.']);
    }

    /**
     * Test order with unavailable stocks
     *
     * @return void
     */
    public function test_order_with_unavailable_stocks()
    {
        $token = $this->authenticate();

        $product = Product::factory()->create(['available_stock' => 50]);

        $response = $this->post(
            '/api/order',
            [
                'product_id' => $product->id,
                'quantity' => 100,
            ],
            [
                'Authorization' => sprintf('Bearer %s', $token),
            ]
        );

        $response->assertStatus(400)
            ->assertExactJson(['message' => 'Failed to order this product due to unavailability of the stock']);
    }

    /**
     * Test product stocks deduct when an order is placed
     *
     * @return void
     */
    public function test_product_stocks_deduct_when_order_is_placed()
    {
        $product = Product::factory()->create(['available_stock' => 50]);

        $product->orders()->create(['quantity' => 5]);

        $product->refresh();

        $this->assertTrue($product->available_stock === 45);
    }
}
