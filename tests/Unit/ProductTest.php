<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'price',
            'photo',
        ];

        $product = new Product;
        $this->assertEquals($fillable, $product->getFillable());
    }

    public function test_it_uses_soft_deletes_trait(): void
    {
        $traits = class_uses(Product::class);
        $this->assertContains('Illuminate\Database\Eloquent\SoftDeletes', $traits);
    }

    public function test_it_casts_price_to_float(): void
    {
        $product = Product::factory()->create(['price' => '19.99']);
        $this->assertIsFloat($product->price);
        $this->assertEquals(19.99, $product->price);
    }
}
