<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Article::class;
    public function definition(): array
    {
        $articleTitle = fake()->unique()->sentence(rand(5, 10));
        $slug = Str::slug($articleTitle);

        return [
            'admin_id' => User::factory(),
            'title' => $articleTitle,
            'slug' => $slug,
            'content' =>fake()->paragraphs(rand(3, 5), true),
            'thumbnail' => 'thumbnails/article_' . rand(1, 15) . '.jpg'
        ];
    }
}
