<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductReview;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductReview>
 */
class ProductReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = ProductReview::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->boolean(70) ? fake()->sentence(rand(7, 15)) : null,
        ];
    }

    public function highRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(4, 5),
        ]);
    }

    public function lowRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(1, 2),
        ]);
    }
}
