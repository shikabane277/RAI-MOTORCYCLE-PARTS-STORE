@extends('layouts.admin')
@section('title', 'Order ' . $order->order_number)
@section('page-title', 'Order Detail')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        {{-- Items --}}
        <div class="dark-card p-4 mb-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-heading);margin-bottom:1rem;">Items ({{ $order->items->count() }})</h2>
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
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-heading);margin-bottom:1rem;"><i class="bi bi-geo-alt text-gold me-2"></i>Ship To</h2>
            <div style="font-size:.9rem;line-height:2;color:var(--mb-text);">
                <strong>{{ $order->ship_recipient }}</strong><br>
                {{ $order->ship_line1 }}, Brgy. {{ $order->ship_barangay }}<br>
                {{ $order->ship_city }}, {{ $order->ship_province }} {{ $order->ship_zip }}<br>
                {{ $order->ship_phone }}
            </div>
        </div>

        {{-- Admin Notes --}}
        <div class="dark-card p-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-heading);margin-bottom:1rem;">Admin Notes</h2>
            <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                @csrf @method('PUT')
                <textarea name="admin_notes" class="form-control mb-2" rows="3" placeholder="Internal notes...">{{ $order->admin_notes }}</textarea>
                <button type="submit" class="btn btn-dark-surface btn-sm">Save Notes</button>
            </form>
        </div>
    </div>

    {{-- Right: Status + Tracking --}}
    <div class="col-lg-4">
        {{-- Quick Process Order Actions --}}
        <div class="dark-card p-4 mb-3" style="border:1px solid var(--mb-gold-border);">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-heading);margin-bottom:1rem;">
                <i class="bi bi-play-circle text-gold me-2"></i>Quick Process Order
            </h2>
            <div class="d-grid gap-2">
                @if($order->status === 'pending_payment')
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                        @csrf
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-gold w-100 py-2 font-bold">
                            <i class="bi bi-check-circle me-1"></i>Approve &amp; Confirm Order
                        </button>
                    </form>
                @elseif($order->status === 'confirmed')
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                        @csrf
                        <input type="hidden" name="status" value="processing">
                        <button type="submit" class="btn btn-gold w-100 py-2 font-bold">
                            <i class="bi bi-box-seam me-1"></i>Move to "To Ship" (Start Packing)
                        </button>
                    </form>
                @elseif($order->status === 'processing')
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                        @csrf
                        <input type="hidden" name="status" value="shipped">
                        <button type="submit" class="btn btn-primary w-100 py-2 font-bold">
                            <i class="bi bi-truck me-1"></i>Dispatch Order (Mark as Shipped)
                        </button>
                    </form>
                @elseif($order->status === 'shipped')
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                        @csrf
                        <input type="hidden" name="status" value="delivered">
                        <button type="submit" class="btn btn-success w-100 py-2 font-bold">
                            <i class="bi bi-house-check me-1"></i>Mark as Delivered
                        </button>
                    </form>
                @else
                    <div style="font-size:.85rem;color:var(--mb-muted);" class="text-center">
                        Current Status: <strong class="text-gold">{{ ucfirst(str_replace('_',' ',$order->status)) }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <div class="dark-card p-4 mb-3">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-heading);margin-bottom:1rem;">Order Info</h2>
            <div class="d-flex flex-column gap-2" style="font-size:.88rem;">
                <div class="d-flex justify-content-between"><span style="color:var(--mb-muted);">Order Number</span><span class="text-gold fw-bold">{{ $order->order_number }}</span></div>
                <div class="d-flex justify-content-between"><span style="color:var(--mb-muted);">Date Placed</span><span>{{ $order->placed_at?->format('M d, Y H:i') }}</span></div>
                <div class="d-flex justify-content-between"><span style="color:var(--mb-muted);">Payment</span><span>{{ strtoupper(str_replace('_',' ',$order->payment_method)) }}</span></div>
                @if($order->gcash_number)
                <div class="d-flex justify-content-between"><span style="color:var(--mb-muted);">GCash Mobile No.</span><span style="color:#007dfe;font-weight:600;">{{ $order->gcash_number }}</span></div>
                @endif
                <div class="d-flex justify-content-between align-items-center">
                    <span style="color:var(--mb-muted);">Pay Status</span>
                    <span style="color:{{ $order->payment_status === 'paid' ? 'var(--mb-green)' : 'var(--mb-gold)' }};font-weight:600;">{{ ucfirst($order->payment_status) }}</span>
                </div>
                @if($order->coupon_code)<div class="d-flex justify-content-between"><span style="color:var(--mb-muted);">Coupon</span><span class="text-gold">{{ $order->coupon_code }}</span></div>@endif
                @if($order->notes)<div class="mt-1 p-2" style="background:var(--mb-surface);border-radius:6px;font-size:.82rem;color:var(--mb-muted);">&#x1F4DD; {{ $order->notes }}</div>@endif
            </div>
        </div>

        {{-- Shopee Order Progress Stage --}}
        <div class="dark-card p-4 mb-3">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-heading);margin-bottom:1rem;">
                <i class="bi bi-diagram-3 text-gold me-2"></i>Shopee Order Lifecycle
            </h2>
            <div class="d-flex flex-column gap-2">
                @php $step = $order->shopee_step; @endphp
                @foreach([
                    1 => ['Order Placed', 'bi-bag-check', 'pending_payment, confirmed'],
                    2 => ['To Ship', 'bi-box-seam', 'processing'],
                    3 => ['To Receive', 'bi-truck', 'shipped'],
                    4 => ['Received', 'bi-check-circle', 'delivered, completed'],
                ] as $num => [$label, $icon, $statuses])
                <div class="d-flex align-items-center justify-content-between p-2" style="border-radius:6px;background:{{ $step >= $num ? 'rgba(245,166,35,0.1)' : 'var(--mb-surface)' }};border:1px solid {{ $step >= $num ? 'rgba(245,166,35,0.3)' : 'var(--mb-border)' }};">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi {{ $icon }}" style="color:{{ $step >= $num ? 'var(--mb-gold)' : 'var(--mb-muted)' }};"></i>
                        <span style="font-size:.88rem;font-weight:{{ $step === $num ? '700' : '500' }};color:{{ $step >= $num ? '#fff' : 'var(--mb-muted)' }};">
                            Step {{ $num }}: {{ $label }}
                        </span>
                    </div>
                    @if($step > $num)
                        <span class="badge bg-success" style="font-size:.65rem;">DONE</span>
                    @elseif($step === $num)
                        <span class="badge bg-warning text-dark" style="font-size:.65rem;">CURRENT</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Update Status --}}
        <div class="dark-card p-4 mb-3">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-heading);margin-bottom:1rem;">
                <i class="bi bi-pencil-square text-gold me-2"></i>Update Order Status &amp; Log Activity
            </h2>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Status Stage *</label>
                    <select name="status" id="admin_status_select" class="form-select" onchange="updatePresetTitle(this.value)">
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Order Placed &mdash; Confirmed</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>To Ship &mdash; Preparing &amp; Packing Items</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>To Receive &mdash; Picked up / In Transit</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Received &mdash; Delivered to Buyer</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Courier</label>
                    <input type="text" name="courier" class="form-control form-control-sm" value="{{ old('courier', $order->courier ?? 'J&T Express') }}" placeholder="J&T Express, Lalamove Express, etc.">
                </div>
                <div class="mb-2">
                    <label class="form-label">Tracking Number</label>
                    <input type="text" name="tracking_number" class="form-control form-control-sm" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="e.g. JNT-PH-123456 or LLM-PH-123456">
                </div>
                <div class="mb-2">
                    <label class="form-label">Status Update Title for Buyer *</label>
                    <input type="text" name="log_title" id="log_title_input" class="form-control form-control-sm" placeholder="e.g. Parcel picked up by rider" value="{{ old('log_title') }}">
                    @php $isJnT = str_contains(strtolower($order->courier ?? ''), 'j&t'); @endphp
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        @if($isJnT)
                            <button type="button" class="btn btn-dark-surface py-0 px-2" style="font-size:.7rem;" onclick="setPreset('Parcel picked up by J&T Express rider')">📌 Picked up by J&T</button>
                            <button type="button" class="btn btn-dark-surface py-0 px-2" style="font-size:.7rem;" onclick="setPreset('Package packed & handed to J&T hub')">📦 Handed to J&T Hub</button>
                            <button type="button" class="btn btn-dark-surface py-0 px-2" style="font-size:.7rem;" onclick="setPreset('Parcel out for delivery by J&T rider')">🚚 Out for delivery</button>
                        @else
                            <button type="button" class="btn btn-dark-surface py-0 px-2" style="font-size:.7rem;" onclick="setPreset('Parcel picked up by Lalamove rider')">📌 Picked up by rider</button>
                            <button type="button" class="btn btn-dark-surface py-0 px-2" style="font-size:.7rem;" onclick="setPreset('Package packed & awaiting rider pickup')">📦 Package packed</button>
                            <button type="button" class="btn btn-dark-surface py-0 px-2" style="font-size:.7rem;" onclick="setPreset('Rider approaching delivery location')">🛵 Out for delivery</button>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status Update Details (Optional)</label>
                    <textarea name="log_description" class="form-control form-control-sm" rows="2" placeholder="e.g. Rider Juan (09171234567) has picked up the parcel. ETA 30 mins.">{{ old('log_description') }}</textarea>
                </div>
                <button type="submit" class="btn btn-gold w-100 py-2 fw-bold">
                    <i class="bi bi-send me-1"></i>Save &amp; Notify Buyer
                </button>
            </form>
        </div>

        {{-- Status Activity Log Feed --}}
        <div class="dark-card p-4 mb-3">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-heading);margin-bottom:1rem;">
                <i class="bi bi-clock-history text-gold me-2"></i>Status Activity Log
            </h2>
            @if($order->statusLogs->isEmpty())
                <div style="font-size:.85rem;color:var(--mb-muted);">No timeline logs recorded yet.</div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($order->statusLogs as $log)
                    <div class="p-2" style="background:var(--mb-surface);border-radius:6px;border-left:3px solid var(--mb-gold);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="font-size:.88rem;font-weight:600;color:var(--mb-heading);">{{ $log->title }}</div>
                            <span class="badge bg-secondary" style="font-size:.65rem;">{{ strtoupper(str_replace('_',' ',$log->status)) }}</span>
                        </div>
                        @if($log->description)
                        <div style="font-size:.8rem;color:var(--mb-text);" class="mt-1">{{ $log->description }}</div>
                        @endif
                        <div style="font-size:.72rem;color:var(--mb-muted);" class="mt-1">
                            <i class="bi bi-clock me-1"></i>{{ $log->created_at->format('M d, Y h:i A') }} ({{ $log->created_at->diffForHumans() }})
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.packing-slip', $order) }}" class="btn btn-outline-gold btn-sm w-100" target="_blank">
                <i class="bi bi-printer me-1"></i>Packing Slip
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-dark-surface btn-sm w-100">Back</a>
        </div>
    </div>

<script>
function setPreset(text) {
    document.getElementById('log_title_input').value = text;
}
function updatePresetTitle(status) {
    const presets = {
        'confirmed': 'Order Confirmed by Store',
        'processing': 'To Ship — Seller is preparing your parcel at warehouse',
        'shipped': 'To Receive — Parcel picked up by Lalamove rider',
        'delivered': 'Received — Parcel delivered to buyer',
        'completed': 'Order Completed',
        'cancelled': 'Order Cancelled'
    };
    if (presets[status] && !document.getElementById('log_title_input').value) {
        document.getElementById('log_title_input').value = presets[status];
    }
}
</script>
</div>
@endsection
