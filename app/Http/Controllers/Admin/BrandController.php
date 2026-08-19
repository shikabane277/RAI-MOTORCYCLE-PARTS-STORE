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
        $brands = Brand::withCount('products')->orderBy('name')->get();
        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:brands,name',
            'logo_url'    => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;

        Brand::create($validated);

        return redirect()->back()->with('success', 'Brand "' . $validated['name'] . '" created successfully!');
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:brands,name,' . $brand->id,
            'logo_url'    => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : $brand->is_active;

        $brand->update($validated);

        return redirect()->back()->with('success', 'Brand updated successfully!');
    }

    public function toggle(Brand $brand)
    {
        $brand->update(['is_active' => !$brand->is_active]);

        return redirect()->back()->with('success', 'Brand status updated!');
    }

    public function destroy(Brand $brand)
    {
        // Reassign products to null if deleting brand
        $brand->products()->update(['brand_id' => null]);
        $brand->delete();

        return redirect()->back()->with('success', 'Brand deleted successfully!');
    }
}
