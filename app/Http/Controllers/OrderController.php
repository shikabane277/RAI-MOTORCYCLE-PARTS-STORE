<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\LalamoveService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function trackForm()
    {
        $userOrders = auth()->check() ? auth()->user()->orders()->with(['items'])->latest()->take(5)->get() : collect();
        return view('track-order', compact('userOrders'));
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'email'        => 'nullable|string',
        ]);

        $rawInput = trim($request->order_number);
        $searchQuery = strtoupper($rawInput);
        $cleanQuery = str_replace('-', '', $searchQuery);

        // Flexible Order Lookup (with dashes, without dashes, or by tracking number)
        $order = Order::where('order_number', $rawInput)
            ->orWhere('order_number', $searchQuery)
            ->orWhereRaw("REPLACE(order_number, '-', '') = ?", [$cleanQuery])
            ->orWhere('tracking_number', $rawInput)
            ->with(['items', 'shipments', 'user', 'statusLogs'])
            ->first();

        if (!$order) {
            $userOrders = auth()->check() ? auth()->user()->orders()->with(['items'])->latest()->take(5)->get() : collect();
            return back()->withInput()->withErrors(['order_number' => "Order '{$rawInput}' was not found. Please verify your order number (e.g. MB-2026-12345)."]);
        }

        // Optional email verification if user supplied an email
        if (!empty($request->email)) {
            $orderEmail = strtolower(trim($order->user?->email ?? $order->guest_email ?? ''));
            $inputEmail = strtolower(trim($request->email));
            if ($orderEmail && $orderEmail !== $inputEmail) {
                $userOrders = auth()->check() ? auth()->user()->orders()->with(['items'])->latest()->take(5)->get() : collect();
                return back()->withInput()->withErrors(['email' => 'The email address provided does not match this order.']);
            }
        }

        $lalamoveService = app(LalamoveService::class);
        $lalamoveTracking = null;
        try {
            $lalamoveTracking = $lalamoveService->getTrackingStatus($order->tracking_number ?? 'LLM-PH-ORDER');
        } catch (\Throwable $e) {
            // Safe fallback
        }

        $userOrders = auth()->check() ? auth()->user()->orders()->with(['items'])->latest()->take(5)->get() : collect();

        return view('track-order', compact('order', 'lalamoveTracking', 'userOrders'));
    }
}
