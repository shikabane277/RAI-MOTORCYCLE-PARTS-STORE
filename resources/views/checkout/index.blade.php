@extends('layouts.app')
@section('title', 'Checkout — RAI MOTORCYCLE PARTS')

@section('content')
<div class="container-xl py-4">
    <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:1rem;">
        <i class="bi bi-bag-check text-gold me-2"></i>Checkout
    </h1>

    {{-- Lalamove Dispatch Banner --}}
    @if(isset($lalamoveWindow))
    <div class="mb-4 p-3 d-flex align-items-center gap-3" style="background:{{ $lalamoveWindow['is_active'] ? 'rgba(0,200,83,0.1)' : 'rgba(245,166,35,0.1)' }};border:1px solid {{ $lalamoveWindow['is_active'] ? 'rgba(0,200,83,0.3)' : 'rgba(245,166,35,0.3)' }};border-radius:var(--mb-radius-sm);">
        <div style="font-size:1.5rem;">🛵</div>
        <div>
            <div style="font-weight:700;color:{{ $lalamoveWindow['is_active'] ? 'var(--mb-green)' : 'var(--mb-gold)' }};font-size:.95rem;">
                {{ $lalamoveWindow['message'] }}
            </div>
            <div style="font-size:.82rem;color:var(--mb-muted);">
                {{ $lalamoveWindow['subtext'] }}
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        {{-- ── Left: Form ─────────────────────────────────── --}}
        <div class="col-lg-7">
            <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
                @csrf

                {{-- Shipping Address --}}
                <div class="dark-card p-4 mb-4">
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1.25rem;">
                        <i class="bi bi-geo-alt text-gold me-2"></i>Shipping Address
                    </h2>

                    @auth
                    @if(isset($defaultAddress))
                    <div class="mb-3 p-3" style="background:rgba(245,166,35,0.07);border:1px solid rgba(245,166,35,0.2);border-radius:var(--mb-radius-sm);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="font-size:.9rem;">
                                <div style="font-weight:600;color:var(--mb-text);">{{ $defaultAddress->recipient_name }}</div>
                                <div style="color:var(--mb-muted);">{{ $defaultAddress->line1 }}, Brgy. {{ $defaultAddress->barangay }}, {{ $defaultAddress->city }}, {{ $defaultAddress->province }}</div>
                                <div style="color:var(--mb-muted);">{{ $defaultAddress->phone }}</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-dark-surface" onclick="document.getElementById('addr-fields').classList.toggle('d-none')">
                                Change
                            </button>
                        </div>
                        {{-- Hidden inputs from default address --}}
                        <input type="hidden" name="recipient_name" value="{{ $defaultAddress->recipient_name }}">
                        <input type="hidden" name="phone" value="{{ $defaultAddress->phone }}">
                        <input type="hidden" name="line1" value="{{ $defaultAddress->line1 }}">
                        <input type="hidden" name="barangay" value="{{ $defaultAddress->barangay }}">
                        <input type="hidden" name="city" value="{{ $defaultAddress->city }}">
                        <input type="hidden" name="province" value="{{ $defaultAddress->province }}">
                        <input type="hidden" name="region" value="{{ $defaultAddress->region }}">
                        <input type="hidden" name="zip_code" value="{{ $defaultAddress->zip_code }}">
                    </div>
                    @endif
                    @endauth

                    <div id="addr-fields" class="{{ isset($defaultAddress) ? 'd-none' : '' }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="recipient_name" class="form-control" value="{{ old('recipient_name', auth()->user()?->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="phone" class="form-control" placeholder="09XX XXX XXXX" value="{{ old('phone', auth()->user()?->phone) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Street / House No. / Building *</label>
                                <input type="text" name="line1" class="form-control" value="{{ old('line1') }}" placeholder="123 Rizal St, Unit 2B" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Barangay *</label>
                                <input type="text" name="barangay" class="form-control" value="{{ old('barangay') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City / Municipality *</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Province *</label>
                                <input type="text" name="province" class="form-control" value="{{ old('province') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ZIP Code</label>
                                <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}" placeholder="1000">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Region</label>
                                <input type="text" name="region" class="form-control" value="{{ old('region') }}" placeholder="NCR">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Notes --}}
                <div class="dark-card p-4 mb-4">
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1rem;">
                        <i class="bi bi-chat-text text-gold me-2"></i>Order Notes <span style="color:var(--mb-muted);font-weight:400;font-size:.9rem;">(Optional)</span>
                    </h2>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Special instructions, color preferences, etc.">{{ old('notes') }}</textarea>
                </div>

                {{-- Payment Method --}}
                <div class="dark-card p-4">
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1.25rem;">
                        <i class="bi bi-wallet2 text-gold me-2"></i>Payment Method
                    </h2>
                    <div class="d-flex flex-column gap-2">
                        @foreach([
                            ['cod', '&#x1F4B5;', 'Cash on Delivery (COD)', 'Pay cash when your order arrives at your doorstep.'],
                            ['qrph', '&#x1F4F3;', 'Online Payment (GCash / Maya / Card / QR Ph)', 'Pay securely using GCash, Maya, Visa/Mastercard, or QR Ph via PayMongo.'],
                        ] as [$val, $icon, $label, $desc])
                        <label class="d-flex align-items-start gap-3 p-3 dark-card-hover" style="cursor:pointer;border-radius:var(--mb-radius-sm);border:1px solid var(--mb-border);">
                            <input type="radio" name="payment_method" value="{{ $val }}" class="form-check-input mt-1" {{ old('payment_method', 'cod') === $val ? 'checked' : '' }} required>
                            <div>
                                <div style="font-weight:600;color:var(--mb-text);">{!! $icon !!} {{ $label }}</div>
                                <div style="font-size:.82rem;color:var(--mb-muted);">{{ $desc }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        {{-- ── Right: Summary ──────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="dark-card p-4 sticky-top" style="top:80px;">
                <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1.25rem;">Your Order</h3>

                {{-- Items --}}
                <div class="checkout-items mb-3" style="max-height:240px;overflow-y:auto;">
                    @foreach($cart->items as $item)
                    @php $v = $item->variant; @endphp
                    <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid var(--mb-border);">
                        <div style="width:40px;height:40px;background:var(--mb-surface);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            @if($v->image_url)<img src="{{ $v->image_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">@else🔩@endif
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div style="font-size:.85rem;font-weight:600;color:var(--mb-text);" class="text-truncate">{{ $v->product->name }}</div>
                            <div style="font-size:.75rem;color:var(--mb-muted);">{{ $v->color }} &bull; {{ $v->thread_size ?? $v->material }} &bull; x{{ $item->qty }}</div>
                        </div>
                        <div style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);font-size:.95rem;flex-shrink:0;">
                            &#x20B1;{{ number_format($v->effective_price * $item->qty, 2) }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr class="divider-gold">

                <div class="d-flex justify-content-between mb-1" style="font-size:.9rem;">
                    <span style="color:var(--mb-muted);">Subtotal</span>
                    <span>&#x20B1;{{ number_format($cart->subtotal, 2) }}</span>
                </div>
                @if($discount > 0)
                <div class="d-flex justify-content-between mb-1" style="font-size:.9rem;">
                    <span style="color:var(--mb-green);">Coupon ({{ $coupon->code }})</span>
                    <span style="color:var(--mb-green);">-&#x20B1;{{ number_format($discount, 2) }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-3" style="font-size:.9rem;">
                    <span style="color:var(--mb-muted);">Shipping</span>
                    <span>{{ $shippingFee === 0 ? 'FREE' : '₱'.number_format($shippingFee,2) }}</span>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <span style="font-weight:700;">Grand Total</span>
                    <span class="product-price" style="font-size:1.4rem;" id="order-grand-total">&#x20B1;{{ number_format($cart->subtotal + $shippingFee - $discount, 2) }}</span>
                </div>

                <button type="button" id="btn-place-order" class="btn btn-gold w-100 py-2" onclick="handleCheckoutSubmit()">
                    <i class="bi bi-lock me-1"></i>Place Order
                </button>
                <p class="text-center mt-2" style="font-size:.75rem;color:var(--mb-muted);">
                    <i class="bi bi-shield-check me-1"></i>Your info is secured and encrypted
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function handleCheckoutSubmit() {
    const form = document.getElementById('checkout-form');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    // Disable button to prevent double-clicks
    const btn = document.getElementById('btn-place-order');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Processing...';
    form.submit();
}
</script>
@endsection

