<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        $sleeves = ['long', 'short'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $oneSize = 'One Size';
        $colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow', 'Pink', 'Purple', 'Orange', 'Gray', 'Brown', 'Navy'];

        $categories->each(function (Category $category) use ($sizes, $oneSize, $colors, $sleeves) {

            // Generate produk secara manual agar bisa atur gender berdasarkan category_id
            for ($i = 0; $i < rand(10, 20); $i++) {

                // Tentukan gender berdasarkan category_id
                $gender = match ($category->id) {
                    1 => fake()->randomElement(['men', 'women']),
                    2 => 'unisex',
                    default => fake()->randomElement(['men', 'women', 'unisex']),
                };

                $product = Product::factory()->create([
                    'category_id' => $category->id,
                    'gender' => $gender,
                ]);

                // Logika product variant
                if ($category->id === 1) {
                    $numColors = rand(1, 4);
                    $productColors = fake()->randomElements($colors, $numColors);

                    foreach ($productColors as $color) {
                        foreach ($sizes as $size) {
                            if ($product->gender === 'men') {
                                foreach ($sleeves as $sleeve) {
                                    ProductVariant::factory()->create([
                                        'product_id' => $product->id,
                                        'size' => $size,
                                        'color' => $color,
                                        'sleeve' => $sleeve,
                                        'stock' => fake()->numberBetween(10, 150),
                                        'price' => fake()->boolean(20) ? fake()->randomFloat(2, $product->price * 0.9, $product->price * 1.1) : null,
                                    ]);
                                }
                            } else {
                                ProductVariant::factory()->create([
                                    'product_id' => $product->id,
                                    'size' => $size,
                                    'color' => $color,
                                    'sleeve' => null,
                                    'stock' => fake()->numberBetween(10, 150),
                                    'price' => fake()->boolean(20) ? fake()->randomFloat(2, $product->price * 0.9, $product->price * 1.1) : null,
                                ]);
                            }
                        }
                    }
                } elseif ($category->id === 2) {
                    ProductVariant::factory()->create([
                        'product_id' => $product->id,
                        'size' => $oneSize,
                        'color' => fake()->randomElement($colors),
                        'sleeve' => null,
                        'stock' => fake()->numberBetween(50, 500),
                        'price' => fake()->boolean(30) ? fake()->randomFloat(2, $product->price * 0.9, $product->price * 1.1) : null,
                    ]);
                }

            } // End for
        });
    }
}
