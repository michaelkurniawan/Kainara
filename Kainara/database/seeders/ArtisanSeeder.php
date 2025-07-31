<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ArtisanProfile;
use App\Models\Portfolio;
// Hapus use App\Models\User;

class ArtisanSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 15 Pendaftaran Artisan (tanpa membuat user)
        ArtisanProfile::factory(15)->create()->each(function ($profile) {
            // Untuk setiap pendaftaran, buat 1 sampai 3 portfolio
            Portfolio::factory(rand(1, 3))->create([
                'artisan_profile_id' => $profile->id,
            ]);
        });
    }
}