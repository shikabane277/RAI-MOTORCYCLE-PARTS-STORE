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
        $forcedStep = session("lalamove_step_{$order->id}", null);
        $lalamoveTracking = $lalamoveService->getTrackingStatus($order->tracking_number ?? 'LLM-PH-DEMO123', $forcedStep);

        return view('track-order', compact('order', 'lalamoveTracking'));
    }

    /**
     * Advance simulated Lalamove delivery stage (for testing/demo)
     */
    public function advanceLalamoveStep(Request $request, Order $order)
    {
        $currentStep = session("lalamove_step_{$order->id}", 2);
        $nextStep = ($currentStep >= 4) ? 1 : $currentStep + 1;
        session(["lalamove_step_{$order->id}" => $nextStep]);

        if ($nextStep === 4) {
            $order->update(['status' => 'delivered']);
        } elseif ($nextStep === 3) {
            $order->update(['status' => 'shipped']);
        }

        return redirect()->back()->with('success', "Lalamove delivery status updated to Step {$nextStep}!");
    }
}
