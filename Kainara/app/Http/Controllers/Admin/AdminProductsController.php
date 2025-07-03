<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categoryId = $request->input('category_id'); // Dapatkan category_id dari request

        $products = Product::when($categoryId, function ($query, $categoryId) {
            $query->where('category_id', $categoryId);
        })
        ->with('category')
        ->withSum('variants', 'stock') // Ini akan membuat atribut $product->variants_sum_stock
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        // Ambil semua kategori untuk dropdown filter
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        // Save image if provided
        $imagePath = null;
        if ($validatedData->hasFile('image')) {
            $imagePath = $validatedData->file('image')->store('product_images', 'public');
        }
        
        // Generate a unique slug
        $slug = Str::slug($validatedData->name);
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        try {
            $product = Product::create([
                'category_id' => $validatedData->category_id,
                'name' => $validatedData->name,
                'origin' => $validatedData->origin,
                'description' => $validatedData->description,
                'price' => $validatedData->price,
                'image' => $imagePath,
                'material' => $validatedData->material,
                'slug' => $slug, 
            ]);

            if (is_array($validatedData->variants) && count($validatedData->variants) > 0) {
                foreach ($validatedData->variants as $variantData) {  
                    $variantPrice = $variantData['price'] ?? $validatedData->price;
    
                    $product->variants()->create([
                        'color' => $variantData['color'],
                        'size' => $variantData['size'],
                        'stock' => $variantData['stock'],
                        'price' => $variantPrice,
                    ]);
                }
            }

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            Storage::disk('public')->delete($imagePath);
            return redirect()->back()->withInput()->withErrors('error', 'Failed to create new product');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category', 'variants', 'reviews');
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.update', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validatedData = $request->validated();

        $oldImage = $product->image;
        $newImage = $oldImage;

        if ($request->hasFile('image')) {
            $newImage = $request->file('image')->store('product_images', 'public');
        }

        // Slug generation
        $slug = $product->slug;
        if ($product->name !== $validatedData['name']) {
            $slug = Str::slug($validatedData['name']);
            $originalSlug = $slug;
            $count = 1;

            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
        }

        try {
            // Update product
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

            // Handle variants
            $submittedVariants = [];

            if (isset($validatedData['variants']) && is_array($validatedData['variants'])) {
                foreach ($validatedData['variants'] as $variantData) {
                    if (!empty($variantData['id'])) {
                        $variant = ProductVariant::find($variantData['id']);
                        if ($variant && $variant->product_id === $product->id) {
                            $variant->update([
                                'color' => $variantData['color'],
                                'size' => $variantData['size'],
                                'stock' => $variantData['stock'],
                                'price' => $variantData['price'] ?? $validatedData['price'],
                            ]);
                            $submittedVariants[] = $variant->id;
                        }
                    } else {
                        $newVariant = $product->variants()->create([
                            'color' => $variantData['color'],
                            'size' => $variantData['size'],
                            'stock' => $variantData['stock'],
                            'price' => $variantData['price'] ?? $validatedData['price'],
                        ]);
                        $submittedVariants[] = $newVariant->id;
                    }
                }
            }

            // Delete unsubmitted variants
            $currentVariantIds = $product->variants->pluck('id')->toArray();
            $variantsToDelete = array_diff($currentVariantIds, $submittedVariants);

            if (!empty($variantsToDelete)) {
                $product->variants()->whereIn('id', $variantsToDelete)->delete();
            }

            // Delete old image if new image was uploaded
            if ($request->hasFile('image') && $oldImage && $oldImage !== $newImage) {
                Storage::disk('public')->delete($oldImage);
            }

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            // Delete new image if update failed
            if ($request->hasFile('image') && $newImage && $newImage !== $oldImage) {
                Storage::disk('public')->delete($newImage);
            }

            return redirect()->back()->withErrors(['product' => 'Failed to update product. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete(); // Variant => On Delete Cascade

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
