<?php

namespace Database\Factories;

use App\Models\Order;
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
        return [
            'customer_id' => \App\Models\Customer::factory(),
            'status' => $this->faker->randomElement(['pending', 'preparing', 'ready', 'delivered', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }

    public function withProducts(int $count = 3)
    {
        return $this->afterCreating(function (Order $order) use ($count) {
            $products = \App\Models\Product::factory()->count($count)->create();

            foreach ($products as $product) {
                $order->products()->attach($product->id, [
                    'quantity' => $this->faker->numberBetween(1, 5),
                    'price' => $product->price,
                ]);
            }

            $order->update(['total_amount' => $order->calculateTotalAmount()]);
        });
    }

    public function withSpecificProducts(array $productsData)
    {
        return $this->afterCreating(function (Order $order) use ($productsData) {
            $totalAmount = 0;

            foreach ($productsData as $data) {
                $product = $data['product'];
                $quantity = $data['quantity'] ?? 1;

                $order->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

                $totalAmount += ($product->price * $quantity);
            }

            $order->update(['total_amount' => $totalAmount]);
        });
    }

    public function status(string $status)
    {
        return $this->state(function (array $attributes) use ($status) {
            return [
                'status' => $status,
            ];
        });
    }
}
