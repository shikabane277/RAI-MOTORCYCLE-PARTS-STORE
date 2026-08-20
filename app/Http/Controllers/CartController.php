<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getOrCreateCart(): Cart
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }
        $sessionId = session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.variant.product');

        $shippingFee = $cart->subtotal >= 1500 ? 0 : 89;

        $coupon   = null;
        $discount = 0;
        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if ($coupon) {
                $discount = $coupon->calculateDiscount($cart->subtotal, $shippingFee);
            }
        }

        return view('cart', compact('cart', 'coupon', 'discount', 'shippingFee'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'qty'        => 'required|integer|min:1|max:99',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        if (!$variant->is_in_stock) {
            return back()->withErrors(['variant_id' => 'This item is out of stock.']);
        }

        $cart = $this->getOrCreateCart();

        $item = $cart->items()->where('product_variant_id', $variant->id)->first();
        if ($item) {
            $item->increment('qty', $request->qty);
        } else {
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'qty'                => $request->qty,
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['cart_count' => $cart->item_count, 'message' => 'Added to cart!']);
        }

        return back()->with('success', '✅ Added to cart!');
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate(['qty' => 'required|integer|min:1|max:99']);
        $item->update(['qty' => $request->qty]);
        return back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $item)
    {
        $item->delete();
        return back()->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $cart   = $this->getOrCreateCart();
        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon || !$coupon->isValid($cart->subtotal)) {
            return back()->withErrors(['code' => 'Invalid or expired coupon code.']);
        }

        $cart->update(['coupon_code' => $coupon->code]);
        return back()->with('success', 'Coupon ' . $coupon->code . ' applied!');
    }

    public function removeCoupon()
    {
        $cart = $this->getOrCreateCart();
        $cart->update(['coupon_code' => null]);
        return back()->with('success', 'Coupon removed.');
    }

    public function count()
    {
        $cart  = $this->getOrCreateCart();
        return response()->json(['count' => $cart->item_count]);
    }
}
