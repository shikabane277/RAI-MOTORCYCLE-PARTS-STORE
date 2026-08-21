@extends('layouts.app')
@section('title', 'Your Cart — RAI MOTORCYCLE PARTS')

@section('content')
<div class="container-xl py-4">
    <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:var(--mb-heading);margin-bottom:1.5rem;">
        <i class="bi bi-bag text-gold me-2"></i>Your Cart
    </h1>

    @if($cart->items->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:4rem;">&#x1F6D2;</div>
        <h2 style="font-family:'Rajdhani',sans-serif;color:var(--mb-muted);">Your cart is empty</h2>
        <a href="{{ route('shop.index') }}" class="btn btn-gold mt-3">Browse Products</a>
    </div>
    @else
    <div class="row g-4">
        {{-- ── Items ──────────────────────────────────────── --}}
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-3">
                @foreach($cart->items as $item)
                @php $variant = $item->variant; $product = $variant->product; @endphp
                <div class="cart-item d-flex flex-wrap flex-sm-nowrap gap-3 align-items-start">
                    {{-- Image --}}
                    <a href="{{ route('product.show', $product->slug) }}"
                       style="width:80px;height:80px;flex-shrink:0;background:var(--mb-surface);border-radius:var(--mb-radius-sm);overflow:hidden;display:flex;align-items:center;justify-content:center;">
                        @if($variant->image_url)
                            <img src="{{ $variant->image_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <span style="font-size:2rem;">&#x1F529;</span>
                        @endif
                    </a>
                    {{-- Info --}}
                    <div class="flex-grow-1" style="min-width:180px;">
                        <div class="product-brand">{{ $product->brand?->name ?? 'RAI' }}</div>
                        <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none">
                            <div style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;font-weight:600;color:var(--mb-text);">{{ $product->name }}</div>
                        </a>
                        <div style="font-size:.8rem;color:var(--mb-muted);">
                            {{ $variant->variant_name ?: $variant->color }}
                        </div>
                        <div style="font-size:.75rem;color:var(--mb-muted);">SKU: {{ $variant->variant_sku }}</div>
                        {{-- Qty + remove (mobile) --}}
                        <div class="d-flex align-items-center gap-3 mt-2 flex-wrap">
                            <form method="POST" action="{{ route('cart.update', $item->id) }}">
                                @csrf @method('PATCH')
                                <div class="qty-control">
                                    <button type="button" class="qty-btn" data-action="minus"><i class="bi bi-dash"></i></button>
                                    <input type="number" name="qty" class="qty-input" value="{{ $item->qty }}" min="1" max="99"
                                           onchange="this.closest('form').submit()">
                                    <button type="button" class="qty-btn" data-action="plus"><i class="bi bi-plus"></i></button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="color:var(--mb-red);background:none;border:none;font-size:.8rem;">
                                    <i class="bi bi-trash me-1"></i>Remove
                                </button>
                            </form>
                        </div>
                    </div>
                    {{-- Price --}}
                    <div class="text-end ms-auto" style="flex-shrink:0;">
                        @if($variant->is_on_sale)
                            <div class="product-price">&#x20B1;{{ number_format($variant->sale_price * $item->qty, 2) }}</div>
                            <div style="font-size:.8rem;color:var(--mb-muted);text-decoration:line-through;">&#x20B1;{{ number_format($variant->price * $item->qty, 2) }}</div>
                        @else
                            <div class="product-price">&#x20B1;{{ number_format($variant->effective_price * $item->qty, 2) }}</div>
                        @endif
                        <div style="font-size:.75rem;color:var(--mb-muted);">&#x20B1;{{ number_format($variant->effective_price, 2) }} ea.</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Coupon form --}}
            <div class="dark-card p-3 mt-3 d-flex gap-2 align-items-start">
                <form method="POST" action="{{ route('cart.coupon') }}" class="d-flex gap-2 flex-grow-1">
                    @csrf
                    <input type="text" name="code" class="form-control" placeholder="Enter coupon code" value="{{ $cart->coupon_code ?? '' }}">
                    <button type="submit" class="btn btn-outline-gold btn-sm px-3">Apply</button>
                </form>
                @if($cart->coupon_code)
                <form method="POST" action="{{ route('cart.coupon.remove') }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm" style="color:var(--mb-red);background:none;border:none;"><i class="bi bi-x-circle"></i></button>
                </form>
                @endif
            </div>

            <div class="mt-3">
                <a href="{{ route('shop.index') }}" class="btn btn-dark-surface btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Continue Shopping
                </a>
            </div>
        </div>

        {{-- ── Summary ─────────────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="dark-card p-4 sticky-top" style="top:80px;">
                <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.2rem;font-weight:700;color:var(--mb-heading);margin-bottom:1.25rem;">Order Summary</h3>
                <div class="d-flex justify-content-between mb-2" style="font-size:.9rem;">
                    <span style="color:var(--mb-muted);">Subtotal</span>
                    <span>&#x20B1;{{ number_format($cart->subtotal, 2) }}</span>
                </div>
                @if($discount > 0)
                <div class="d-flex justify-content-between mb-2" style="font-size:.9rem;">
                    <span style="color:var(--mb-green);">Discount ({{ $coupon->code }})</span>
                    <span style="color:var(--mb-green);">-&#x20B1;{{ number_format($discount, 2) }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-2" style="font-size:.9rem;">
                    <span style="color:var(--mb-muted);">Shipping</span>
                    @if($shippingFee === 0)
                        <span style="color:var(--mb-green);">FREE</span>
                    @else
                        <span>&#x20B1;{{ number_format($shippingFee, 2) }}</span>
                    @endif
                </div>
                @if($cart->subtotal < 1500)
                <div class="mb-2" style="background:var(--mb-gold-dim);border-radius:6px;padding:.5rem .75rem;font-size:.8rem;color:var(--mb-gold);">
                    Add &#x20B1;{{ number_format(1500 - $cart->subtotal, 2) }} more for free shipping!
                </div>
                @endif
                <hr class="divider-gold my-3">
                <div class="d-flex justify-content-between mb-4">
                    <span style="font-weight:700;font-size:1rem;">Total</span>
                    <span class="product-price" style="font-size:1.4rem;">&#x20B1;{{ number_format($cart->subtotal + $shippingFee - $discount, 2) }}</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="btn btn-gold w-100 py-2 mb-2">
                    Proceed to Checkout <i class="bi bi-arrow-right ms-1"></i>
                </a>
                <div class="d-flex justify-content-center gap-2 flex-wrap mt-2" style="font-size:.75rem;color:var(--mb-muted);">
                    @foreach(['Google Pay','GCash','Maya','COD','Bank Transfer'] as $pm)
                    <span style="background:var(--mb-surface);border:1px solid var(--mb-border);border-radius:4px;padding:.15rem .5rem;">{{ $pm }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
