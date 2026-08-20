@extends('layouts.admin')
@section('title', 'Edit Banner')
@section('page-title', 'Edit Hero Banner')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="dark-card p-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--mb-heading);" class="mb-3">
                Edit Hero Banner: {{ $banner->title }}
            </h2>

            <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Banner Headline Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Subtitle / Description</label>
                    <textarea name="subtitle" class="form-control" rows="2">{{ old('subtitle', $banner->subtitle) }}</textarea>
                </div>

                {{-- Image Source Options --}}
                <div class="p-3 mb-3" style="background:var(--mb-surface);border-radius:var(--mb-radius);border:1px solid var(--mb-border);">
                    <label class="form-label fw-bold"><i class="bi bi-image me-1 text-gold"></i>Banner Image Source</label>
                    
                    @if($banner->image_url)
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <img src="{{ $banner->image_url }}" alt="Preview" style="height:60px;width:100px;object-fit:cover;border-radius:6px;border:1px solid var(--mb-border);">
                        <span class="small text-muted">Current image: {{ $banner->image_url }}</span>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Option A: Upload New Image File</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Option B: Image URL</label>
                            <input type="text" name="image_url" id="image_url_input" class="form-control" value="{{ old('image_url', $banner->image_url) }}">
                        </div>
                    </div>

                    @if($products->count())
                    <div class="mt-3">
                        <label class="form-label small">Option C: Pick Image From Store Products</label>
                        <select class="form-select" onchange="if(this.value) document.getElementById('image_url_input').value = this.value;">
                            <option value="">-- Choose a product image --</option>
                            @foreach($products as $product)
                                @if($product->primary_image_url)
                                    <option value="{{ $product->primary_image_url }}" {{ $banner->image_url === $product->primary_image_url ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Target Link URL</label>
                        <input type="text" name="link_url" class="form-control" value="{{ old('link_url', $banner->link_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Button Label</label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $banner->button_text) }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $banner->sort_order) }}">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $banner->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active &amp; Visible on Home Page</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-gold">Save Changes</button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-dark-surface">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
