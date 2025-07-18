<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUsers = User::where('role', 'admin')->get();

        if ($adminUsers->isEmpty()) {
            $this->call(UserSeeder::class);
            $adminUsers = User::where('role', 'admin')->get();
        }

        if ($adminUsers->isEmpty()) {
            $adminUsers->add(
                User::factory()->admin()->create([
                    'first_name' => 'Default',
                    'last_name' => 'Admin',
                    'email' => 'default.admin@example.com',
                    'password' => Hash::make('password'),
                ])
            );
        }

        $numberOfArticles = rand(30, 60);

        for($i = 0; $i < $numberOfArticles; $i++) {
            Article::factory()->create([
                'admin_id' => $adminUsers->random()->id,
            ]);
        }
    }
}