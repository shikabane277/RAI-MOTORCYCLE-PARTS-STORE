@extends('layouts.admin')
@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" class="d-flex gap-2 align-items-center">
        <input type="search" name="search" class="form-control" style="max-width:280px;" placeholder="Search products..." value="{{ request('search') }}">
        <select name="status" class="form-select" style="width:auto;" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
        <button type="submit" class="btn btn-dark-surface btn-sm"><i class="bi bi-search"></i></button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn btn-gold"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
</div>

<div class="dark-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark-custom mb-0">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Variants</th>
                    <th>Base Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--mb-text);font-size:.9rem;">{{ Str::limit($product->name, 45) }}</div>
                        <div style="font-size:.72rem;color:var(--mb-muted);">{{ $product->slug }}</div>
                        @if($product->is_featured)<span style="background:var(--mb-gold-dim);color:var(--mb-gold);font-size:.65rem;padding:.15rem .45rem;border-radius:4px;font-weight:700;">FEATURED</span>@endif
                        @if($product->is_new_arrival)<span style="background:var(--mb-blue-dim);color:var(--mb-blue);font-size:.65rem;padding:.15rem .45rem;border-radius:4px;font-weight:700;margin-left:.2rem;">NEW</span>@endif
                    </td>
                    <td style="font-size:.85rem;color:var(--mb-muted);">{{ $product->category?->name ?? '—' }}</td>
                    <td style="font-size:.85rem;color:var(--mb-muted);">{{ $product->brand?->name ?? '—' }}</td>
                    <td>
                        <span style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-text);">{{ $product->variants->count() }}</span>
                        <span style="font-size:.72rem;color:var(--mb-muted);"> variants</span>
                        <div style="font-size:.72rem;color:{{ $product->total_stock <= 0 ? 'var(--mb-red)' : ($product->total_stock <= 10 ? 'var(--mb-gold)' : 'var(--mb-green)') }};">
                            {{ $product->total_stock }} in stock
                        </div>
                    </td>
                    <td style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);">&#x20B1;{{ number_format($product->base_price, 2) }}</td>
                    <td>
                        <span class="status-badge {{ $product->status === 'active' ? 'status-delivered' : ($product->status === 'draft' ? 'status-processing' : 'status-cancelled') }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-dark-surface btn-sm"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('product.show', $product->slug) }}" class="btn btn-dark-surface btn-sm" target="_blank"><i class="bi bi-eye"></i></a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Archive this product?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="background:var(--mb-red-dim);color:var(--mb-red);border:1px solid rgba(229,57,53,0.3);"><i class="bi bi-archive"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:var(--mb-muted);">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $products->withQueryString()->links() }}</div>
@endsection
