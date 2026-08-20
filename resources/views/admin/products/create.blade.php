@extends('layouts.admin')
@section('title', 'Create Product')
@section('page-title', 'Add New Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="dark-card p-4">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                <h5 style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);" class="mb-3">
                    Product Basic Information
                </h5>

                <div class="mb-3">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Titanium Caliper Bolts Set" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">— No Brand —</option>
                            @foreach($brands as $b)<option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">— No Category —</option>
                            @foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->parent_id ? '↳ ' : '' }}{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Short Description</label>
                    <input type="text" name="short_description" class="form-control" value="{{ old('short_description') }}" placeholder="e.g. High-tensile Grade 5 Titanium hardware kit." maxlength="500">
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Base Price (₱) *</label>
                        <input type="number" name="base_price" step="0.01" class="form-control" value="{{ old('base_price', 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Weight (grams)</label>
                        <input type="number" name="weight_grams" class="form-control" value="{{ old('weight_grams', 100) }}">
                    </div>
                </div>

                {{-- ── Multiple Product Photos Upload ──────────────── --}}
                <div class="p-3 mb-4" style="background:var(--mb-surface);border:1px solid var(--mb-border);border-radius:var(--mb-radius);">
                    <h6 style="font-family:'Rajdhani',sans-serif;color:var(--mb-gold);font-weight:700;" class="mb-2">
                        <i class="bi bi-images me-1"></i> Product Photos (Select Multiple Images)
                    </h6>
                    <p style="font-size:.8rem;color:var(--mb-muted);" class="mb-3">
                        You can select multiple photo files simultaneously from your device, or provide image URLs.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small font-bold">Upload Multiple Image Files</label>
                            <input type="file" name="image_files[]" class="form-control" multiple accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small font-bold">Primary Image URL (Optional)</label>
                            <input type="text" name="image_url" class="form-control" value="{{ old('image_url') }}" placeholder="https://example.com/photo.jpg">
                        </div>
                    </div>
                </div>

                {{-- ── Customizable Product Variants Matrix ───────── --}}
                <div class="p-3 mb-4" style="background:var(--mb-surface);border:1px solid var(--mb-gold-border);border-radius:var(--mb-radius);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 style="font-family:'Rajdhani',sans-serif;color:var(--mb-gold);font-weight:700;" class="mb-0">
                                <i class="bi bi-diagram-3 me-1"></i> Customizable Product Variants (Size, Color, Qty, Design)
                            </h6>
                            <span style="font-size:.78rem;color:var(--mb-muted);">
                                Add multiple custom variants (e.g. 5x25/CNC/4pcs, 8GB+256GB, Gold/M6x20) with individual prices and stock quantities.
                            </span>
                        </div>
                        <button type="button" class="btn btn-outline-gold btn-sm" id="btn-add-variant">
                            <i class="bi bi-plus-lg me-1"></i> Add Variant Row
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0" id="variant-table">
                            <thead>
                                <tr>
                                    <th style="min-width:180px;">Custom Variant Name / Label *</th>
                                    <th style="width:120px;">Price (₱)</th>
                                    <th style="width:110px;">Stock Qty *</th>
                                    <th style="width:140px;">SKU (Optional)</th>
                                    <th style="width:50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="variant-rows">
                                <tr class="variant-row">
                                    <td>
                                        <input type="text" name="variants[0][variant_name]" class="form-control form-control-sm" placeholder="e.g. 5x25/CNC/4pcs or 8GB+256GB" value="5x25/CNC/4pcs" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="variants[0][price]" class="form-control form-control-sm" placeholder="Price" value="">
                                    </td>
                                    <td>
                                        <input type="number" name="variants[0][stock_qty]" class="form-control form-control-sm" placeholder="Stock" value="50" min="0" required>
                                    </td>
                                    <td>
                                        <input type="text" name="variants[0][sku]" class="form-control form-control-sm" placeholder="SKU">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-link text-danger btn-remove-row" style="text-decoration:none;"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured" style="color:var(--mb-text);">Featured Product</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1" id="is_new_arrival" {{ old('is_new_arrival', 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_new_arrival" style="color:var(--mb-text);">New Arrival</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-gold">Create Product &amp; Variants</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-dark-surface">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let variantIndex = 1;
    const tbody = document.getElementById('variant-rows');
    const addBtn = document.getElementById('btn-add-variant');

    addBtn.addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.className = 'variant-row';
        tr.innerHTML = `
            <td>
                <input type="text" name="variants[${variantIndex}][variant_name]" class="form-control form-control-sm" placeholder="e.g. 5x20/CON/4pcs or 12GB+512GB" required>
            </td>
            <td>
                <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control form-control-sm" placeholder="Price">
            </td>
            <td>
                <input type="number" name="variants[${variantIndex}][stock_qty]" class="form-control form-control-sm" placeholder="Stock" value="50" min="0" required>
            </td>
            <td>
                <input type="text" name="variants[${variantIndex}][sku]" class="form-control form-control-sm" placeholder="SKU">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-link text-danger btn-remove-row" style="text-decoration:none;"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
        variantIndex++;
    });

    tbody.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            const rows = tbody.querySelectorAll('.variant-row');
            if (rows.length > 1) {
                e.target.closest('.variant-row').remove();
            } else {
                alert('At least one variant row is required.');
            }
        }
    });
});
</script>
@endsection
