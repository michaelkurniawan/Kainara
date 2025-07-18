<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Vendor;
use App\Models\Gender; // Import the Gender model

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure categories, vendors, and genders exist before creating products
        $this->call(CategorySeeder::class);
        $this->call(VendorSeeder::class);
        $this->call(GenderSeeder::class); // Call GenderSeeder to populate genders

        $categories = Category::all();
        $otherApprovedVendors = Vendor::where('is_approved', true)
                                      ->where('name', '!=', 'Kainara')
                                      ->get();

        $kainaraVendor = Vendor::firstOrCreate(
            ['name' => 'Kainara'],
            [
                'email' => 'info@kainara.com',
                'phone_number' => '000-000-0000',
                'address' => 'Jl. Kainara No. 1',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10000',
                'business_type' => 'Textile',
                'business_description' => 'Official vendor for Kainara products.',
                'is_approved' => true,
            ]
        );

        $shirtCategory = Category::where('name', 'Shirt')->first();
        $fabricCategory = Category::where('name', 'Fabric')->first(); // Get Fabric category

        // Get Gender IDs
        $maleGender = Gender::where('name', 'Male')->first();
        $femaleGender = Gender::where('name', 'Female')->first();
        $unisexGender = Gender::where('name', 'Unisex')->first();

        // Fallback checks (should ideally not be hit if seeders are called correctly)
        if ($categories->isEmpty()) { $this->call(CategorySeeder::class); $categories = Category::all(); }
        if ($otherApprovedVendors->isEmpty() && $kainaraVendor === null) {
            Vendor::factory()->count(5)->create(['is_approved' => true]);
            $otherApprovedVendors = Vendor::where('is_approved', true)->where('name', '!=', 'Kainara')->get();
        }
        if (!$maleGender || !$femaleGender || !$unisexGender) {
            $this->call(GenderSeeder::class);
            $maleGender = Gender::where('name', 'Male')->first();
            $femaleGender = Gender::where('name', 'Female')->first();
            $unisexGender = Gender::where('name', 'Unisex')->first();
        }


        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $oneSize = 'One Size';
        $colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow', 'Pink', 'Purple', 'Orange', 'Gray', 'Brown', 'Navy'];

        $categories->each(function (Category $category) use ($sizes, $oneSize, $colors, $otherApprovedVendors, $kainaraVendor, $shirtCategory, $fabricCategory, $maleGender, $femaleGender, $unisexGender) {
            // Determine vendor_id based on category
            $selectedVendorId = null;
            if ($shirtCategory && $category->id === $shirtCategory->id) {
                $selectedVendorId = $kainaraVendor->id;
            } else {
                $selectedVendorId = $otherApprovedVendors->isNotEmpty() ? $otherApprovedVendors->random()->id : ($kainaraVendor ? $kainaraVendor->id : null);
            }

            // Determine gender_id based on category
            $selectedGenderId = null;
            if ($shirtCategory && $category->id === $shirtCategory->id) {
                // For 'Shirt' category, randomly assign Male or Female
                $selectedGenderId = fake()->boolean() ? $maleGender->id : $femaleGender->id;
            } elseif ($fabricCategory && $category->id === $fabricCategory->id) {
                // For 'Fabric' category, always Unisex
                $selectedGenderId = $unisexGender->id;
            } else {
                // For other categories, randomly assign any gender (or default to Unisex if only one option)
                $genders = Gender::all();
                $selectedGenderId = $genders->isNotEmpty() ? $genders->random()->id : null;
            }


            // Only create product if a vendor and gender can be assigned
            if ($selectedVendorId && $selectedGenderId) {
                Product::factory(rand(10, 20))->create([
                    'category_id' => $category->id,
                    'vendor_id' => $selectedVendorId,
                    'gender_id' => $selectedGenderId, // Assign the determined gender ID
                ])->each(function (Product $product) use ($sizes, $oneSize, $colors, $shirtCategory) {

                    // Variant creation logic remains the same
                    if ($shirtCategory && $product->category_id === $shirtCategory->id) {
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
                    } elseif ($product->category_id === 2) { // Assuming category_id 2 is 'Fabric'
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
            }
        });
    }
}
