<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Vendor;
use App\Models\Gender; // Import the Gender model
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->input('vendor_id'));
        }

        if ($request->filled('gender_id')) { // Filter by gender_id
            $query->where('gender_id', $request->input('gender_id'));
        }

        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->input('search'));

            $query->where(function($q) use ($search) {
                $q->where(DB::raw('LOWER(products.name)'), 'like', '%' . $search . '%')
                  ->orWhere(DB::raw('LOWER(products.description)'), 'like', '%' . $search . '%')
                  ->orWhere(DB::raw('LOWER(products.origin)'), 'like', '%' . $search . '%')
                  ->orWhereHas('category', function ($sq) use ($search) {
                      $sq->where(DB::raw('LOWER(name)'), 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('vendor', function ($sq) use ($search) {
                      $sq->where(DB::raw('LOWER(name)'), 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('gender', function ($sq) use ($search) { // Search by gender name
                      $sq->where(DB::raw('LOWER(name)'), 'like', '%' . $search . '%');
                  });
            });
        }

        $products = $query->with('category', 'vendor', 'gender') // Eager load gender
                          ->withSum('variants', 'stock')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        $products->appends($request->query());

        $categories = Category::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        $genders = Gender::orderBy('name')->get(); // Fetch all genders

        return view('admin.products.index', compact('products', 'categories', 'vendors', 'genders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        $genders = Gender::orderBy('name')->get(); // Fetch all genders
        return view('admin.products.create', compact('categories', 'vendors', 'genders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product_images', 'public');
        }

        $slug = Str::slug($validatedData['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Determine vendor_id based on category
        $vendorId = null;
        $shirtCategory = Category::where('name', 'Shirt')->first();

        if ($shirtCategory && $validatedData['category_id'] == $shirtCategory->id) {
            $kainaraVendor = Vendor::firstOrCreate(
                ['name' => 'Kainara'],
                [
                    'email' => 'info@kainara.com',
                    'phone_number' => '000-000-0000',
                    'address' => 'Jl. Kainara No. 1',
                    'city' => 'Jakarta',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '10000',
                    'business_type' => 'Textile',
                    'business_description' => 'Official vendor for Kainara products.',
                    'is_approved' => true,
                ]
            );
            $vendorId = $kainaraVendor->id;
        } else {
            $vendorId = $validatedData['vendor_id'];
        }

        // Determine gender_id based on category
        $genderId = null;
        $shirtCategory = Category::where('name', 'Shirt')->first();
        $fabricCategory = Category::where('name', 'Fabric')->first();
        $unisexGender = Gender::where('name', 'Unisex')->first();

        if ($shirtCategory && $validatedData['category_id'] == $shirtCategory->id) {
            // For 'Shirt' category, use the selected gender from the form (Male/Female)
            $genderId = $validatedData['gender_id'];
        } elseif ($fabricCategory && $validatedData['category_id'] == $fabricCategory->id) {
            // For 'Fabric' category, always assign Unisex
            $genderId = $unisexGender->id;
        } else {
            // For other categories, use the selected gender from the form
            $genderId = $validatedData['gender_id'];
        }


        try {
            $product = Product::create([
                'category_id' => $validatedData['category_id'],
                'vendor_id' => $vendorId,
                'gender_id' => $genderId, // Assign the determined gender ID
                'name' => $validatedData['name'],
                'origin' => $validatedData['origin'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'image' => $imagePath,
                'material' => $validatedData['material'] ?? null,
                'slug' => $slug,
            ]);

            if (isset($validatedData['variants']) && is_array($validatedData['variants']) && count($validatedData['variants']) > 0) {
                foreach ($validatedData['variants'] as $variantData) {
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
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to create new product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category', 'variants', 'reviews', 'vendor', 'gender'); // Eager load gender
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        $genders = Gender::orderBy('name')->get(); // Fetch all genders
        return view('admin.products.update', compact('product', 'categories', 'vendors', 'genders'));
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

        $slug = $product->slug;
        if ($product->name !== $validatedData['name']) {
            $slug = Str::slug($validatedData['name']);
            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
        }

        // Determine vendor_id based on category (same logic as store)
        $vendorId = null;
        $shirtCategory = Category::where('name', 'Shirt')->first();

        if ($shirtCategory && $validatedData['category_id'] == $shirtCategory->id) {
            $kainaraVendor = Vendor::firstOrCreate(
                ['name' => 'Kainara'],
                [
                    'email' => 'info@kainara.com',
                    'phone_number' => '000-000-0000',
                    'address' => 'Jl. Kainara No. 1',
                    'city' => 'Jakarta',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '10000',
                    'business_type' => 'Textile',
                    'business_description' => 'Official vendor for Kainara products.',
                    'is_approved' => true,
                ]
            );
            $vendorId = $kainaraVendor->id;
        } else {
            $vendorId = $validatedData['vendor_id'];
        }

        // Determine gender_id based on category (same logic as store)
        $genderId = null;
        $shirtCategory = Category::where('name', 'Shirt')->first();
        $fabricCategory = Category::where('name', 'Fabric')->first();
        $unisexGender = Gender::where('name', 'Unisex')->first();

        if ($shirtCategory && $validatedData['category_id'] == $shirtCategory->id) {
            $genderId = $validatedData['gender_id'];
        } elseif ($fabricCategory && $validatedData['category_id'] == $fabricCategory->id) {
            $genderId = $unisexGender->id;
        } else {
            $genderId = $validatedData['gender_id'];
        }


        try {
            $product->update([
                'category_id' => $validatedData['category_id'],
                'vendor_id' => $vendorId,
                'gender_id' => $genderId, // Update with the determined gender ID
                'name' => $validatedData['name'],
                'origin' => $validatedData['origin'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'image' => $newImage,
                'material' => $validatedData['material'] ?? null,
                'slug' => $slug,
            ]);

            $submittedVariantIds = [];
            if (isset($validatedData['variants']) && is_array($validatedData['variants'])) {
                foreach ($validatedData['variants'] as $variantData) {
                    if (isset($variantData['id']) && !empty($variantData['id'])) {
                        $variant = ProductVariant::find($variantData['id']);
                        if ($variant && $variant->product_id === $product->id) {
                            $variant->update([
                                'color' => $variantData['color'],
                                'size' => $variantData['size'],
                                'stock' => $variantData['stock'],
                                'price' => $variantData['price'] ?? $validatedData['price'],
                            ]);
                            $submittedVariantIds[] = $variant->id;
                        }
                    } else {
                        $newVariant = $product->variants()->create([
                            'color' => $variantData['color'],
                            'size' => $variantData['size'],
                            'stock' => $variantData['stock'],
                            'price' => $variantData['price'] ?? $validatedData['price'],
                        ]);
                        $submittedVariantIds[] = $newVariant->id;
                    }
                }
            }

            $product->variants()->whereNotIn('id', $submittedVariantIds)->delete();

            if ($request->hasFile('image') && $oldImage && $oldImage !== $newImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            if ($request->hasFile('image') && $newImage && $newImage !== $oldImage) {
                Storage::disk('public')->delete($newImage);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}
