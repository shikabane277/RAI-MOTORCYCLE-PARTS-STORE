<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        return view('account.dashboard', compact('user', 'recentOrders'));
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->with('items')->latest()->paginate(10);
        return view('account.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.variant.product', 'shipments', 'payments');
        return view('account.order-detail', compact('order'));
    }

    public function addresses()
    {
        $addresses = auth()->user()->addresses()->get();
        return view('account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'line1'          => 'required|string|max:200',
            'barangay'       => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'zip_code'       => 'nullable|string|max:10',
            'is_default'     => 'boolean',
        ]);

        if ($request->is_default) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        auth()->user()->addresses()->create($validated);

        return back()->with('success', 'Address added!');
    }

    public function updateAddress(Request $request, Address $address)
    {
        $this->authorize('update', $address);
        $address->update($request->validate([
            'label' => 'string|max:50', 'recipient_name' => 'string|max:100',
            'phone' => 'string|max:20', 'line1' => 'string|max:200',
            'barangay' => 'string|max:100', 'city' => 'string|max:100',
            'province' => 'string|max:100', 'zip_code' => 'nullable|string|max:10',
        ]));
        return back()->with('success', 'Address updated.');
    }

    public function deleteAddress(Address $address)
    {
        $this->authorize('delete', $address);
        $address->delete();
        return back()->with('success', 'Address deleted.');
    }

    public function wishlist()
    {
        $items = auth()->user()->wishlists()->with('variant.product')->get();
        return view('account.wishlist', compact('items'));
    }

    public function profile()
    {
        return view('account.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);
        auth()->user()->update($validated);
        return back()->with('success', 'Profile updated.');
    }
}
