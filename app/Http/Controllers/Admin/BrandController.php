<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->get();
        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $v = $request->validate(['name' => 'required|string|max:100', 'description' => 'nullable|string']);
        Brand::create(array_merge($v, ['slug' => Str::slug($v['name']), 'is_active' => true]));
        return back()->with('success', 'Brand created!');
    }

    public function update(Request $request, Brand $brand)
    {
        $brand->update($request->validate(['name' => 'required|string|max:100', 'description' => 'nullable|string', 'is_active' => 'boolean']));
        return back()->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand)
    {
        $brand->update(['is_active' => false]);
        return back()->with('success', 'Brand deactivated.');
    }
}
