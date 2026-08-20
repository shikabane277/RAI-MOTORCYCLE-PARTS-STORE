<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $shippingFee = $cart->subtotal >= 1500 ? 0 : 89;

        $coupon   = null;
        $discount = 0;
        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if ($coupon) $discount = $coupon->calculateDiscount($cart->subtotal, $shippingFee);
        }
        $defaultAddress = auth()->check() ? auth()->user()->addresses()->where('is_default', true)->first() : null;

        $lalamoveService = app(\App\Services\LalamoveService::class);
        $lalamoveWindow = $lalamoveService->isSameDayWindowActive();

        return view('checkout.index', compact('cart', 'coupon', 'discount', 'shippingFee', 'defaultAddress', 'lalamoveWindow'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_name'  => 'required|string|max:100',
            'phone'           => 'required|string|max:20',
            'line1'           => 'required|string|max:200',
            'barangay'        => 'required|string|max:100',
            'city'            => 'required|string|max:100',
            'province'        => 'required|string|max:100',
            'region'          => 'nullable|string|max:100',
            'zip_code'        => 'nullable|string|max:10',
            'shipping_method' => 'required|in:same_day,standard',
            'payment_method' => 'required|in:cod,google_pay,qrph,gcash',
            'gcash_number'   => 'required_if:payment_method,gcash|nullable|string|max:20',
            'notes'          => 'nullable|string|max:500',
        ], [
            'gcash_number.required_if' => 'Please enter your GCash Mobile Number when selecting GCash as payment method.',
            'shipping_method.required' => 'Please select a delivery service method.',
        ]);

        $lalamoveService = app(\App\Services\LalamoveService::class);
        $lalamoveWindow = $lalamoveService->isSameDayWindowActive();

        if ($request->shipping_method === 'same_day' && !$lalamoveWindow['is_active']) {
            return back()->withInput()->withErrors([
                'shipping_method' => 'Lalamove Same-Day Delivery is currently closed (Cutoff was 4:00 PM). Please select J&T Express Standard Shipping.'
            ]);
        }

        $courier = $request->shipping_method === 'same_day' ? 'Lalamove Express' : 'J&T Express';
        $trackingNumber = ($request->shipping_method === 'same_day' ? 'LLM-PH-' : 'JNT-PH-') . strtoupper(\Illuminate\Support\Str::random(8));

        $cart = $this->getCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate totals
        $subtotal = $cart->items->sum(fn($item) => $item->variant->effective_price * $item->qty);
        $shippingFee = $subtotal >= 1500 ? 0 : 89;
        $discount = 0;
        $couponCode = null;
        $appliedCoupon = null;

        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal, $shippingFee);
                $couponCode = $coupon->code;
                $appliedCoupon = $coupon;
            }
        }

        $grandTotal = $subtotal + $shippingFee - $discount;
        $isOnlinePayment = in_array($request->payment_method, ['google_pay', 'qrph', 'gcash']);
        $orderStatus = $isOnlinePayment ? 'pending_payment' : 'confirmed';

        $orderId = null;

        DB::transaction(function () use ($request, $cart, $subtotal, $shippingFee, $discount, $grandTotal, $couponCode, $appliedCoupon, $orderStatus, $isOnlinePayment, $courier, $trackingNumber, &$orderId) {
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
                'gcash_number'    => $request->payment_method === 'gcash' ? $request->gcash_number : null,
                'payment_status'  => 'pending',
                'status'          => $orderStatus,
                'courier'         => $courier,
                'tracking_number' => $trackingNumber,
                'notes'           => $request->notes,
                'placed_at'       => now(),
            ]);

            // Create initial status log
            $order->statusLogs()->create([
                'status'      => $orderStatus,
                'title'       => $isOnlinePayment ? 'Order Placed (Awaiting Payment)' : 'Order Placed & Confirmed (COD)',
                'description' => $isOnlinePayment
                    ? 'Order placed successfully. Awaiting payment authorization from payment gateway.'
                    : 'Order placed via Cash on Delivery. Seller has been notified to prepare your items for shipment.',
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
                'gcash'      => 'paymongo_gcash',
                default      => $request->payment_method,
            };

            Payment::create([
                'order_id' => $order->id,
                'gateway'  => $gateway,
                'amount'   => $grandTotal,
                'status'   => 'pending',
            ]);

            // Burn the coupon use inside the transaction so a failure here
            // cannot leave the count incremented against an order that was
            // never created.
            $appliedCoupon?->increment('usage_count');

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

            $paymentType = match($request->payment_method) {
                'google_pay' => 'card',
                'gcash'      => 'gcash',
                default      => 'qrph',
            };
            $payMongoResult = $payMongoService->createCheckoutSession($order, $paymentType);

            if (!empty($payMongoResult['success']) && !empty($payMongoResult['checkout_url'])) {
                // Remember the session id so both the customer's return trip and
                // the webhook can verify this payment against PayMongo instead
                // of trusting whatever lands on the success URL.
                if (!empty($payMongoResult['session_id'])) {
                    $order->payments()->update(['gateway_ref' => $payMongoResult['session_id']]);
                }

                return redirect()->away($payMongoResult['checkout_url']);
            }

            $reason    = $payMongoResult['message'] ?? 'PayMongo session creation failed.';
            $errorCode = $payMongoResult['error_code'] ?? null;

            // Log before cancelling — cancelFailedOrder deletes the order and
            // with it every trace of what went wrong.
            Log::error('Checkout: could not start online payment, cancelling order', [
                'order'      => $order->order_number,
                'method'     => $request->payment_method,
                'total'      => $order->grand_total,
                'error_code' => $errorCode,
                'reason'     => $reason,
            ]);

            // If PayMongo fails, restore the cart — cancel the order
            $this->cancelFailedOrder($order);

            // An unavailable method or a too-small total is a specific, useful
            // answer for the customer; only genuinely opaque gateway faults get
            // the generic message.
            $actionable = in_array($errorCode, ['payment_method_not_enabled', 'below_minimum', 'not_configured'], true);

            return redirect()->route('checkout.index')->with(
                'error',
                $actionable || config('app.debug')
                    ? $reason
                    : 'Payment gateway is currently unavailable. Please try again or choose a different payment method.'
            );
        }

        return redirect()->route('checkout.success', $order ? $order->order_number : session('last_order_number'));
    }

    public function success(Order $order, Request $request)
    {
        // The ?paymongo=success flag only says where the customer came from —
        // anyone can type it — so ask PayMongo whether the money actually moved.
        // Checked on every view of an online order, not just the redirect, so a
        // customer who closed the gateway tab still sees the truth on reload.
        if (in_array($order->payment_method, ['google_pay', 'qrph', 'gcash'], true)) {
            $this->confirmOnlinePayment($order);
        }

        $order->load(['items', 'payments']);
        return view('checkout.success', compact('order'));
    }

    /**
     * Verify an online payment with PayMongo and settle the order only if the
     * gateway confirms it. Safe to call repeatedly; the webhook does the same
     * work independently and whichever arrives first wins.
     */
    private function confirmOnlinePayment(Order $order): void
    {
        if ($order->payment_status === 'paid') {
            $this->clearBuyersCart($order);
            return;
        }

        $payment = $order->payments()->whereNotNull('gateway_ref')->latest('id')->first();

        if (!$payment || !str_starts_with((string) $payment->gateway_ref, 'cs_')) {
            Log::warning('Checkout: no PayMongo session recorded, cannot verify payment', [
                'order' => $order->order_number,
            ]);
            return;
        }

        $result = app(\App\Services\PayMongoService::class)->verifyCheckoutSessionPaid($payment->gateway_ref);

        if (empty($result['paid'])) {
            Log::info('Checkout: PayMongo has not confirmed this payment', [
                'order'      => $order->order_number,
                'session_id' => $payment->gateway_ref,
                'status'     => $result['status'] ?? null,
                'message'    => $result['message'] ?? null,
            ]);
            return;
        }

        if (! $this->amountCoversOrder($order, $result['amount'] ?? null)) {
            Log::error('Checkout: PayMongo reports a paid amount below the order total, not settling', [
                'order'      => $order->order_number,
                'session_id' => $payment->gateway_ref,
                'paid'       => $result['amount'] ?? null,
                'expected'   => (int) round(((float) $order->grand_total) * 100),
            ]);

            return;
        }

        if ($order->settleOnlinePayment($result['payment_id'] ?? null, $result['session'] ?? [])) {
            Log::info('Checkout: payment confirmed by PayMongo on return', [
                'order'      => $order->order_number,
                'session_id' => $payment->gateway_ref,
                'payment_id' => $result['payment_id'] ?? null,
            ]);
        }

        // Payment went through — the cart has served its purpose.
        $this->clearBuyersCart($order);
    }

    /**
     * Refuse to settle for less than the order is worth. PayMongo reports the
     * captured amount in centavos; over-payment is odd but harmless, short
     * payment must never mark an order paid.
     */
    private function amountCoversOrder(Order $order, ?int $paidCentavos): bool
    {
        $expected = (int) round(((float) $order->grand_total) * 100);

        // No amount reported — the paid flag came from the session itself, and
        // the session was created for exactly grand_total, so let it through.
        if ($paidCentavos === null) {
            return true;
        }

        return $paidCentavos >= $expected;
    }

    /**
     * Empty the cart that produced this order — and only that one.
     *
     * The success page can be reopened at any time (reload, history, a shared
     * link), and it is not behind auth, so clearing "whatever cart the current
     * visitor has" would silently delete a shopper's in-progress cart. store()
     * stamps last_order_id on the session that placed the order, so that is the
     * only session allowed to have its cart cleared here.
     */
    private function clearBuyersCart(Order $order): void
    {
        if ((int) session('last_order_id') !== (int) $order->id) {
            return;
        }

        $cart = $this->getCart();

        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }
    }

    /**
     * Cancel a failed order — restore stock, release the coupon and remove the order
     */
    private function cancelFailedOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if (!$item->product_variant_id) {
                    continue;
                }

                $variant = $item->variant;

                if (!$variant) {
                    continue;
                }

                $variant->increment('stock_qty', $item->qty);

                // Balance the 'sale' row written when the order was placed, so
                // the inventory ledger still reconciles with stock_qty.
                InventoryLog::create([
                    'product_variant_id' => $variant->id,
                    'change_qty'         => $item->qty,
                    'stock_after'        => $variant->fresh()->stock_qty,
                    'reason'             => 'return',
                    'reference'          => $order->order_number . ' (payment failed)',
                    'created_at'         => now(),
                ]);
            }

            // Give the coupon use back — the order it was spent on is going away.
            if ($order->coupon_code) {
                Coupon::where('code', $order->coupon_code)->where('usage_count', '>', 0)->decrement('usage_count');
            }

            $order->payments()->delete();
            $order->items()->delete();
            $order->delete();
        });
    }
}
