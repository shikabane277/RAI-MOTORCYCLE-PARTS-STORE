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

        return view('checkout.index', compact('cart', 'coupon', 'discount', 'shippingFee', 'defaultAddress'));
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
            'payment_method' => 'required|in:cod,gcash,maya,bank_transfer,google_pay',
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

        DB::transaction(function () use ($request, $cart, $subtotal, $shippingFee, $discount, $grandTotal, $couponCode) {
            // Create order
            $order = Order::create([
                'order_number'   => Order::generateOrderNumber(),
                'user_id'        => auth()->id(),
                'guest_name'     => auth()->check() ? null : $request->recipient_name,
                'guest_email'    => auth()->check() ? null : $request->guest_email,
                'guest_phone'    => auth()->check() ? null : $request->phone,
                'ship_recipient' => $request->recipient_name,
                'ship_phone'     => $request->phone,
                'ship_line1'     => $request->line1,
                'ship_barangay'  => $request->barangay,
                'ship_city'      => $request->city,
                'ship_province'  => $request->province,
                'ship_region'    => $request->region,
                'ship_zip'       => $request->zip_code,
                'subtotal'       => $subtotal,
                'shipping_fee'   => $shippingFee,
                'discount_total' => $discount,
                'grand_total'    => $grandTotal,
                'coupon_code'    => $couponCode,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'google_pay' ? 'paid' : 'pending',
                'status'         => 'confirmed',
                'notes'          => $request->notes,
                'placed_at'      => now(),
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
            Payment::create([
                'order_id' => $order->id,
                'gateway'  => $request->payment_method,
                'amount'   => $grandTotal,
                'status'   => 'pending',
            ]);

            // Clear cart
            $cart->items()->delete();
            $cart->delete();

            session(['last_order_number' => $order->order_number]);
        });

        return redirect()->route('checkout.success', session('last_order_number'));
    }

    public function success(Order $order)
    {
        $order->load('items');
        return view('checkout.success', compact('order'));
    }
}
