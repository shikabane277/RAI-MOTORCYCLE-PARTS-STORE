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
        {{-- ── Image Gallery (All Product Photos) ────────────── --}}
        <div class="col-lg-6">
            <div class="dark-card p-3" style="border-radius:var(--mb-radius);">
                @php
                    $allImages = collect([$product->primary_image_url]);
                    foreach ($product->variants as $v) {
                        if ($v->image_url) $allImages->push($v->image_url);
                        if (is_array($v->images)) {
                            foreach ($v->images as $extraImg) $allImages->push($extraImg);
                        }
                    }
                    $allImages = $allImages->filter()->unique()->values();
                    $mainImg = $allImages->first() ?: '/images/logo.png';
                @endphp
                
                {{-- Main image --}}
                <div id="main-image-wrap" style="aspect-ratio:1;border-radius:var(--mb-radius-sm);overflow:hidden;background:var(--mb-surface);display:flex;align-items:center;justify-content:center;">
                    @if($mainImg)
                        <img id="main-image" src="{{ $mainImg }}" alt="{{ $product->name }}"
                             style="width:100%;height:100%;object-fit:cover;" loading="eager">
                    @else
                        <div style="font-size:6rem;opacity:.3;">&#x1F529;</div>
                    @endif
                </div>

                {{-- Thumbnail strip for all uploaded product photos --}}
                @if($allImages->count() > 1)
                <div class="d-flex gap-2 mt-3 overflow-auto" style="padding-bottom:.5rem;">
                    @foreach($allImages as $idx => $imgUrl)
                    <div class="thumb-img {{ $loop->first ? 'active' : '' }}" onclick="document.getElementById('main-image').src='{{ $imgUrl }}'; document.querySelectorAll('.thumb-img').forEach(t=>t.style.borderColor='var(--mb-border)'); this.style.borderColor='var(--mb-gold)';"
                         style="width:64px;height:64px;flex-shrink:0;border-radius:8px;overflow:hidden;border:2px solid {{ $loop->first ? 'var(--mb-gold)' : 'var(--mb-border)' }};cursor:pointer;background:var(--mb-surface);">
                        <img src="{{ $imgUrl }}" alt="Photo {{ $idx+1 }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ── Product Details & Customizable Variants ──────── --}}
        <div class="col-lg-6">
            @if($product->brand)
                <div class="product-brand mb-1">{{ $product->brand->name }}</div>
            @endif
            <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:var(--mb-heading);line-height:1.2;margin-bottom:.75rem;">
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

            {{-- Data-Driven Dynamic Multi-Group Option Selector System --}}
            @php
                $optionGroups = $product->parsed_option_groups;
            @endphp

            @if(count($optionGroups) > 0)
                <div class="d-flex flex-column gap-3 mb-4" id="dynamic-option-groups-container">
                    @foreach($optionGroups as $gIdx => $group)
                    <div class="option-group-wrapper">
                        <label class="form-label font-bold text-uppercase mb-2" style="letter-spacing:1px;font-size:.85rem;color:var(--mb-heading);">
                            {{ $group['name'] }}: 
                            <span id="group-selected-val-{{ $gIdx }}" class="text-gold fw-bold ms-1">
                                {{ $group['values'][0]['label'] ?? '' }}
                            </span>
                        </label>

                        <div class="d-flex flex-wrap gap-2 group-values-container" data-group-index="{{ $gIdx }}" data-group-name="{{ e($group['name']) }}">
                            @foreach($group['values'] as $vIdx => $val)
                            @php
                                $vLabel = $val['label'];
                                $vImg   = $val['image'] ?? null;
                                $isDis  = $val['disabled'] ?? false;
                            @endphp
                            <div class="dynamic-option-btn {{ $vIdx === 0 && !$isDis ? 'active' : '' }} {{ $isDis ? 'disabled-out-of-stock' : '' }}"
                                 data-group-index="{{ $gIdx }}"
                                 data-value="{{ e($vLabel) }}"
                                 style="display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border:1px solid {{ $vIdx === 0 && !$isDis ? 'var(--mb-gold)' : 'var(--mb-border)' }};border-radius:6px;background:{{ $vIdx === 0 && !$isDis ? 'var(--mb-gold-dim)' : 'var(--mb-card)' }};cursor:{{ $isDis ? 'not-allowed' : 'pointer' }};transition:all 0.2s ease;{{ $isDis ? 'opacity:0.45;background:var(--mb-surface);' : '' }}">
                                
                                @if(!empty($vImg))
                                    <img src="{{ $vImg }}" alt="{{ $vLabel }}" style="width:28px;height:28px;object-fit:cover;border-radius:4px;border:1px solid var(--mb-border);">
                                @endif
                                
                                <span style="font-family:'Rajdhani',sans-serif;font-size:0.95rem;font-weight:600;color:var(--mb-text);{{ $isDis ? 'text-decoration:line-through;' : '' }}">
                                    {{ $vLabel }}
                                </span>

                                @if($isDis)
                                    <span class="badge bg-secondary ms-1" style="font-size:0.65rem;">UNAVAILABLE</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            {{-- Customer Live Stock Available Counter --}}
            <div class="stock-status mb-4 p-3" style="background:var(--mb-surface);border:1px solid var(--mb-border);border-radius:8px;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span style="color:var(--mb-muted);font-size:.88rem;"><i class="bi bi-box-seam text-gold me-1"></i> Available Inventory Stock:</span>
                    <div id="stock-badge-container">
                        @if($firstVariant)
                            @if($firstVariant->stock_qty > 10)
                                <span class="badge bg-success" style="font-size:.85rem;padding:6px 12px;">
                                    <i class="bi bi-check-circle me-1"></i> In Stock ({{ $firstVariant->stock_qty }} units available)
                                </span>
                            @elseif($firstVariant->stock_qty > 0)
                                <span class="badge bg-warning text-dark" style="font-size:.85rem;padding:6px 12px;">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Low Stock (Only {{ $firstVariant->stock_qty }} left!)
                                </span>
                            @else
                                <span class="badge bg-danger" style="font-size:.85rem;padding:6px 12px;">
                                    <i class="bi bi-x-circle me-1"></i> Out of Stock (0 items)
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Add to Cart form --}}
            <form action="{{ route('cart.add') }}" method="POST" class="ajax-add-to-cart">
                @csrf
                <input type="hidden" name="variant_id" id="selected-variant-id" value="{{ $firstVariant?->id }}">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="qty-control d-flex align-items-center">
                        <button type="button" class="qty-btn" data-action="minus">-</button>
                        <input type="number" name="qty" class="qty-input" value="1" min="1" max="99">
                        <button type="button" class="qty-btn" data-action="plus">+</button>
                    </div>
                    <button type="submit" class="btn btn-gold btn-lg flex-grow-1" id="btn-add-to-cart" {{ $firstVariant?->stock_qty <= 0 ? 'disabled' : '' }}>
                        <i class="bi bi-cart-plus me-1"></i> Add To Cart
                    </button>
                </div>
            </form>

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
                    <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:var(--mb-heading);margin-bottom:1rem;">Write a Review</h3>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const variantsData = @json($product->variants->map(fn($v) => [
        'id'         => $v->id,
        'label'      => $v->label,
        'price'      => (float)$v->price,
        'sale_price' => $v->sale_price ? (float)$v->sale_price : null,
        'stock'      => (int)$v->stock_qty,
        'image'      => $v->image_url ?: $product->primary_image_url,
    ]));

    // Handle Dynamic Option Button Click across unlimited groups
    document.querySelectorAll('.dynamic-option-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (btn.classList.contains('disabled-out-of-stock')) return;

            const groupIdx = btn.dataset.groupIndex;
            const val = btn.dataset.value;

            // Deselect other buttons in the same group
            const container = btn.closest('.group-values-container');
            if (container) {
                container.querySelectorAll('.dynamic-option-btn').forEach(b => {
                    b.classList.remove('active');
                    b.style.borderColor = 'var(--mb-border)';
                    b.style.background = 'var(--mb-card)';
                });
            }

            // Highlight selected button
            btn.classList.add('active');
            btn.style.borderColor = 'var(--mb-gold)';
            btn.style.background = 'var(--mb-gold-dim)';

            // Update group selected label text
            const labelEl = document.getElementById(`group-selected-val-${groupIdx}`);
            if (labelEl) labelEl.textContent = val;

            // Collect selections across all option groups
            const selectedVals = [];
            document.querySelectorAll('.group-values-container').forEach(cnt => {
                const activeBtn = cnt.querySelector('.dynamic-option-btn.active');
                if (activeBtn) selectedVals.push(activeBtn.dataset.value);
            });

            // Find matching variant
            const selectedComboName = selectedVals.join(' - ');
            let matchedVariant = variantsData.find(v => v.label === selectedComboName);

            // Fallback matches
            if (!matchedVariant && selectedVals.length > 0) {
                matchedVariant = variantsData.find(v => selectedVals.every(sv => v.label.includes(sv)));
            }
            if (!matchedVariant && selectedVals.length > 0) {
                matchedVariant = variantsData.find(v => v.label.includes(selectedVals[0]));
            }
            if (!matchedVariant && variantsData.length > 0) {
                matchedVariant = variantsData[0];
            }

            if (matchedVariant) {
                // Hidden variant ID input
                const hiddenInput = document.getElementById('selected-variant-id');
                if (hiddenInput) hiddenInput.value = matchedVariant.id;

                // Main Image
                if (matchedVariant.image) {
                    const mainImg = document.getElementById('main-image');
                    if (mainImg) mainImg.src = matchedVariant.image;
                }

                // Price Display
                const priceContainer = document.querySelector('.selected-price');
                if (priceContainer) {
                    if (matchedVariant.sale_price && matchedVariant.sale_price < matchedVariant.price) {
                        priceContainer.innerHTML = `
                            <span class="product-price" style="font-size:1.6rem;">₱${matchedVariant.sale_price.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
                            <span class="product-price-original ms-2">₱${matchedVariant.price.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
                            <span class="badge ms-2" style="background:var(--mb-red);font-size:.8rem;">SALE</span>
                        `;
                    } else {
                        priceContainer.innerHTML = `
                            <span class="product-price" style="font-size:1.6rem;">₱${matchedVariant.price.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
                        `;
                    }
                }

                // Stock Badge Display
                const stockContainer = document.getElementById('stock-badge-container');
                const submitBtn = document.getElementById('btn-add-to-cart');
                const stock = matchedVariant.stock;

                if (stockContainer) {
                    if (stock > 10) {
                        stockContainer.innerHTML = `<span class="badge bg-success" style="font-size:.85rem;padding:6px 12px;"><i class="bi bi-check-circle me-1"></i> In Stock (${stock} units available)</span>`;
                        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Add To Cart'; }
                    } else if (stock > 0) {
                        stockContainer.innerHTML = `<span class="badge bg-warning text-dark" style="font-size:.85rem;padding:6px 12px;"><i class="bi bi-exclamation-triangle me-1"></i> Low Stock (Only ${stock} left!)</span>`;
                        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Add To Cart'; }
                    } else {
                        stockContainer.innerHTML = `<span class="badge bg-danger" style="font-size:.85rem;padding:6px 12px;"><i class="bi bi-x-circle me-1"></i> Out of Stock (0 items)</span>`;
                        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = 'Out of Stock'; }
                    }
                }
            }
        });
    });
});
</script>
@endsection
