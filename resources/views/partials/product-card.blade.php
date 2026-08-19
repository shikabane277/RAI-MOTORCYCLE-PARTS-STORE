@php
    $firstVariant = $product->variants->first();
    $saleVariant  = $product->variants->firstWhere('sale_price', '!=', null);
    $displayVariant = $saleVariant ?? $firstVariant;
    $isSale = $displayVariant && $displayVariant->is_on_sale;
    $price  = $displayVariant?->price ?? $product->base_price;
    $salePrice = $displayVariant?->sale_price;
    $fitsCount = $product->relationLoaded('motorcycleModels') ? $product->motorcycleModels->count() : 0;
    $fitSession = session('fitment');
@endphp
<div class="product-card h-100 d-flex flex-column">
    {{-- Badges --}}
    @if($isSale)
        @php $savePct = round((($price - $salePrice) / $price) * 100); @endphp
        <div class="badge-sale">SAVE {{ $savePct }}%</div>
    @elseif($product->is_new_arrival)
        <div class="badge-new">NEW</div>
    @endif

    {{-- Wishlist button --}}
    @auth
    <button class="btn-wishlist" data-variant="{{ $firstVariant?->id }}" title="Add to wishlist">
        <i class="bi bi-heart{{ auth()->user()->wishlists()->where('product_variant_id', $firstVariant?->id)->exists() ? '-fill active' : '' }}"></i>
    </button>
    @endauth

    {{-- Image --}}
    <a href="{{ route('product.show', $product->slug) }}" class="product-card-img-wrap">
        @if($displayVariant?->image_url)
            <img src="{{ $displayVariant->image_url }}" alt="{{ $product->name }}" class="product-card-img" loading="lazy">
        @else
            {{-- Placeholder with gradient --}}
            <div class="product-card-img d-flex align-items-center justify-content-center"
                 style="background:linear-gradient(135deg,var(--mb-surface),var(--mb-card));font-size:3rem;">
                &#x1F529;
            </div>
        @endif
    </a>

    {{-- Body --}}
    <div class="product-card-body d-flex flex-column flex-grow-1">
        <div class="product-brand">{{ $product->brand?->name ?? 'MachBolt' }}</div>
        <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none">
            <div class="product-name">{{ $product->name }}</div>
        </a>

        {{-- Stars --}}
        @php $rating = $product->average_rating; $count = $product->review_count; @endphp
        @if($count > 0)
        <div class="d-flex align-items-center gap-1 mb-1">
            <div class="stars">
                @for($s = 1; $s <= 5; $s++)
                    <i class="bi bi-star{{ $s <= round($rating) ? '-fill' : '' }}"></i>
                @endfor
            </div>
            <span style="font-size:.72rem;color:var(--mb-muted);">({{ $count }})</span>
        </div>
        @endif

        {{-- Fitment chip --}}
        @if($fitsCount > 0)
        <div class="mb-2">
            <span class="fits-chip"><i class="bi bi-motorcycle"></i>Fits {{ $fitsCount }} model{{ $fitsCount > 1 ? 's' : '' }}</span>
        </div>
        @endif

        {{-- Colors --}}
        @php $colors = $product->variants->pluck('color')->unique()->values(); @endphp
        @if($colors->count() > 1)
        <div class="d-flex gap-1 mb-2" style="flex-wrap:wrap;">
            @foreach($colors->take(6) as $color)
                <div class="color-swatch swatch-{{ strtolower($color) }}" title="{{ $color }}"
                     style="width:18px;height:18px;border-radius:50%;cursor:default;"
                     title="{{ $color }}"></div>
            @endforeach
            @if($colors->count() > 6)
                <span style="font-size:.7rem;color:var(--mb-muted);line-height:18px;">+{{ $colors->count() - 6 }}</span>
            @endif
        </div>
        @endif

        <div class="mt-auto">
            {{-- Price --}}
            <div class="d-flex align-items-center gap-2 mb-2">
                @if($isSale && $salePrice)
                    <span class="product-price">&#x20B1;{{ number_format($salePrice, 2) }}</span>
                    <span class="product-price-original">&#x20B1;{{ number_format($price, 2) }}</span>
                @else
                    <span class="product-price">&#x20B1;{{ number_format($price, 2) }}</span>
                @endif
            </div>

            {{-- Add to Cart --}}
            @if($firstVariant && $firstVariant->is_in_stock)
            <form class="ajax-add-to-cart" method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="variant_id" value="{{ $firstVariant->id }}">
                <input type="hidden" name="qty" value="1">
                <button type="submit" class="btn btn-gold btn-sm w-100">
                    <i class="bi bi-bag-plus me-1"></i>Add to Cart
                </button>
            </form>
            @else
            <button class="btn btn-dark-surface btn-sm w-100" disabled>
                <i class="bi bi-x-circle me-1"></i>Out of Stock
            </button>
            @endif
        </div>
    </div>
</div>
