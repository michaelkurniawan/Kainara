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
        // Pastikan categories, vendors, dan genders ada sebelum membuat products
        $this->call(CategorySeeder::class);
        $this->call(VendorSeeder::class);
        $this->call(GenderSeeder::class); // Panggil GenderSeeder untuk mengisi genders

        $categories = Category::all();
        // Ambil semua vendor (tidak perlu filter is_approved lagi)
        $otherVendors = Vendor::where('name', '!=', 'Kainara')->get();

        // Pastikan vendor 'Kainara' ada dan ambil ID-nya
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
                // 'is_approved' => true, // DIHAPUS karena kolom tidak ada
            ]
        );

        $shirtCategory = Category::where('name', 'Shirt')->first();
        $fabricCategory = Category::where('name', 'Fabric')->first();

        // Ambil ID gender yang dibutuhkan (pastikan mereka dibuat oleh GenderSeeder)
        $maleGenderId = Gender::where('name', 'Male')->first()->id;
        $femaleGenderId = Gender::where('name', 'Female')->first()->id;
        $unisexGenderId = Gender::where('name', 'Unisex')->first()->id;

        // Fallback jika tidak ada kategori atau vendor/gender (seharusnya tidak terjadi jika seeder dipanggil dengan benar)
        if ($categories->isEmpty()) { $this->call(CategorySeeder::class); $categories = Category::all(); }
        // Removed the 'is_approved' check here as the column no longer exists
        if ($otherVendors->isEmpty() && $kainaraVendor === null) {
            Vendor::factory()->count(5)->create();
            $otherVendors = Vendor::where('name', '!=', 'Kainara')->get();
        }
        if (!$maleGenderId || !$femaleGenderId || !$unisexGenderId) {
            $this->call(GenderSeeder::class);
            $maleGenderId = Gender::where('name', 'Male')->first()->id;
            $femaleGenderId = Gender::where('name', 'Female')->first()->id;
            $unisexGenderId = Gender::where('name', 'Unisex')->first()->id;
        }


        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $oneSize = 'One Size';
        $colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow', 'Pink', 'Purple', 'Orange', 'Gray', 'Brown', 'Navy'];

        $categories->each(function (Category $category) use ($sizes, $oneSize, $colors, $otherVendors, $kainaraVendor, $shirtCategory, $fabricCategory, $maleGenderId, $femaleGenderId, $unisexGenderId) {
            // Tentukan vendor_id berdasarkan kategori
            $selectedVendorId = null;
            if ($shirtCategory && $category->id === $shirtCategory->id) {
                $selectedVendorId = $kainaraVendor->id;
            } else {
                $selectedVendorId = $otherVendors->isNotEmpty() ? $otherVendors->random()->id : ($kainaraVendor ? $kainaraVendor->id : null);
            }

            // Tentukan gender_id berdasarkan kategori untuk seeder
            $selectedGenderId = null;
            if ($shirtCategory && $category->id === $shirtCategory->id) {
                // Jika Shirt, pilih Male atau Female secara acak
                $selectedGenderId = fake()->randomElement([$maleGenderId, $femaleGenderId]);
            } elseif ($fabricCategory && $category->id === $fabricCategory->id) {
                // Jika Fabric, pilih Unisex
                $selectedGenderId = $unisexGenderId;
            } else {
                // Untuk kategori lain, bisa Unisex atau acak dari Male/Female/Unisex
                $selectedGenderId = fake()->randomElement([$maleGenderId, $femaleGenderId, $unisexGenderId]);
            }


            // Hanya membuat produk jika ada vendor dan gender yang bisa di-assign
            if ($selectedVendorId && $selectedGenderId) {
                Product::factory(rand(10, 20))->create([
                    'category_id' => $category->id,
                    'vendor_id' => $selectedVendorId,
                    'gender_id' => $selectedGenderId, // Assign gender yang sudah ditentukan
                ])->each(function (Product $product) use ($sizes, $oneSize, $colors, $shirtCategory) {

                    // Logika pembuatan varian tetap sama
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
                    } elseif ($product->category_id === 2) { // Asumsi category_id 2 adalah 'Fabric'
                        ProductVariant::factory()->create([
                            'product_id' => $product->id,
                            'size' => $oneSize,
                            'color' => fake()->randomElement($colors),
                            'stock' => fake()->numberBetween(50, 500),
                            'price' => fake()->boolean(30) ? fake()->randomFloat(2, $product->price * 0.9, $product->price * 1.1) : null,
                        ]);
                    } else { // Untuk kategori lain
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