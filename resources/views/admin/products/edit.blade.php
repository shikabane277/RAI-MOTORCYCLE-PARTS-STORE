@extends('layouts.admin')
@section('title', 'Edit Product — ' . $product->name)
@section('page-title', 'Edit Product')

@section('content')
<div class="row g-4">
    {{-- Main form --}}
    <div class="col-lg-8">
        <div class="dark-card p-4 mb-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-heading);margin-bottom:1.25rem;">Product Details</h2>
            <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                {{-- Product Photo / Image Upload Section --}}
                <div class="p-3 mb-3" style="background:var(--mb-surface);border:1px solid var(--mb-border);border-radius:var(--mb-radius-sm);">
                    <h6 style="font-family:'Rajdhani',sans-serif;color:var(--mb-gold);font-weight:700;" class="mb-2">
                        <i class="bi bi-images me-1"></i> Product Photos (Multiple Images Support)
                    </h6>
                    @if($product->primary_image_url)
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <img src="{{ $product->primary_image_url }}" alt="Preview" style="height:60px;width:60px;object-fit:cover;border-radius:6px;border:1px solid var(--mb-border);">
                        <span class="small text-muted">Current photo: {{ $product->primary_image_url }}</span>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Option A: Upload Multiple Image Files</label>
                            <input type="file" name="image_files[]" class="form-control" multiple accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Option B: Primary Image URL</label>
                            <input type="text" name="image_url" class="form-control" value="{{ old('image_url', $product->primary_image_url) }}" placeholder="https://example.com/photo.jpg or /images/logo.png">
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">— No Brand —</option>
                            @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ $product->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">— No Category —</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $product->category_id == $c->id ? 'selected' : '' }}>
                                {{ $c->parent_id ? '↳ ' : '' }}{{ $c->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Short Description</label>
                    <input type="text" name="short_description" class="form-control" value="{{ old('short_description', $product->short_description) }}" maxlength="500">
                </div>
                <div class="mb-3">
                    <label class="form-label">Full Description</label>
                    <textarea name="description" class="form-control" rows="6">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Base Price (₱) *</label>
                        <input type="number" name="base_price" class="form-control" step="0.01" value="{{ old('base_price', $product->base_price) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['active','draft','archived'] as $s)
                            <option value="{{ $s }}" {{ $product->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Weight (grams)</label>
                        <input type="number" name="weight_grams" class="form-control" value="{{ old('weight_grams', $product->weight_grams) }}">
                    </div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ $product->is_featured ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured" style="color:var(--mb-text);">Featured</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1" id="is_new_arrival" {{ $product->is_new_arrival ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_new_arrival" style="color:var(--mb-text);">New Arrival</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-gold">Save Changes</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-dark-surface ms-2">Cancel</a>
            </form>
        </div>

        {{-- Variants Table --}}
        <div class="dark-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-heading);margin:0;">Variants ({{ $product->variants->count() }})</h2>
                <button class="btn btn-outline-gold btn-sm" data-bs-toggle="collapse" data-bs-target="#add-variant-form">
                    <i class="bi bi-plus-lg me-1"></i>Add Variant
                </button>
            </div>

            {{-- Add variant form --}}
            <div class="collapse mb-3" id="add-variant-form">
                <div style="background:var(--mb-surface);border-radius:var(--mb-radius-sm);padding:1rem;border:1px solid var(--mb-border);">
                    <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2 mb-2">
                            <div class="col-md-5">
                                <label class="form-label font-bold">Custom Variant Label / Combination *</label>
                                <input type="text" name="variant_name" class="form-control" placeholder="e.g. 5x25/CNC/4pcs - Gold, 8GB+256GB - Black" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-bold">Price (₱) *</label>
                                <input type="number" name="price" step="0.01" class="form-control" value="{{ $product->base_price }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label font-bold">Stock Qty *</label>
                                <input type="number" name="stock_qty" class="form-control" value="50" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label font-bold">Variant Photo</label>
                                <input type="file" name="image_file" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Connected Variant</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark-custom mb-0">
                    <thead><tr><th>Photo</th><th>Variant Label</th><th>SKU</th><th>Price</th><th>Sale</th><th>Stock</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($product->variants as $v)
                        <tr>
                            <td>
                                @if($v->image_url)
                                    <img src="{{ $v->image_url }}" alt="{{ $v->label }}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid var(--mb-border);">
                                @else
                                    <div style="width:36px;height:36px;border-radius:6px;background:var(--mb-surface);display:flex;align-items:center;justify-content:center;color:var(--mb-muted);"><i class="bi bi-image"></i></div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-gold" style="font-size:.95rem;">{{ $v->label }}</span>
                            </td>
                            <td style="font-size:.78rem;color:var(--mb-muted);">{{ $v->variant_sku }}</td>
                            <td style="font-family:'Rajdhani',sans-serif;font-weight:700;">&#x20B1;{{ number_format($v->price, 2) }}</td>
                            <td style="color:var(--mb-green);font-size:.85rem;">{{ $v->sale_price ? '₱'.number_format($v->sale_price,2) : '—' }}</td>
                            <td>
                                <span style="color:{{ $v->stock_qty <= 0 ? 'var(--mb-red)' : ($v->stock_qty <= 10 ? 'var(--mb-gold)' : 'var(--mb-green)') }};font-weight:700;font-family:'Rajdhani',sans-serif;">{{ $v->stock_qty }}</span>
                            </td>
                            <td>
                                @if($v->is_active)
                                    <span style="color:var(--mb-green);"><i class="bi bi-check-circle-fill"></i> Active</span>
                                @else
                                    <span style="color:var(--mb-red);"><i class="bi bi-x-circle-fill"></i> Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar info --}}
    <div class="col-lg-4">
        <div class="dark-card p-4 mb-3">
            <h3 style="font-family:'Rajdhani',sans-serif;font-size:.95rem;color:var(--mb-muted);margin-bottom:.75rem;">Stats</h3>
            <div class="d-flex justify-content-between mb-2" style="font-size:.88rem;"><span style="color:var(--mb-muted);">Total Stock</span><span style="font-weight:700;">{{ $product->total_stock }} units</span></div>
            <div class="d-flex justify-content-between mb-2" style="font-size:.88rem;"><span style="color:var(--mb-muted);">Views</span><span>{{ number_format($product->views) }}</span></div>
            <div class="d-flex justify-content-between mb-2" style="font-size:.88rem;"><span style="color:var(--mb-muted);">Avg. Rating</span><span>{{ $product->average_rating ? $product->average_rating . ' / 5' : 'No reviews' }}</span></div>
        </div>
        <div class="dark-card p-4">
            <h3 style="font-family:'Rajdhani',sans-serif;font-size:.95rem;color:var(--mb-muted);margin-bottom:.75rem;">Quick Actions</h3>
            <div class="d-grid gap-2">
                <a href="{{ route('product.show', $product->slug) }}" class="btn btn-dark-surface btn-sm" target="_blank"><i class="bi bi-eye me-1"></i>View Storefront</a>
                <a href="{{ route('admin.inventory.index') }}?search={{ $product->slug }}" class="btn btn-dark-surface btn-sm"><i class="bi bi-clipboard-data me-1"></i>Inventory Logs</a>
            </div>
        </div>
    </div>
</div>
@endsection
