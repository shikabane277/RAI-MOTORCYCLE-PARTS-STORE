@extends('layouts.app')
@section('title', 'Order Confirmed — RAI MOTORCYCLE PARTS')

@section('content')
<div class="container-xl py-5">
    <div class="text-center mb-5">
        <div style="width:80px;height:80px;background:rgba(0,200,83,0.12);border:2px solid rgba(0,200,83,0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2rem;">
            ✅
        </div>
        <h1 style="font-family:'Rajdhani',sans-serif;font-size:2rem;font-weight:700;color:#fff;">Order Confirmed!</h1>
        <p style="color:var(--mb-muted);font-size:1rem;">Thanks for your order. We're preparing your parts now.</p>
        <div style="font-family:'Rajdhani',sans-serif;font-size:1.3rem;color:var(--mb-gold);font-weight:700;margin-top:.5rem;">
            Order # {{ $order->order_number }}
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-7">
            {{-- Order details --}}
            <div class="dark-card p-4 mb-4">
                <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:#fff;margin-bottom:1rem;">Order Details</h2>
                @foreach($order->items as $item)
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid var(--mb-border);">
                    <div>
                        <div style="font-weight:600;color:var(--mb-text);font-size:.9rem;">{{ $item->product_name }}</div>
                        <div style="font-size:.78rem;color:var(--mb-muted);">{{ $item->variant_label }} &bull; x{{ $item->qty }}</div>
                    </div>
                    <div style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);">&#x20B1;{{ number_format($item->line_total, 2) }}</div>
                </div>
                @endforeach
                <div class="mt-3">
                    <div class="d-flex justify-content-between py-1" style="font-size:.9rem;"><span style="color:var(--mb-muted);">Subtotal</span><span>&#x20B1;{{ number_format($order->subtotal, 2) }}</span></div>
                    @if($order->discount_total > 0)
                    <div class="d-flex justify-content-between py-1" style="font-size:.9rem;"><span style="color:var(--mb-green);">Discount</span><span style="color:var(--mb-green);">-&#x20B1;{{ number_format($order->discount_total, 2) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between py-1" style="font-size:.9rem;"><span style="color:var(--mb-muted);">Shipping ({{ $order->courier ?? 'Lalamove' }})</span><span>{{ $order->shipping_fee == 0 ? 'FREE' : '₱'.number_format($order->shipping_fee, 2) }}</span></div>
                    <div class="d-flex justify-content-between pt-2 mt-1" style="border-top:1px solid var(--mb-border);">
                        <span style="font-weight:700;">Grand Total</span>
                        <span class="product-price" style="font-size:1.3rem;">&#x20B1;{{ number_format($order->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Lalamove Express Delivery Badge --}}
            <div class="dark-card p-4 mb-4" style="background:rgba(245,166,35,0.05);border:1px solid rgba(245,166,35,0.3);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:1.5rem;">🛵</span>
                        <div>
                            <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:#fff;margin:0;">Lalamove Express Delivery</h3>
                            <div style="font-size:.78rem;color:var(--mb-muted);">Tracking No: <strong style="font-family:monospace;color:var(--mb-gold);">{{ $order->tracking_number }}</strong></div>
                        </div>
                    </div>
                    <a href="{{ route('order.track') }}" class="btn btn-gold btn-sm"><i class="bi bi-geo-alt me-1"></i>Track Lalamove Rider</a>
                </div>
                <div style="font-size:.85rem;color:var(--mb-muted);">
                    Orders placed between <strong>8:00 AM – 4:00 PM</strong> are dispatched same-day via Lalamove.
                </div>
            </div>

            {{-- Payment instructions --}}
            @if($order->payment_method !== 'cod')
            <div class="dark-card p-4 mb-4" style="border-color:rgba(245,166,35,0.3);">
                <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:var(--mb-gold);margin-bottom:1rem;">
                    <i class="bi bi-wallet2 me-2"></i>Payment Status &amp; Instructions
                </h2>
                @if($order->payment_method === 'google_pay')
                    @if($order->payment_status === 'paid')
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <svg width="24" height="24" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                        <strong style="color:var(--mb-green);font-size:1.1rem;">Paid &amp; Verified via Google Pay</strong>
                    </div>
                    <p style="color:var(--mb-text);">Your payment of <strong>&#x20B1;{{ number_format($order->grand_total, 2) }}</strong> has been authorized &amp; confirmed.</p>
                    <p style="color:var(--mb-muted);font-size:.85rem;margin:0;">Ref ID: <span style="font-family:monospace;color:var(--mb-gold);">{{ $order->payments->first()?->transaction_id ?? 'GPAY-VERIFIED-AUTH' }}</span></p>
                    @else
                    <div class="p-3 style-pending-box" style="background:rgba(255,193,7,0.1);border:1px solid rgba(255,193,7,0.3);border-radius:10px;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span style="font-size:1.3rem;">⚠️</span>
                            <strong style="color:#ffc107;font-size:1.05rem;">Payment Pending — Google Pay Authorization Not Completed</strong>
                        </div>
                        <p style="color:var(--mb-text);font-size:.9rem;">
                            Your order has been logged, but payment authorization via Google Pay was not completed during checkout.
                        </p>
                        <button type="button" class="btn btn-gold w-100 py-2 mt-2 font-weight-bold" onclick="openGpayAuthModal()">
                            <svg width="20" height="20" viewBox="0 0 24 24" class="me-1"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                            Authorize &amp; Pay &#x20B1;{{ number_format($order->grand_total, 2) }} Now via Google Pay
                        </button>
                    </div>
                    @endif
                @elseif($order->payment_method === 'gcash')
                <p style="color:var(--mb-text);">Send <strong>&#x20B1;{{ number_format($order->grand_total, 2) }}</strong> to our GCash number:</p>
                <div style="font-family:'Rajdhani',sans-serif;font-size:1.5rem;color:var(--mb-gold);font-weight:700;">09XX-XXX-XXXX</div>
                <p style="color:var(--mb-muted);font-size:.85rem;margin-top:.5rem;">Account name: RAI MOTORCYCLE PARTS. Use your order number <strong>{{ $order->order_number }}</strong> as reference. Screenshot proof and send to our FB/Viber.</p>
                @elseif($order->payment_method === 'maya')
                <p style="color:var(--mb-text);">Send <strong>&#x20B1;{{ number_format($order->grand_total, 2) }}</strong> to our Maya number:</p>
                <div style="font-family:'Rajdhani',sans-serif;font-size:1.5rem;color:var(--mb-gold);font-weight:700;">09XX-XXX-XXXX</div>
                <p style="color:var(--mb-muted);font-size:.85rem;margin-top:.5rem;">Reference: <strong>{{ $order->order_number }}</strong>. Send payment screenshot to our FB page or Viber.</p>
                @elseif($order->payment_method === 'bank_transfer')
                <p style="color:var(--mb-text);">Transfer <strong>&#x20B1;{{ number_format($order->grand_total, 2) }}</strong> to:</p>
                <div style="font-size:.9rem;line-height:2;color:var(--mb-text);">
                    <div><span style="color:var(--mb-muted);">BDO:</span> <strong>1234 5678 9012</strong></div>
                    <div><span style="color:var(--mb-muted);">BPI:</span> <strong>0123 4567 89</strong></div>
                    <div><span style="color:var(--mb-muted);">Account Name:</span> <strong>RAI MOTORCYCLE PARTS</strong></div>
                </div>
                <p style="color:var(--mb-muted);font-size:.85rem;margin-top:.5rem;">Reference: <strong>{{ $order->order_number }}</strong>. Send transfer receipt to our FB or Viber.</p>
                @endif
            </div>
            @else
            <div class="dark-card p-4 mb-4">
                <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:#fff;margin-bottom:.75rem;">
                    <i class="bi bi-cash-coin text-gold me-2"></i>Cash on Delivery
                </h2>
                <p style="color:var(--mb-muted);font-size:.9rem;">Prepare <strong style="color:var(--mb-text);">&#x20B1;{{ number_format($order->grand_total, 2) }}</strong> in exact change when your rider arrives. COD orders are processed within 1 business day.</p>
            </div>
            @endif

            {{-- Shipping address --}}
            <div class="dark-card p-4 mb-4">
                <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:#fff;margin-bottom:.75rem;"><i class="bi bi-geo-alt text-gold me-2"></i>Deliver To</h2>
                <div style="color:var(--mb-text);font-size:.9rem;line-height:1.8;">
                    <strong>{{ $order->ship_recipient }}</strong><br>
                    {{ $order->ship_line1 }}, Brgy. {{ $order->ship_barangay }}<br>
                    {{ $order->ship_city }}, {{ $order->ship_province }}<br>
                    {{ $order->ship_phone }}
                </div>
            </div>

            <div class="d-flex gap-3 flex-wrap">
                @auth
                <a href="{{ route('account.orders') }}" class="btn btn-outline-gold">View My Orders</a>
                @endauth
                <a href="{{ route('shop.index') }}" class="btn btn-dark-surface">Continue Shopping</a>
                <a href="{{ route('order.track') }}" class="btn btn-gold"><i class="bi bi-geo-alt me-1"></i>Track Lalamove Order</a>
            </div>
        </div>
    </div>
</div>

@if($order->payment_method === 'google_pay' && $order->payment_status !== 'paid')
{{-- Modal for pending Google Pay authorization --}}
<div class="modal fade" id="gpaySuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content" style="background:#1e1e1e;border:1px solid #333;color:#fff;border-radius:16px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <svg width="24" height="24" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                    <span style="font-weight:700;">Google Pay Authorization</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="text-center my-2">
                    <div style="font-size:.85rem;color:#aaa;">Payable for Order <strong>#{{ $order->order_number }}</strong></div>
                    <div style="font-family:'Rajdhani',sans-serif;font-size:2rem;font-weight:700;color:#fff;">
                        &#x20B1;{{ number_format($order->grand_total, 2) }}
                    </div>
                </div>
                <div class="p-3 my-3" style="background:#2a2a2a;border-radius:10px;">
                    <div style="font-size:.85rem;font-weight:600;">Google Pay Balance / Card •••• 4242</div>
                    <div style="font-size:.78rem;color:#999;">Instant 1-tap confirmation</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-gold w-100 py-2" id="gpay-modal-submit-btn" onclick="executeGooglePayAuth()">
                    Confirm &amp; Pay &#x20B1;{{ number_format($order->grand_total, 2) }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openGpayAuthModal() {
    new bootstrap.Modal(document.getElementById('gpaySuccessModal')).show();
}

function executeGooglePayAuth() {
    const btn = document.getElementById('gpay-modal-submit-btn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Authorizing...';
    btn.disabled = true;

    const token = 'GPAY-TOK-' + Math.random().toString(36).substring(2, 10).toUpperCase() + '-' + Date.now();

    fetch("{{ route('checkout.gpay.pay', $order->order_number) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ gpay_token: token })
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            window.location.reload();
        } else {
            alert('Google Pay authorization failed.');
        }
    })
    .catch(err => {
        alert('Error authorizing Google Pay payment.');
        btn.disabled = false;
        btn.innerHTML = 'Confirm & Pay';
    });
}
</script>
@endif

@endsection
