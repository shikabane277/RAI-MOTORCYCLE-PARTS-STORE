@extends('layouts.app')
@section('title', 'Track Order — RAI MOTORCYCLE PARTS')

@section('content')
<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="text-center mb-4">
                <h1 style="font-family:'Rajdhani',sans-serif;color:#fff;font-weight:700;font-size:2.2rem;" class="mb-2">
                    <i class="bi bi-geo-alt text-gold me-2"></i>Track Your Lalamove Delivery
                </h1>
                <p style="color:var(--mb-muted);font-size:.95rem;">
                    RAI MOTORCYCLE PARTS uses <strong>Lalamove Express</strong> as our default same-day courier.
                </p>
            </div>

            {{-- ── Tracking Form ────────────────────────────────────────── --}}
            <div class="dark-card p-4 mb-4">
                <form method="POST" action="{{ route('order.track.search') }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Order Number</label>
                            <input type="text" name="order_number" class="form-control" placeholder="e.g. MB-2026-12345" value="{{ old('order_number', isset($order) ? $order->order_number : '') }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com" value="{{ old('email', isset($order) ? ($order->user?->email ?? $order->guest_email) : '') }}" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-gold w-100 py-2">
                                <i class="bi bi-search me-1"></i>Track
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ── Order Results Card ────────────────────────────────────── --}}
            @if(isset($order))
            <div class="dark-card p-4 mb-4" style="border:1px solid var(--mb-gold-dim);">
                
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-3 mb-3" style="border-bottom:1px solid var(--mb-border);">
                    <div>
                        <div style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1.4rem;color:var(--mb-gold);">
                            {{ $order->order_number }}
                        </div>
                        <div style="font-size:.82rem;color:var(--mb-muted);">
                            Placed on {{ $order->placed_at?->format('M d, Y h:i A') }} &bull; Courier: <strong>{{ $order->courier ?? 'Lalamove Express' }}</strong>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="status-badge status-{{ $order->status }}" style="font-size:.9rem;">
                            {{ ucfirst(str_replace('_',' ',$order->status)) }}
                        </span>
                        <div style="font-size:.78rem;color:var(--mb-muted);" class="mt-1">
                            Pay Method: {{ strtoupper(str_replace('_',' ',$order->payment_method)) }} ({{ ucfirst($order->payment_status) }})
                        </div>
                    </div>
                </div>

                {{-- 🚀 Lalamove Live Tracking Card --}}
                @if(isset($lalamoveTracking) && $lalamoveTracking['success'])
                <div class="p-4 mb-4" style="background:var(--mb-surface);border-radius:var(--mb-radius);border:1px solid rgba(245,166,35,0.3);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size:1.5rem;">🛵</span>
                            <div>
                                <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:#fff;margin:0;">
                                    Lalamove Real-Time Status
                                </h3>
                                <div style="font-size:.78rem;color:var(--mb-muted);">
                                    Tracking ID: <strong style="font-family:monospace;color:var(--mb-gold);">{{ $lalamoveTracking['tracking_number'] ?? $order->tracking_number }}</strong>
                                </div>
                            </div>
                        </div>
                        <span class="badge bg-{{ $lalamoveTracking['badge'] ?? 'primary' }} px-3 py-2" style="font-size:.85rem;">
                            {{ $lalamoveTracking['title'] ?? 'Lalamove Express' }}
                        </span>
                    </div>

                    {{-- Progress Steps --}}
                    <div class="row g-2 text-center my-4">
                        @php $currentStep = $lalamoveTracking['step'] ?? 2; @endphp
                        @foreach([
                            1 => ['Driver Assigned', '🛵'],
                            2 => ['Heading to Warehouse', '🏬'],
                            3 => ['In Transit to You', '💨'],
                            4 => ['Delivered', '🎉']
                        ] as $stepNum => [$stepTitle, $stepIcon])
                        <div class="col-3">
                            <div class="p-2" style="border-radius:var(--mb-radius-sm);background:{{ $currentStep >= $stepNum ? 'var(--mb-gold-dim)' : 'rgba(255,255,255,0.03)' }};border:1px solid {{ $currentStep >= $stepNum ? 'var(--mb-gold)' : 'var(--mb-border)' }};">
                                <div style="font-size:1.3rem;">{{ $stepIcon }}</div>
                                <div style="font-size:.72rem;font-weight:600;color:{{ $currentStep >= $stepNum ? 'var(--mb-gold)' : 'var(--mb-muted)' }};">{{ $stepTitle }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Driver Card --}}
                    @if(isset($lalamoveTracking['driver']))
                    <div class="d-flex justify-content-between align-items-center p-3" style="background:var(--mb-card);border-radius:var(--mb-radius-sm);border:1px solid var(--mb-border);">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:42px;height:42px;background:var(--mb-gold-dim);border:1px solid var(--mb-gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
                                👤
                            </div>
                            <div>
                                <div style="font-weight:700;color:var(--mb-text);font-size:.92rem;">{{ $lalamoveTracking['driver']['name'] }}</div>
                                <div style="font-size:.78rem;color:var(--mb-muted);">{{ $lalamoveTracking['driver']['vehicle'] }}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div style="font-size:.75rem;color:var(--mb-muted);">Estimated Arrival</div>
                            <div style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-green);font-size:1.1rem;">
                                {{ $lalamoveTracking['eta'] ?? '30-45 mins' }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mt-3 text-end" style="font-size:.75rem;color:var(--mb-muted);">
                        <i class="bi bi-clock-history me-1"></i>Last updated: {{ $lalamoveTracking['updated_at'] }}
                    </div>
                </div>
                @endif

                {{-- Shipping Address & Items --}}
                <div class="row g-4 mb-3">
                    <div class="col-md-6">
                        <h4 style="font-family:'Rajdhani',sans-serif;font-size:1rem;color:#fff;margin-bottom:.5rem;">
                            <i class="bi bi-geo-alt text-gold me-1"></i>Delivery Address
                        </h4>
                        <div style="font-size:.88rem;color:var(--mb-text);line-height:1.6;">
                            <strong>{{ $order->ship_recipient }}</strong> ({{ $order->ship_phone }})<br>
                            {{ $order->ship_line1 }}, Brgy. {{ $order->ship_barangay }}<br>
                            {{ $order->ship_city }}, {{ $order->ship_province }} {{ $order->ship_zip }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4 style="font-family:'Rajdhani',sans-serif;font-size:1rem;color:#fff;margin-bottom:.5rem;">
                            <i class="bi bi-receipt text-gold me-1"></i>Order Summary
                        </h4>
                        <div style="font-size:.88rem;color:var(--mb-text);line-height:1.6;">
                            <div>Subtotal: &#x20B1;{{ number_format($order->subtotal, 2) }}</div>
                            <div>Shipping: {{ $order->shipping_fee == 0 ? 'FREE' : '₱'.number_format($order->shipping_fee, 2) }}</div>
                            @if($order->discount_total > 0)<div class="text-green">Discount: -&#x20B1;{{ number_format($order->discount_total, 2) }}</div>@endif
                            <div style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1.1rem;color:var(--mb-gold);" class="mt-1">
                                Total: &#x20B1;{{ number_format($order->grand_total, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Item list --}}
                <h4 style="font-family:'Rajdhani',sans-serif;font-size:1rem;color:#fff;margin-bottom:.75rem;">Items Ordered</h4>
                @foreach($order->items as $item)
                <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid var(--mb-border);">
                    <div style="width:40px;height:40px;background:var(--mb-surface);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        @if($item->image_url)<img src="{{ $item->image_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">@else🔩@endif
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size:.88rem;font-weight:600;color:var(--mb-text);">{{ $item->product_name }}</div>
                        <div style="font-size:.75rem;color:var(--mb-muted);">{{ $item->variant_label }} &bull; Qty: {{ $item->qty }}</div>
                    </div>
                    <div style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);">&#x20B1;{{ number_format($item->line_total, 2) }}</div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- ── Lalamove Dispatch Notice Card ────────────────────────────── --}}
            <div class="dark-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:var(--mb-gold-dim);border:1px solid var(--mb-gold);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">
                        🛵
                    </div>
                    <div>
                        <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:#fff;margin:0;">
                            Lalamove Same-Day Delivery Schedule
                        </h3>
                        <div style="font-size:.88rem;color:var(--mb-muted);margin-top:.2rem;">
                            Orders placed between <strong>8:00 AM and 4:00 PM</strong> (Manila Time) are dispatched same-day via Lalamove. Orders placed after 4:00 PM will be dispatched at 8:00 AM the next morning.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
