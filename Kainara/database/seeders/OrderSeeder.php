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
        // Membuat 20 order dengan status acak (menggunakan definisi default factory)
        Order::factory(20)->create()->each(function ($order) {
            $this->createOrderItems($order);
        });

        // Membuat 10 order secara spesifik dengan status 'Order Confirmed'
        // Menggunakan state 'confirmed()' yang baru dari factory
        Order::factory(10)->confirmed()->create()->each(function ($order) {
            $this->createOrderItems($order);
        });
    }

    /**
     * Fungsi pembantu untuk membuat item pesanan untuk pesanan tertentu.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    protected function createOrderItems(Order $order): void
    {
        $subtotal = 0;

        // Membuat antara 1 hingga 5 item pesanan untuk setiap pesanan
        for ($i = 0; $i < rand(1, 5); $i++) {
            $product = Product::inRandomOrder()->first();

            // Memastikan produk ada sebelum melanjutkan
            if (!$product) {
                // Jika tidak ada produk yang ditemukan, lewati pembuatan item pesanan untuk iterasi ini.
                // Dalam aplikasi nyata, Anda mungkin ingin menambahkan peringatan atau memastikan produk di-seed terlebih dahulu.
                continue;
            }

            $productVariant = null;
            $variantSize = null;
            $variantColor = null;
            $price = $product->price;

            // Memeriksa apakah produk memiliki varian dan memilih satu secara acak
            if ($product->variants()->exists()) {
                $productVariant = $product->variants()->inRandomOrder()->first();
                if ($productVariant) {
                    // Menggunakan harga varian jika tersedia, jika tidak menggunakan harga produk
                    $price = $productVariant->price ?: $product->price;
                    $variantSize = $productVariant->size;
                    $variantColor = $productVariant->color;
                }
            }

            $quantity = rand(1, 3); // Kuantitas acak untuk item
            $subtotal += $price * $quantity; // Menghitung subtotal untuk pesanan

            // Membuat item pesanan
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

        // Memperbarui subtotal pesanan setelah semua item ditambahkan
        $order->subtotal = $subtotal;
        $order->save();
    }
}
