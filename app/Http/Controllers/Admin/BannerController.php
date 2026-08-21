<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->orderByDesc('id')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.banners.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'subtitle'    => 'nullable|string|max:300',
            'image_url'   => 'nullable|string',
            'image_file'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'link_url'    => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'boolean',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date',
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image_url'] = CloudinaryService::upload($request->file('image_file'), 'banners');
        }

        if (empty($validated['image_url'])) {
            return back()->withErrors(['image_url' => 'Please provide an Image URL, upload an Image file, or select a product image.'])->withInput();
        }

        unset($validated['image_file']);
        $validated['is_active'] = $request->boolean('is_active', true);
        Banner::create($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Hero Banner created successfully!');
    }

    public function edit(Banner $banner)
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.banners.edit', compact('banner', 'products'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'subtitle'    => 'nullable|string|max:300',
            'image_url'   => 'nullable|string',
            'image_file'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'link_url'    => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'boolean',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date',
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image_url'] = CloudinaryService::upload($request->file('image_file'), 'banners');
        }

        if (empty($validated['image_url'])) {
            $validated['image_url'] = $banner->image_url;
        }

        unset($validated['image_file']);
        $validated['is_active'] = $request->boolean('is_active');
        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted.');
    }
}
