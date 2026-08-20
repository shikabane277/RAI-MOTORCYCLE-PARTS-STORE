@extends('layouts.app')
@section('title', 'Track Order — RAI MOTORCYCLE PARTS')

@section('content')
<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="text-center mb-4">
                <h1 style="font-family:'Rajdhani',sans-serif;color:#fff;font-weight:700;font-size:2.2rem;" class="mb-2">
                    <i class="bi bi-geo-alt text-gold me-2"></i>Track Your Order Delivery
                </h1>
                <p style="color:var(--mb-muted);font-size:.95rem;">
                    Track your package real-time. Ships via <strong>J&amp;T Express</strong> and <strong>Lalamove Express Same-Day</strong>.
                </p>
            </div>

            {{-- ── Tracking Search Form ──────────────────────────────────── --}}
            <div class="dark-card p-4 mb-4" style="border:1px solid var(--mb-gold-border);">
                @if($errors->any())
                <div class="alert alert-danger mb-3 py-2 px-3 style="font-size:.88rem;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('order.track.search') }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color:#fff;">Order Number *</label>
                            <input type="text" name="order_number" class="form-control" placeholder="e.g. MB-2026-12345 or MB202612345" value="{{ old('order_number', isset($order) ? $order->order_number : '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="color:var(--mb-muted);">Email Address (Optional)</label>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com" value="{{ old('email', isset($order) ? ($order->user?->email ?? $order->guest_email) : '') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-gold w-100 py-2 fw-bold">
                                <i class="bi bi-search me-1"></i>Track
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Logged In User Quick Order Selector --}}
            @auth
            @if(isset($userOrders) && $userOrders->isNotEmpty() && !isset($order))
            <div class="dark-card p-4 mb-4">
                <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:#fff;margin-bottom:1rem;">
                    <i class="bi bi-clock-history text-gold me-2"></i>Your Recent Orders
                </h3>
                <div class="row g-3">
                    @foreach($userOrders as $uOrder)
                    <div class="col-md-6">
                        <div class="p-3" style="background:var(--mb-surface);border-radius:var(--mb-radius-sm);border:1px solid var(--mb-border);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);font-size:1rem;">
                                    {{ $uOrder->order_number }}
                                </span>
                                <span class="status-badge status-{{ $uOrder->status }}" style="font-size:.75rem;">
                                    {{ ucfirst(str_replace('_',' ',$uOrder->status)) }}
                                </span>
                            </div>
                            <div style="font-size:.8rem;color:var(--mb-muted);" class="mb-2">
                                Placed {{ $uOrder->placed_at?->format('M d, Y') }} &bull; Total: <strong>₱{{ number_format($uOrder->grand_total, 2) }}</strong>
                            </div>
                            <form method="POST" action="{{ route('order.track.search') }}">
                                @csrf
                                <input type="hidden" name="order_number" value="{{ $uOrder->order_number }}">
                                <button type="submit" class="btn btn-outline-gold btn-sm w-100">
                                    <i class="bi bi-geo-alt me-1"></i>Track Status
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endauth

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

                {{-- 🛒 4-Stage Order Progress Tracker --}}
                <div class="p-4 mb-4" style="background:var(--mb-surface);border-radius:var(--mb-radius);border:1px solid var(--mb-gold-dim);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.15rem;color:#fff;margin:0;">
                            <i class="bi bi-box-seam text-gold me-2"></i>Delivery Status: <span class="text-gold">{{ $order->shopee_status_label }}</span>
                        </h3>
                        <span class="badge bg-gold text-dark fw-bold px-3 py-1" style="font-size:.8rem;">
                            Step {{ $order->shopee_step }} of 4
                        </span>
                    </div>

                    {{-- Progress Pipeline Bar --}}
                    <div class="row g-2 text-center my-3">
                        @php $sStep = $order->shopee_step; @endphp
                        @foreach([
                            1 => ['Order Placed', '📝', 'Order confirmed'],
                            2 => ['To Ship', '📦', 'Preparing items'],
                            3 => ['To Receive', '🛵', 'In transit / Picked up'],
                            4 => ['Received', '🎉', 'Parcel delivered']
                        ] as $stepNum => [$stepTitle, $stepIcon, $stepDesc])
                        <div class="col-3">
                            <div class="p-3 position-relative" style="border-radius:var(--mb-radius-sm);background:{{ $sStep >= $stepNum ? 'rgba(245,166,35,0.12)' : 'rgba(255,255,255,0.02)' }};border:1.5px solid {{ $sStep >= $stepNum ? 'var(--mb-gold)' : 'var(--mb-border)' }};box-shadow:{{ $sStep === $stepNum ? '0 0 15px rgba(245,166,35,0.3)' : 'none' }};">
                                <div style="font-size:1.6rem;margin-bottom:.3rem;">{{ $stepIcon }}</div>
                                <div style="font-size:.85rem;font-weight:700;color:{{ $sStep >= $stepNum ? '#fff' : 'var(--mb-muted)' }};">{{ $stepTitle }}</div>
                                <div style="font-size:.7rem;color:{{ $sStep >= $stepNum ? 'var(--mb-gold)' : 'var(--mb-muted)' }};" class="d-none d-md-block mt-1">{{ $stepDesc }}</div>
                                @if($sStep >= $stepNum)
                                <div class="mt-2" style="font-size:.65rem;color:var(--mb-green);font-weight:700;">
                                    <i class="bi bi-check-circle-fill me-1"></i>{{ $sStep === $stepNum ? 'IN PROGRESS' : 'COMPLETED' }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Detailed Activity Log Feed --}}
                    <div class="mt-4 pt-3 border-top" style="border-color:var(--mb-border)!important;">
                        <h4 style="font-family:'Rajdhani',sans-serif;font-size:1rem;color:#fff;margin-bottom:1rem;">
                            <i class="bi bi-clock-history me-1 text-gold"></i>Parcel Activity Log
                        </h4>
                        @if($order->statusLogs->isEmpty())
                        <div class="p-3 text-center" style="background:var(--mb-card);border-radius:6px;font-size:.85rem;color:var(--mb-muted);">
                            Order confirmed and queued for fulfillment. Updates will appear here as your package progresses.
                        </div>
                        @else
                        <div class="d-flex flex-column gap-3 position-relative ps-3" style="border-left:2px solid var(--mb-gold-dim);">
                            @foreach($order->statusLogs as $index => $log)
                            <div class="position-relative">
                                <div style="position:absolute;left:-23px;top:2px;width:12px;height:12px;border-radius:50%;background:{{ $index === 0 ? 'var(--mb-gold)' : 'var(--mb-muted)' }};box-shadow:{{ $index === 0 ? '0 0 8px var(--mb-gold)' : 'none' }};"></div>
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <strong style="font-size:.92rem;color:{{ $index === 0 ? '#fff' : 'var(--mb-text)' }};">
                                        {{ $log->title }}
                                    </strong>
                                    <span style="font-size:.75rem;color:var(--mb-muted);">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                @if($log->description)
                                <div style="font-size:.83rem;color:var(--mb-muted);margin-top:.2rem;">
                                    {{ $log->description }}
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

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
