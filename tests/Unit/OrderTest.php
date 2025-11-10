<?php

namespace Tests\Unit;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes(): void
    {
        $fillable = [
            'customer_id',
            'status',
            'total_amount',
        ];

        $order = new Order;
        $this->assertEquals($fillable, $order->getFillable());
    }

    public function test_it_uses_soft_deletes_trait(): void
    {
        $traits = class_uses(Order::class);
        $this->assertContains('Illuminate\Database\Eloquent\SoftDeletes', $traits);
    }

    public function test_order_belongs_to_customer(): void
    {
        $order = Order::factory()->create();
        $this->assertInstanceOf(\App\Models\Customer::class, $order->customer);
    }

    public function test_status_must_be_valid(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Order::factory()->create(['status' => 'invalid_status']);
    }

    public function test_it_calculates_total_amount_correctly(): void
    {
        $order = Order::factory()->create();
        $product1 = \App\Models\Product::factory()->create(['price' => 50.25]);
        $product2 = \App\Models\Product::factory()->create(['price' => 100.50]);

        $order->products()->attach($product1->id, ['quantity' => 1, 'price' => $product1->price]);
        $order->products()->attach($product2->id, ['quantity' => 2, 'price' => $product2->price]);

        $this->assertEquals(251.25, $order->calculateTotalAmount());
    }

    public function test_it_maintains_price_accorded_in_order_even_if_changed_price_in_product(): void
    {
        $order = Order::factory()->create();
        $product = \App\Models\Product::factory()->create(['price' => 75.00]);

        $order->products()->attach($product->id, ['quantity' => 2, 'price' => $product->price]);

        $product->price = 100.00;
        $product->save();

        $this->assertEquals(150.00, $order->calculateTotalAmount());
    }
}
