<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent', 'children')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->where('is_active', true)->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'parent_id'  => 'nullable|exists:categories,id',
            'icon'       => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'boolean',
        ]);
        $validated['slug'] = Str::slug($validated['name']);
        Category::create($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Category created!');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $category->update($request->validate([
            'name'       => 'required|string|max:100',
            'parent_id'  => 'nullable|exists:categories,id',
            'icon'       => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'boolean',
        ]));
        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->withErrors(['category' => 'Cannot delete category with products. Reassign products first.']);
        }
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
