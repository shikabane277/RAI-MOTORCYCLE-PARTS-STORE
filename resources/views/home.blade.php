@extends('layouts.app')

@section('title', 'RAI MOTORCYCLE PARTS — Precision CNC Parts for Filipino Riders')
@section('meta_description', 'Shop CNC-machined motorcycle bolts, fasteners, levers, foot pegs, and accessories. Premium titanium and anodized aluminum hardware. Fast PH shipping.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     HERO CAROUSEL
═══════════════════════════════════════════════════════════ --}}
<div class="hero-carousel swiper hero-swiper">
    <div class="swiper-wrapper">
        @forelse($banners as $banner)
        <div class="swiper-slide">
            <div class="hero-slide" style="{{ $banner->image_url && file_exists(public_path(ltrim($banner->image_url,'/'))) ? 'background-image:url('.$banner->image_url.');background-size:cover;background-position:center;' : '' }}">
                <div style="position:absolute;inset:0;background:linear-gradient(90deg,rgba(13,13,15,.92) 0%,rgba(13,13,15,.5) 60%,transparent 100%);"></div>
                <div class="container-xl hero-slide-content">
                    <div class="col-lg-6">
                        <div class="hero-eyebrow mb-2">RAI MOTORCYCLE PARTS</div>
                        <h1 class="hero-title mb-3">{!! nl2br(e($banner->title)) !!}</h1>
                        @if($banner->subtitle)
                            <p style="color:rgba(232,232,240,.75);font-size:1.05rem;max-width:480px;margin-bottom:2rem;">{{ $banner->subtitle }}</p>
                        @endif
                        @if($banner->link_url)
                            <a href="{{ $banner->link_url }}" class="btn btn-gold px-4 py-2 me-3">
                                {{ $banner->button_text ?? 'Shop Now' }} <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        @endif
                        <a href="{{ route('fitment.index') }}" class="btn btn-outline-gold px-4 py-2">
                            <i class="bi bi-motorcycle me-1"></i>Find Parts for My Bike
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        {{-- Fallback hero --}}
        <div class="swiper-slide">
            <div class="hero-slide">
                <div class="container-xl hero-slide-content">
                    <div class="col-lg-6">
                        <div class="hero-eyebrow mb-2">RAI MOTORCYCLE PARTS</div>
                        <h1 class="hero-title mb-3">Precision CNC Parts for <span>Filipino Riders</span></h1>
                        <p style="color:rgba(232,232,240,.75);font-size:1.05rem;max-width:480px;margin-bottom:2rem;">
                            Titanium &amp; anodized aluminum hardware — built for the streets and the track.
                        </p>
                        <a href="{{ route('shop.index') }}" class="btn btn-gold px-4 py-2 me-3">Shop Now <i class="bi bi-arrow-right ms-1"></i></a>
                        <a href="{{ route('fitment.index') }}" class="btn btn-outline-gold px-4 py-2"><i class="bi bi-motorcycle me-1"></i>Fitment Finder</a>
                    </div>
                </div>
            </div>
        </div>
        @endforelse
    </div>
    <div class="swiper-pagination" style="bottom:24px;"></div>
    <div class="swiper-button-next" style="color:var(--mb-gold)!important;"></div>
    <div class="swiper-button-prev" style="color:var(--mb-gold)!important;"></div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SHOP BY MOTORCYCLE — Fitment Finder Widget
═══════════════════════════════════════════════════════════ --}}
<section class="py-4" style="background:var(--mb-surface);border-bottom:1px solid var(--mb-border);">
    <div class="container-xl">
        <div class="fitment-widget">
            <div class="row align-items-center g-3">
                <div class="col-lg-3">
                    <div class="section-label">Find Your Fit</div>
                    <h2 class="section-title mb-0" style="font-size:1.4rem;">Shop by Motorcycle</h2>
                    <p style="color:var(--mb-muted);font-size:.85rem;margin-top:.3rem;">Select your bike to see compatible parts</p>
                </div>
                <div class="col-lg-7">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <select id="fitment-make" class="fitment-select">
                                <option value="">— Select Make —</option>
                                @php $makes = \App\Models\MotorcycleModel::where('is_active',true)->distinct()->orderBy('make')->pluck('make'); @endphp
                                @foreach($makes as $make)
                                    <option value="{{ $make }}" {{ session('fitment.make') === $make ? 'selected' : '' }}>{{ $make }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="fitment-model" class="fitment-select" {{ session('fitment') ? '' : 'disabled' }}>
                                <option value="">— Select Model —</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="fitment-year" class="fitment-select" {{ session('fitment') ? '' : 'disabled' }}>
                                <option value="">— Select Year —</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <button id="fitment-submit" class="btn btn-gold w-100" disabled>
                        <i class="bi bi-search me-1"></i>Find Parts
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     CATEGORY ICON GRID
═══════════════════════════════════════════════════════════ --}}
<section class="py-5">
    <div class="container-xl">
        <div class="text-center mb-4">
            <div class="section-label">Browse By Category</div>
            <h2 class="section-title">What Are You Looking For?</h2>
        </div>
        <div class="row g-3 stagger-children">
            @foreach($categories as $i => $cat)
            <div class="col-6 col-md-4 col-lg-3 fade-up" style="--i:{{ $i }}">
                <a href="{{ route('shop.category', $cat->slug) }}" class="cat-icon-card">
                    <span class="cat-icon">{{ $cat->icon }}</span>
                    <div class="cat-name">{{ $cat->name }}</div>
                    <div style="font-size:.75rem;color:var(--mb-muted);margin-top:.25rem;">{{ $cat->products()->count() }} products</div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     DEALS RAIL
═══════════════════════════════════════════════════════════ --}}
@if($deals->count())
<section class="py-5" style="background:var(--mb-surface);">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <div class="section-label">Limited Time</div>
                <h2 class="section-title mb-0">&#x1F525; Bolt Kit Deals</h2>
                <div class="section-divider"></div>
            </div>
            <a href="{{ route('shop.index') }}?sort=sale" class="btn btn-outline-gold btn-sm">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="swiper product-swiper">
            <div class="swiper-wrapper">
                @foreach($deals as $product)
                <div class="swiper-slide">
                    @include('partials.product-card', ['product' => $product])
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════
     NEW ARRIVALS
═══════════════════════════════════════════════════════════ --}}
@if($newArrivals->count())
<section class="py-5">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <div class="section-label">Just Dropped</div>
                <h2 class="section-title mb-0">&#x2728; New Arrivals</h2>
                <div class="section-divider"></div>
            </div>
            <a href="{{ route('shop.index') }}?new=1" class="btn btn-outline-gold btn-sm">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="swiper product-swiper">
            <div class="swiper-wrapper">
                @foreach($newArrivals as $product)
                <div class="swiper-slide">
                    @include('partials.product-card', ['product' => $product])
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════
     BEST SELLERS
═══════════════════════════════════════════════════════════ --}}
@if($bestSellers->count())
<section class="py-5" style="background:var(--mb-surface);">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <div class="section-label">Rider Favorites</div>
                <h2 class="section-title mb-0">&#x1F3C6; Best Sellers</h2>
                <div class="section-divider"></div>
            </div>
            <a href="{{ route('shop.index') }}?sort=best" class="btn btn-outline-gold btn-sm">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            @foreach($bestSellers->take(8) as $i => $product)
            <div class="col-6 col-md-4 col-lg-3 fade-up" style="--i:{{ $i }}">
                @include('partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════
     TRUST STRIP
═══════════════════════════════════════════════════════════ --}}
<section class="trust-strip">
    <div class="container-xl">
        <div class="row text-center">
            <div class="col-6 col-md-3">
                <div class="trust-item"><i class="bi bi-shield-check"></i><strong>Precision Guarantee</strong><span>CNC machined to spec</span></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-item"><i class="bi bi-truck"></i><strong>Fast Nationwide</strong><span>J&amp;T &amp; Ninja Van</span></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-item"><i class="bi bi-wallet2"></i><strong>GCash &amp; Maya</strong><span>COD also available</span></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-item"><i class="bi bi-headset"></i><strong>Rider Support</strong><span>Chat &amp; Viber support</span></div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     REVIEWS WALL
═══════════════════════════════════════════════════════════ --}}
@if($reviews->count())
<section class="py-5">
    <div class="container-xl">
        <div class="text-center mb-5">
            <div class="section-label">What Riders Say</div>
            <h2 class="section-title">Verified Reviews</h2>
            <p style="color:var(--mb-muted);">Real feedback from real riders across the Philippines</p>
        </div>
        <div class="row g-3">
            @foreach($reviews as $i => $review)
            <div class="col-md-6 col-lg-4 fade-up" style="--i:{{ $i }}">
                <div class="review-card h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="reviewer-avatar">{{ substr($review->user?->name ?? 'A', 0, 1) }}</div>
                        <div>
                            <div style="font-weight:600;font-size:.9rem;color:var(--mb-text);">{{ $review->user?->name ?? 'Anonymous' }}</div>
                            @if($review->bike_model)
                                <div style="font-size:.75rem;color:var(--mb-muted);">&#x1F3CD;&#xFE0F; {{ $review->bike_model }}</div>
                            @endif
                        </div>
                        <div class="ms-auto">
                            <div class="stars">
                                @for($s = 1; $s <= 5; $s++)
                                    <i class="bi bi-star{{ $s <= $review->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <p style="color:var(--mb-text);font-size:.9rem;line-height:1.6;margin:0;">{{ $review->comment }}</p>
                    @if($review->product)
                        <div class="mt-2" style="font-size:.75rem;color:var(--mb-muted);">On: <a href="{{ route('product.show', $review->product->slug) }}" style="color:var(--mb-gold);">{{ Str::limit($review->product->name, 40) }}</a></div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════
     SEO CONTENT BLOCKS
═══════════════════════════════════════════════════════════ --}}
<section class="py-5" style="background:var(--mb-surface);border-top:1px solid var(--mb-border);">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <h3 style="font-family:'Rajdhani',sans-serif;font-size:1rem;color:var(--mb-gold);margin-bottom:.75rem;">About RAI MOTORCYCLE PARTS</h3>
                <p style="color:var(--mb-muted);font-size:.85rem;line-height:1.7;">
                    We're a Filipino-owned store specializing in CNC-machined motorcycle fasteners and accessories. Every bolt is precision-machined and individually inspected before shipping.
                </p>
            </div>
            <div class="col-md-6 col-lg-3">
                <h3 style="font-family:'Rajdhani',sans-serif;font-size:1rem;color:var(--mb-gold);margin-bottom:.75rem;">Shipping &amp; Delivery</h3>
                <p style="color:var(--mb-muted);font-size:.85rem;line-height:1.7;">
                    Metro Manila orders placed before 12NN ship same day. Nationwide delivery via J&amp;T Express and Ninja Van. Flat-rate shipping — free on orders &#x20B1;1,500+.
                </p>
            </div>
            <div class="col-md-6 col-lg-3">
                <h3 style="font-family:'Rajdhani',sans-serif;font-size:1rem;color:var(--mb-gold);margin-bottom:.75rem;">Payment Security</h3>
                <p style="color:var(--mb-muted);font-size:.85rem;line-height:1.7;">
                    Pay securely via GCash, Maya, Bank Transfer, or Cash on Delivery. We never store your payment credentials. All transactions are encrypted end-to-end.
                </p>
            </div>
            <div class="col-md-6 col-lg-3">
                <h3 style="font-family:'Rajdhani',sans-serif;font-size:1rem;color:var(--mb-gold);margin-bottom:.75rem;">Rewards Program</h3>
                <p style="color:var(--mb-muted);font-size:.85rem;line-height:1.7;">
                    Earn loyalty points on every completed order. Redeem them for discounts on future purchases. Registered riders earn 5% back in points automatically.
                </p>
            </div>
        </div>
    </div>
</section>

@endsection
