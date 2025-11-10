<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_it_create_new_order_and_send_email_to_customer(): void
    {
        Mail::fake();
        $customer = \App\Models\Customer::factory()->create();
        $product1 = \App\Models\Product::factory()->create(['price' => 30.00]);
        $product2 = \App\Models\Product::factory()->create(['price' => 70.00]);

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'status' => 'pending',
            'total_amount' => 130.00,
        ]);

        Mail::assertSent(\App\Mail\OrderCreated::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email);
        });

    }

    public function test_it_fails_to_create_order_with_invalid_data(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_id' => null,
            'products' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customer_id', 'products']);
    }

    public function test_it_creates_order_with_multiple_products()
    {
        $order = Order::factory()->withProducts(3)->create();

        $this->assertCount(3, $order->products);
        $this->assertGreaterThan(0, $order->total_amount);
    }

    public function test_it_creates_order_with_specific_products_and_quantities()
    {
        $product1 = \App\Models\Product::factory()->create(['price' => 10.00]);
        $product2 = \App\Models\Product::factory()->create(['price' => 20.00]);

        $order = Order::factory()->withSpecificProducts([
            ['product' => $product1, 'quantity' => 2],
            ['product' => $product2, 'quantity' => 3],
        ])->create();

        $this->assertCount(2, $order->products);
        $this->assertEquals(80.00, $order->total_amount); // (10*2) + (20*3)
    }
}
