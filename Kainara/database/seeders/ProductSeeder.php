<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $oneSize = 'One Size';
        $colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow', 'Pink', 'Purple', 'Orange', 'Gray', 'Brown', 'Navy'];

        $categories->each(function (Category $category) use ($sizes, $oneSize, $colors) {
            Product::factory(rand(10, 20))->create([
                'category_id' => $category->id,
            ])->each(function (Product $product) use ($sizes, $oneSize, $colors) {

                if ($product->category_id === 1) { 
                    $numColorsForProduct = rand(1, 4);
                    $productColors = fake()->randomElements($colors, $numColorsForProduct);

                    foreach ($productColors as $color) {
                        foreach ($sizes as $size) {
                            if (fake()->boolean(85)) { 
                                ProductVariant::factory()->create([
                                    'product_id' => $product->id,
                                    'size' => $size,
                                    'color' => $color,
                                    'stock' => fake()->numberBetween(10, 150),
                                    'price' => fake()->boolean(20) ? fake()->randomFloat(2, $product->price * 0.9, $product->price * 1.1) : null,
                                ]);
                            }
                        }
                    }
                } elseif ($product->category_id === 2) {
                    ProductVariant::factory()->create([
                        'product_id' => $product->id,
                        'size' => $oneSize,
                        'color' => fake()->randomElement($colors),
                        'stock' => fake()->numberBetween(50, 500),
                        'price' => fake()->boolean(30) ? fake()->randomFloat(2, $product->price * 0.9, $product->price * 1.1) : null,
                    ]);
                } else {
                    $hasOneSizeOption = fake()->boolean(20);

                    if ($hasOneSizeOption) {
                        ProductVariant::factory()->create([
                            'product_id' => $product->id,
                            'size' => $oneSize,
                            'color' => fake()->randomElement($colors),
                            'stock' => fake()->numberBetween(50, 500),
                            'price' => fake()->boolean(30) ? fake()->randomFloat(2, $product->price * 0.9, $product->price * 1.1) : null,
                        ]);
                    } else {
                        $numColorsForProduct = rand(1, 4);
                        $productColors = fake()->randomElements($colors, $numColorsForProduct);

                        foreach ($productColors as $color) {
                            foreach ($sizes as $size) {
                                if (fake()->boolean(85)) {
                                    ProductVariant::factory()->create([
                                        'product_id' => $product->id,
                                        'size' => $size,
                                        'color' => $color,
                                        'stock' => fake()->numberBetween(10, 150),
                                        'price' => fake()->boolean(20) ? fake()->randomFloat(2, $product->price * 0.9, $product->price * 1.1) : null,
                                    ]);
                                }
                            }
                        }
                    }
                }
            });
        });
    }
}