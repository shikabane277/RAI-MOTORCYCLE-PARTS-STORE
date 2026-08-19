<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\MotorcycleModel;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['variants', 'brand', 'category', 'approvedReviews']);

        $this->applyFilters($query, $request);

        $products    = $query->paginate(20)->withQueryString();
        $categories  = Category::topLevel()->where('is_active', true)->with('children')->get();
        $brands      = Brand::where('is_active', true)->orderBy('name')->get();
        $materials   = ProductAttribute::ofType('material')->active()->get();
        $colors      = ProductAttribute::ofType('color')->active()->get();
        $threadSizes = ProductAttribute::ofType('thread_size')->active()->get();
        $currentCategory = null;

        return view('shop.index', compact('products', 'categories', 'brands', 'materials', 'colors', 'threadSizes', 'currentCategory'));
    }

    public function category(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // Get all subcategory IDs + this category
        $categoryIds = [$category->id];
        $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());

        $query = Product::active()->whereIn('category_id', $categoryIds)
                        ->with(['variants', 'brand', 'approvedReviews']);

        $this->applyFilters($query, $request);

        $products    = $query->paginate(20)->withQueryString();
        $categories  = Category::topLevel()->where('is_active', true)->with('children')->get();
        $brands      = Brand::where('is_active', true)->orderBy('name')->get();
        $materials   = ProductAttribute::ofType('material')->active()->get();
        $colors      = ProductAttribute::ofType('color')->active()->get();
        $threadSizes = ProductAttribute::ofType('thread_size')->active()->get();

        return view('shop.index', compact('products', 'categories', 'brands', 'materials', 'colors', 'threadSizes', 'category'));
    }

    private function applyFilters($query, Request $request): void
    {
        // Price range
        if ($request->min_price) {
            $query->whereHas('variants', fn($q) => $q->where('price', '>=', $request->min_price));
        }
        if ($request->max_price) {
            $query->whereHas('variants', fn($q) => $q->where('price', '<=', $request->max_price));
        }

        // Material
        if ($request->material) {
            $query->whereHas('variants', fn($q) => $q->where('material', $request->material));
        }

        // Color
        if ($request->color) {
            $query->whereHas('variants', fn($q) => $q->where('color', $request->color));
        }

        // Thread size
        if ($request->thread_size) {
            $query->whereHas('variants', fn($q) => $q->where('thread_size', $request->thread_size));
        }

        // Brand
        if ($request->brand) {
            $query->where('brand_id', $request->brand);
        }

        // Fitment (session bike)
        $fitment = session('fitment');
        if ($request->fitment_filter && $fitment) {
            $query->whereHas('motorcycleModels', fn($q) => $q->where('motorcycle_models.id', $fitment['id']));
        }

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        match($request->sort) {
            'price_asc'  => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'newest'     => $query->latest(),
            default      => $query->orderByDesc('is_featured')->orderByDesc('views'),
        };
    }
}
