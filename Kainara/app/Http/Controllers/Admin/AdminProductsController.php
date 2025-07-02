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
        // Save image if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product_images', 'public');
        }
        
        // Generate a unique slug
        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Create the product
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'origin' => $request->origin,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'material' => $request->material,
            'slug' => $slug, 
        ]);

        // Handle all variants
        if (is_array($request->variants) && count($request->variants) > 0) {
            foreach ($request->variants as $variantData) {
                $colorToStore = $variantData['color'] ?? '';
                $sizeToStore = $variantData['size'] ?? '';

                if (empty($colorToStore)) {
                    $colorToStore = 'Unknown Color';
                }
                if (empty($sizeToStore)) {
                    $sizeToStore = 'Unknown Size';
                }

                $variantPrice = $variantData['price'] ?? $request->price;

                $product->variants()->create([
                    'color' => $colorToStore,
                    'size' => $sizeToStore,
                    'stock' => $variantData['stock'],
                    'price' => $variantPrice,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk dan varian berhasil dibuat.');
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
        // Validate the request
        $validatedData = $request->validated();

        // Update product image if provided
        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            try {
                if($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('product_images', 'public');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['image' => 'Failed to upload image: ']);
            }
        }

        // Create new unique slug if the name has changed
        $slug = $product->slug;
        if ($product->name !== $validatedData['name']) {
            $slug = Str::slug($validatedData['name']);
            $originalSlug = $slug;
            $count = 1;

            while(Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
        }

        // Update the product data
        try {
            $product->update([
                'category_id' => $validatedData['category_id'],
                'name' => $validatedData['name'],
                'origin' => $validatedData['origin'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'image' => $imagePath,
                'material' => $validatedData['material'] ?? null,
                'slug' => $slug,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['product' => 'Failed to update product data']);
        }

        // Update the variants
        $submittedVariants = [];

        if (isset($validatedData['variants']) && is_array($validatedData['variants'])) {
            foreach ($validatedData['variants'] as $variantData) {
                if (isset($variantData['id']) && $variantData['id']) {
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant && $variant->product_id === $product->id) {
                        $variant->update([
                            'color' => $variantData['color'],
                            'size' => $variantData['size'] ,
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

        // Delete variants that were not submitted
        $currentProductVariantIds = $product->variants->pluck('id')->toArray();
        $variantsToDelete = array_diff($currentProductVariantIds, $submittedVariants);

        if (!empty($variantsToDelete)) {
            $product->variants()->whereIn('id', $variantsToDelete)->delete();
        }

        return redirect()->route('admin.products.index')->with('success', 'Product Updated Successfully');
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
