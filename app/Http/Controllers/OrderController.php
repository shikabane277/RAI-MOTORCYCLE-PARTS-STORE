<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\LalamoveService;
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

        $order = Order::where('order_number', strtoupper(trim($request->order_number)))
                      ->with(['items', 'shipments'])
                      ->first();

        if (!$order) {
            return back()->withErrors(['order_number' => 'Order not found. Please check your order number.']);
        }

        // Verify ownership via email
        $email = $order->user?->email ?? $order->guest_email;
        if ($email && strtolower(trim($email)) !== strtolower(trim($request->email))) {
            return back()->withErrors(['order_number' => 'Order number and email address do not match our records.']);
        }

        $lalamoveService = app(LalamoveService::class);
        $lalamoveTracking = $lalamoveService->getTrackingStatus($order->tracking_number ?? 'LLM-PH-ORDER');

        return view('track-order', compact('order', 'lalamoveTracking'));
    }
}
