<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create() { return view('admin.coupons.create'); }

    public function store(Request $request)
    {
        Coupon::create($request->validate([
            'code'        => 'required|string|max:50|unique:coupons',
            'type'        => 'required|in:percentage,fixed',
            'value'       => 'required|numeric|min:0',
            'min_spend'   => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date',
            'is_active'   => 'boolean',
        ]));
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created!');
    }

    public function edit(Coupon $coupon) { return view('admin.coupons.edit', compact('coupon')); }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($request->validate([
            'value'      => 'required|numeric|min:0',
            'min_spend'  => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'is_active'  => 'boolean',
        ]));
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->update(['is_active' => false]);
        return back()->with('success', 'Coupon deactivated.');
    }
}
