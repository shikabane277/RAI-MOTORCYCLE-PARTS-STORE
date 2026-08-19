<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Packing Slip — {{ $order->order_number }}</title>
<style>
body { font-family: Arial, sans-serif; color: #000; background: #fff; font-size: 13px; margin: 24px; }
h1 { font-size: 1.5rem; margin: 0; }
table { width: 100%; border-collapse: collapse; margin-top: 12px; }
th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; font-size: 12px; }
th { background: #f0f0f0; font-weight: 700; }
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
.total-row td { font-weight: 700; background: #f9f9f9; }
@media print { .no-print { display: none; } }
</style>
</head>
<body>
<div class="header">
    <div>
        <h1>MachBolt PH</h1>
        <div style="color:#666;">Precision CNC Parts for Filipino Riders</div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:1.2rem;font-weight:700;">PACKING SLIP</div>
        <div>Order: <strong>{{ $order->order_number }}</strong></div>
        <div>Date: {{ $order->placed_at?->format('M d, Y') ?? now()->format('M d, Y') }}</div>
    </div>
</div>

<div style="display:flex;gap:32px;margin-bottom:16px;">
    <div>
        <strong>Ship To:</strong><br>
        {{ $order->ship_recipient }}<br>
        {{ $order->ship_line1 }}<br>
        Brgy. {{ $order->ship_barangay }}, {{ $order->ship_city }}<br>
        {{ $order->ship_province }} {{ $order->ship_zip }}<br>
        {{ $order->ship_phone }}
    </div>
    <div>
        <strong>Payment:</strong> {{ strtoupper(str_replace('_',' ',$order->payment_method)) }}<br>
        <strong>Status:</strong> {{ ucfirst($order->payment_status) }}<br>
        @if($order->courier)<strong>Courier:</strong> {{ $order->courier }}<br>@endif
        @if($order->tracking_number)<strong>Tracking:</strong> {{ $order->tracking_number }}<br>@endif
    </div>
</div>

<table>
    <thead><tr><th>#</th><th>Product</th><th>SKU</th><th>Variant</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
    <tbody>
        @foreach($order->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->product_name }}</td>
            <td style="font-family:monospace;">{{ $item->variant_sku }}</td>
            <td>{{ $item->variant_label }}</td>
            <td>{{ $item->qty }}</td>
            <td>₱{{ number_format($item->unit_price, 2) }}</td>
            <td>₱{{ number_format($item->line_total, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total-row"><td colspan="6" style="text-align:right;">Subtotal</td><td>₱{{ number_format($order->subtotal, 2) }}</td></tr>
        @if($order->discount_total > 0)
        <tr class="total-row"><td colspan="6" style="text-align:right;">Discount</td><td>-₱{{ number_format($order->discount_total, 2) }}</td></tr>
        @endif
        <tr class="total-row"><td colspan="6" style="text-align:right;">Shipping</td><td>{{ $order->shipping_fee == 0 ? 'FREE' : '₱'.number_format($order->shipping_fee,2) }}</td></tr>
        <tr class="total-row"><td colspan="6" style="text-align:right;font-size:1rem;">GRAND TOTAL</td><td style="font-size:1rem;">₱{{ number_format($order->grand_total, 2) }}</td></tr>
    </tbody>
</table>

@if($order->notes)
<div style="margin-top:16px;padding:8px 12px;background:#fffde7;border:1px solid #f0c020;border-radius:4px;">
    <strong>Customer Note:</strong> {{ $order->notes }}
</div>
@endif

<div style="margin-top:24px;font-size:11px;color:#666;text-align:center;">
    Thank you for your order! — MachBolt PH | machbolt.ph
</div>

<div class="no-print" style="margin-top:24px;">
    <button onclick="window.print()" style="padding:8px 20px;background:#F5A623;border:none;border-radius:6px;font-weight:700;cursor:pointer;">Print / Save as PDF</button>
</div>
</body>
</html>
