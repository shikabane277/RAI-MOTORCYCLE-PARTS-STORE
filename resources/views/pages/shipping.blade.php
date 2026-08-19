@extends('layouts.app')
@section('title', 'Shipping & Delivery — RAI MOTORCYCLE PARTS')

@section('content')
<div class="container py-5">
    <div style="max-width:800px;">
        <h1 style="font-family:'Rajdhani',sans-serif;color:#fff;font-weight:700;" class="mb-3">
            <i class="bi bi-truck text-gold me-2"></i>Shipping &amp; Delivery Policy
        </h1>

        <div class="dark-card p-4 mb-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.2rem;color:var(--mb-gold);margin-bottom:.75rem;">
                🛵 Lalamove Express (Default Courier)
            </h2>
            <p style="color:var(--mb-text);line-height:1.7;">
                <strong>RAI MOTORCYCLE PARTS</strong> uses <strong>Lalamove Express</strong> as our primary delivery courier for fast, reliable door-to-door delivery.
            </p>
            <div class="p-3 mb-3" style="background:var(--mb-surface);border-radius:var(--mb-radius-sm);border-left:4px solid var(--mb-gold);">
                <div style="font-weight:700;color:#fff;">⏰ Same-Day Dispatch Time Window (8:00 AM – 4:00 PM Manila Time)</div>
                <ul class="mb-0 mt-2" style="color:var(--mb-muted);font-size:.9rem;padding-left:1.2rem;line-height:1.7;">
                    <li><strong>Orders placed between 8:00 AM and 4:00 PM:</strong> Dispatched via Lalamove on the same day.</li>
                    <li><strong>Orders placed after 4:00 PM:</strong> Processed and scheduled for 8:00 AM Lalamove pickup the next morning.</li>
                    <li><strong>Orders placed before 8:00 AM:</strong> Dispatched at 8:00 AM on the same business day.</li>
                </ul>
            </div>
            <p style="color:var(--mb-muted);font-size:.88rem;">
                You can track your rider in real-time on our <a href="{{ route('order.track') }}" class="text-gold">Track Order</a> page.
            </p>
        </div>

        <div class="dark-card p-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.2rem;color:var(--mb-gold);margin-bottom:.75rem;">
                📦 Rates &amp; Free Shipping
            </h2>
            <ul style="color:var(--mb-muted);font-size:.9rem;line-height:1.8;">
                <li><strong>Free Delivery:</strong> Orders with a subtotal of <strong>&#x20B1;1,500 or higher</strong> qualify for FREE shipping!</li>
                <li><strong>Standard Flat Rate:</strong> Orders under &#x20B1;1,500 have a flat shipping rate of <strong>&#x20B1;89</strong> nationwide.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
