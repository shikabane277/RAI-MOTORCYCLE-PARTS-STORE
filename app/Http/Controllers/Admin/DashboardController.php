<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\ProductVariant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Order states that represent money the store has actually earned.
     */
    private const REVENUE_STATUSES = ['completed', 'delivered'];

    public function index()
    {
        $totalRevenue   = $this->revenueQuery()->sum('grand_total');
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

        $revenueChart  = $this->revenueLastDays(7);
        $revenueTotal  = $revenueChart->sum('revenue');

        // Read "today" off the chart's final bucket rather than running a
        // separate query, so the KPI can never disagree with the last bar.
        $todayRevenue  = (float) $revenueChart->last()['revenue'];

        return view('admin.dashboard', compact(
            'totalRevenue', 'todayRevenue', 'totalOrders', 'pendingOrders',
            'newOrders', 'pendingReviews', 'lowStockItems', 'outOfStock',
            'totalProducts', 'totalCustomers', 'revenueChart', 'revenueTotal'
        ));
    }

    /**
     * Base filter for earned revenue.
     *
     * Refunds are handled by snapshotting at query time: an order that is
     * refunded leaves `status` as refunded/return_requested and drops out of
     * this filter, so historical bars re-render without it and totals stay
     * truthful on the next load. The payment_status guard catches the case
     * where the money is sent back but the fulfilment status was left as
     * delivered.
     */
    private function revenueQuery()
    {
        return Order::whereIn('status', self::REVENUE_STATUSES)
                    ->where('payment_status', '!=', 'refunded');
    }

    /**
     * Revenue for a rolling window of $days ending today, split into product
     * sales and shipping fees.
     *
     * Buckets are keyed on the order date (when the sale happened) in the
     * store's business timezone, and every day in the window is emitted even
     * when it has no orders, so the x-axis always shows $days consecutive
     * labels instead of only the days present in the table.
     */
    private function revenueLastDays(int $days)
    {
        $tz = config('store.timezone');

        $windowStart = Carbon::now($tz)->startOfDay()->subDays($days - 1);
        $windowEnd   = Carbon::now($tz)->endOfDay();

        // Pre-seed every day at zero, then fold orders onto the scaffold.
        $buckets = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $windowStart->copy()->addDays($i);

            $buckets[$day->toDateString()] = [
                'date'             => $day->format('M d'),
                'full_date'        => $day->format('D, M j, Y'),
                'product_revenue'  => 0.0,
                'shipping_revenue' => 0.0,
                'revenue'          => 0.0,
                'orders'           => 0,
            ];
        }

        // One pass over the window. The range is widened to UTC because that is
        // how the column is stored; rows that fall outside the local window
        // after conversion are discarded during bucketing below.
        $orders = $this->revenueQuery()
            ->whereBetween(DB::raw('COALESCE(placed_at, created_at)'), [
                $windowStart->copy()->utc(),
                $windowEnd->copy()->utc(),
            ])
            ->get(['placed_at', 'created_at', 'shipping_fee', 'discount_total', 'grand_total']);

        foreach ($orders as $order) {
            $placedAt = $order->placed_at ?? $order->created_at;

            if (! $placedAt) {
                continue;
            }

            $key = $placedAt->copy()->setTimezone($tz)->toDateString();

            if (! isset($buckets[$key])) {
                continue;
            }

            [$productRevenue, $shippingRevenue] = $this->splitOrderRevenue($order);

            $buckets[$key]['product_revenue']  += $productRevenue;
            $buckets[$key]['shipping_revenue'] += $shippingRevenue;
            $buckets[$key]['revenue']          += $productRevenue + $shippingRevenue;
            $buckets[$key]['orders']++;
        }

        return collect(array_values($buckets));
    }

    /**
     * Split one order's grand total into [product sales, shipping fees].
     *
     * grand_total is subtotal + shipping_fee - discount_total, so charting raw
     * subtotal against raw shipping_fee overstates every discounted order.
     * The discount is instead netted off the component it was applied to, which
     * keeps each segment honest and guarantees the stacked bar height equals
     * grand_total (and therefore agrees with the Total Revenue card).
     *
     * A free-shipping coupon is written as a discount exactly equal to the
     * shipping fee (see Coupon::calculateDiscount); every other coupon type
     * discounts the products.
     */
    private function splitOrderRevenue(Order $order): array
    {
        $shipping = (float) $order->shipping_fee;
        $discount = (float) $order->discount_total;

        $shippingRevenue = ($shipping > 0 && abs($discount - $shipping) < 0.01) ? 0.0 : $shipping;

        // Derived from grand_total rather than subtotal so the two segments
        // always sum to the amount actually charged, and never go negative.
        $productRevenue = max(0.0, (float) $order->grand_total - $shippingRevenue);

        return [$productRevenue, $shippingRevenue];
    }
}
