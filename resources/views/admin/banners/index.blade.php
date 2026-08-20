@extends('layouts.admin')
@section('title', 'Hero Banners')
@section('page-title', 'Hero Banner Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <p style="color:var(--mb-muted);font-size:.9rem;" class="mb-0">
            Create, edit, and organize home page hero carousel banners. You can upload custom product photos or select existing product images.
        </p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Add New Hero Banner
    </a>
</div>

<div class="row g-4">
    @forelse($banners as $banner)
    <div class="col-md-6 col-lg-4">
        <div class="dark-card h-100 overflow-hidden d-flex flex-column" style="position:relative;">
            <div style="height:180px;background:var(--mb-surface);position:relative;overflow:hidden;">
                @if($banner->image_url)
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">No Image</div>
                @endif
                <div style="position:absolute;top:10px;right:10px;">
                    @if($banner->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>
            </div>

            <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                <div>
                    <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--mb-heading);" class="mb-1">
                        {{ $banner->title }}
                    </h3>
                    @if($banner->subtitle)
                        <p style="font-size:.85rem;color:var(--mb-muted);" class="mb-2">{{ Str::limit($banner->subtitle, 80) }}</p>
                    @endif
                    @if($banner->link_url)
                        <div style="font-size:.78rem;color:var(--mb-gold);" class="text-truncate">
                            <i class="bi bi-link-45deg me-1"></i>{{ $banner->link_url }}
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" style="border-color:var(--mb-border)!important;">
                    <span style="font-size:.78rem;color:var(--mb-muted);">Sort Order: {{ $banner->sort_order ?? 0 }}</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-dark-surface btn-sm"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Delete this banner?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-dark-surface btn-sm text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="dark-card p-5 text-center" style="color:var(--mb-muted);">
            <i class="bi bi-images fs-1 text-gold mb-2 d-block"></i>
            <h5>No Banners Configured</h5>
            <p>Click 'Add New Hero Banner' above to feature your product images on the home page slider.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
