@extends('layouts.app')
@section('title', $product->name . ' — RAI MOTORCYCLE PARTS')
@section('meta_description', $product->short_description ?? Str::limit($product->description, 160))

@section('content')
@php $firstVariant = $product->variants->first(); @endphp
<div class="container-xl py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background:none;padding:0;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
            @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('shop.category', $product->category->slug) }}">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active">{{ Str::limit($product->name, 50) }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        {{-- ── Image Gallery ─────────────────────────────── --}}
        <div class="col-lg-6">
            <div class="dark-card p-3" style="border-radius:var(--mb-radius);">
                {{-- Main image --}}
                <div id="main-image-wrap" style="aspect-ratio:1;border-radius:var(--mb-radius-sm);overflow:hidden;background:var(--mb-surface);display:flex;align-items:center;justify-content:center;">
                    @if($firstVariant?->image_url)
                        <img id="main-image" src="{{ $firstVariant->image_url }}" alt="{{ $product->name }}"
                             style="width:100%;height:100%;object-fit:cover;" loading="eager">
                    @else
                        <div style="font-size:6rem;opacity:.3;">&#x1F529;</div>
                    @endif
                </div>
                {{-- Thumbnail strip --}}
                @if($product->variants->count() > 1)
                <div class="d-flex gap-2 mt-3 overflow-auto" style="padding-bottom:.5rem;">
                    @foreach($product->variants as $v)
                    <div class="thumb-img {{ $loop->first ? 'active' : '' }}" data-src="{{ $v->image_url }}"
                         style="width:64px;height:64px;flex-shrink:0;border-radius:8px;overflow:hidden;border:2px solid {{ $loop->first ? 'var(--mb-gold)' : 'var(--mb-border)' }};cursor:pointer;background:var(--mb-surface);">
                        @if($v->image_url)
                        <img src="{{ $v->image_url }}" alt="{{ $v->color }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">&#x1F529;</div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ── Product Info ──────────────────────────────── --}}
        <div class="col-lg-6">
            @if($product->brand)
                <div class="product-brand mb-1">{{ $product->brand->name }}</div>
            @endif
            <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:#fff;line-height:1.2;margin-bottom:.75rem;">
                {{ $product->name }}
            </h1>

            {{-- Rating summary --}}
            @if($product->review_count > 0)
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="stars">
                    @for($s = 1; $s <= 5; $s++)<i class="bi bi-star{{ $s <= round($product->average_rating) ? '-fill' : '' }}"></i>@endfor
                </div>
                <span style="color:var(--mb-muted);font-size:.88rem;">{{ $product->average_rating }} ({{ $product->review_count }} reviews)</span>
            </div>
            @endif

            {{-- Price --}}
            <div class="selected-price mb-3">
                @if($firstVariant)
                    @if($firstVariant->is_on_sale)
                        <span class="product-price" style="font-size:1.6rem;">&#x20B1;{{ number_format($firstVariant->sale_price, 2) }}</span>
                        <span class="product-price-original ms-2">&#x20B1;{{ number_format($firstVariant->price, 2) }}</span>
                        <span class="badge ms-2" style="background:var(--mb-red);font-size:.8rem;">SALE</span>
                    @else
                        <span class="product-price" style="font-size:1.6rem;">&#x20B1;{{ number_format($firstVariant->price, 2) }}</span>
                    @endif
                @endif
            </div>

            {{-- Short description --}}
            @if($product->short_description)
            <p style="color:var(--mb-muted);font-size:.93rem;line-height:1.7;margin-bottom:1.5rem;">{{ $product->short_description }}</p>
            @endif

            {{-- Color swatches --}}
            @if($variantsByColor->count() > 1)
            <div class="mb-3">
                <div class="form-label">Color: <span id="selected-color-label" class="text-gold">{{ $firstVariant?->color }}</span></div>
                <div class="color-swatch-wrap">
                    @foreach($variantsByColor as $color => $variants)
                    @php $v = $variants->first(); @endphp
                    <div class="color-swatch swatch-{{ strtolower($color) }} {{ $loop->first ? 'active' : '' }}"
                         data-variant-id="{{ $v->id }}"
                         data-price="{{ $v->price }}"
                         data-sale-price="{{ $v->sale_price }}"
                         data-stock="{{ $v->stock_qty }}"
                         data-color="{{ $color }}"
                         title="{{ $color }}">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Pack qty selectors --}}
            @php $packOptions = $product->variants->pluck('pack_qty')->unique()->sort(); @endphp
            @if($packOptions->count() > 1)
            <div class="mb-3">
                <div class="form-label">Pack Quantity</div>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach($packOptions as $qty)
                    <div class="size-option {{ $loop->first ? 'active' : '' }}">{{ $qty }}pc</div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Stock status --}}
            <div class="stock-status mb-3">
                @if($firstVariant)
                    @if($firstVariant->is_in_stock)
                        @if($firstVariant->is_low_stock)
                            <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock ({{ $firstVariant->stock_qty }} left)</span>
                        @else
                            <span style="color:var(--mb-green);"><i class="bi bi-check-circle me-1"></i>In Stock</span>
                        @endif
                    @else
                        <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>
                    @endif
                @endif
            </div>

            {{-- Add to Cart form --}}
            @if($firstVariant && $firstVariant->is_in_stock)
            <form class="ajax-add-to-cart d-flex gap-3 align-items-center mb-3" method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="variant_id" id="selected-variant-id" value="{{ $firstVariant->id }}">
                <div class="qty-control">
                    <button type="button" class="qty-btn" data-action="minus"><i class="bi bi-dash"></i></button>
                    <input type="number" name="qty" class="qty-input" value="1" min="1" max="99">
                    <button type="button" class="qty-btn" data-action="plus"><i class="bi bi-plus"></i></button>
                </div>
                <button type="submit" class="btn btn-gold flex-grow-1 py-2">
                    <i class="bi bi-bag-plus me-1"></i>Add to Cart
                </button>
            </form>
            <a href="{{ route('cart.index') }}" class="btn btn-outline-gold w-100 mb-3">Buy Now — Checkout</a>
            @endif

            {{-- Meta (SKU, brand) --}}
            <div style="font-size:.8rem;color:var(--mb-muted);">
                @if($firstVariant) SKU: <span class="text-gold">{{ $firstVariant->variant_sku }}</span> &bull; @endif
                Category: <a href="{{ route('shop.category', $product->category?->slug ?? 'shop') }}" style="color:var(--mb-muted);">{{ $product->category?->name }}</a>
            </div>
        </div>
    </div>

    {{-- ── Tabs ─────────────────────────────────────────── --}}
    <div class="mt-5">
        <ul class="nav nav-tabs gap-1" style="border-bottom:1px solid var(--mb-border);">
            @foreach(['Description','Specifications','Shipping & Returns','Reviews'] as $tab)
            <li class="nav-item">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-{{ $loop->index }}"
                        style="{{ $loop->first ? 'color:var(--mb-gold);border-bottom-color:var(--mb-gold)!important;' : 'color:var(--mb-muted);' }}background:none;border:none;border-bottom:2px solid transparent;padding:.75rem 1.25rem;font-weight:600;">
                    {{ $tab }}
                </button>
            </li>
            @endforeach
        </ul>
        <div class="tab-content pt-4">
            {{-- Description --}}
            <div class="tab-pane fade show active" id="tab-0">
                <div style="color:var(--mb-text);line-height:1.8;max-width:800px;">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
            {{-- Specifications --}}
            <div class="tab-pane fade" id="tab-1">
                <div class="table-responsive" style="max-width:600px;">
                    <table class="table table-dark-custom">
                        <tbody>
                            @if($firstVariant)
                            @if($firstVariant->thread_size)<tr><td style="color:var(--mb-muted);">Thread Size</td><td>{{ $firstVariant->thread_size }}</td></tr>@endif
                            @if($firstVariant->length_mm)<tr><td style="color:var(--mb-muted);">Length</td><td>{{ $firstVariant->length_mm }}mm</td></tr>@endif
                            @if($firstVariant->head_type)<tr><td style="color:var(--mb-muted);">Head Type</td><td>{{ ucfirst($firstVariant->head_type) }}</td></tr>@endif
                            @if($firstVariant->material)<tr><td style="color:var(--mb-muted);">Material</td><td>{{ $firstVariant->material }}</td></tr>@endif
                            @if($firstVariant->finish)<tr><td style="color:var(--mb-muted);">Finish</td><td>{{ ucfirst($firstVariant->finish) }}</td></tr>@endif
                            <tr><td style="color:var(--mb-muted);">Pack Quantity</td><td>{{ $firstVariant->pack_qty }} piece(s)</td></tr>
                            @if($product->weight_grams)<tr><td style="color:var(--mb-muted);">Weight</td><td>{{ $product->weight_grams }}g</td></tr>@endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Shipping --}}
            <div class="tab-pane fade" id="tab-2">
                <div style="color:var(--mb-text);line-height:1.8;max-width:700px;">
                    <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:var(--mb-gold);">Shipping</h3>
                    <p>Metro Manila orders placed before 12NN ship same day. Provincial orders are processed and handed to J&amp;T Express or Ninja Van within 1 business day.</p>
                    <p>Free shipping on orders &#x20B1;1,500 and above. Flat-rate &#x20B1;89 for orders below threshold.</p>
                    <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:var(--mb-gold);margin-top:1rem;">Returns &amp; Warranty</h3>
                    <p>7-day return window for unused, uninstalled parts in original packaging. Manufacturing defects are covered — installation damage is not.</p>
                    <p>Contact us via Viber or Facebook Messenger for return requests.</p>
                </div>
            </div>
            {{-- Reviews --}}
            <div class="tab-pane fade" id="tab-3">
                @forelse($product->approvedReviews as $review)
                <div class="review-card mb-3" style="max-width:700px;">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="reviewer-avatar">{{ substr($review->user?->name ?? 'A', 0, 1) }}</div>
                        <div>
                            <strong style="color:var(--mb-text);">{{ $review->user?->name ?? 'Anonymous' }}</strong>
                            @if($review->bike_model)<div style="font-size:.75rem;color:var(--mb-muted);">&#x1F3CD;&#xFE0F; {{ $review->bike_model }}</div>@endif
                        </div>
                        <div class="ms-auto stars">
                            @for($s=1;$s<=5;$s++)<i class="bi bi-star{{ $s<=$review->rating ? '-fill':'' }}"></i>@endfor
                        </div>
                    </div>
                    <p style="color:var(--mb-text);font-size:.9rem;line-height:1.6;margin:0;">{{ $review->comment }}</p>
                    @if($review->admin_reply)
                    <div class="mt-2 p-2" style="background:var(--mb-gold-dim);border-radius:var(--mb-radius-sm);font-size:.82rem;color:var(--mb-gold);">
                        <i class="bi bi-reply me-1"></i><strong>RAI MOTORCYCLE PARTS:</strong> {{ $review->admin_reply }}
                    </div>
                    @endif
                </div>
                @empty
                <p style="color:var(--mb-muted);">No reviews yet. Be the first to review this product!</p>
                @endforelse

                @auth
                <div class="mt-4 dark-card p-4" style="max-width:700px;">
                    <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:#fff;margin-bottom:1rem;">Write a Review</h3>
                    <form method="POST" action="{{ route('reviews.store') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div class="d-flex gap-2">
                                @foreach([1,2,3,4,5] as $r)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="rating" value="{{ $r }}" id="r{{ $r }}">
                                    <label class="form-check-label text-gold" for="r{{ $r }}">{{ str_repeat('★', $r) }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Motorcycle (optional)</label>
                            <input type="text" name="bike_model" class="form-control" placeholder="e.g. Yamaha Sniper 155">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Review</label>
                            <textarea name="comment" class="form-control" rows="4" placeholder="Share your experience..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-gold">Submit Review</button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- Cross-sell --}}
    @if($crossSell->count())
    <div class="mt-5">
        <div class="section-label">You Might Also Like</div>
        <h2 class="section-title mb-4">Complete Your Kit</h2>
        <div class="row g-3">
            @foreach($crossSell as $p)
            <div class="col-6 col-md-3">@include('partials.product-card', ['product' => $p])</div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Thumbnail swap
document.querySelectorAll('.thumb-img').forEach(thumb => {
    thumb.addEventListener('click', () => {
        document.querySelectorAll('.thumb-img').forEach(t => t.style.borderColor = 'var(--mb-border)');
        thumb.style.borderColor = 'var(--mb-gold)';
        const src = thumb.dataset.src;
        if (src) {
            const mainImg = document.getElementById('main-image');
            if (mainImg) mainImg.src = src;
        }
    });
});
// Update color label on swatch click
document.querySelectorAll('.color-swatch').forEach(s => {
    s.addEventListener('click', () => {
        const label = document.getElementById('selected-color-label');
        if (label && s.dataset.color) label.textContent = s.dataset.color;
    });
});
</script>
@endpush
@endsection
