@extends('layouts.admin')
@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
{{-- Status tab bar --}}
<div class="d-flex gap-2 mb-3 flex-wrap">
    @php $statuses = ['','confirmed','processing','shipped','delivered','completed','cancelled']; @endphp
    @foreach($statuses as $s)
    <a href="{{ route('admin.orders.index', $s ? ['status' => $s] : []) }}"
       class="btn btn-sm {{ request('status') === $s ? 'btn-gold' : 'btn-dark-surface' }}">
        {{ $s ? ucfirst(str_replace('_',' ',$s)) : 'All' }}
        @if(isset($statusCounts[$s])) <span class="ms-1">({{ $statusCounts[$s] }})</span>@endif
    </a>
    @endforeach
</div>

<div class="d-flex gap-2 mb-3">
    <form method="GET" class="d-flex gap-2 flex-grow-1">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <input type="search" name="search" class="form-control" style="max-width:300px;" placeholder="Order number or recipient..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-dark-surface btn-sm"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="dark-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark-custom mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);">{{ $order->order_number }}</a>
                    </td>
                    <td>
                        <div style="font-size:.88rem;color:var(--mb-text);">{{ $order->ship_recipient }}</div>
                        <div style="font-size:.72rem;color:var(--mb-muted);">{{ $order->user?->email ?? $order->guest_email }}</div>
                    </td>
                    <td style="font-size:.82rem;color:var(--mb-muted);">{{ $order->placed_at?->format('M d, Y') ?? '—' }}</td>
                    <td style="font-size:.85rem;">{{ $order->items->count() }} item(s)</td>
                    <td style="font-family:'Rajdhani',sans-serif;font-weight:700;">&#x20B1;{{ number_format($order->grand_total, 0) }}</td>
                    <td>
                        <div style="font-size:.82rem;color:var(--mb-text);">{{ strtoupper(str_replace('_',' ',$order->payment_method)) }}</div>
                        @if($order->gcash_number)<div style="font-size:.72rem;color:#007dfe;">{{ $order->gcash_number }}</div>@endif
                        <span style="font-size:.7rem;color:{{ $order->payment_status === 'paid' ? 'var(--mb-green)' : 'var(--mb-gold)' }};">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            {{-- Quick Stage Advance Button --}}
                            @if($order->status === 'pending_payment')
                                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="btn btn-sm btn-gold py-1 px-2" style="font-size:.75rem;" title="Approve & Confirm Order">
                                        <i class="bi bi-check-circle me-1"></i>Confirm
                                    </button>
                                </form>
                            @elseif($order->status === 'confirmed')
                                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="processing">
                                    <button type="submit" class="btn btn-sm btn-gold py-1 px-2" style="font-size:.75rem;" title="Start Packing (To Ship)">
                                        <i class="bi bi-box-seam me-1"></i>Pack (To Ship)
                                    </button>
                                </form>
                            @elseif($order->status === 'processing')
                                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="shipped">
                                    <button type="submit" class="btn btn-sm btn-primary py-1 px-2" style="font-size:.75rem;" title="Dispatch Order (To Receive)">
                                        <i class="bi bi-truck me-1"></i>Ship
                                    </button>
                                </form>
                            @elseif($order->status === 'shipped')
                                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit" class="btn btn-sm btn-success py-1 px-2" style="font-size:.75rem;" title="Mark Delivered">
                                        <i class="bi bi-house-check me-1"></i>Deliver
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-dark-surface btn-sm" title="View Details"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.orders.packing-slip', $order) }}" class="btn btn-dark-surface btn-sm" target="_blank" title="Print Packing Slip"><i class="bi bi-printer"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4" style="color:var(--mb-muted);">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->withQueryString()->links() }}</div>
@endsection
