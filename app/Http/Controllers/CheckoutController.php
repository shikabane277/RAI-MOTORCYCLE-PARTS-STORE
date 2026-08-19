<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private function getCart(): ?Cart
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())->with('items.variant.product')->first();
        }
        return Cart::where('session_id', session()->getId())->with('items.variant.product')->first();
    }

    public function index()
    {
        $cart = $this->getCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $coupon   = null;
        $discount = 0;
        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if ($coupon) $discount = $coupon->calculateDiscount($cart->subtotal);
        }

        $shippingFee = $cart->subtotal >= 1500 ? 0 : 89;
        $defaultAddress = auth()->check() ? auth()->user()->addresses()->where('is_default', true)->first() : null;

        $lalamoveService = app(\App\Services\LalamoveService::class);
        $lalamoveWindow = $lalamoveService->isSameDayWindowActive();

        return view('checkout.index', compact('cart', 'coupon', 'discount', 'shippingFee', 'defaultAddress', 'lalamoveWindow'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'line1'          => 'required|string|max:200',
            'barangay'       => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'region'         => 'nullable|string|max:100',
            'zip_code'       => 'nullable|string|max:10',
            'payment_method' => 'required|in:cod,google_pay,qrph',
            'notes'          => 'nullable|string|max:500',
        ]);

        $cart = $this->getCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate totals
        $subtotal = $cart->items->sum(fn($item) => $item->variant->effective_price * $item->qty);
        $shippingFee = $subtotal >= 1500 ? 0 : 89;
        $discount = 0;
        $couponCode = null;

        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
                $couponCode = $coupon->code;
                $coupon->increment('usage_count');
            }
        }

        $grandTotal = $subtotal + $shippingFee - $discount;
        $isOnlinePayment = in_array($request->payment_method, ['google_pay', 'qrph']);
        $orderStatus = $isOnlinePayment ? 'pending_payment' : 'confirmed';

        $orderId = null;

        DB::transaction(function () use ($request, $cart, $subtotal, $shippingFee, $discount, $grandTotal, $couponCode, $orderStatus, $isOnlinePayment, &$orderId) {
            // Create order
            $order = Order::create([
                'order_number'    => Order::generateOrderNumber(),
                'user_id'         => auth()->id(),
                'guest_name'      => auth()->check() ? null : $request->recipient_name,
                'guest_email'     => auth()->check() ? null : $request->guest_email,
                'guest_phone'     => auth()->check() ? null : $request->phone,
                'ship_recipient'  => $request->recipient_name,
                'ship_phone'      => $request->phone,
                'ship_line1'      => $request->line1,
                'ship_barangay'   => $request->barangay,
                'ship_city'       => $request->city,
                'ship_province'   => $request->province,
                'ship_region'     => $request->region,
                'ship_zip'        => $request->zip_code,
                'subtotal'        => $subtotal,
                'shipping_fee'    => $shippingFee,
                'discount_total'  => $discount,
                'grand_total'     => $grandTotal,
                'coupon_code'     => $couponCode,
                'payment_method'  => $request->payment_method,
                'payment_status'  => 'pending',
                'status'          => $orderStatus,
                'courier'         => 'Lalamove Express',
                'tracking_number' => 'LLM-PH-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'notes'           => $request->notes,
                'placed_at'       => now(),
            ]);

            // Create order items and decrement stock
            foreach ($cart->items as $cartItem) {
                $variant = $cartItem->variant;
                $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'product_name'       => $variant->product->name,
                    'variant_sku'        => $variant->variant_sku,
                    'variant_label'      => $variant->label,
                    'image_url'          => $variant->image_url,
                    'qty'                => $cartItem->qty,
                    'unit_price'         => $variant->effective_price,
                    'line_total'         => $variant->effective_price * $cartItem->qty,
                ]);

                // Decrement stock
                $variant->decrement('stock_qty', $cartItem->qty);

                // Log inventory
                InventoryLog::create([
                    'product_variant_id' => $variant->id,
                    'change_qty'         => -$cartItem->qty,
                    'stock_after'        => $variant->fresh()->stock_qty,
                    'reason'             => 'sale',
                    'reference'          => $order->order_number,
                    'created_at'         => now(),
                ]);
            }

            // Create payment record
            $gateway = match($request->payment_method) {
                'google_pay' => 'paymongo_card',
                'qrph'       => 'paymongo_qrph',
                default      => $request->payment_method,
            };

            Payment::create([
                'order_id' => $order->id,
                'gateway'  => $gateway,
                'amount'   => $grandTotal,
                'status'   => 'pending',
            ]);

            // Only clear cart for COD (immediate confirmation)
            // For online payments, cart is cleared after payment succeeds
            if (!$isOnlinePayment) {
                $cart->items()->delete();
                $cart->delete();
            }

            $orderId = $order->id;
            session(['last_order_id' => $order->id, 'last_order_number' => $order->order_number]);
        });

        $order = Order::find($orderId);

        // Handle PayMongo checkout for online payments
        if ($isOnlinePayment && $order) {
            $payMongoService = app(\App\Services\PayMongoService::class);

            // Card-only session for Google Pay, QR Ph session for qrph
            $paymentType = ($request->payment_method === 'google_pay') ? 'card' : 'qrph';
            $payMongoResult = $payMongoService->createCheckoutSession($order, $paymentType);

            if (!empty($payMongoResult['success']) && !empty($payMongoResult['checkout_url'])) {
                return redirect()->away($payMongoResult['checkout_url']);
            }

            // If PayMongo fails, restore the cart — cancel the order
            $this->cancelFailedOrder($order);
            return redirect()->route('checkout.index')->with('error', 'Payment gateway is currently unavailable. Please try again or choose a different payment method.');
        }

        return redirect()->route('checkout.success', $order ? $order->id : session('last_order_number'));
    }

    public function success(Order $order, Request $request)
    {
        // PayMongo success callback — mark order as paid and clear cart
        if ($request->query('paymongo') === 'success' && $order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
            ]);
            $order->payments()->update(['status' => 'paid']);

            // Now clear the cart since payment succeeded
            $cart = $this->getCart();
            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }
        }

        $order->load(['items', 'payments']);
        return view('checkout.success', compact('order'));
    }

    /**
     * Cancel a failed order — restore stock and remove the order
     */
    private function cancelFailedOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_variant_id) {
                $variant = $item->variant;
                if ($variant) {
                    $variant->increment('stock_qty', $item->qty);
                }
            }
        }
        $order->payments()->delete();
        $order->items()->delete();
        $order->delete();
    }
}
