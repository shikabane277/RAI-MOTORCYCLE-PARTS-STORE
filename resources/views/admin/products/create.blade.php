@extends('layouts.admin')
@section('title', 'Create Product')
@section('page-title', 'Add New Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="dark-card p-4">
            <form method="POST" action="{{ route('admin.products.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
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
                    <input type="text" name="short_description" class="form-control" value="{{ old('short_description') }}" maxlength="500">
                </div>
                <div class="mb-3">
                    <label class="form-label">Full Description</label>
                    <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                </div>
                <div class="row g-3 mb-3">
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

                {{-- Initial Inventory & Stock Section --}}
                <div class="p-3 mb-3" style="background:var(--mb-surface);border:1px solid var(--mb-gold-border);border-radius:var(--mb-radius-sm);">
                    <h6 style="font-family:'Rajdhani',sans-serif;color:var(--mb-gold);font-weight:700;" class="mb-3">
                        <i class="bi bi-box-seam me-1"></i> Initial Inventory & Stock
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Initial Stock Quantity *</label>
                            <input type="number" name="initial_stock" class="form-control" value="{{ old('initial_stock', 50) }}" min="0" required>
                            <span style="font-size:.75rem;color:var(--mb-muted);">Number of items available for sale immediately.</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU (Stock Keeping Unit)</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="Auto-generated if left blank (e.g. RAI-BOLT-001)">
                            <span style="font-size:.75rem;color:var(--mb-muted);">Unique SKU identifier.</span>
                        </div>
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
                    <button type="submit" class="btn btn-gold">Create Product</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-dark-surface">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
