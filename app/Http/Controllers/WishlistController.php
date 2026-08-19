<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(ProductVariant $variant)
    {
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
