<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoriesController extends Controller
{
    /**
     * Display a listing of the stories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // In a real application, you would fetch data from a database here, e.g.:
        // $stories = Story::all();
        // return view('ListStories', compact('stories'));

        // For now, since the Blade file is hardcoded with example data,
        // we'll just return the view directly.
        return view('Stories\ListStories');
    }
}