<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PortfolioFactory extends Factory
{
    public function definition(): array
    {
        $fabricTypes = ['cotton', 'silk', 'linen', 'wool', 'rayon', 'polyester'];

        return [
            // artisan_profile_id akan diisi oleh seeder
            'project_title' => $this->faker->catchPhrase(),
            'project_description' => $this->faker->realText(250),
            'fabric_type' => $this->faker->randomElement($fabricTypes),
            'other_fabric_type' => null,
            'year_created' => $this->faker->year(),
            'photo_paths' => [
                'placeholders/portfolio_placeholder.jpg',
                'placeholders/portfolio_2.jpg',], 
            'video_link' => $this->faker->optional(0.3)->url(), 
        ];
    }
}