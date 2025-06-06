<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory(20)->create()->each(function ($order) {
            $subtotal = 0;

            for ($i = 0; $i < rand(1, 5); $i++) {
                $product = Product::inRandomOrder()->first();
                $productVariant = null;
                $variantSize = null;
                $variantColor = null;
                $price = $product->price;

                if ($product->variants()->exists()) {
                    $productVariant = $product->variants()->inRandomOrder()->first();
                    if ($productVariant) {
                        $price = $productVariant->price ?: $product->price;
                        $variantSize = $productVariant->size;
                        $variantColor = $productVariant->color;
                    }
                }

                $quantity = rand(1, 3);
                $subtotal += $price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $productVariant ? $productVariant->id : null,
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                    'variant_size' => $variantSize,
                    'variant_color' => $variantColor,
                    'price' => $price,
                    'quantity' => $quantity,
                ]);
            }

            $order->subtotal = $subtotal;
            $order->save();
        });
    }
}
