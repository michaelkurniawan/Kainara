<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => 1,
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'origin' => $this->faker->word,
            'type' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomNumber(),
            'image' => 'images/default.jpg',
        ];
    }
}
