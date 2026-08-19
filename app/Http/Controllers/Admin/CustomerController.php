<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class CustomerController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = User::where('role', 'customer')->withCount('orders');
        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        $customers = $query->latest()->paginate(25)->withQueryString();
        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        $user->load(['orders.items', 'addresses', 'reviews']);
        return view('admin.customers.show', compact('user'));
    }
}
