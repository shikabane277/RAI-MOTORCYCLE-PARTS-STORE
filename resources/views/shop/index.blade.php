@extends('layouts.app')
@section('title', ($category->name ?? 'Shop') . ' — MachBolt PH')

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
                <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1rem;">
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
                    <div class="mb-3">
                        <div class="form-label">Material</div>
                        @foreach(['Titanium Gr5','Stainless A4','7075 Aluminum','Chromoly'] as $mat)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material" value="{{ $mat }}" id="mat_{{ $loop->index }}" {{ request('material') === $mat ? 'checked' : '' }}>
                            <label class="form-check-label" for="mat_{{ $loop->index }}" style="color:var(--mb-muted);font-size:.88rem;">{{ $mat }}</label>
                        </div>
                        @endforeach
                    </div>
                    {{-- Color --}}
                    <div class="mb-3">
                        <div class="form-label">Color</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['Black','Red','Blue','Gold','Silver','Rainbow'] as $c)
                            <div class="color-swatch swatch-{{ strtolower($c) }} {{ request('color') === $c ? 'active' : '' }}"
                                 title="{{ $c }}" onclick="document.getElementById('color-input').value='{{ $c }}';document.getElementById('filter-form').submit();"
                                 style="cursor:pointer;"></div>
                            @endforeach
                        </div>
                        <input type="hidden" id="color-input" name="color" value="{{ request('color') }}">
                    </div>
                    {{-- Thread Size --}}
                    <div class="mb-3">
                        <div class="form-label">Thread Size</div>
                        @foreach(['M5','M6','M8','M10','M12'] as $ts)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="thread_size" value="{{ $ts }}" id="ts_{{ $loop->index }}" {{ request('thread_size') === $ts ? 'checked' : '' }}>
                            <label class="form-check-label" for="ts_{{ $loop->index }}" style="color:var(--mb-muted);font-size:.88rem;">{{ $ts }}</label>
                        </div>
                        @endforeach
                    </div>
                    {{-- Brand --}}
                    <div class="mb-3">
                        <div class="form-label">Brand</div>
                        @foreach($brands as $brand)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="brand" value="{{ $brand->id }}" id="brand_{{ $brand->id }}" {{ request('brand') == $brand->id ? 'checked' : '' }}>
                            <label class="form-check-label" for="brand_{{ $brand->id }}" style="color:var(--mb-muted);font-size:.88rem;">{{ $brand->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    {{-- Fitment filter --}}
                    @if(session('fitment'))
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="fitment_filter" value="1" id="fitment_filter" {{ request('fitment_filter') ? 'checked' : '' }}>
                            <label class="form-check-label" for="fitment_filter" style="color:var(--mb-text);font-size:.88rem;">
                                <i class="bi bi-motorcycle text-gold me-1"></i>Fits my {{ session('fitment.make') }} {{ session('fitment.model') }}
                            </label>
                        </div>
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
                    <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.5rem;font-weight:700;color:#fff;margin:0;">
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
