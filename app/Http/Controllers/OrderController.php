<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function trackForm()
    {
        return view('track-order');
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'email'        => 'required|email',
        ]);

        $order = Order::where('order_number', strtoupper($request->order_number))
                      ->with(['items', 'shipments'])
                      ->first();

        if (!$order) {
            return back()->withErrors(['order_number' => 'Order not found.']);
        }

        // Verify ownership via email
        $email = $order->user?->email ?? $order->guest_email;
        if (strtolower($email) !== strtolower($request->email)) {
            return back()->withErrors(['order_number' => 'Order not found.']);
        }

        $lalamoveService = app(\App\Services\LalamoveService::class);
        $lalamoveTracking = $lalamoveService->getTrackingStatus($order->tracking_number ?? 'LLM-PH-DEMO123');

        return view('track-order', compact('order', 'lalamoveTracking'));
    }
}
