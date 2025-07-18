<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use firstOrCreate to prevent duplicates if run multiple times
        Gender::firstOrCreate(['name' => 'Male']);
        Gender::firstOrCreate(['name' => 'Female']);
        Gender::firstOrCreate(['name' => 'Unisex']);
    }
}
