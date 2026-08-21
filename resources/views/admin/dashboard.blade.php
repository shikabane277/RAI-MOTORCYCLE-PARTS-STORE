@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- ── KPI Cards ────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-gold">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-value mt-1">&#x20B1;{{ number_format($totalRevenue, 0) }}</div>
                    <div style="font-size:.78rem;color:var(--mb-muted);margin-top:.25rem;">Today: &#x20B1;{{ number_format($todayRevenue, 0) }}</div>
                </div>
                <i class="bi bi-cash-stack kpi-icon text-gold"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-blue">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-label">Total Orders</div>
                    <div class="kpi-value mt-1">{{ number_format($totalOrders) }}</div>
                    <div style="font-size:.78rem;color:var(--mb-blue);margin-top:.25rem;">{{ $pendingOrders }} pending</div>
                </div>
                <i class="bi bi-bag-check kpi-icon" style="color:var(--mb-blue);"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-green">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-label">Products Active</div>
                    <div class="kpi-value mt-1">{{ number_format($totalProducts) }}</div>
                    <div style="font-size:.78rem;color:var(--mb-muted);margin-top:.25rem;">{{ $outOfStock }} out of stock</div>
                </div>
                <i class="bi bi-box-seam kpi-icon" style="color:var(--mb-green);"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-red">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-label">Customers</div>
                    <div class="kpi-value mt-1">{{ number_format($totalCustomers) }}</div>
                    <div style="font-size:.78rem;color:var(--mb-red);margin-top:.25rem;">{{ $pendingReviews }} reviews pending</div>
                </div>
                <i class="bi bi-people kpi-icon" style="color:var(--mb-red);"></i>
            </div>
        </div>
    </div>
</div>

{{-- ── Main Grid ─────────────────────────────────────────── --}}
<div class="row g-3">

    {{-- Revenue Chart ──────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="dark-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;font-weight:700;color:var(--mb-heading);margin:0;">Revenue — Last 7 Days</h2>
                <span style="font-size:.8rem;color:var(--mb-muted);">{{ $revenueChart->first()['date'] }} &ndash; {{ $revenueChart->last()['date'] }} &middot; Completed + Delivered</span>
            </div>
            @if($revenueTotal > 0)
                <div style="height:240px;"><canvas id="revenue-chart"></canvas></div>
            @else
                <div class="d-flex flex-column align-items-center justify-content-center text-center" style="height:240px;">
                    <i class="bi bi-bar-chart-line" style="font-size:2rem;color:var(--mb-muted);opacity:.45;"></i>
                    <p style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-heading);margin:.75rem 0 .25rem;">No revenue in this period</p>
                    <p style="font-size:.82rem;color:var(--mb-muted);margin:0;">Completed and delivered orders will show up here.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Alerts ──────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="dark-card p-4 h-100">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;font-weight:700;color:var(--mb-heading);margin-bottom:1rem;">Alerts</h2>
            <div class="d-flex flex-column gap-2">
                @if($pendingOrders > 0)
                <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="d-flex align-items-center gap-3 p-3 dark-card-hover text-decoration-none" style="border-radius:var(--mb-radius-sm);border:1px solid var(--mb-border);">
                    <i class="bi bi-bag-check" style="font-size:1.3rem;color:var(--mb-blue);"></i>
                    <div>
                        <div style="font-weight:600;color:var(--mb-text);font-size:.88rem;">{{ $pendingOrders }} Pending Orders</div>
                        <div style="font-size:.75rem;color:var(--mb-muted);">Awaiting processing</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--mb-muted);"></i>
                </a>
                @endif
                @if($lowStockItems->count() > 0)
                <a href="{{ route('admin.inventory.index', ['filter' => 'low']) }}" class="d-flex align-items-center gap-3 p-3 dark-card-hover text-decoration-none" style="border-radius:var(--mb-radius-sm);border:1px solid var(--mb-border);">
                    <i class="bi bi-exclamation-triangle" style="font-size:1.3rem;color:var(--mb-gold);"></i>
                    <div>
                        <div style="font-weight:600;color:var(--mb-text);font-size:.88rem;">{{ $lowStockItems->count() }} Low Stock SKUs</div>
                        <div style="font-size:.75rem;color:var(--mb-muted);">Restock recommended</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--mb-muted);"></i>
                </a>
                @endif
                @if($outOfStock > 0)
                <a href="{{ route('admin.inventory.index', ['filter' => 'out']) }}" class="d-flex align-items-center gap-3 p-3 dark-card-hover text-decoration-none" style="border-radius:var(--mb-radius-sm);border:1px solid rgba(229,57,53,0.3);">
                    <i class="bi bi-x-circle" style="font-size:1.3rem;color:var(--mb-red);"></i>
                    <div>
                        <div style="font-weight:600;color:var(--mb-text);font-size:.88rem;">{{ $outOfStock }} Out of Stock SKUs</div>
                        <div style="font-size:.75rem;color:var(--mb-muted);">Needs immediate restock</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--mb-muted);"></i>
                </a>
                @endif
                @if($pendingReviews > 0)
                <a href="{{ route('admin.reviews.index') }}" class="d-flex align-items-center gap-3 p-3 dark-card-hover text-decoration-none" style="border-radius:var(--mb-radius-sm);border:1px solid var(--mb-border);">
                    <i class="bi bi-chat-square-text" style="font-size:1.3rem;color:var(--mb-green);"></i>
                    <div>
                        <div style="font-weight:600;color:var(--mb-text);font-size:.88rem;">{{ $pendingReviews }} Reviews to Moderate</div>
                        <div style="font-size:.75rem;color:var(--mb-muted);">Awaiting approval</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--mb-muted);"></i>
                </a>
                @endif
                @if($pendingOrders === 0 && $lowStockItems->isEmpty() && $pendingReviews === 0)
                <div class="text-center py-3" style="color:var(--mb-green);">
                    <i class="bi bi-check-circle fs-3"></i>
                    <div style="font-size:.88rem;margin-top:.5rem;">All clear!</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- New Orders ─────────────────────────────────────── --}}
    <div class="col-lg-7">
        <div class="dark-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;font-weight:700;color:var(--mb-heading);margin:0;">New Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-gold btn-sm">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark-custom">
                    <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($newOrders as $order)
                        <tr>
                            <td><span style="font-family:'Rajdhani',sans-serif;color:var(--mb-gold);font-weight:600;">{{ $order->order_number }}</span></td>
                            <td style="font-size:.88rem;">{{ $order->ship_recipient }}</td>
                            <td style="font-family:'Rajdhani',sans-serif;font-weight:600;">&#x20B1;{{ number_format($order->grand_total, 0) }}</td>
                            <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span></td>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-dark-surface btn-sm">View</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center" style="color:var(--mb-muted);">No new orders</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Low Stock ───────────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="dark-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;font-weight:700;color:var(--mb-heading);margin:0;">Low Stock Items</h2>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-gold btn-sm">Manage</a>
            </div>
            @forelse($lowStockItems->take(6) as $variant)
            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid var(--mb-border);">
                <div style="min-width:0;">
                    <div style="font-size:.85rem;font-weight:600;color:var(--mb-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($variant->product->name, 32) }}</div>
                    <div style="font-size:.72rem;color:var(--mb-muted);">{{ $variant->variant_sku }} &bull; {{ $variant->color }}</div>
                </div>
                <span style="font-family:'Rajdhani',sans-serif;font-weight:700;color:{{ $variant->stock_qty <= 5 ? 'var(--mb-red)' : 'var(--mb-gold)' }};flex-shrink:0;margin-left:.75rem;">
                    {{ $variant->stock_qty }} left
                </span>
            </div>
            @empty
            <div class="text-center py-3" style="color:var(--mb-muted);font-size:.88rem;">No low stock items</div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4/dist/chart.umd.min.js"></script>
<script>
(() => {
    const canvas = document.getElementById('revenue-chart');

    // Absent when the window has no revenue and the empty state rendered instead.
    if (! canvas || typeof Chart === 'undefined') return;

    const PESO   = '\u20B1';
    const series = @json($revenueChart);

    const money = (value, dp = 2) => PESO + Number(value || 0).toLocaleString('en-PH', {
        minimumFractionDigits: dp,
        maximumFractionDigits: dp,
    });

    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: series.map(day => day.date),
            datasets: [
                {
                    label: 'Product Sales',
                    data: series.map(day => day.product_revenue),
                    backgroundColor: 'rgba(245, 166, 35, 0.85)',
                    borderColor: '#F5A623',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Shipping Fees',
                    data: series.map(day => day.shipping_revenue),
                    backgroundColor: 'rgba(0, 210, 255, 0.85)',
                    borderColor: '#00D2FF',
                    borderWidth: 1,
                    borderRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            // Index mode so hovering a day's column reports the whole stack,
            // including days whose bars have no height to hit.
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    labels: {
                        color: '#B3B3C1',
                        font: { family: 'Rajdhani', size: 13 },
                        usePointStyle: true,
                        boxWidth: 8,
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(18,18,26,0.95)',
                    borderColor: 'rgba(255,255,255,0.12)',
                    borderWidth: 1,
                    padding: 10,
                    titleFont: { family: 'Rajdhani', size: 13, weight: '700' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        title:  items => series[items[0].dataIndex].full_date,
                        label:  item  => ' ' + item.dataset.label + ': ' + money(item.parsed.y),
                        footer: items => {
                            const day = series[items[0].dataIndex];
                            return 'Total: ' + money(day.revenue)
                                 + '  (' + day.orders + (day.orders === 1 ? ' order)' : ' orders)');
                        },
                    },
                },
            },
            scales: {
                x: {
                    stacked: true,
                    ticks: { color: '#7A7A8C' },
                    grid: { display: false },
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    // Whole-peso gridlines, so a small range cannot repeat the
                    // same rounded label on several lines.
                    ticks: { color: '#7A7A8C', precision: 0, callback: value => money(value, 0) },
                    grid: { color: 'rgba(255,255,255,0.05)' },
                },
            },
        },
    });
})();
</script>
@endpush
