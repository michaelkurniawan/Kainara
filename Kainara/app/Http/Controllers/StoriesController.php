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
}
