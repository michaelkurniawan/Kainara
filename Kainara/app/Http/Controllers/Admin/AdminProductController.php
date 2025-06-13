<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str; // Import Str facade for slug generation

class AdminProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        // Get all products, with eager loading category and variants (if any)
        $products = Product::with('category', 'variants')->get(); // Ensure 'variants' is loaded
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        // Get all categories for dropdown
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id', // Ensure category_id exists in the categories table
            'description' => 'nullable|string',
            'origin' => 'required|string|max:255', // Validation for 'origin' field
            'image' => 'nullable|string|max:255',     // <-- Tambahkan validasi untuk 'image'
            'material' => 'nullable|string|max:255',  // <-- Tambahkan validasi untuk 'material'
        ]);

        // Automatically generate slug from product name
        $validated['slug'] = Str::slug($validated['name']);

        // Create a new product in the database
        Product::create($validated);

        // Redirect to product list page with success message
        return redirect()->route('admin.products.index')->with('success', 'Product added successfully.');
    }

    /**
     * Show the form for editing an existing product.
     */
    public function edit($id)
    {
        // Find product by ID, will result in 404 error if not found
        $product = Product::findOrFail($id);
        // Get all categories for dropdown
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update an existing product in storage.
     */
    public function update(Request $request, $id)
    {
        // Find product by ID
        $product = Product::findOrFail($id);

        // Validate incoming form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'origin' => 'required|string|max:255', // Validation for 'origin' field
            'image' => 'nullable|string|max:255',     // <-- Tambahkan validasi untuk 'image'
            'material' => 'nullable|string|max:255',  // <-- Tambahkan validasi untuk 'material'
        ]);

        // Automatically generate slug from product name on update as well
        $validated['slug'] = Str::slug($validated['name']);

        // Update product data
        $product->update($validated);

        // Redirect to product list page with success message
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        // Find product by ID
        $product = Product::findOrFail($id);
        // Delete product
        $product->delete();

        // Redirect to product list page with success message
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
