<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Requests\Admin\AdminStoreArticleRequest;
use App\Http\Requests\Admin\AdminUpdateArticleRequest;
use Illuminate\Support\Facades\Storage; // Tambahkan ini jika belum ada
use Illuminate\Support\Str; // Tambahkan ini jika belum ada
use Illuminate\Support\Facades\Auth;

class AdminArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::latest()->paginate(10);
        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminStoreArticleRequest $request)
    {
        $validatedData = $request->validated();

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('article_thumbnails', 'public');
        }

        $slug = Str::slug($validatedData['title']);
        $originalSlug = $slug;
        $count = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        try {
            // Membuat artikel di database
            Article::create([
                'title' => $validatedData['title'],
                'slug' => $slug,
                'content' => $validatedData['content'],
                'thumbnail' => $thumbnailPath,
                'admin_id' => Auth::id(), // Mengisi admin_id secara otomatis dengan ID user yang sedang login
            ]);

            return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal membuat artikel. Silakan coba lagi.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return view('admin.articles.show', compact('article'));
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        return view('admin.articles.update', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateArticleRequest $request, Article $article)
    {
        $validatedData = $request->validated();

        $thumbnailPath = $article->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail && Storage::disk('public')->exists($article->thumbnail)) {
                Storage::disk('public')->delete($article->thumbnail);
            }

            $thumbnailPath = $request->file('thumbnail')->store('article_thumbnail', 'public');
        }

        $slug = $article->slug;
        if ($validatedData['title'] !== $article->title) {
            $slug = Str::slug($validatedData['title']);
            $originalSlug = $slug;
            $count = 1;
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
        }

        try {
            $article->update([
                'title' => $validatedData['title'],
                'slug' => $slug,
                'content' => $validatedData['content'],
                'thumbnail' => $thumbnailPath,
                'admin_id' => $article->admin_id,
            ]);
    
            return redirect()->route('admin.articles.index')->with('sucess', 'Article updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update article. Please try again']);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        if ($article->thumbnail && Storage::disk('public')->exists($article->thumbnail)) {
            Storage::disk('public')->delete($article->thumbnail);
        }
        
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
