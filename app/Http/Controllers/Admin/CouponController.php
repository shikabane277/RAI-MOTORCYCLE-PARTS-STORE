<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::latest();

        if ($request->search) {
            $s = strtoupper(trim($request->search));
            $query->where('code', 'like', "%{$s}%");
        }

        $coupons = $query->paginate(15)->withQueryString();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'code'      => strtoupper(trim($request->code)),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:coupons,code',
            'type'        => 'required|in:percentage,fixed,free_shipping',
            'value'       => 'nullable|numeric|min:0',
            'min_spend'   => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date|after_or_equal:starts_at',
            'is_active'   => 'boolean',
        ]);

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully!');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->merge([
            'code'      => strtoupper(trim($request->code)),
            'is_active' => $request->boolean('is_active'),
        ]);

        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type'        => 'required|in:percentage,fixed,free_shipping',
            'value'       => 'nullable|numeric|min:0',
            'min_spend'   => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date|after_or_equal:starts_at',
            'is_active'   => 'boolean',
        ]);

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted permanently.');
    }

    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        $status = $coupon->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Coupon {$coupon->code} has been {$status}.");
    }
}
