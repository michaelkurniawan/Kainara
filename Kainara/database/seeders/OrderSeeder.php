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
     * Metode ini membuat contoh pesanan dan item pesanan.
     */
    public function run(): void
    {
        // Membuat 20 pesanan dengan status acak (menggunakan definisi default factory)
        Order::factory(20)->create()->each(function ($order) {
            $this->createOrderItems($order);
        });

        // Membuat 10 pesanan secara spesifik dengan status 'Order Confirmed'
        Order::factory(10)->confirmed()->create()->each(function ($order) {
            $this->createOrderItems($order);
        });
    }

    /**
     * Fungsi pembantu untuk membuat item pesanan untuk pesanan tertentu.
     * Ini juga menghitung subtotal dan menetapkan biaya pengiriman,
     * yang akan digunakan oleh accessor grand_total.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    protected function createOrderItems(Order $order): void
    {
        $subtotal = 0;

        for ($i = 0; $i < rand(1, 5); $i++) {
            $product = Product::inRandomOrder()->first();

            if (!$product) {
                continue;
            }

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

            $quantity = rand(1, 3); // Kuantitas acak untuk item
            $subtotal += $price * $quantity; // Menghitung subtotal untuk pesanan

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
        $order->shipping_cost = (float)rand(0, 50000); 
        $order->save(); 
    }
}