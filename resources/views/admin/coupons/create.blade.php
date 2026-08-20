@extends('layouts.admin')
@section('title', 'Create Coupon — RAI MOTORCYCLE PARTS')
@section('page-title', 'Create Discount Coupon')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-dark-surface text-gold mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to Coupons List
    </a>
    <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.6rem;color:#fff;font-weight:700;margin:0;">
        <i class="bi bi-plus-circle text-gold me-2"></i>Create New Coupon
    </h1>
    <p style="color:var(--mb-muted);font-size:.85rem;margin:0;">Add a new discount coupon code for customer redemptions during checkout.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="dark-card p-4">
            <form method="POST" action="{{ route('admin.coupons.store') }}">
                @csrf

                {{-- Coupon Code --}}
                <div class="mb-3">
                    <label class="form-label font-bold text-white">Coupon Code *</label>
                    <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror" 
                           placeholder="e.g. RAI10OFF, SUMMER2026, FREESHIP" value="{{ old('code') }}" required style="font-family:monospace;letter-spacing:1px;font-weight:700;">
                    <div class="form-text" style="font-size:.78rem;color:var(--mb-muted);">Codes are automatically converted to UPPERCASE (e.g. rai10 ➔ RAI10).</div>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    {{-- Discount Type --}}
                    <div class="col-md-6">
                        <label class="form-label font-bold text-white">Discount Type *</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Percentage Discount (%)</option>
                            <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount Off (₱)</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Discount Value --}}
                    <div class="col-md-6">
                        <label class="form-label font-bold text-white">Discount Value *</label>
                        <input type="number" step="0.01" min="0.01" name="value" class="form-control @error('value') is-invalid @enderror" 
                               placeholder="e.g. 10 for 10% or 100 for ₱100 off" value="{{ old('value') }}" required>
                        @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    {{-- Minimum Spend --}}
                    <div class="col-md-6">
                        <label class="form-label text-white">Minimum Order Spend (₱)</label>
                        <input type="number" step="0.01" min="0" name="min_spend" class="form-control @error('min_spend') is-invalid @enderror" 
                               placeholder="e.g. 500 (Leave 0 or empty for no minimum)" value="{{ old('min_spend') }}">
                        @error('min_spend')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Usage Limit --}}
                    <div class="col-md-6">
                        <label class="form-label text-white">Usage Limit (Max Uses)</label>
                        <input type="number" min="1" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" 
                               placeholder="e.g. 100 (Leave empty for unlimited)" value="{{ old('usage_limit') }}">
                        @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    {{-- Starts At --}}
                    <div class="col-md-6">
                        <label class="form-label text-white">Valid From Date (Optional)</label>
                        <input type="date" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at') }}">
                        @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Expires At --}}
                    <div class="col-md-6">
                        <label class="form-label text-white">Expiration Date (Optional)</label>
                        <input type="date" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at') }}">
                        @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Is Active Switch --}}
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label font-bold text-white ms-2" for="is_active">
                        Active &amp; Ready for Use
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-gold py-2 px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i>Save &amp; Publish Coupon
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-dark-surface py-2 px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
