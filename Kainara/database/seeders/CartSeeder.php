<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty()) {
            $this->call(UserSeeder::class);
            $users = User::all();
        }

        if ($products->isEmpty()) {
            $this->call(ProductSeeder::class);
            $users = Product::all();
        }

        $users->each(function (User $user) use ($products) {
            if ($user->cart()->exists()) {
                $user->cart->cartItems->delete;
                $user->cart()->delete();
            }

            $cart = Cart::factory()->create([
                'user_id' => $user->id
            ]);

            if (fake()->boolean(70)) {
                $numberOfItems = fake()->numberBetween(1, 5);

                $randomProducts = $products->random($numberOfItems);

                foreach ($randomProducts as $product) {
                    $productVariantId = null;

                    if($product->variants->isNotEmpty()) {
                        $availableVariants = $product->variants->where('stock', '>', 0);
                        if ($availableVariants->isNotEmpty()) {
                            $productVariantId = $availableVariants->random()->id;
                        }else {
                            $productVariantId = $product->variants->random()->id;
                        }
                    }

                    CartItem::factory()->create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'product_variant_id' => $productVariantId,
                        'quantity' => fake()->numberBetween(1, 5),
                    ]);
                }
            }
        });
    }
}