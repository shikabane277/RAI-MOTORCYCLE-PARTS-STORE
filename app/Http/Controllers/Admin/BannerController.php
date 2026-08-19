<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create() { return view('admin.banners.create'); }

    public function store(Request $request)
    {
        Banner::create($request->validate([
            'title'       => 'required|string|max:200',
            'subtitle'    => 'nullable|string|max:300',
            'image_url'   => 'required|string',
            'link_url'    => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'boolean',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date',
        ]));
        return redirect()->route('admin.banners.index')->with('success', 'Banner created!');
    }

    public function edit(Banner $banner) { return view('admin.banners.edit', compact('banner')); }

    public function update(Request $request, Banner $banner)
    {
        $banner->update($request->validate([
            'title'       => 'required|string|max:200',
            'subtitle'    => 'nullable|string|max:300',
            'image_url'   => 'required|string',
            'link_url'    => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'boolean',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date',
        ]));
        return redirect()->route('admin.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return back()->with('success', 'Banner deleted.');
    }
}
