<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'sku' => fake()->unique()->bothify('PRD??-####'),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'unit' => fake()->randomElement(['pcs', 'kg', 'liter', 'meter', 'box']),
            'price' => fake()->randomFloat(2, 1000, 1000000),
            'status' => 'active',
            'created_by' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
