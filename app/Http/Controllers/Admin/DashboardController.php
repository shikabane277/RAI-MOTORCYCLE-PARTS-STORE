<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\ProductVariant;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue   = Order::whereIn('status', ['completed', 'delivered'])->sum('grand_total');
        $todayRevenue   = Order::whereIn('status', ['completed', 'delivered'])->whereDate('placed_at', today())->sum('grand_total');
        $totalOrders    = Order::count();
        $pendingOrders  = Order::whereIn('status', ['confirmed', 'processing'])->count();
        $newOrders      = Order::where('status', 'confirmed')->latest()->take(5)->get();
        $pendingReviews = Review::pending()->count();

        $lowStockItems  = ProductVariant::where('stock_qty', '>', 0)
                                        ->whereRaw('stock_qty <= low_stock_threshold')
                                        ->with('product')
                                        ->get();

        $outOfStock     = ProductVariant::where('stock_qty', 0)->with('product')->count();
        $totalProducts  = Product::active()->count();
        $totalCustomers = \App\Models\User::where('role', 'customer')->count();

        // Revenue last 7 days
        $revenueChart = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'date'    => $date->format('M d'),
                'revenue' => Order::whereIn('status', ['completed', 'delivered'])
                                   ->whereDate('placed_at', $date)
                                   ->sum('grand_total'),
            ];
        });

        return view('admin.dashboard', compact(
            'totalRevenue', 'todayRevenue', 'totalOrders', 'pendingOrders',
            'newOrders', 'pendingReviews', 'lowStockItems', 'outOfStock',
            'totalProducts', 'totalCustomers', 'revenueChart'
        ));
    }
}
