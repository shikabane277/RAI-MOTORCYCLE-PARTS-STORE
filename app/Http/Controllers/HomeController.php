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
        try {
            $banners = Banner::active()->get();
        } catch (\Throwable $e) {
            $banners = collect();
        }

        try {
            $categories = Category::topLevel()->withCount('products')->where('is_active', true)->orderBy('sort_order')->get();
        } catch (\Throwable $e) {
            $categories = collect();
        }

        try {
            $deals = Product::active()
                ->whereHas('variants', fn($q) => $q->whereNotNull('sale_price')->where('sale_price', '>', 0))
                ->with(['variants', 'brand', 'category'])
                ->take(8)->get();
        } catch (\Throwable $e) {
            $deals = collect();
        }

        try {
            $newArrivals = Product::newArrivals()->with(['variants', 'brand', 'category'])->take(8)->get();
        } catch (\Throwable $e) {
            $newArrivals = collect();
        }

        try {
            $bestSellers = Product::bestSellers()->active()->with(['variants', 'brand', 'category'])->take(8)->get();
        } catch (\Throwable $e) {
            $bestSellers = collect();
        }

        try {
            $reviews = Review::approved()->with('user')->latest()->take(12)->get();
        } catch (\Throwable $e) {
            $reviews = collect();
        }

        try {
            $featuredProducts = Product::featured()->with(['variants', 'brand', 'category'])->take(4)->get();
        } catch (\Throwable $e) {
            $featuredProducts = collect();
        }

        return view('home', compact(
            'banners', 'categories', 'deals', 'newArrivals', 'bestSellers', 'reviews', 'featuredProducts'
        ));
    }
}
