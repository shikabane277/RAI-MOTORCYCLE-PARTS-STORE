@extends('layouts.admin')
@section('title', 'Order ' . $order->order_number)
@section('page-title', 'Order Detail')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        {{-- Items --}}
        <div class="dark-card p-4 mb-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:#fff;margin-bottom:1rem;">Items ({{ $order->items->count() }})</h2>
            @foreach($order->items as $item)
            <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid var(--mb-border);">
                <div style="flex-shrink:0;width:50px;height:50px;background:var(--mb-surface);border-radius:var(--mb-radius-sm);display:flex;align-items:center;justify-content:center;">
                    @if($item->image_url)<img src="{{ $item->image_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:var(--mb-radius-sm);">@else<span>&#x1F529;</span>@endif
                </div>
                <div class="flex-grow-1">
                    <div style="font-weight:600;font-size:.9rem;color:var(--mb-text);">{{ $item->product_name }}</div>
                    <div style="font-size:.75rem;color:var(--mb-muted);">{{ $item->variant_label }} &bull; SKU: {{ $item->variant_sku }} &bull; x{{ $item->qty }}</div>
                </div>
                <div style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);">&#x20B1;{{ number_format($item->line_total, 2) }}</div>
            </div>
            @endforeach
            <div class="mt-3">
                <div class="d-flex justify-content-between py-1 text-sm" style="font-size:.88rem;"><span style="color:var(--mb-muted);">Subtotal</span><span>&#x20B1;{{ number_format($order->subtotal, 2) }}</span></div>
                @if($order->discount_total > 0)<div class="d-flex justify-content-between py-1" style="font-size:.88rem;"><span style="color:var(--mb-green);">Discount</span><span style="color:var(--mb-green);">-&#x20B1;{{ number_format($order->discount_total, 2) }}</span></div>@endif
                <div class="d-flex justify-content-between py-1" style="font-size:.88rem;"><span style="color:var(--mb-muted);">Shipping</span><span>{{ $order->shipping_fee == 0 ? 'FREE' : '₱'.number_format($order->shipping_fee,2) }}</span></div>
                <div class="d-flex justify-content-between pt-2 mt-1" style="border-top:1px solid var(--mb-border);font-weight:700;">
                    <span>Grand Total</span><span class="text-gold" style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;">&#x20B1;{{ number_format($order->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Shipping address --}}
        <div class="dark-card p-4 mb-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:#fff;margin-bottom:1rem;"><i class="bi bi-geo-alt text-gold me-2"></i>Ship To</h2>
            <div style="font-size:.9rem;line-height:2;color:var(--mb-text);">
                <strong>{{ $order->ship_recipient }}</strong><br>
                {{ $order->ship_line1 }}, Brgy. {{ $order->ship_barangay }}<br>
                {{ $order->ship_city }}, {{ $order->ship_province }} {{ $order->ship_zip }}<br>
                {{ $order->ship_phone }}
            </div>
        </div>

        {{-- Admin Notes --}}
        <div class="dark-card p-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:#fff;margin-bottom:1rem;">Admin Notes</h2>
            <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                @csrf @method('PUT')
                <textarea name="admin_notes" class="form-control mb-2" rows="3" placeholder="Internal notes...">{{ $order->admin_notes }}</textarea>
                <button type="submit" class="btn btn-dark-surface btn-sm">Save Notes</button>
            </form>
        </div>
    </div>

    {{-- Right: Status + Tracking --}}
    <div class="col-lg-4">
        <div class="dark-card p-4 mb-3">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:#fff;margin-bottom:1rem;">Order Info</h2>
            <div class="d-flex flex-column gap-2" style="font-size:.88rem;">
                <div class="d-flex justify-content-between"><span style="color:var(--mb-muted);">Order Number</span><span class="text-gold fw-bold">{{ $order->order_number }}</span></div>
                <div class="d-flex justify-content-between"><span style="color:var(--mb-muted);">Date Placed</span><span>{{ $order->placed_at?->format('M d, Y H:i') }}</span></div>
                <div class="d-flex justify-content-between"><span style="color:var(--mb-muted);">Payment</span><span>{{ strtoupper(str_replace('_',' ',$order->payment_method)) }}</span></div>
                <div class="d-flex justify-content-between align-items-center">
                    <span style="color:var(--mb-muted);">Pay Status</span>
                    <span style="color:{{ $order->payment_status === 'paid' ? 'var(--mb-green)' : 'var(--mb-gold)' }};font-weight:600;">{{ ucfirst($order->payment_status) }}</span>
                </div>
                @if($order->coupon_code)<div class="d-flex justify-content-between"><span style="color:var(--mb-muted);">Coupon</span><span class="text-gold">{{ $order->coupon_code }}</span></div>@endif
                @if($order->notes)<div class="mt-1 p-2" style="background:var(--mb-surface);border-radius:6px;font-size:.82rem;color:var(--mb-muted);">&#x1F4DD; {{ $order->notes }}</div>@endif
            </div>
        </div>

        {{-- Update Status --}}
        <div class="dark-card p-4 mb-3">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:#fff;margin-bottom:1rem;">Update Status</h2>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['pending_payment','confirmed','processing','shipped','delivered','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Courier</label>
                    <select name="courier" class="form-select">
                        <option value="J&T Express" {{ $order->courier === 'J&T Express' ? 'selected' : '' }}>J&T Express</option>
                        <option value="Ninja Van" {{ $order->courier === 'Ninja Van' ? 'selected' : '' }}>Ninja Van</option>
                        <option value="LBC" {{ $order->courier === 'LBC' ? 'selected' : '' }}>LBC</option>
                        <option value="2GO" {{ $order->courier === '2GO' ? 'selected' : '' }}>2GO</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tracking Number</label>
                    <input type="text" name="tracking_number" class="form-control" value="{{ $order->tracking_number }}" placeholder="e.g. JT123456789PH">
                </div>
                <button type="submit" class="btn btn-gold w-100">Update</button>
            </form>
        </div>

        {{-- Tracking timeline --}}
        @if($order->tracking_number)
        <div class="dark-card p-4 mb-3">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:#fff;margin-bottom:.75rem;">Shipping</h2>
            <div style="font-size:.88rem;color:var(--mb-muted);">{{ $order->courier }}</div>
            <div style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);font-size:1rem;">{{ $order->tracking_number }}</div>
        </div>
        @endif

        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.packing-slip', $order) }}" class="btn btn-outline-gold btn-sm w-100" target="_blank">
                <i class="bi bi-printer me-1"></i>Packing Slip
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-dark-surface btn-sm w-100">Back</a>
        </div>
    </div>
</div>
@endsection
