<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'title'      => 'nullable|string|max:150',
            'comment'    => 'nullable|string|max:2000',
            'bike_model' => 'nullable|string|max:100',
        ]);

        Review::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'status'  => 'pending',
        ]));

        return back()->with('success', 'Review submitted! It will appear once approved.');
    }
}
