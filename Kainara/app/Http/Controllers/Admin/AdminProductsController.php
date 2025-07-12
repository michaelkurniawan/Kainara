<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Http\Requests\Admin\StoreProductRequest; // Assuming you have this request
use App\Http\Requests\Admin\UpdateProductRequest; // Assuming you have this request
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Added for potential future DB queries

class AdminProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start with a base query for Product
        $query = Product::query();

        // Filter by category_id if provided
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Search logic (similar to AdminUserController for consistency)
        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->input('search'));

            $query->where(function($q) use ($search) {
                $q->where(DB::raw('LOWER(name)'), 'like', '%' . $search . '%')
                  ->orWhere(DB::raw('LOWER(description)'), 'like', '%' . $search . '%')
                  ->orWhere(DB::raw('LOWER(origin)'), 'like', '%' . $search . '%')
                  ->orWhereHas('category', function ($sq) use ($search) {
                      $sq->where(DB::raw('LOWER(name)'), 'like', '%' . $search . '%');
                  });
            });
        }

        // Load relationships and sum stock
        $products = $query->with('category')
                          ->withSum('variants', 'stock')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        // Append all request query parameters to the pagination links
        $products->appends($request->query());

        // Fetch all categories for the filter dropdown, ordered by name
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch all categories for the dropdown, ordered by name
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        $imagePath = null;
        // Check if an image file was uploaded
        if ($request->hasFile('image')) {
            // Store the image and get its path
            $imagePath = $request->file('image')->store('product_images', 'public');
        }

        // Generate a unique slug for the product name
        $slug = Str::slug($validatedData['name']);
        $originalSlug = $slug;
        $count = 1;

        // Ensure the slug is unique, appending a counter if necessary
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        try {
            // Create the product record in the database
            $product = Product::create([
                'category_id' => $validatedData['category_id'],
                'name' => $validatedData['name'],
                'origin' => $validatedData['origin'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'image' => $imagePath,
                'material' => $validatedData['material'] ?? null, // Use null coalesce for optional fields
                'slug' => $slug,
            ]);

            // Handle product variants if they exist in the validated data
            if (isset($validatedData['variants']) && is_array($validatedData['variants']) && count($validatedData['variants']) > 0) {
                foreach ($validatedData['variants'] as $variantData) {
                    // Use the variant's price if provided, otherwise use the product's base price
                    $variantPrice = $variantData['price'] ?? $validatedData['price'];

                    $product->variants()->create([
                        'color' => $variantData['color'],
                        'size' => $variantData['size'],
                        'stock' => $variantData['stock'],
                        'price' => $variantPrice,
                    ]);
                }
            }

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            // If an error occurs during product creation, delete the uploaded image
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            // Redirect back with input and an error message
            return redirect()->back()->withInput()->with('error', 'Failed to create new product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Eager load category, variants, and reviews for the product
        $product->load('category', 'variants', 'reviews');
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Fetch all categories for the dropdown
        $categories = Category::orderBy('name')->get();
        return view('admin.products.update', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validatedData = $request->validated();

        $oldImage = $product->image; // Store the current image path
        $newImage = $oldImage; // Initialize new image path with the old one

        // Check if a new image file was uploaded
        if ($request->hasFile('image')) {
            $newImage = $request->file('image')->store('product_images', 'public');
        }

        // Slug generation: only regenerate if the product name has changed
        $slug = $product->slug;
        if ($product->name !== $validatedData['name']) {
            $slug = Str::slug($validatedData['name']);
            $originalSlug = $slug;
            $count = 1;

            // Ensure the new slug is unique, excluding the current product's ID
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
        }

        try {
            // Update the product record
            $product->update([
                'category_id' => $validatedData['category_id'],
                'name' => $validatedData['name'],
                'origin' => $validatedData['origin'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'image' => $newImage,
                'material' => $validatedData['material'] ?? null,
                'slug' => $slug,
            ]);

            $submittedVariantIds = [];

            // Handle updating and creating product variants
            if (isset($validatedData['variants']) && is_array($validatedData['variants'])) {
                foreach ($validatedData['variants'] as $variantData) {
                    if (isset($variantData['id']) && !empty($variantData['id'])) {
                        // Find existing variant and update it
                        $variant = ProductVariant::find($variantData['id']);
                        if ($variant && $variant->product_id === $product->id) {
                            $variant->update([
                                'color' => $variantData['color'],
                                'size' => $variantData['size'],
                                'stock' => $variantData['stock'],
                                'price' => $variantData['price'] ?? $validatedData['price'],
                            ]);
                            $submittedVariantIds[] = $variant->id; // Add updated variant's ID
                        }
                    } else {
                        // Create a new variant
                        $newVariant = $product->variants()->create([
                            'color' => $variantData['color'],
                            'size' => $variantData['size'],
                            'stock' => $variantData['stock'],
                            'price' => $variantData['price'] ?? $validatedData['price'],
                        ]);
                        $submittedVariantIds[] = $newVariant->id; // Add new variant's ID
                    }
                }
            }

            // Delete variants that were not submitted (removed by the user)
            $product->variants()->whereNotIn('id', $submittedVariantIds)->delete();

            // Delete the old image only if a new one was successfully uploaded and it's different
            if ($request->hasFile('image') && $oldImage && $oldImage !== $newImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            // If an error occurs during update, delete the newly uploaded image
            if ($request->hasFile('image') && $newImage && $newImage !== $oldImage) {
                Storage::disk('public')->delete($newImage);
            }
            // Redirect back with input and an error message
            return redirect()->back()->withInput()->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Delete associated image if it exists and is not the default
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // Delete the product (variants will be deleted via cascade if set up in migration)
            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}