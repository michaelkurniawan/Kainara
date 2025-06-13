<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProductReview;
use App\Models\Product;

class ProductReviewSeeder extends Seeder
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
            $products = Product::all();
        }

        $numberOfReviews = rand(10, 15);

        for ($i = 0; $i < $numberOfReviews; $i++) {
            ProductReview::factory()->create([
                'user_id' => $users->random()->id,
                'product_id' => $products->random()->id,
            ]);
        }

        ProductReview::factory(10)->highRating()->create([
            'user_id' => $users->random()->id,
            'product_id' => $products->random()->id,
        ]);

        ProductReview::factory(10)->lowRating()->create([
            'user_id' => $users->random()->id,
            'product_id' => $products->random()->id,
        ]);
    }
}
