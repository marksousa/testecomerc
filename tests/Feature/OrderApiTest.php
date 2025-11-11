<?php

namespace Tests\Feature;

use App\Mail\OrderCreated;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
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
        $customer = Customer::factory()->create();
        $product1 = Product::factory()->create(['price' => 30.00]);
        $product2 = Product::factory()->create(['price' => 70.00]);

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

        Mail::assertSent(OrderCreated::class, function ($mail) use ($customer) {
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
        $product1 = Product::factory()->create(['price' => 10.00]);
        $product2 = Product::factory()->create(['price' => 20.00]);

        $order = Order::factory()->withSpecificProducts([
            ['product' => $product1, 'quantity' => 2],
            ['product' => $product2, 'quantity' => 3],
        ])->create();

        $this->assertCount(2, $order->products);
        $this->assertEquals(80.00, $order->total_amount); // (10*2) + (20*3)
    }

    public function test_it_can_create_order_with_multiple_items_and_send_email(): void
    {
        Mail::fake();

        $customer = Customer::factory()->create();
        $product1 = Product::factory()->create(['price' => 10.00]);
        $product2 = Product::factory()->create(['price' => 5.00]);
        $product3 = Product::factory()->create(['price' => 8.00]);

        $payload = [
            'customer_id' => $customer->id,
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 3],
                ['product_id' => $product3->id, 'quantity' => 1],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'status' => 'pending',
            'total_amount' => 43.00, // (10*2) + (5*3) + (8*1)
        ]);

        Mail::assertSent(OrderCreated::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email);
        });
    }

    public function test_it_validates_customer_exists(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_id' => 99999,
            'products' => [
                ['product_id' => 1, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['customer_id']);
    }

    public function test_it_validates_product_exists(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'products' => [
                ['product_id' => 99999, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['products.0.product_id']);
    }

    public function test_it_validates_minimum_quantity(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'products' => [
                ['product_id' => $product->id, 'quantity' => 0],
            ],
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['products.0.quantity']);
    }

    public function test_it_can_list_orders_by_customer(): void
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        Order::factory()->create(['customer_id' => $customer1->id]);
        Order::factory()->create(['customer_id' => $customer1->id]);
        Order::factory()->create(['customer_id' => $customer2->id]);

        $response = $this->getJson("/api/customers/{$customer1->id}/orders");

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }

    public function test_soft_delete_cascades_to_order_items(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        $order->products()->attach($product->id, ['quantity' => 2, 'price' => 10.00]);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(204);

        // Verificar se pedido está marcado como deletado
        $this->assertNotNull(Order::withTrashed()->find($order->id)->deleted_at);

        // Verificar se itens também estão deletados (soft delete)
        $this->assertDatabaseMissing('order_product', [
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_it_can_get_order_with_relations(): void
    {
        $customer = Customer::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $order = Order::factory()->create(['customer_id' => $customer->id]);
        $order->products()->attach([
            $product1->id => ['quantity' => 2, 'price' => 10.00],
            $product2->id => ['quantity' => 1, 'price' => 5.00],
        ]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('customer.id', $customer->id)
                 ->assertJsonPath('products', function ($products) {
                     return count($products) === 2;
                 });
    }
}
