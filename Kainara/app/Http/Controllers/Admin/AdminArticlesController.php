<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str; // Import Str facade for slug generation

class AdminArticlesController extends Controller
{
    /**
     * Display a listing of the articles.
     */
    public function index()
    {
        // Get all articles, with eager loading the admin who wrote them
        $articles = Article::with('admin')->get(); // Ensure 'admin' is loaded
        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        // Get all admins for dropdown (if needed)
        $admins = Admin::all(); // Get all admins if you want to allow an admin to be assigned
        return view('admin.articles.create', compact('admins'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming form data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:articles,slug', // Ensure unique slug
            'content' => 'required|string',
            'thumbnail' => 'nullable|string|max:255',
            'admin_id' => 'required|exists:admins,id', // Ensure admin_id exists in the admins table
        ]);

        // Automatically generate slug from article title if not provided
        if (!$validated['slug']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Create a new article in the database
        Article::create($validated);

        // Redirect to articles list page with success message
        return redirect()->route('admin.articles.index')->with('success', 'Article added successfully.');
    }

    /**
     * Show the form for editing an existing article.
     */
    public function edit($id)
    {
        // Find article by ID, will result in 404 error if not found
        $article = Article::findOrFail($id);
        // Get all admins for dropdown (if needed)
        return view('admin.articles.edit', compact('article'));
    }

    /**
     * Update an existing article in storage.
     */
    public function update(Request $request, $id)
    {
        // Find article by ID
        $article = Article::findOrFail($id);

        // Validate incoming form data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:articles,slug,' . $article->id, // Ensure unique slug
            'content' => 'required|string',
            'thumbnail' => 'nullable|string|max:255',
            'admin_id' => 'required|exists:admins,id', // Ensure admin_id exists
        ]);

        // Automatically generate slug from article title if not provided
        if (!$validated['slug']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Update article data
        $article->update($validated);

        // Redirect to articles list page with success message
        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy($id)
    {
        // Find article by ID
        $article = Article::findOrFail($id);
        // Delete article
        $article->delete();

        // Redirect to articles list page with success message
        return redirect()->route('admin.articles.index')->with('success', 'Article deleted successfully.');
    }
}
