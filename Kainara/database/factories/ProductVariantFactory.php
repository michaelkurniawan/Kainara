<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductVariant;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ProductVariant::class;
    
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'size' => fake()->word(),
            'color' => fake()->colorName(),
            'stock' => fake()->numberBetween(0, 100),
            'price' => fake()->boolean(30) ? fake()->randomFloat(2, 10000, 5000000) : null,
        ];
    }
}