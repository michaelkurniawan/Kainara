<?php

namespace App\Http\Controllers;

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
        $articles = Article::all(); // Ambil semua data dari tabel 'articles'

        // Kirim data ke view
        return view('Stories.ListStories', compact('articles'));
    }

    /**
     * Display the specified story.
     *
     * @param  int  $id  The ID of the article
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $story = Article::findOrFail($id);

        $previousStory = Article::where('id', '<', $story->id)
                                ->orderBy('id', 'desc')
                                ->first();

        $nextStory = Article::where('id', '>', $story->id)
                            ->orderBy('id', 'asc')
                            ->first();

        return view('Stories.DetailStories', compact('story', 'previousStory', 'nextStory'));
    }

}
