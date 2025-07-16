<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $originFilter = $request->query('origins');
        $colorFilter = $request->query('colors');
        $sortOption = $request->query('sort');

        $products = Product::query()
        ->when($originFilter, function ($query, $origin) {
            return $query->whereIn('origin', (array) $origin);
        })
        ->when($colorFilter, function ($query, $colors) {
            return $query->whereHas('variants', function ($q) use ($colors) {
                $q->whereIn('color', (array) $colors);
            });
        })
        ->when($sortOption, function ($query, $sortOption) {
            return match ($sortOption) {
                'az' => $query->orderBy('name', 'asc'),
                'za' => $query->orderBy('name', 'desc'),
                'price_low' => $query->orderBy('price', 'asc'),
                'price_high' => $query->orderBy('price', 'desc'),
                default => $query,
            };
        })
        ->paginate(9);

        $availableOrigins = Product::select('origin')->distinct()->pluck('origin')->toArray();
        $availableColors = ProductVariant::select('color')->distinct()->pluck('color')->toArray();

        return view('products.index', compact(
            'products',
            'availableOrigins',
            'availableColors',
            'originFilter',
            'colorFilter',
            'sortOption'
        ));
    }

    /**
     * Display the specified product using its slug.
     *
     * @param  string  $slug The slug of the product
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Use where('slug', $slug) instead of findOrFail($id)
        $product = Product::with('variants')->where('slug', $slug)->firstOrFail();
        return view('products.detail', compact('product'));
    }
}