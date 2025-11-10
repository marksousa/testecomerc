<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $first = $this->faker->firstName();
        $last = $this->faker->lastName();
        $suffix = $this->faker->unique()->numberBetween(1, 99999);

        return [
            'name' => "{$first} {$last}",
            'email' => strtolower("{$first}.{$last}.{$suffix}@example.com"),
            'phone' => $this->faker->phoneNumber(),
            'birthdate' => $this->faker->date(),
            'address' => $this->faker->address(),
            'address_line_two' => $this->faker->secondaryAddress(),
            'neighborhood' => $this->faker->word(),
            'zip_code' => $this->faker->postcode(),
        ];
    }
}
