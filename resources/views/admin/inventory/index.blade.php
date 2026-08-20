@extends('layouts.admin')
@section('title', 'Inventory')
@section('page-title', 'Inventory Management')

@section('content')
<div class="d-flex gap-2 mb-3 flex-wrap">
    @foreach(['' => 'All SKUs', 'low' => '⚠️ Low Stock', 'out' => '🔴 Out of Stock'] as $f => $label)
    <a href="{{ route('admin.inventory.index', $f ? ['filter' => $f] : []) }}" class="btn btn-sm {{ request('filter') === $f ? 'btn-gold' : 'btn-dark-surface' }}">{{ $label }}</a>
    @endforeach
    <form method="GET" class="d-flex gap-2 ms-auto">
        <input type="hidden" name="filter" value="{{ request('filter') }}">
        <input type="search" name="search" class="form-control" style="max-width:220px;" placeholder="SKU or product..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-dark-surface btn-sm"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="dark-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-dark-custom mb-0">
                    <thead>
                        <tr><th>SKU</th><th>Product</th><th>Variant</th><th>Stock</th><th>Low Threshold</th><th>Adjust</th></tr>
                    </thead>
                    <tbody>
                        @forelse($variants as $variant)
                        <tr>
                            <td style="font-size:.78rem;color:var(--mb-gold);font-family:monospace;">{{ $variant->variant_sku }}</td>
                            <td style="font-size:.85rem;color:var(--mb-text);">{{ Str::limit($variant->product->name, 36) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <div class="color-swatch swatch-{{ strtolower($variant->color) }}" style="width:14px;height:14px;cursor:default;border-radius:50%;flex-shrink:0;"></div>
                                    <span style="font-size:.82rem;">{{ $variant->color }}</span>
                                </div>
                                @if($variant->thread_size)<div style="font-size:.72rem;color:var(--mb-muted);">{{ $variant->thread_size }}</div>@endif
                            </td>
                            <td>
                                <span style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;
                                    color:{{ $variant->stock_qty <= 0 ? 'var(--mb-red)' : ($variant->stock_qty <= $variant->low_stock_threshold ? 'var(--mb-gold)' : 'var(--mb-green)') }};">
                                    {{ $variant->stock_qty }}
                                </span>
                            </td>
                            <td style="font-size:.85rem;color:var(--mb-muted);">{{ $variant->low_stock_threshold }}</td>
                            <td>
                                <button class="btn btn-dark-surface btn-sm" data-bs-toggle="modal" data-bs-target="#adjust-{{ $variant->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                        {{-- Modal --}}
                        <div class="modal fade" id="adjust-{{ $variant->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="background:var(--mb-card);border:1px solid var(--mb-border);border-radius:var(--mb-radius);">
                                    <div class="modal-header" style="border-color:var(--mb-border);">
                                        <h5 class="modal-title" style="font-family:'Rajdhani',sans-serif;color:#fff;">Adjust Stock — {{ $variant->variant_sku }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.inventory.adjust', $variant) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <div style="font-size:.88rem;color:var(--mb-muted);">Current stock: <strong style="color:var(--mb-text);">{{ $variant->stock_qty }}</strong></div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Change Quantity (use negative to reduce)</label>
                                                <input type="number" name="change_qty" class="form-control" placeholder="+10 or -5" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Reason</label>
                                                <select name="reason" class="form-select">
                                                    <option value="restock">Restock</option>
                                                    <option value="manual_adjustment">Manual Adjustment</option>
                                                    <option value="damaged">Damaged / Lost</option>
                                                    <option value="recount">Physical Recount</option>
                                                </select>
                                            </div>
                                            <div class="mb-1">
                                                <label class="form-label">Reference (optional)</label>
                                                <input type="text" name="reference" class="form-control" placeholder="e.g. PO-2024-001">
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="border-color:var(--mb-border);">
                                            <button type="button" class="btn btn-dark-surface" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-gold">Apply Adjustment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5" style="color:var(--mb-muted);">
                                <i class="bi bi-box-seam d-block mb-2" style="font-size:2rem;color:var(--mb-gold);"></i>
                                <div class="fw-semibold mb-1" style="color:var(--mb-text);">No inventory SKUs found</div>
                                <div style="font-size:.8rem;" class="mb-3">Create a new product with initial stock to start tracking inventory.</div>
                                <a href="{{ route('admin.products.create') }}" class="btn btn-gold btn-sm">
                                    <i class="bi bi-plus-lg me-1"></i> Add Product & Initial Stock
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $variants->withQueryString()->links() }}</div>
    </div>

    {{-- Recent Logs --}}
    <div class="col-lg-4">
        <div class="dark-card p-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:#fff;margin-bottom:1rem;">Recent Adjustments</h2>
            @forelse($logs as $log)
            <div class="py-2" style="border-bottom:1px solid var(--mb-border);">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="min-width:0;">
                        <div style="font-size:.8rem;color:var(--mb-gold);font-family:monospace;">{{ $log->variant?->variant_sku }}</div>
                        <div style="font-size:.75rem;color:var(--mb-muted);">{{ ucfirst(str_replace('_',' ',$log->reason)) }}</div>
                        @if($log->reference)<div style="font-size:.72rem;color:var(--mb-muted);">{{ $log->reference }}</div>@endif
                    </div>
                    <div style="text-align:right;flex-shrink:0;margin-left:.5rem;">
                        <span style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:.95rem;color:{{ $log->change_qty > 0 ? 'var(--mb-green)' : 'var(--mb-red)' }};">
                            {{ $log->change_qty > 0 ? '+' : '' }}{{ $log->change_qty }}
                        </span>
                        <div style="font-size:.7rem;color:var(--mb-muted);">→ {{ $log->stock_after }}</div>
                        <div style="font-size:.68rem;color:var(--mb-muted);">{{ $log->created_at?->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div style="color:var(--mb-muted);font-size:.88rem;">No recent adjustments.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
