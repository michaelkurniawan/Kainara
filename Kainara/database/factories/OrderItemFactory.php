<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::inRandomOrder()->first() ?: Product::factory()->create();
        $productVariant = null;
        $variantSize = null;
        $variantColor = null;
        $price = $product->price;

        if ($product->variants()->exists()) {
            $productVariant = $product->variants()->inRandomOrder()->first();
            if ($productVariant) {
                $price = $productVariant->price;
                $variantSize = $productVariant->size;
                $variantColor = $productVariant->color;
            }
        }

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'product_variant_id' => $productVariant ? $productVariant->id : null,
            'product_name' => $product->name,
            'product_image' => $product->image,
            'variant_size' => $variantSize,
            'variant_color' => $variantColor,
            'price' => $price,
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }
}
