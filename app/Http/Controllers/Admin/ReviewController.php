<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $pending  = Review::pending()->with(['user', 'product'])->latest()->get();
        $approved = Review::approved()->with(['user', 'product'])->latest()->take(20)->get();
        return view('admin.reviews.index', compact('pending', 'approved'));
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        return back()->with('success', 'Review approved.');
    }

    public function hide(Review $review)
    {
        $review->update(['status' => 'hidden']);
        return back()->with('success', 'Review hidden.');
    }

    public function reply(\Illuminate\Http\Request $request, Review $review)
    {
        $review->update(['admin_reply' => $request->validate(['reply' => 'required|string|max:1000'])['reply']]);
        return back()->with('success', 'Reply saved.');
    }
}
