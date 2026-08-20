@extends('layouts.admin')
@section('title', 'Add New Banner')
@section('page-title', 'Add New Hero Banner')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="dark-card p-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--mb-heading);" class="mb-3">
                Hero Banner Details
            </h2>

            <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Banner Headline Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. Precision CNC Parts for Filipino Riders" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Subtitle / Description</label>
                    <textarea name="subtitle" class="form-control" rows="2" placeholder="e.g. Titanium &amp; anodized aluminum hardware built for streets and track.">{{ old('subtitle') }}</textarea>
                </div>

                {{-- Image Source Options --}}
                <div class="p-3 mb-3" style="background:var(--mb-surface);border-radius:var(--mb-radius);border:1px solid var(--mb-border);">
                    <label class="form-label fw-bold"><i class="bi bi-image me-1 text-gold"></i>Banner Image Source</label>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Option A: Upload Image File</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Option B: Image URL</label>
                            <input type="text" name="image_url" id="image_url_input" class="form-control" value="{{ old('image_url') }}" placeholder="https://example.com/banner.jpg or /images/logo.png">
                        </div>
                    </div>

                    @if($products->count())
                    <div class="mt-3">
                        <label class="form-label small">Option C: Pick Image From Store Products</label>
                        <select class="form-select" onchange="if(this.value) document.getElementById('image_url_input').value = this.value;">
                            <option value="">-- Choose a product image --</option>
                            @foreach($products as $product)
                                @if($product->primary_image_url)
                                    <option value="{{ $product->primary_image_url }}">{{ $product->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Target Link URL (When clicked)</label>
                        <input type="text" name="link_url" class="form-control" value="{{ old('link_url', '/shop') }}" placeholder="e.g. /shop or /products/titanium-bolt">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Button Label</label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', 'Shop Now') }}" placeholder="e.g. Shop Now, Explore Kit">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Active &amp; Visible on Home Page</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-gold">Create Hero Banner</button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-dark-surface">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
