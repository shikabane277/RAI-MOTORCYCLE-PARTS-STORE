@extends('layouts.admin')
@section('title', 'Coupons Management — RAI MOTORCYCLE PARTS')
@section('page-title', 'Discount Coupons')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.6rem;color:#fff;font-weight:700;margin:0;">
            <i class="bi bi-tags text-gold me-2"></i>Coupon Management
        </h1>
        <p style="color:var(--mb-muted);font-size:.85rem;margin:0;">Create, edit, track usage, and manage discount promo codes for your store.</p>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-gold py-2 px-3 fw-bold">
        <i class="bi bi-plus-lg me-1"></i>Create New Coupon
    </a>
</div>

{{-- ── Quick Stats ────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="dark-card p-3 d-flex align-items-center gap-3">
            <div style="width:42px;height:42px;background:rgba(245,166,35,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;" class="text-gold">
                <i class="bi bi-ticket-perforated"></i>
            </div>
            <div>
                <div style="font-size:.78rem;color:var(--mb-muted);text-transform:uppercase;">Total Coupons</div>
                <div style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1.4rem;color:#fff;">{{ $coupons->total() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dark-card p-3 d-flex align-items-center gap-3">
            <div style="width:42px;height:42px;background:rgba(0,200,83,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--mb-green);">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <div style="font-size:.78rem;color:var(--mb-muted);text-transform:uppercase;">Active Promo Codes</div>
                <div style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1.4rem;color:var(--mb-green);">{{ \App\Models\Coupon::where('is_active', true)->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dark-card p-3 d-flex align-items-center gap-3">
            <div style="width:42px;height:42px;background:rgba(0,125,254,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#007dfe;">
                <i class="bi bi-cart-check"></i>
            </div>
            <div>
                <div style="font-size:.78rem;color:var(--mb-muted);text-transform:uppercase;">Total Redemptions</div>
                <div style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1.4rem;color:#fff;">{{ \App\Models\Coupon::sum('usage_count') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dark-card p-3 d-flex align-items-center gap-3">
            <div style="width:42px;height:42px;background:rgba(255,82,82,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#ff5252;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div style="font-size:.78rem;color:var(--mb-muted);text-transform:uppercase;">Expired / Disabled</div>
                <div style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1.4rem;color:#fff;">{{ \App\Models\Coupon::where('is_active', false)->orWhere(fn($q)=>$q->whereNotNull('expires_at')->where('expires_at','<',now()))->count() }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Search & Filter ────────────────────────────────────────── --}}
<div class="dark-card p-3 mb-4">
    <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-2">
        <div class="col-md-9">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control bg-transparent border-secondary  " placeholder="Search by coupon code (e.g. RAI10, SUMMER2026)..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-gold flex-grow-1"><i class="bi bi-filter me-1"></i>Search</button>
            @if(request('search'))
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-dark-surface"><i class="bi bi-x-circle me-1"></i>Clear</a>
            @endif
        </div>
    </form>
</div>

{{-- ── Coupons Table ─────────────────────────────────────────── --}}
<div class="dark-card p-0 overflow-hidden">
    @if($coupons->isEmpty())
        <div class="p-5 text-center">
            <div style="font-size:3rem;" class="mb-2">🎟️</div>
            <h3 style="font-family:'Rajdhani',sans-serif;color:#fff;">No Coupons Found</h3>
            <p style="color:var(--mb-muted);max-width:400px;margin:0 auto 1.5rem auto;font-size:.9rem;">
                @if(request('search'))
                    No coupons matched your search query "{{ request('search') }}".
                @else
                    There are currently no promo coupons in the system. Click below to create your first discount promo code!
                @endif
            </p>
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-gold py-2 px-4 fw-bold">
                <i class="bi bi-plus-lg me-1"></i>Create First Coupon
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="font-size:.9rem;">
                <thead style="background:var(--mb-surface);border-bottom:1px solid var(--mb-gold-dim);">
                    <tr>
                        <th class="ps-3 py-3">Coupon Code</th>
                        <th class="py-3">Discount Value</th>
                        <th class="py-3">Min. Spend</th>
                        <th class="py-3">Usage</th>
                        <th class="py-3">Validity Window</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-3 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                    @php
                        $isExpired = $coupon->expires_at && now()->gt($coupon->expires_at);
                        $isLimitReached = $coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit;
                    @endphp
                    <tr>
                        <td class="ps-3 py-3">
                            <span class="badge" style="font-family:monospace;font-size:.95rem;background:rgba(245,166,35,0.15);color:var(--mb-gold);border:1px dashed var(--mb-gold);padding:6px 12px;">
                                <i class="bi bi-ticket-perforated me-1"></i>{{ $coupon->code }}
                            </span>
                        </td>
                        <td>
                            @if($coupon->type === 'percentage')
                                <span class="fw-bold text-success" style="font-size:1.05rem;">{{ (float)$coupon->value }}% OFF</span>
                            @else
                                <span class="fw-bold text-gold" style="font-size:1.05rem;">&#x20B1;{{ number_format($coupon->value, 2) }} OFF</span>
                            @endif
                        </td>
                        <td>
                            @if($coupon->min_spend > 0)
                                &#x20B1;{{ number_format($coupon->min_spend, 2) }}
                            @else
                                <span style="color:var(--mb-muted);">No Minimum</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;color:#fff;">
                                {{ $coupon->usage_count }} {{ $coupon->usage_limit ? '/ ' . $coupon->usage_limit : 'uses' }}
                            </div>
                            @if($isLimitReached)
                                <span class="badge bg-danger" style="font-size:.68rem;">Limit Reached</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size:.8rem;color:var(--mb-muted);">
                                @if($coupon->starts_at)
                                    <div>From: {{ $coupon->starts_at->format('M d, Y') }}</div>
                                @endif
                                @if($coupon->expires_at)
                                    <div class="{{ $isExpired ? 'text-danger fw-bold' : '' }}">
                                        Until: {{ $coupon->expires_at->format('M d, Y') }}
                                    </div>
                                @else
                                    <span class="text-success">Never Expires</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if(!$coupon->is_active)
                                <span class="badge bg-secondary">Inactive</span>
                            @elseif($isExpired)
                                <span class="badge bg-danger">Expired</span>
                            @elseif($isLimitReached)
                                <span class="badge bg-warning text-dark">Exhausted</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td class="pe-3 text-end">
                            <div class="d-inline-flex gap-1">
                                {{-- Toggle Active Status --}}
                                <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $coupon->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $coupon->is_active ? 'Deactivate Coupon' : 'Activate Coupon' }}">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>

                                {{-- Edit Button --}}
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-dark-surface text-gold border-gold" title="Edit Coupon">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                {{-- Delete Button --}}
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete coupon {{ $coupon->code }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Coupon">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
        <div class="p-3 border-top border-secondary d-flex justify-content-center">
            {{ $coupons->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
