<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_it_can_get_a_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'name' => $product->name,
            ]);
    }

    public function test_it_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_it_can_paginate_products(): void
    {
        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/products?page=2&per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_it_can_create_a_product(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/products', [
            'name' => 'Pastel de Queijo',
            'price' => '14.30',
            'photo' => UploadedFile::fake()->image('product.jpg'),
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'name' => 'Pastel de Queijo',
        ]);

        Storage::disk('public')->assertExists($response->json('photo'));
    }

    public function test_it_can_update_a_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Pastel de Frango',
            'price' => '20.00',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Pastel de Frango',
            'price' => 20.00,
        ]);
    }

    public function test_it_can_delete_a_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");
        $response->assertStatus(204);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => now(),
        ]);
    }

    public function test_it_can_validate_product_creation(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => '',
            'price' => '-10',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price']);
    }

    public function test_it_can_create_product_with_photo_upload(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('product.jpg', 200, 200);

        $response = $this->postJson('/api/products', [
            'name' => 'Pastel de Carne',
            'price' => 14.55,
            'photo' => $file,
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('name', 'Pastel de Carne');

        $this->assertDatabaseHas('products', [
            'name' => 'Pastel de Carne',
            'price' => 14.55,
        ]);

        // Verifica se arquivo foi armazenado
        Storage::disk('public')->assertExists($response->json('photo'));
    }

    public function test_it_validates_image_format(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/products', [
            'name' => 'Pastel de Carne',
            'price' => 12.50,
            'photo' => $file,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['photo']);
    }

    public function test_it_validates_image_max_size(): void
    {
        $file = UploadedFile::fake()->image('product.jpg')->size(3000); // 3MB > 2MB max

        $response = $this->postJson('/api/products', [
            'name' => 'Pastel de Carne',
            'price' => 12.50,
            'photo' => $file,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['photo']);
    }

    public function test_it_lists_products_with_photo_url(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['photo' => $product->photo]);
    }
}
