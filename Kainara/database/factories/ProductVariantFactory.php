<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(), // default, akan di-override
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'color' => 'Black', // default warna
            'stock' => $this->faker->numberBetween(5, 50),
            'price' => null,
        ];
    }

    public function withColorByIndex(int $index)
    {
        $colors = [
            'Brown',  // batik1
            'Red',    // batik2
            'Brown',  // batik3
            'Blue',   // batik4
            'Blue',   // batik5
            'Blue',   // batik6
            'Grey',   // batik7
            'Beige',  // batik8
            'Yellow', // batik9
        ];

        $color = $colors[$index] ?? 'Black';

        return $this->state(fn () => ['color' => $color]);
    }

}
