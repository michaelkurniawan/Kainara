<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Gender; // Import the Gender model
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productName = fake()->unique()->words(rand(2, 4), true);
        $slug = Str::slug($productName);

        // Get a random existing vendor ID (ensure vendors are seeded first)
        $vendorId = Vendor::inRandomOrder()->first()->id ?? Vendor::factory();

        // Determine gender_id based on category (will be overridden in ProductSeeder for specific logic)
        $genderId = Gender::inRandomOrder()->first()->id ?? Gender::factory(); // Default random

        return [
            'category_id' => Category::factory(),
            'vendor_id' => $vendorId,
            'gender_id' => $genderId, // Assign a random gender ID by default (will be refined in seeder)
            'name' => $productName,
            'slug' => $slug,
            'origin' => fake()->country(),
            'description' => fake()->paragraphs(rand(3, 7), true),
            'price' => fake()->randomFloat(2, 10000, 5000000),
            'image' => 'product_placeholder_' . rand(1, 10) . '.jpg',
            'material' => fake()->randomElement(['Cotton', 'Wool', 'Polyester', 'Leather', 'Silk']),
        ];
    }
}
