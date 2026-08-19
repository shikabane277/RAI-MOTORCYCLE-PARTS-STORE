@extends('layouts.app')
@section('title', 'My Account — MachBolt PH')

@section('content')
<div class="container-xl py-5">
    <div class="row g-4">
        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="dark-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="reviewer-avatar" style="width:48px;height:48px;font-size:1.1rem;">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div>
                        <div style="font-weight:700;color:var(--mb-text);">{{ auth()->user()->name }}</div>
                        <div style="font-size:.78rem;color:var(--mb-muted);">{{ auth()->user()->email }}</div>
                        <div style="font-size:.72rem;color:var(--mb-gold);">{{ number_format(auth()->user()->loyalty_points ?? 0) }} pts</div>
                    </div>
                </div>
                <nav class="d-flex flex-column gap-1">
                    <a href="{{ route('account.dashboard') }}" class="admin-nav-item active"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a href="{{ route('account.orders') }}" class="admin-nav-item"><i class="bi bi-bag-check"></i> My Orders</a>
                    <a href="{{ route('account.wishlist') }}" class="admin-nav-item"><i class="bi bi-heart"></i> Wishlist</a>
                    <a href="{{ route('account.addresses') }}" class="admin-nav-item"><i class="bi bi-geo-alt"></i> Addresses</a>
                    <a href="{{ route('account.profile') }}" class="admin-nav-item"><i class="bi bi-person"></i> Profile</a>
                    <hr class="divider-gold my-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="admin-nav-item w-100 text-start border-0 bg-transparent" style="color:var(--mb-red);">
                            <i class="bi bi-box-arrow-left"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        {{-- Main --}}
        <div class="col-lg-9">
            <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:1.5rem;">
                Welcome back, {{ explode(' ', auth()->user()->name)[0] }}!
            </h1>

            {{-- Quick stats --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="dark-card p-3 text-center">
                        <div style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:var(--mb-gold);">
                            {{ $orderCount ?? 0 }}
                        </div>
                        <div style="font-size:.8rem;color:var(--mb-muted);">Total Orders</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dark-card p-3 text-center">
                        <div style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:var(--mb-gold);">
                            {{ $pendingCount ?? 0 }}
                        </div>
                        <div style="font-size:.8rem;color:var(--mb-muted);">In Transit</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dark-card p-3 text-center">
                        <div style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:var(--mb-gold);">
                            {{ number_format(auth()->user()->loyalty_points ?? 0) }}
                        </div>
                        <div style="font-size:.8rem;color:var(--mb-muted);">Loyalty Points</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dark-card p-3 text-center">
                        <div style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:var(--mb-gold);">
                            {{ $wishlistCount ?? 0 }}
                        </div>
                        <div style="font-size:.8rem;color:var(--mb-muted);">Wishlist Items</div>
                    </div>
                </div>
            </div>

            {{-- Recent orders --}}
            <div class="dark-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:#fff;margin:0;">Recent Orders</h2>
                    <a href="{{ route('account.orders') }}" class="btn btn-outline-gold btn-sm">View All</a>
                </div>
                @forelse($recentOrders ?? [] as $order)
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid var(--mb-border);">
                    <div>
                        <div style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);">{{ $order->order_number }}</div>
                        <div style="font-size:.78rem;color:var(--mb-muted);">{{ $order->placed_at?->format('M d, Y') }} &bull; {{ $order->items->count() }} item(s)</div>
                    </div>
                    <div class="text-end">
                        <div style="font-family:'Rajdhani',sans-serif;font-weight:700;">&#x20B1;{{ number_format($order->grand_total, 2) }}</div>
                        <span class="status-badge status-{{ $order->status }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                    </div>
                </div>
                @empty
                <p style="color:var(--mb-muted);font-size:.9rem;">No orders yet. <a href="{{ route('shop.index') }}" class="text-gold">Start shopping!</a></p>
                @endforelse
            </div>

            {{-- Quick links --}}
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <a href="{{ route('account.orders') }}" class="cat-icon-card"><span class="cat-icon">&#x1F4E6;</span><div class="cat-name">My Orders</div></a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('account.wishlist') }}" class="cat-icon-card"><span class="cat-icon">&#x2764;&#xFE0F;</span><div class="cat-name">Wishlist</div></a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('account.addresses') }}" class="cat-icon-card"><span class="cat-icon">&#x1F4CD;</span><div class="cat-name">Addresses</div></a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('order.track') }}" class="cat-icon-card"><span class="cat-icon">&#x1F69A;</span><div class="cat-name">Track Order</div></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
