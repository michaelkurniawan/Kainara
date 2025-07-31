<?php

namespace App\Http\Controllers\User;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LatestStoriesController extends Controller
{
    /**
     * Show the homepage with latest stories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // dd('Controller is running and this method is being called.'); 
        $latestArticles = Article::orderBy('created_at', 'desc')
                                 ->take(5)
                                 ->get();

        $featuredArticle = null;
        $smallArticles = collect(); 

        if ($latestArticles->isNotEmpty()) {
            $featuredArticle = $latestArticles->shift(); 

            $smallArticles = $latestArticles;
        }

        return view('welcome', [
            'featuredArticle' => $featuredArticle,
            'smallArticles' => $smallArticles,
        ]);
    }
}