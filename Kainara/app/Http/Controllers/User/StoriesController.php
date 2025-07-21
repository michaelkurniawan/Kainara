<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Article;

class StoriesController extends Controller
{
    /**
     * Display a listing of the stories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $articles = Article::select('id', 'slug', 'title', 'thumbnail', 'content', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->paginate(8); // Ganti sesuai jumlah yang kamu mau per halaman

        return view('Stories.ListStories', compact('articles'));
    }

    /**
     * Display the specified story.
     *
     * @param  int  $id  The ID of the article
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        $story = Article::where('slug', $slug)->firstOrFail();

        $previousStory = Article::where('id', '<', $story->id)
                                ->orderBy('id', 'desc')
                                ->first();

        $nextStory = Article::where('id', '>', $story->id)
                            ->orderBy('id', 'asc')
                            ->first();

        return view('Stories.DetailStories', compact('story', 'previousStory', 'nextStory'));
    }
}
