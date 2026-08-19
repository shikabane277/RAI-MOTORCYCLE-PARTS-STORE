<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
                          ->where('status', 'active')
                          ->with([
                              'brand',
                              'category',
                              'variants' => fn($q) => $q->where('is_active', true)->orderBy('color'),
                              'approvedReviews.user',
                              'motorcycleModels',
                          ])
                          ->firstOrFail();

        // Increment view count
        $product->increment('views');

        // Cross-sell: same category, different product
        $crossSell = Product::active()
                            ->where('category_id', $product->category_id)
                            ->where('id', '!=', $product->id)
                            ->with('variants')
                            ->take(4)
                            ->get();

        // Group variants by color for gallery
        $variantsByColor = $product->variants->groupBy('color');

        return view('shop.product', compact('product', 'crossSell', 'variantsByColor'));
    }
}
