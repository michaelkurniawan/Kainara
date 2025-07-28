<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Gender;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $originFilter = $request->query('origins');
        $colorFilter = $request->query('colors');
        $sortOption = $request->query('sort');

        $genderFilter = $request->route('gender') ?? $request->query('gender');
        $categoryNameFilter = $request->route('category_name') ?? $request->query('category_name');

        if ($request->has('gender') && !$request->route('gender')) {
            $queryParams = $request->except(['gender']);
            return redirect()->route('products.gender.index', array_merge(['gender' => $request->query('gender')], $queryParams));
        }
        if ($request->has('category_name') && !$request->route('category_name')) {
            $queryParams = $request->except(['category_name']);
            return redirect()->route('products.category.index', array_merge(['category_name' => $request->query('category_name')], $queryParams));
        }

        if (!$genderFilter && !$categoryNameFilter) {
            return redirect()->route('products.gender.index', ['gender' => 'Male']);
        }

        $products = Product::query()
            ->when($originFilter, function ($query, $origin) {
                return $query->whereIn('origin', (array) $origin);
            })
            ->when($colorFilter, function ($query, $colors) {
                return $query->whereHas('variants', function ($q) use ($colors) {
                    $q->whereIn('color', (array) $colors);
                });
            })
            ->when($genderFilter, function ($query, $genderName) {
                $gender = Gender::where('name', $genderName)->first();
                if ($gender) {
                    return $query->where('gender_id', $gender->id);
                }
                return $query;
            })
            ->when($categoryNameFilter, function ($query, $categoryName) {
                $category = Category::where('name', $categoryName)->first();
                if ($category) {
                    return $query->where('category_id', $category->id);
                }
                return $query;
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

        $queryAvailableOrigins = Product::query();
        if ($genderFilter) {
            $gender = Gender::where('name', $genderFilter)->first();
            if ($gender) {
                $queryAvailableOrigins->where('gender_id', $gender->id);
            }
        }
        if ($categoryNameFilter) {
            $category = Category::where('name', $categoryNameFilter)->first();
            if ($category) {
                $queryAvailableOrigins->where('category_id', $category->id);
            }
        }
        $availableOrigins = $queryAvailableOrigins->select('origin')->distinct()->pluck('origin')->toArray();

        $queryAvailableColors = ProductVariant::query();
        if ($genderFilter) {
            $gender = Gender::where('name', $genderFilter)->first();
            if ($gender) {
                $queryAvailableColors->whereHas('product', function ($q) use ($gender) {
                    $q->where('gender_id', $gender->id);
                });
            }
        }
        if ($categoryNameFilter) {
            $category = Category::where('name', $categoryNameFilter)->first();
            if ($category) {
                $queryAvailableColors->whereHas('product', function ($q) use ($category) {
                    $q->where('category_id', $category->id);
                });
            }
        }
        $availableColors = $queryAvailableColors->select('color')->distinct()->pluck('color')->toArray();

        return view('products.index', compact(
            'products',
            'availableOrigins',
            'availableColors',
            'originFilter',
            'colorFilter',
            'sortOption',
            'genderFilter',
            'categoryNameFilter'
        ));
    }

    public function show($slug)
    {
        $product = Product::with(['variants', 'gender'])->where('slug', $slug)->firstOrFail();

        $sizeChartComponent = 'components.popupsizechart.default';

        if ($product->gender) {
            $productNameLower = Str::lower($product->name);

            if ($product->gender->name === 'Male') {
                if (Str::startsWith($productNameLower, "men's woven vest")) {
                    $sizeChartComponent = 'components.popupsizechart.menvest';
                } elseif (Str::startsWith($productNameLower, "men's outerwear")) {
                    $sizeChartComponent = 'components.popupsizechart.menouterwear';
                } else {
                    $sizeChartComponent = 'components.popupsizechart.men';
                }
            } elseif ($product->gender->name === 'Female') {
                if (Str::startsWith($productNameLower, "women's vest")) {
                    $sizeChartComponent = 'components.popupsizechart.womenvest';
                } elseif (Str::startsWith($productNameLower, "women's outerwear")) {
                    $sizeChartComponent = 'components.popupsizechart.womenouterwear';
                } elseif (Str::startsWith($productNameLower, "women's dress")) {
                    $sizeChartComponent = 'components.popupsizechart.womendress';
                } elseif (Str::startsWith($productNameLower, "women's blouse")) {
                    $sizeChartComponent = 'components.popupsizechart.womenblouse';
                } else {
                    $sizeChartComponent = 'components.popupsizechart.women';
                }
            }
        }

        $productReviews = $product->reviews()
                                  ->with('user') // Memuat relasi user
                                  ->orderBy('created_at', 'desc')
                                  ->get()
                                  ->map(function($review) {
                                      $firstName = $review->user->first_name ?? '';
                                      $lastName = $review->user->last_name ?? '';

                                      $fullName = trim(str_replace('  ', ' ', $firstName . ' ' . $lastName));

                                      return [
                                          'user_name' => $fullName ?: 'Pengguna Anonim', 
                                          'rating' => (float) $review->rating,
                                          'comment' => $review->comment,
                                          'created_at' => $review->created_at->toDateTimeString(),
                                      ];
                                  });

        return view('products.detail', compact('product', 'sizeChartComponent', 'productReviews'));
    }
}