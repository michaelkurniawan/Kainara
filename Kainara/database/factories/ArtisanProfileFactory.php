<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArtisanProfileFactory extends Factory
{
    public function definition(): array
    {
        $businessTypes = ['batik_artisan', 'tenun_artisan', 'fabric_seller', 'fashion_designer'];
        $provinces = ['Jawa Barat', 'Jawa Tengah', 'DKI Jakarta', 'Bali', 'Sumatera Utara'];
        
        return [
            // user_id akan di-handle oleh seeder (bisa null)
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'date_of_birth' => $this->faker->dateTimeBetween('-50 years', '-20 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'phone_number' => $this->faker->phoneNumber(),
            'home_address' => $this->faker->streetAddress(),
            'home_province' => $this->faker->randomElement($provinces),
            'home_city' => $this->faker->city(),
            'home_postal_code' => $this->faker->postcode(),
            'business_name' => $this->faker->company(),
            'business_type' => $this->faker->randomElement($businessTypes),
            'other_business_type' => null,
            'business_description' => $this->faker->paragraph(),
            'business_phone_number' => $this->faker->optional()->phoneNumber(),
            'business_email' => $this->faker->optional()->companyEmail(),
            'business_address' => $this->faker->optional()->streetAddress(),
            'business_province' => $this->faker->optional()->randomElement($provinces),
            'business_city' => $this->faker->optional()->city(),
            'business_postal_code' => $this->faker->optional()->postcode(),
        ];
    }
}