<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'variants'])->withTrashed();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $brands     = Brand::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('brands', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:200',
            'brand_id'          => 'nullable|exists:brands,id',
            'category_id'       => 'nullable|exists:categories,id',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'base_price'        => 'required|numeric|min:0',
            'weight_grams'      => 'nullable|integer',
            'status'            => 'required|in:active,draft,archived',
            'is_featured'       => 'boolean',
            'is_new_arrival'    => 'boolean',
            'initial_stock'     => 'nullable|integer|min:0',
            'sku'               => 'nullable|string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);

        $initialStock = (int) ($validated['initial_stock'] ?? 50);
        $skuInput = $validated['sku'] ?? null;
        unset($validated['initial_stock'], $validated['sku']);

        $imageUrl = $request->input('image_url');
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imageUrl = '/uploads/products/' . $filename;
        }

        $product = Product::create($validated);

        $sku = $skuInput ? strtoupper(trim($skuInput)) : 'RAI-' . strtoupper(Str::slug(substr($product->name, 0, 10))) . '-' . rand(100, 999);

        ProductVariant::create([
            'product_id'          => $product->id,
            'variant_sku'         => $sku,
            'color'               => 'Standard',
            'material'            => 'Standard',
            'pack_qty'            => 1,
            'price'               => $product->base_price,
            'stock_qty'           => $initialStock,
            'low_stock_threshold' => 5,
            'image_url'           => $imageUrl,
            'is_active'           => true,
        ]);

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' created successfully!");
    }

    public function edit(Product $product)
    {
        $product->load('variants', 'motorcycleModels');
        $brands      = Brand::where('is_active', true)->orderBy('name')->get();
        $categories  = Category::where('is_active', true)->get();
        $materials   = \App\Models\ProductAttribute::ofType('material')->active()->get();
        $colors      = \App\Models\ProductAttribute::ofType('color')->active()->get();
        $threadSizes = \App\Models\ProductAttribute::ofType('thread_size')->active()->get();

        return view('admin.products.edit', compact('product', 'brands', 'categories', 'materials', 'colors', 'threadSizes'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:200',
            'brand_id'          => 'nullable|exists:brands,id',
            'category_id'       => 'nullable|exists:categories,id',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'base_price'        => 'required|numeric|min:0',
            'status'            => 'required|in:active,draft,archived',
            'is_featured'       => 'boolean',
            'is_new_arrival'    => 'boolean',
        ]);

        $imageUrl = $request->input('image_url');
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imageUrl = '/uploads/products/' . $filename;
        }

        $product->update($validated);

        if ($imageUrl) {
            $variant = $product->variants()->first();
            if ($variant) {
                $variant->update(['image_url' => $imageUrl]);
            }
        }

        return back()->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->update(['status' => 'archived']);
        $product->delete(); // soft delete
        return redirect()->route('admin.products.index')->with('success', 'Product archived.');
    }

    public function storeVariant(Request $request, Product $product)
    {
        $validated = $request->validate([
            'thread_size'  => 'nullable|string|max:20',
            'thread_pitch' => 'nullable|string|max:10',
            'length_mm'    => 'nullable|integer',
            'head_type'    => 'nullable|string|max:50',
            'material'     => 'required|string|max:100',
            'color'        => 'required|string|max:50',
            'finish'       => 'nullable|string|max:50',
            'pack_qty'     => 'required|integer|min:1',
            'price'        => 'required|numeric|min:0',
            'sale_price'   => 'nullable|numeric|min:0',
            'stock_qty'    => 'required|integer|min:0',
        ]);

        $sku = strtoupper(
            substr(preg_replace('/[^A-Za-z0-9]/', '', $product->slug), 0, 8) .
            '-' . strtoupper(substr($validated['color'], 0, 3)) .
            '-' . Str::random(4)
        );

        $product->variants()->create(array_merge($validated, [
            'variant_sku' => $sku,
            'low_stock_threshold' => 10,
            'is_active' => true,
        ]));

        return back()->with('success', 'Variant added!');
    }

    public function updateVariant(Request $request, Product $product, ProductVariant $variant)
    {
        $variant->update($request->validate([
            'price'      => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_qty'  => 'required|integer|min:0',
            'is_active'  => 'boolean',
        ]));
        return back()->with('success', 'Variant updated!');
    }

    public function destroyVariant(ProductVariant $variant)
    {
        $variant->update(['is_active' => false]);
        return back()->with('success', 'Variant deactivated.');
    }
}
