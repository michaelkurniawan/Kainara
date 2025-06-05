<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => "Men's Outerwear - Woven Jepara Beige",
                'origin' => 'Central Java',
                'description' => "Showcase an elegant ethnic style with the Jepara Woven Men's Outerwear. Made from the signature Troso ikat woven fabric of Troso Village, Jepara, this outerwear brings a strong traditional feel to modern menswear.",
                'price' => 125000,
                'image' => 'images/batik1.jpg',
                'type' => 'Lengan Panjang'
            ],
            [
                'name' => "Men's Outerwear - Maroon Lurik Motif",
                'origin' => 'Yogyakarta',
                'description' => "Made from quality woven fabric with a modern cut, this outerwear is suitable to be worn on various occasions, whether formal, casual, or as a symbol of pride for local culture. Lightweight, comfortable and full of character, this is the right choice for men who want to look stylish with an ethnic touch.",
                'price' => 110000,
                'image' => 'images/batik2.jpg',
                'type' => 'Lengan Panjang'

            ],
            [
                'name' => "Men's Woven Vest",
                'origin' => 'East Nusa Tenggara',
                'description' => "With a modern and clean cut, this vest is suitable for formal, semi-formal or casual wear. The comfortable fabric lining and distinctive woven texture make this vest not only stylish, but also full of character.",
                'price' => 155000,
                'image' => 'images/batik3.jpg',
                'type' => 'Lengan Panjang'
            ],
            [
                'name' => "Men's Outerwear - Blue Jepara Weaving",
                'origin' => 'Central Java',
                'description' => "The soothing blue color combined with the distinctive ikat motifs, woven manually using non-machine looms (ATBM) and natural dyes, creates clothing that is not only beautiful to look at, but also full of cultural significance. The pieces are designed to be comfortable and stylish, suitable for a variety of occasions, from formal to casual.",
                'price' => 135000,
                'image' => 'images/batik4.jpg',
                'type' => 'Lengan Panjang'

            ],
            [
                'name' => "Men's Outerwear - Blue Lurik Motif",
                'origin' => 'Yogyakarta',
                'description' => "Look elegant with a touch of culture in the blue lurik-patterned Men's Luaran, a symbol of classy simplicity. Made with quality woven materials and modern cuts, this outerwear is comfortable to wear for various activities, from formal, casual, to cultural activities.",
                'price' => 115000,
                'image' => 'images/batik5.jpg',
                'type' => 'Lengan Pendek'

            ],
            [
                'name' => "Red and Blue Bird of Paradise Motif Batik Shirt",
                'origin' => 'Papua',
                'description' => "This long-sleeved batik shirt features a majestic bird of paradise motif dominated by red and blue colors, combined with floral accents and traditional patterns. The upper part of the shirt has a light background with delicate ornaments, while the lower part is dominated by dark colors with small star and flower motifs. This design exudes luxury and elegance, suitable for both formal and semi-formal occasions.",
                'price' => 135000,
                'image' => 'images/batik6.jpg',
                'type' => 'Lengan Pendek'
            ],
            [
                'name' => "Blue Eagle Batik Shirt with a Classic Touch",
                'origin' => 'West Java',
                'description' => "This long-sleeved batik shirt showcases a dashing eagle motif dominated by red and blue colors, combined with a background of classic patterns and floral ornaments. The bottom of the shirt features geometric motifs and small flowers with an elegant dark base color. This design exudes traditional strength and beauty, perfect for a classy formal look.",
                'price' => 145000,
                'image' => 'images/batik7.jpg',
                'type' => 'Dress'

            ],
            [
                'name' => "Soft Bird of Paradise Batik Shirt",
                'origin' => 'Papua',
                'description' => "This long-sleeved batik shirt is stunning with its graceful bird of paradise motif, adorned with a mix of soft colors such as pink, light blue and earthy brown on a beige background. The detailed and symmetrical batik pattern creates a harmonious and elegant impression. A touch of dark color on the collar and cuffs provides a chic contrast. This shirt is ideal for a charming and classy look, suitable for any occasion.",
                'price' => 140000,
                'image' => 'images/batik8.jpg',
                'type' => 'Dress'
            ],
            [
                'name' => "Golden Garuda Batik Shirt with Mega Clouds",
                'origin' => 'Central Java',
                'description' => "This long-sleeved batik shirt features a strong garuda bird motif with a dominance of gold and brown colors, interspersed with touches of light blue and red. Cloudy mega cloud motifs and traditional flora add beauty to the design. The detailed background pattern enriches the overall look. This shirt exudes an aura of luxury and cultural pride, perfect for formal or semi-formal occasions where you want to impress.",
                'price' => 155000,
                'image' => 'images/batik9.jpg',
                'type' => 'Dress'
            ],
        ];

        // Loop through the products array and create each product
        foreach ($products as $index => $product) {
            $createdProduct = Product::create([
                'category_id' => 1, // Set your category_id or modify as needed
                'name' => $product['name'],
                'slug' => \Str::slug($product['name']),
                'origin' => $product['origin'],
                'type' => $product['type'],
                'description' => $product['description'],
                'price' => $product['price'],
                'image' => $product['image'],
            ]);

            // Create product variants for each product
            foreach (['S', 'M', 'L', 'XL'] as $size) {
                ProductVariant::create([
                    'product_id' => $createdProduct->id,
                    'size' => $size,
                    'color' => $this->getColorByIndex($index),
                    'stock' => rand(5, 50), // Example stock range
                    'price' => null, // Or specify a price adjustment
                ]);
            }
        }
    }

    /**
     * Get color by product index.
     */
    private function getColorByIndex(int $index): string
    {
        $colors = [
            'Brown',  // batik1
            'Red',    // batik2
            'Brown',  // batik3
            'Blue',   // batik4
            'Blue',   // batik5
            'Blue',   // batik6
            'Grey',   // batik7
            'Beige',  // batik8
            'Yellow', // batik9
        ];

        return $colors[$index] ?? 'Black';
    }
}
