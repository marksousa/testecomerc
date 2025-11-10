<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $defaultImagePath = database_path('factories/images/default.jpg');

        $storedPath = Storage::disk('public')->putFile('products', new File($defaultImagePath));

        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 10, 20),
            'photo' => $storedPath,
        ];
    }
}
