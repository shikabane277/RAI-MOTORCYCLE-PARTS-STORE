<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(ProductVariant $variant)
    {
        if (!auth()->check()) {
            if (request()->ajax()) {
                return response()->json(['redirect' => route('login'), 'message' => 'Please login to save items to your wishlist.']);
            }
            return redirect()->route('login')->with('info', 'Please login to save items to your wishlist.');
        }

        $user = auth()->user();
        $existing = Wishlist::where('user_id', $user->id)
                            ->where('product_variant_id', $variant->id)
                            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from wishlist.';
            $added   = false;
        } else {
            Wishlist::create(['user_id' => $user->id, 'product_variant_id' => $variant->id]);
            $message = 'Added to wishlist!';
            $added   = true;
        }

        if (request()->ajax()) {
            return response()->json(['added' => $added, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
