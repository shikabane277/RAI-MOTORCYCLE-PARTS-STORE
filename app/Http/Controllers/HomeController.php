<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        $banners      = Banner::active()->get();
        $categories   = Category::topLevel()->where('is_active', true)->orderBy('sort_order')->get();
        $deals        = Product::active()->where('status', 'active')
                               ->whereHas('variants', fn($q) => $q->whereNotNull('sale_price'))
                               ->with(['variants' => fn($q) => $q->whereNotNull('sale_price')->where('is_active', true)])
                               ->take(8)->get();
        $newArrivals  = Product::newArrivals()->with('variants')->take(8)->get();
        $bestSellers  = Product::bestSellers()->active()->with('variants')->take(8)->get();
        $reviews      = Review::approved()->with('user')->latest()->take(12)->get();
        $featuredProducts = Product::featured()->with(['variants', 'brand'])->take(4)->get();

        return view('home', compact(
            'banners', 'categories', 'deals', 'newArrivals', 'bestSellers', 'reviews', 'featuredProducts'
        ));
    }
}
