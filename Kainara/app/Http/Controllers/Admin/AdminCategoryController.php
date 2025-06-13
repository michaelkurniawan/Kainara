<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category; // Impor model Category
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Untuk membuat slug otomatis

class AdminCategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Menampilkan form untuk membuat kategori baru.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Menyimpan kategori baru yang dibuat.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name', // Nama kategori harus unik
        ]);
        // Buat slug dari nama kategori
        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit kategori tertentu.
     */
    public function edit(Category $category) // Menggunakan route model binding
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Memperbarui kategori tertentu di database.
     */
    public function update(Request $request, Category $category) // Menggunakan route model binding
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id, // Nama harus unik, kecuali untuk kategori saat ini
        ]);
        // Perbarui slug
        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori dari database.
     */
    public function destroy(Category $category) // Menggunakan route model binding
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
