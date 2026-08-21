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
            'base_price'        => 'nullable|numeric|min:0',
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

        // 1. Process Dedicated Main Cover Image Upload
        if ($request->hasFile('cover_image_file')) {
            $coverFile = $request->file('cover_image_file');
            $coverFilename = 'cover_' . time() . '_' . rand(100, 999) . '.' . $coverFile->getClientOriginalExtension();
            $coverFile->move(public_path('uploads/products'), $coverFilename);
            $validated['image_url'] = '/uploads/products/' . $coverFilename;
        }

        // 2. Process Additional Gallery Photo Uploads
        $uploadedImages = [];
        if (!empty($validated['image_url'])) {
            $uploadedImages[] = $validated['image_url'];
        }

        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $idx => $file) {
                $filename = 'prod_' . time() . '_' . $idx . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products'), $filename);
                $uploadedImages[] = '/uploads/products/' . $filename;
            }
        }

        if (empty($validated['image_url']) && !empty($uploadedImages)) {
            $validated['image_url'] = $uploadedImages[0];
        }

        $validated['images'] = !empty($uploadedImages) ? array_values(array_unique($uploadedImages)) : null;

        if (empty($validated['base_price']) && $request->has('variants') && is_array($request->variants)) {
            $prices = array_column($request->variants, 'price');
            $validPrices = array_filter($prices, fn($p) => is_numeric($p) && $p > 0);
            $validated['base_price'] = !empty($validPrices) ? min($validPrices) : 0;
        }

        $product = Product::create($validated);

        // Check if customizable variants array was submitted
        if ($request->has('variants') && is_array($request->variants) && count($request->variants) > 0) {
            foreach ($request->variants as $idx => $vData) {
                $t1 = trim($vData['tier1_option'] ?? '');
                $t2 = trim($vData['tier2_option'] ?? '');
                $vName = $vData['variant_name'] ?? '';

                if (empty($vName)) {
                    if (!empty($t1) && !empty($t2)) {
                        $vName = "{$t1} - {$t2}";
                    } elseif (!empty($t1)) {
                        $vName = $t1;
                    }
                }

                if (empty($vName) && empty($vData['price'])) continue;
                if (empty($vName)) $vName = 'Standard';

                $vImg = $vData['image_url'] ?? null;
                if ($request->hasFile("variants.{$idx}.image_file")) {
                    $vFile = $request->file("variants.{$idx}.image_file");
                    $vFilename = 'var_' . time() . '_' . $idx . '.' . $vFile->getClientOriginalExtension();
                    $vFile->move(public_path('uploads/products'), $vFilename);
                    $vImg = '/uploads/products/' . $vFilename;
                }

                if (empty($vImg) && !empty($uploadedImages)) {
                    $vImg = $uploadedImages[$idx] ?? $uploadedImages[0];
                }

                $vSku = !empty($vData['sku'])
                    ? strtoupper(trim($vData['sku']))
                    : 'RAI-' . strtoupper(Str::slug(substr($product->name, 0, 8))) . '-' . strtoupper(Str::slug(substr($vName, 0, 6))) . '-' . rand(10, 99);

                ProductVariant::create([
                    'product_id'          => $product->id,
                    'variant_name'        => $vName,
                    'variant_sku'         => $vSku,
                    'price'               => !empty($vData['price']) ? $vData['price'] : $product->base_price,
                    'sale_price'          => !empty($vData['sale_price']) ? $vData['sale_price'] : null,
                    'stock_qty'           => (int) ($vData['stock_qty'] ?? 0),
                    'low_stock_threshold' => 5,
                    'image_url'           => $vImg,
                    'images'              => !empty($uploadedImages) ? $uploadedImages : null,
                    'is_active'           => true,
                ]);
            }
        } else {
            // Default single variant
            $sku = $skuInput ? strtoupper(trim($skuInput)) : 'RAI-' . strtoupper(Str::slug(substr($product->name, 0, 10))) . '-' . rand(100, 999);
            ProductVariant::create([
                'product_id'          => $product->id,
                'variant_name'        => 'Standard',
                'variant_sku'         => $sku,
                'color'               => 'Standard',
                'material'            => 'Standard',
                'pack_qty'            => 1,
                'price'               => $product->base_price,
                'stock_qty'           => $initialStock,
                'low_stock_threshold' => 5,
                'image_url'           => $imageUrl,
                'images'              => !empty($uploadedImages) ? $uploadedImages : null,
                'is_active'           => true,
            ]);
        }

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
            'base_price'        => 'nullable|numeric|min:0',
            'weight_grams'      => 'nullable|integer',
            'status'            => 'required|in:active,draft,archived',
            'is_featured'       => 'boolean',
            'is_new_arrival'    => 'boolean',
        ]);

        // Preserve existing Cover Image URL unless new cover photo uploaded
        if ($request->hasFile('cover_image_file')) {
            $coverFile = $request->file('cover_image_file');
            $coverFilename = 'cover_' . time() . '_' . rand(100, 999) . '.' . $coverFile->getClientOriginalExtension();
            $coverFile->move(public_path('uploads/products'), $coverFilename);
            $validated['image_url'] = '/uploads/products/' . $coverFilename;
        } else {
            $validated['image_url'] = $product->primary_image_url ?: $product->image_url;
        }

        // Preserve existing Additional Gallery Photos
        $existingImages = is_array($product->images) ? $product->images : [];
        $uploadedImages = $existingImages;
        if (!empty($validated['image_url']) && !in_array($validated['image_url'], $uploadedImages)) {
            array_unshift($uploadedImages, $validated['image_url']);
        }

        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $idx => $file) {
                $filename = 'prod_' . time() . '_' . $idx . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products'), $filename);
                $uploadedImages[] = '/uploads/products/' . $filename;
            }
        }

        $validated['images'] = !empty($uploadedImages) ? array_values(array_unique($uploadedImages)) : null;

        // Process Option Config
        if ($request->filled('tier1_name')) {
            $optionConfig = [
                ['name' => $request->tier1_name, 'display_style' => 'swatch'],
            ];
            if ($request->filled('tier2_name') && $request->has('tier2_name')) {
                $optionConfig[] = ['name' => $request->tier2_name, 'display_style' => 'swatch'];
            }
            $validated['option_config'] = $optionConfig;
        }

        if (empty($validated['base_price']) && $request->has('variants') && is_array($request->variants)) {
            $prices = array_column($request->variants, 'price');
            $validPrices = array_filter($prices, fn($p) => is_numeric($p) && $p > 0);
            $validated['base_price'] = !empty($validPrices) ? min($validPrices) : ($product->base_price ?: 0);
        }

        $product->update($validated);

        // Process Variants Update safely without wiping existing images or details
        if ($request->has('variants') && is_array($request->variants) && count($request->variants) > 0) {
            $existingVariants = $product->variants->keyBy('variant_name');
            $activeVariantNames = [];

            foreach ($request->variants as $idx => $vData) {
                $t1 = trim($vData['tier1_option'] ?? '');
                $t2 = trim($vData['tier2_option'] ?? '');
                $vName = $vData['variant_name'] ?? '';

                if (empty($vName)) {
                    if (!empty($t1) && !empty($t2)) {
                        $vName = "{$t1} - {$t2}";
                    } elseif (!empty($t1)) {
                        $vName = $t1;
                    }
                }

                if (empty($vName) && empty($vData['price'])) continue;
                if (empty($vName)) $vName = 'Standard';

                $activeVariantNames[] = $vName;
                $existingVar = $existingVariants->get($vName);

                // Preserve existing variant image if no new file uploaded
                $vImg = !empty($vData['existing_image']) ? $vData['existing_image'] : ($existingVar?->image_url);

                if ($request->hasFile("variants.{$idx}.image_file")) {
                    $vFile = $request->file("variants.{$idx}.image_file");
                    $vFilename = 'var_' . time() . '_' . $idx . '.' . $vFile->getClientOriginalExtension();
                    $vFile->move(public_path('uploads/products'), $vFilename);
                    $vImg = '/uploads/products/' . $vFilename;
                }

                if (empty($vImg)) {
                    $vImg = $product->primary_image_url ?: ($uploadedImages[0] ?? null);
                }

                $vSku = $existingVar?->variant_sku ?: ('RAI-' . strtoupper(Str::slug(substr($product->name, 0, 8))) . '-' . strtoupper(Str::slug(substr($vName, 0, 6))) . '-' . rand(10, 99));

                ProductVariant::updateOrCreate(
                    [
                        'product_id'   => $product->id,
                        'variant_name' => $vName,
                    ],
                    [
                        'variant_sku'         => $vSku,
                        'price'               => !empty($vData['price']) ? $vData['price'] : ($product->base_price ?: 0),
                        'sale_price'          => !empty($vData['sale_price']) ? $vData['sale_price'] : null,
                        'stock_qty'           => (int) ($vData['stock_qty'] ?? 0),
                        'low_stock_threshold' => 5,
                        'image_url'           => $vImg,
                        'images'              => !empty($uploadedImages) ? $uploadedImages : null,
                        'is_active'           => true,
                    ]
                );
            }

            // Safely delete only variants that were explicitly removed by admin
            if (!empty($activeVariantNames)) {
                $product->variants()->whereNotIn('variant_name', $activeVariantNames)->delete();
            }
        }

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' updated successfully!");
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
            'variant_name' => 'nullable|string|max:100',
            'thread_size'  => 'nullable|string|max:20',
            'thread_pitch' => 'nullable|string|max:10',
            'length_mm'    => 'nullable|integer',
            'head_type'    => 'nullable|string|max:50',
            'material'     => 'nullable|string|max:100',
            'color'        => 'nullable|string|max:50',
            'finish'       => 'nullable|string|max:50',
            'pack_qty'     => 'nullable|integer|min:1',
            'price'        => 'required|numeric|min:0',
            'sale_price'   => 'nullable|numeric|min:0',
            'stock_qty'    => 'required|integer|min:0',
        ]);

        $vName = $validated['variant_name'] ?? 'Standard';
        $vImg = null;
        if ($request->hasFile('image_file')) {
            $vFile = $request->file('image_file');
            $vFilename = 'var_' . time() . '_' . rand(100, 999) . '.' . $vFile->getClientOriginalExtension();
            $vFile->move(public_path('uploads/products'), $vFilename);
            $vImg = '/uploads/products/' . $vFilename;
        }

        $product->variants()->create(array_merge($validated, [
            'variant_name' => $vName,
            'variant_sku'  => $sku,
            'image_url'    => $vImg,
            'low_stock_threshold' => 5,
            'is_active'    => true,
        ]));

        return back()->with('success', 'Variant added successfully!');
    }

    public function updateVariant(Request $request, Product $product, ProductVariant $variant)
    {
        $validated = $request->validate([
            'variant_name' => 'nullable|string|max:100',
            'price'        => 'required|numeric|min:0',
            'sale_price'   => 'nullable|numeric|min:0',
            'stock_qty'    => 'required|integer|min:0',
            'is_active'    => 'boolean',
        ]);
        $variant->update($validated);
        return back()->with('success', 'Variant updated!');
    }

    public function destroyVariant(ProductVariant $variant)
    {
        $variant->update(['is_active' => false]);
        return back()->with('success', 'Variant deactivated.');
    }
}
