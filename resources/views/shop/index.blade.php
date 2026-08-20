@extends('layouts.app')
@section('title', ($category->name ?? 'Shop') . ' — RAI MOTORCYCLE PARTS')

@section('content')
<div class="container-xl py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background:none;padding:0;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
            @isset($category)<li class="breadcrumb-item active">{{ $category->name }}</li>@endisset
        </ol>
    </nav>

    <div class="row g-4">
        {{-- ── Filter Sidebar ─────────────────────────────── --}}
        <div class="col-lg-3">
            <div class="dark-card p-3 sticky-top" style="top:80px;">
                <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--mb-heading);margin-bottom:1rem;">
                    <i class="bi bi-funnel me-2 text-gold"></i>Filters
                </h2>
                <form method="GET" action="{{ isset($category) ? route('shop.category',$category->slug) : route('shop.index') }}" id="filter-form">
                    {{-- Price Range --}}
                    <div class="mb-3">
                        <div class="form-label">Price Range (&#x20B1;)</div>
                        <div class="d-flex gap-2">
                            <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ request('min_price') }}">
                            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ request('max_price') }}">
                        </div>
                    </div>
                    {{-- Material --}}
                    @if(isset($materials) && $materials->isNotEmpty())
                    <div class="mb-3">
                        <div class="form-label">Material</div>
                        @foreach($materials as $mat)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material" value="{{ $mat->name }}" id="mat_{{ $mat->id }}" {{ request('material') === $mat->name ? 'checked' : '' }}>
                            <label class="form-check-label" for="mat_{{ $mat->id }}" style="color:var(--mb-muted);font-size:.88rem;">{{ $mat->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Color --}}
                    @if(isset($colors) && $colors->isNotEmpty())
                    <div class="mb-3">
                        <div class="form-label">Color</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($colors as $c)
                            <div class="color-swatch {{ request('color') === $c->name ? 'active' : '' }}"
                                 title="{{ $c->name }}" 
                                 onclick="document.getElementById('color-input').value='{{ $c->name }}';document.getElementById('filter-form').submit();"
                                 style="cursor:pointer;width:24px;height:24px;border-radius:50%;border:1px solid rgba(255,255,255,0.3);background:{{ $c->value ?? '#666' }};"></div>
                            @endforeach
                        </div>
                        <input type="hidden" id="color-input" name="color" value="{{ request('color') }}">
                    </div>
                    @endif

                    {{-- Thread Size --}}
                    @if(isset($threadSizes) && $threadSizes->isNotEmpty())
                    <div class="mb-3">
                        <div class="form-label">Thread Size</div>
                        @foreach($threadSizes as $ts)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="thread_size" value="{{ $ts->name }}" id="ts_{{ $ts->id }}" {{ request('thread_size') === $ts->name ? 'checked' : '' }}>
                            <label class="form-check-label" for="ts_{{ $ts->id }}" style="color:var(--mb-muted);font-size:.88rem;">{{ $ts->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Brand --}}
                    @if(isset($brands) && $brands->isNotEmpty())
                    <div class="mb-3">
                        <div class="form-label">Brand</div>
                        @foreach($brands as $brand)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="brand" value="{{ $brand->id }}" id="brand_{{ $brand->id }}" {{ request('brand') == $brand->id ? 'checked' : '' }}>
                            <label class="form-check-label" for="brand_{{ $brand->id }}" style="color:var(--mb-muted);font-size:.88rem;">{{ $brand->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gold btn-sm flex-grow-1">Apply</button>
                        <a href="{{ isset($category) ? route('shop.category',$category->slug) : route('shop.index') }}" class="btn btn-dark-surface btn-sm">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Product Grid ────────────────────────────────── --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.5rem;font-weight:700;color:var(--mb-heading);margin:0;">
                        {{ $category->name ?? 'All Products' }}
                    </h1>
                    <div style="color:var(--mb-muted);font-size:.85rem;">{{ $products->total() }} products</div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <label style="color:var(--mb-muted);font-size:.85rem;">Sort:</label>
                    <select name="sort" form="filter-form" onchange="document.getElementById('filter-form').submit();"
                            class="form-select form-select-sm" style="width:auto;">
                        <option value="" {{ !request('sort') ? 'selected' : '' }}>Featured</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="best" {{ request('sort') === 'best' ? 'selected' : '' }}>Best Sellers</option>
                    </select>
                </div>
            </div>

            @if($products->isEmpty())
            <div class="text-center py-5">
                <div style="font-size:3rem;">&#x1F50D;</div>
                <h3 style="font-family:'Rajdhani',sans-serif;color:var(--mb-muted);">No products found</h3>
                <p style="color:var(--mb-muted);">Try adjusting your filters or <a href="{{ route('shop.index') }}">browse all products</a>.</p>
            </div>
            @else
            <div class="row g-3">
                @foreach($products as $i => $product)
                <div class="col-6 col-md-4 col-xl-4 fade-up" style="--i:{{ $i % 8 }}">
                    @include('partials.product-card', ['product' => $product])
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
