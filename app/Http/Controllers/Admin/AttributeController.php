<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\Brand;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $materials = ProductAttribute::ofType('material')->get();
        $colors = ProductAttribute::ofType('color')->get();
        $threadSizes = ProductAttribute::ofType('thread_size')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.attributes.index', compact('materials', 'colors', 'threadSizes', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:material,color,thread_size',
            'name'       => 'required|string|max:100',
            'value'      => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        ProductAttribute::create([
            'type'       => $request->type,
            'name'       => trim($request->name),
            'value'      => $request->value ? trim($request->value) : null,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => true,
        ]);

        return redirect()->back()->with('success', ucfirst(str_replace('_', ' ', $request->type)) . ' added successfully!');
    }

    public function update(Request $request, ProductAttribute $attribute)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'value'      => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $attribute->update([
            'name'       => trim($request->name),
            'value'      => $request->value ? trim($request->value) : null,
            'sort_order' => $request->sort_order ?? $attribute->sort_order,
            'is_active'  => $request->has('is_active') ? $request->boolean('is_active') : $attribute->is_active,
        ]);

        return redirect()->back()->with('success', 'Attribute updated successfully!');
    }

    public function toggle(ProductAttribute $attribute)
    {
        $attribute->update(['is_active' => !$attribute->is_active]);

        return redirect()->back()->with('success', 'Attribute status updated!');
    }

    public function destroy(ProductAttribute $attribute)
    {
        $attribute->delete();

        return redirect()->back()->with('success', 'Attribute deleted successfully!');
    }
}
