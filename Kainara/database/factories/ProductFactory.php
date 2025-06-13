<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

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
    protected $model = Product::class;

    public function definition(): array
    {
        $productName = fake()->unique()->words(rand(2, 4), true);
        $slug = Str::slug($productName);

        return [
            'category_id' => Category::factory(),
            'name' => $productName,
            'slug' => $slug,
            'origin' => fake()->country(),
            'description' => fake()->paragraphs(rand(3, 7), true),
            'price' => fake()->randomFloat(2, 10000, 5000000),
            'image' => 'product_placeholder_' . rand(1, 10) . '.jpg',
            'gender' => fake()->randomElement(['men', 'women', 'unisex']),    
        ];
    }
}
