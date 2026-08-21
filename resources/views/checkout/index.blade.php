@extends('layouts.app')
@section('title', 'Checkout — RAI MOTORCYCLE PARTS')

@section('content')
<div class="container-xl py-4">
    <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:var(--mb-heading);margin-bottom:1rem;">
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
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--mb-heading);margin-bottom:1.25rem;">
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
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0">Street / House No. / Building *</label>
                                    <button type="button" class="btn btn-link text-gold p-0 text-decoration-none fw-bold" id="btn-toggle-manual-addr" style="font-size:.82rem;">
                                        <i class="bi bi-pencil-square me-1"></i><span id="manual-toggle-label">Type Address Manually</span>
                                    </button>
                                </div>
                                <input type="text" name="line1" class="form-control" value="{{ old('line1') }}" placeholder="123 Rizal St, Unit 2B" required>
                            </div>

                            {{-- Dropdown Container --}}
                            <div id="address-dropdown-wrapper" class="row g-3 col-12 m-0 p-0">
                                <div class="col-md-6">
                                    <label class="form-label">Province / Region *</label>
                                    <select name="province" id="ph-province" class="form-select" required>
                                        <option value="">— Loading Provinces &amp; Regions... —</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City / Municipality *</label>
                                    <select name="city" id="ph-city" class="form-select" required disabled>
                                        <option value="">— Select City / Municipality —</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Barangay *</label>
                                    <select name="barangay" id="ph-barangay" class="form-select" required disabled>
                                        <option value="">— Select Barangay —</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">ZIP Code</label>
                                    <input type="text" name="zip_code" id="ph-zip" class="form-control" value="{{ old('zip_code') }}" placeholder="e.g. 1100" style="font-weight:700;color:var(--mb-gold);">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Region</label>
                                    <input type="text" name="region" id="ph-region" class="form-control" value="{{ old('region', 'Luzon') }}" placeholder="NCR / Region" readonly style="background:var(--mb-surface);">
                                </div>
                            </div>

                            {{-- Manual Text Input Wrapper (Initially Hidden) --}}
                            <div id="address-manual-wrapper" class="row g-3 col-12 m-0 p-0 d-none">
                                <div class="col-md-6">
                                    <label class="form-label">Province / Region *</label>
                                    <input type="text" name="province_manual" id="ph-province-manual" class="form-control" placeholder="e.g. Metro Manila / Cavite / Cebu" value="{{ old('province') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City / Municipality *</label>
                                    <input type="text" name="city_manual" id="ph-city-manual" class="form-control" placeholder="e.g. Quezon City / Bacoor / Cebu City" value="{{ old('city') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Barangay *</label>
                                    <input type="text" name="barangay_manual" id="ph-barangay-manual" class="form-control" placeholder="e.g. Barangay Commonwealth / Brgy. 101" value="{{ old('barangay') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">ZIP Code</label>
                                    <input type="text" name="zip_code_manual" id="ph-zip-manual" class="form-control" placeholder="e.g. 1100" value="{{ old('zip_code') }}" style="font-weight:700;color:var(--mb-gold);">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Region / Island</label>
                                    <input type="text" name="region_manual" id="ph-region-manual" class="form-control" placeholder="Luzon / Visayas / Mindanao" value="{{ old('region', 'Luzon') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delivery Service / Shipping Method --}}
                <div class="dark-card p-4 mb-4">
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--mb-heading);margin-bottom:1.25rem;">
                        <i class="bi bi-truck text-gold me-2"></i>Delivery Courier &amp; Service
                    </h2>

                    @error('shipping_method')
                        <div class="alert alert-danger p-2 mb-3" style="font-size:.85rem;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                        </div>
                    @enderror

                    <div class="d-flex flex-column gap-3">
                        @php
                            $isLalamoveActive = $lalamoveWindow['is_active'] ?? false;
                            $defaultShipping = $isLalamoveActive ? 'same_day' : 'standard';
                            $selectedShipping = old('shipping_method', $defaultShipping);
                        @endphp

                        {{-- 🛵 Same-Day Shipping (Lalamove) --}}
                        <div class="d-flex flex-column">
                            <label class="d-flex align-items-start gap-3 p-3 dark-card-hover {{ !$isLalamoveActive ? 'opacity-50' : '' }}" 
                                   style="cursor:{{ $isLalamoveActive ? 'pointer' : 'not-allowed' }};border-radius:var(--mb-radius-sm);border:1px solid {{ $selectedShipping === 'same_day' && $isLalamoveActive ? 'var(--mb-gold)' : 'var(--mb-border)' }};background:{{ $selectedShipping === 'same_day' && $isLalamoveActive ? 'rgba(245,166,35,0.05)' : 'transparent' }};">
                                <input type="radio" name="shipping_method" value="same_day" class="form-check-input mt-1" 
                                       {{ $selectedShipping === 'same_day' && $isLalamoveActive ? 'checked' : '' }} 
                                       {{ !$isLalamoveActive ? 'disabled' : '' }} required>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div style="font-weight:700;color:var(--mb-heading);font-size:.95rem;">
                                            🛵 Lalamove Express (Same-Day Delivery)
                                        </div>
                                        @if($isLalamoveActive)
                                            <span class="badge bg-success text-white" style="font-size:.7rem;">Active (Cutoff 4:00 PM)</span>
                                        @else
                                            <span class="badge bg-secondary text-white" style="font-size:.7rem;">Closed (Outside 8 AM–4 PM)</span>
                                        @endif
                                    </div>
                                    <div style="font-size:.82rem;color:var(--mb-muted);" class="mt-1">
                                        Order now for fast same-day motorcycle dispatch within Metro Manila &amp; nearby areas.
                                    </div>
                                    @if(!$isLalamoveActive)
                                        <div class="mt-1 text-warning fw-bold" style="font-size:.78rem;">
                                            <i class="bi bi-clock-history me-1"></i>Same-day shipping automatically turns off outside 8:00 AM – 4:00 PM operational window.
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>

                        {{-- 📦 Standard Shipping (J&T Express) --}}
                        <div class="d-flex flex-column">
                            <label class="d-flex align-items-start gap-3 p-3 dark-card-hover" 
                                   style="cursor:pointer;border-radius:var(--mb-radius-sm);border:1px solid {{ $selectedShipping === 'standard' ? 'var(--mb-gold)' : 'var(--mb-border)' }};background:{{ $selectedShipping === 'standard' ? 'rgba(245,166,35,0.05)' : 'transparent' }};">
                                <input type="radio" name="shipping_method" value="standard" class="form-check-input mt-1" 
                                       {{ $selectedShipping === 'standard' ? 'checked' : '' }} required>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div style="font-weight:700;color:var(--mb-heading);font-size:.95rem;">
                                            📦 J&amp;T Express (Standard Nationwide Shipping)
                                        </div>
                                        <span class="badge bg-danger text-white fw-bold" style="font-size:.7rem;background:#e30613!important;">Default Courier</span>
                                    </div>
                                    <div style="font-size:.82rem;color:var(--mb-muted);" class="mt-1">
                                        Standard door-to-door courier delivery nationwide across Metro Manila &amp; Provinces (Est. 2-5 business days).
                                    </div>
                                </div>
                            </label>
                        </div>

                    </div>
                </div>

                {{-- Order Notes --}}
                <div class="dark-card p-4 mb-4">
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--mb-heading);margin-bottom:1rem;">
                        <i class="bi bi-chat-text text-gold me-2"></i>Order Notes <span style="color:var(--mb-muted);font-weight:400;font-size:.9rem;">(Optional)</span>
                    </h2>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Special instructions, color preferences, etc.">{{ old('notes') }}</textarea>
                </div>

                {{-- Payment Method --}}
                <div class="dark-card p-4">
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--mb-heading);margin-bottom:1.25rem;">
                        <i class="bi bi-wallet2 text-gold me-2"></i>Payment Method
                    </h2>
                    <div class="d-flex flex-column gap-2">
                        @foreach([
                            ['cod', '&#x1F4B5;', 'Cash on Delivery (COD)', 'Pay cash when your order arrives at your doorstep.'],
                            ['gcash', '<span class="badge text-white me-1" style="background:#007dfe;font-size:0.75rem;padding:3px 7px;font-weight:700;border-radius:4px;">GCash</span>', 'GCash E-Wallet', 'Direct e-wallet payment using your registered GCash mobile number.'],
                            ['google_pay', '<svg width="18" height="18" viewBox="0 0 24 24" class="me-1"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>', 'Google Pay', 'Fast 1-tap payment using your Google Account or saved cards.'],
                            ['qrph', '&#x1F4F2;', 'QR Ph (GCash / Maya / Banks)', 'Scan the QR code with any Philippine banking or e-wallet app — GCash, Maya, BPI, BDO, UnionBank, and more.'],
                        ] as [$val, $icon, $label, $desc])
                        <div class="d-flex flex-column">
                            <label class="d-flex align-items-start gap-3 p-3 dark-card-hover" style="cursor:pointer;border-radius:var(--mb-radius-sm);border:1px solid var(--mb-border);">
                                <input type="radio" name="payment_method" value="{{ $val }}" class="form-check-input mt-1 payment-method-radio" {{ old('payment_method', 'cod') === $val ? 'checked' : '' }} required>
                                <div>
                                    <div style="font-weight:600;color:var(--mb-text);">{!! $icon !!} {{ $label }}</div>
                                    <div style="font-size:.82rem;color:var(--mb-muted);">{{ $desc }}</div>
                                </div>
                            </label>
                            @if($val === 'gcash')
                            <div id="gcash-extra-fields" class="mt-2 p-3 {{ old('payment_method') === 'gcash' ? '' : 'd-none' }}" style="background:rgba(0,125,254,0.08);border:1px solid rgba(0,125,254,0.3);border-radius:var(--mb-radius-sm);">
                                <label class="form-label mb-1" style="font-size:.85rem;font-weight:600;color:var(--mb-heading);">
                                    <i class="bi bi-phone me-1" style="color:#007dfe;"></i>GCash Account Mobile Number *
                                </label>
                                <input type="text" name="gcash_number" id="gcash_number" class="form-control form-control-sm @error('gcash_number') is-invalid @enderror" placeholder="e.g. 09171234567" value="{{ old('gcash_number', auth()->user()?->phone) }}" {{ old('payment_method') === 'gcash' ? 'required' : '' }}>
                                <div class="form-text mt-1" style="font-size:.78rem;color:var(--mb-muted);">
                                    Enter the 11-digit Philippines mobile number linked to your GCash account.
                                </div>
                                @error('gcash_number')
                                    <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        {{-- ── Right: Summary ──────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="dark-card p-4 sticky-top" style="top:80px;">
                <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--mb-heading);margin-bottom:1.25rem;">Your Order</h3>

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

                <button type="submit" id="btn-place-order" class="btn btn-gold w-100 py-3 font-bold fs-6">
                    <i class="bi bi-lock-fill me-1"></i>Place Order Now
                </button>
                <p class="text-center mt-2" style="font-size:.75rem;color:var(--mb-muted);">
                    <i class="bi bi-shield-check me-1"></i>Your info is secured and encrypted
                </p>
            </div>
        </div>
    </div>
</div>

<script>
const phAddressData = {
    "Metro Manila (NCR)": {
        region: "NCR",
        cities: {
            "Manila City": { zip: "1000", barangays: ["Binondo", "Ermita", "Intramuros", "Malate", "Paco", "Pandacan", "Port Area", "Quiapo", "Sampaloc", "San Miguel", "Santa Ana", "Santa Cruz", "Tondo", "Barangay 1 - 900"] },
            "Quezon City": { zip: "1100", barangays: ["Batasan Hills", "Commonwealth", "Cubao", "Diliman", "Eastwood", "Fairview", "Kamuning", "Loyola Heights", "Novaliches", "Project 8", "San Francisco del Monte", "Teachers Village", "Ugong Norte"] },
            "Makati City": { zip: "1200", barangays: ["Bangkal", "Bel-Air", "Cembo", "Dasmariñas", "Forbes Park", "Guadalupe Nuevo", "Guadalupe Viejo", "Magallanes", "Pembo", "Pio del Pilar", "Poblacion", "San Antonio", "San Lorenzo", "Urdaneta"] },
            "Taguig City": { zip: "1630", barangays: ["BGC (Bonifacio Global City)", "Bambang", "Calzada", "Fort Bonifacio", "Pinagsama", "San Miguel", "Signal Village", "Tuktukan", "Ususan", "Western Bicutan"] },
            "Pasig City": { zip: "1600", barangays: ["Caniogan", "Kapitolyo", "Maybunga", "Oranbo", "Pinagbuhatan", "Rosario", "San Antonio", "Santolan", "Ugong"] },
            "Parañaque City": { zip: "1700", barangays: ["Baclaran", "BF Homes", "Don Bosco", "Marcelo Green", "Moonwalk", "San Antonio", "San Dionisio", "Sun Valley", "Tambo"] },
            "Pasay City": { zip: "1300", barangays: ["Malibay", "Newport City", "San Jose", "Villamor Airbase", "Barangay 1 to 201"] },
            "Mandaluyong City": { zip: "1550", barangays: ["Addition Hills", "Barangka", "Highway Hills", "Mauway", "Plainview", "Wack-Wack"] },
            "Las Piñas City": { zip: "1740", barangays: ["Almanza Uno", "Almanza Dos", "BF International", "Pamplona", "Pilar", "Pulang Lupa", "Talon"] },
            "Muntinlupa City": { zip: "1770", barangays: ["Alabang", "Bayanan", "Cupang", "Poblacion", "Putatan", "Sucat", "Tunasan"] },
            "Caloocan City": { zip: "1400", barangays: ["Bagong Barrio", "Bagumbong", "Camarin", "Deparo", "Grace Park", "Tala"] },
            "Valenzuela City": { zip: "1440", barangays: ["Gen. T. de Leon", "Karuhatan", "Malinta", "Marulas", "Paso de Blas", "Poblacion"] },
            "Marikina City": { zip: "1800", barangays: ["Barangka", "Calumpang", "Concepcion I", "Concepcion II", "San Roque", "Sto. Niño"] },
            "San Juan City": { zip: "1500", barangays: ["Addition Hills", "Greenhills", "Kabayanan", "Little Baguio", "Progreso"] }
        }
    },
    "Cavite": {
        region: "Region IV-A (CALABARZON)",
        cities: {
            "Bacoor City": { zip: "4102", barangays: ["Habay", "Mambog", "Molino I", "Molino II", "Molino III", "Molino IV", "Niog", "Panapaan", "San Nicolas"] },
            "Imus City": { zip: "4103", barangays: ["Anabu I", "Anabu II", "Bayan Luma", "Bucandala", "Carsadang Bago", "Malagasang I", "Malagasang II", "Tanzang Lumat"] },
            "Dasmariñas City": { zip: "4114", barangays: ["Burol", "Langkaan", "Paliparan I", "Paliparan II", "Paliparan III", "Salawag", "Salitran", "Sampaloc"] },
            "General Trias City": { zip: "4107", barangays: ["Bacao", "Manggahan", "Navarro", "Pasong Kawayan", "San Francisco", "Tejero"] },
            "Tagaytay City": { zip: "4120", barangays: ["Kaybagal", "Maharlika", "Mendez Crossing", "Silang Junction", "Sungay"] },
            "Silang": { zip: "4118", barangays: ["Biluso", "Bulihan", "Carmen", "Lalaan I", "Lalaan II", "Tartaria", "Tubuan"] },
            "Kawit": { zip: "4104", barangays: ["Binakayan", "Gahak", "Tabon", "Tramo", "Wakas"] }
        }
    },
    "Laguna": {
        region: "Region IV-A (CALABARZON)",
        cities: {
            "Santa Rosa City": { zip: "4026", barangays: ["Balibago", "Dita", "Don Jose", "Malitlit", "Market Area", "Tagapo"] },
            "Biñan City": { zip: "4024", barangays: ["Canlalay", "De La Paz", "Mamplasan", "Platero", "San Francisco", "Tubigan"] },
            "Calamba City": { zip: "4027", barangays: ["Bucal", "Canlubang", "Paciano Rizal", "Palingon", "Pansol", "Real"] },
            "San Pedro City": { zip: "4023", barangays: ["Chrysanthemum", "Cuyab", "Landayan", "Pacita Complex", "San Vicente"] },
            "Los Baños": { zip: "4030", barangays: ["Batong Malake", "College", "Demang", "Mayondon", "San Antonio"] },
            "Cabuyao City": { zip: "4025", barangays: ["Banlic", "Mamatid", "Pulo", "Pundido", "Sala"] }
        }
    },
    "Batangas": {
        region: "Region IV-A (CALABARZON)",
        cities: {
            "Batangas City": { zip: "4200", barangays: ["Alangilan", "Bolbok", "Calicanto", "Gulod Labac", "Kumintang Ibaba", "Kumintang Ilaya"] },
            "Lipa City": { zip: "4217", barangays: ["Balintawak", "Dagatan", "Inosloban", "Marawoy", "Sabang", "Tambo"] },
            "Tanauan City": { zip: "4232", barangays: ["Bagumbayan", "Darasa", "Natatas", "Sambat", "Trapiche"] },
            "Santo Tomas City": { zip: "4234", barangays: ["Poblacion", "San Antonio", "San Rafael", "Santa Anastacia"] }
        }
    },
    "Rizal": {
        region: "Region IV-A (CALABARZON)",
        cities: {
            "Antipolo City": { zip: "1870", barangays: ["Dalig", "Mambugan", "Mayamot", "San Jose", "San Roque", "Santa Cruz"] },
            "Cainta": { zip: "1900", barangays: ["San Andres", "San Juan", "Santo Domingo", "Santa Rosa"] },
            "Taytay": { zip: "1920", barangays: ["Dolores", "Muzon", "San Isidro", "San Juan"] },
            "San Mateo": { zip: "1850", barangays: ["Ampid I", "Ampid II", "Banaba", "Guitnang Bayan", "Silangan"] }
        }
    },
    "Bulacan": {
        region: "Region III (Central Luzon)",
        cities: {
            "Malolos City": { zip: "3000", barangays: ["Catmon", "Dakila", "Guinhawa", "Longos", "Tikay"] },
            "Meycauayan City": { zip: "3020", barangays: ["Banga", "Calvario", "Malhacan", "Pandayan", "Perez"] },
            "San Jose del Monte City": { zip: "3023", barangays: ["Gaya-Gaya", "Kaypian", "Muzon", "Tungkong Mangga"] },
            "Marilao": { zip: "3019", barangays: ["Abangan Sur", "Ibayo", "Poblacion", "Saog"] }
        }
    },
    "Pampanga": {
        region: "Region III (Central Luzon)",
        cities: {
            "San Fernando City": { zip: "2000", barangays: ["Dolores", "Maimpis", "San Jose", "Sindalan", "Telabastagan"] },
            "Angeles City": { zip: "2009", barangays: ["Anunas", "Balibago", "Cutcut", "Malabanias", "Pulung Maragul"] }
        }
    },
    "Cebu": {
        region: "Region VII (Central Visayas)",
        cities: {
            "Cebu City": { zip: "6000", barangays: ["Banilad", "Capitol Site", "Colon", "Guadalupe", "Kasambagan", "Lahug", "Mabolo"] },
            "Mandaue City": { zip: "6014", barangays: ["Bakilid", "Banilad", "Maguikay", "Subangdaku", "Tipolo"] },
            "Lapu-Lapu City": { zip: "6015", barangays: ["Basak", "Mactan", "Marigondon", "Pusok", "Subabasbas"] }
        }
    },
    "Davao del Sur": {
        region: "Region XI (Davao Region)",
        cities: {
            "Davao City": { zip: "8000", barangays: ["Agdao", "Bajada", "Buhangin", "Matina", "Poblacion", "Talomo", "Toril"] }
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // ── Payment method toggles
    const radios = document.querySelectorAll('.payment-method-radio');
    const gcashExtra = document.getElementById('gcash-extra-fields');
    const gcashInput = document.getElementById('gcash_number');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'gcash') {
                gcashExtra.classList.remove('d-none');
                if (gcashInput) gcashInput.required = true;
            } else {
                gcashExtra.classList.add('d-none');
                if (gcashInput) gcashInput.required = false;
            }
        });
    });

    // ── Address Mode Toggle (Dropdown vs Manual Text Input)
    const btnToggleManual = document.getElementById('btn-toggle-manual-addr');
    const manualToggleLabel = document.getElementById('manual-toggle-label');
    const dropdownWrapper = document.getElementById('address-dropdown-wrapper');
    const manualWrapper = document.getElementById('address-manual-wrapper');
    
    const dropdownInputs = [
        document.getElementById('ph-province'),
        document.getElementById('ph-city'),
        document.getElementById('ph-barangay')
    ];
    const manualInputs = [
        document.getElementById('ph-province-manual'),
        document.getElementById('ph-city-manual'),
        document.getElementById('ph-barangay-manual')
    ];

    let isManualMode = false;

    if (btnToggleManual) {
        btnToggleManual.addEventListener('click', function() {
            isManualMode = !isManualMode;
            if (isManualMode) {
                dropdownWrapper.classList.add('d-none');
                manualWrapper.classList.remove('d-none');
                manualToggleLabel.textContent = 'Select From List';

                dropdownInputs.forEach(el => { if (el) el.removeAttribute('required'); });
                manualInputs.forEach(el => { if (el) el.setAttribute('required', 'required'); });
            } else {
                dropdownWrapper.classList.remove('d-none');
                manualWrapper.classList.add('d-none');
                manualToggleLabel.textContent = 'Type Address Manually';

                manualInputs.forEach(el => { if (el) el.removeAttribute('required'); });
                dropdownInputs.forEach(el => { if (el) el.setAttribute('required', 'required'); });
            }
        });
    }

    // ── Address Selection Engine (Instant Local Dictionary + PSGC API Fallback)
    const provSelect = document.getElementById('ph-province');
    const citySelect = document.getElementById('ph-city');
    const brgySelect = document.getElementById('ph-barangay');
    const zipInput   = document.getElementById('ph-zip');
    const regInput   = document.getElementById('ph-region');

    function populateProvinces() {
        if (!provSelect) return;
        provSelect.innerHTML = '<option value="">— Select Province / Region —</option>';
        
        Object.keys(phAddressData).forEach(prov => {
            const opt = document.createElement('option');
            opt.value = prov;
            opt.textContent = prov;
            provSelect.appendChild(opt);
        });
    }

    if (provSelect) {
        // 1. Immediately populate from instant local dataset
        populateProvinces();

        // 2. Try fetching full PSGC provinces list in background to enrich list if online
        fetch('https://psgc.gitlab.io/api/provinces.json')
            .then(res => res.json())
            .then(provinces => {
                if (!Array.isArray(provinces) || provinces.length === 0) return;
                
                // Add NCR at top if not present
                const existing = Array.from(provSelect.options).map(o => o.value);
                provinces.forEach(p => {
                    if (!existing.includes(p.name)) {
                        const opt = document.createElement('option');
                        opt.value = p.name;
                        opt.dataset.code = p.code;
                        opt.textContent = p.name;
                        provSelect.appendChild(opt);
                    }
                });
            })
            .catch(err => console.log('PSGC background enrichment skipped, using instant dataset.'));

        // On Province Change
        provSelect.addEventListener('change', async function() {
            const selectedOpt = this.options[this.selectedIndex];
            const code = selectedOpt?.dataset?.code;
            const provName = this.value;

            citySelect.innerHTML = '<option value="">— Select City / Municipality —</option>';
            brgySelect.innerHTML = '<option value="">— Select Barangay —</option>';
            citySelect.disabled = true;
            brgySelect.disabled = true;
            if (zipInput) zipInput.value = '';

            if (!provName) return;

            // Check instant local data first
            if (phAddressData[provName]) {
                if (regInput) regInput.value = phAddressData[provName].region || provName;
                const citiesObj = phAddressData[provName].cities;
                Object.keys(citiesObj).forEach(city => {
                    const opt = document.createElement('option');
                    opt.value = city;
                    opt.textContent = city;
                    citySelect.appendChild(opt);
                });
                citySelect.disabled = false;
                return;
            }

            // Fallback to PSGC API for additional provinces
            if (code) {
                try {
                    citySelect.innerHTML = '<option value="">Loading Cities / Municipalities...</option>';
                    const res = await fetch(`https://psgc.gitlab.io/api/provinces/${code}/cities-municipalities.json`);
                    const cities = await res.json();
                    cities.sort((a, b) => a.name.localeCompare(b.name));

                    citySelect.innerHTML = '<option value="">— Select City / Municipality —</option>';
                    cities.forEach(city => {
                        const opt = document.createElement('option');
                        opt.value = city.name;
                        opt.dataset.code = city.code;
                        opt.textContent = city.name;
                        citySelect.appendChild(opt);
                    });
                    citySelect.disabled = false;
                    if (regInput) regInput.value = provName;
                } catch (e) {
                    citySelect.innerHTML = '<option value="">— Select City / Municipality —</option>';
                }
            }
        });

        // On City Change
        citySelect.addEventListener('change', async function() {
            const selectedOpt = this.options[this.selectedIndex];
            const code = selectedOpt?.dataset?.code;
            const cityName = this.value;
            const provName = provSelect.value;

            brgySelect.innerHTML = '<option value="">— Select Barangay —</option>';
            brgySelect.disabled = true;
            if (zipInput) zipInput.value = '';

            if (!cityName) return;

            // Check instant local data first
            if (phAddressData[provName] && phAddressData[provName].cities[cityName]) {
                const cityInfo = phAddressData[provName].cities[cityName];
                if (zipInput) zipInput.value = cityInfo.zip || '';
                
                cityInfo.barangays.forEach(brgy => {
                    const opt = document.createElement('option');
                    opt.value = brgy;
                    opt.textContent = brgy;
                    brgySelect.appendChild(opt);
                });
                brgySelect.disabled = false;
                return;
            }

            // Fallback to PSGC API for barangays if code present
            if (code) {
                try {
                    brgySelect.innerHTML = '<option value="">Loading Barangays...</option>';
                    const res = await fetch(`https://psgc.gitlab.io/api/cities-municipalities/${code}/barangays.json`);
                    const barangays = await res.json();
                    barangays.sort((a, b) => a.name.localeCompare(b.name));

                    brgySelect.innerHTML = '<option value="">— Select Barangay —</option>';
                    barangays.forEach(brgy => {
                        const opt = document.createElement('option');
                        opt.value = brgy.name;
                        opt.textContent = brgy.name;
                        brgySelect.appendChild(opt);
                    });
                    brgySelect.disabled = false;
                } catch (e) {
                    brgySelect.innerHTML = '<option value="">— Select Barangay —</option>';
                }
            }
        });
    }

    // ── Checkout Form Submit Feedback
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const btn = document.getElementById('btn-place-order');
            if (btn) {
                setTimeout(() => {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing Order...';
                }, 50);
            }
        });
    }
});
</script>
@endsection

