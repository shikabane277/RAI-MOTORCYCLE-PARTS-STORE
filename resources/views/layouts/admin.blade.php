<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — RAI MOTORCYCLE PARTS</title>
    <script>
        (function() {
            var theme = localStorage.getItem('rai_theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--mb-dark);">

{{-- Sidebar --}}
<aside class="admin-sidebar d-flex flex-column">
    <div class="p-4 border-bottom" style="border-color:var(--mb-border)!important;">
        <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-2">
            <img src="/images/logo.png" alt="RAI Logo" style="height:34px;width:34px;object-fit:cover;border-radius:50%;border:1px solid var(--mb-gold);">
            <div style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;font-weight:700;line-height:1.2;">
                <span class="text-gold">RAI</span> <span class="brand-text-main">MOTORCYCLE</span>
            </div>
        </a>
        <div style="color:var(--mb-muted);font-size:.68rem;margin-top:.3rem;letter-spacing:.12em;text-transform:uppercase;">Admin Panel</div>
    </div>

    <nav class="flex-grow-1 py-3 overflow-auto">
        <div class="admin-nav-section">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="admin-nav-section mt-2">Catalog</div>
        <a href="{{ route('admin.products.index') }}" class="admin-nav-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Products
        </a>
        <a href="{{ route('admin.categories.index') }}" class="admin-nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
            <i class="bi bi-diagram-3"></i> Categories
        </a>
        <a href="{{ route('admin.brands.index') }}" class="admin-nav-item {{ request()->routeIs('admin.brands*') ? 'active' : '' }}">
            <i class="bi bi-award"></i> Brands
        </a>
        <a href="{{ route('admin.attributes.index') }}" class="admin-nav-item {{ request()->routeIs('admin.attributes*') ? 'active' : '' }}">
            <i class="bi bi-sliders"></i> Filter Options
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="admin-nav-item {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-data"></i> Inventory
        </a>

        @php
            $unprocessedCount = \App\Models\Order::whereIn('status', ['confirmed', 'processing'])->count();
        @endphp
        <div class="admin-nav-section mt-2">Orders</div>
        <a href="{{ route('admin.orders.index') }}" class="admin-nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
            <div><i class="bi bi-bag-check me-2"></i> Orders</div>
            @if($unprocessedCount > 0)
                <span class="badge bg-warning text-dark rounded-pill fw-bold" style="font-size:.7rem;padding:3px 8px;">{{ $unprocessedCount }} To Process</span>
            @endif
        </a>
        <a href="{{ route('admin.customers.index') }}" class="admin-nav-item {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Customers
        </a>

        <div class="admin-nav-section mt-2">Marketing</div>
        <a href="{{ route('admin.coupons.index') }}" class="admin-nav-item {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Coupons
        </a>
        <a href="{{ route('admin.banners.index') }}" class="admin-nav-item {{ request()->routeIs('admin.banners*') ? 'active' : '' }}">
            <i class="bi bi-image"></i> Banners
        </a>
        <a href="{{ route('admin.reviews.index') }}" class="admin-nav-item {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
            <i class="bi bi-chat-square-text"></i> Reviews
        </a>
    </nav>

    <div class="p-3 border-top" style="border-color:var(--mb-border)!important;">
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="reviewer-avatar" style="width:32px;height:32px;font-size:.8rem;">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <div>
                <div style="font-size:.85rem;font-weight:600;color:var(--mb-text);">{{ auth()->user()->name }}</div>
                <div style="font-size:.7rem;color:var(--mb-gold);">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-dark-surface btn-sm w-100" style="font-size:.8rem;">
                <i class="bi bi-box-arrow-left me-1"></i>Logout
            </button>
        </form>
    </div>
</aside>

{{-- Main Content --}}
<div class="admin-content">
    {{-- Top bar --}}
    <div class="px-4 py-3 d-flex align-items-center justify-content-between border-bottom" style="border-color:var(--mb-border)!important;background:var(--mb-header-bg);">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-dark-surface btn-sm d-lg-none" id="admin-sidebar-toggle">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="mb-0" style="font-family:'Rajdhani',sans-serif;font-size:1.3rem;font-weight:700;color:var(--mb-heading);">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-dark-surface btn-sm d-flex align-items-center justify-content-center" id="theme-toggle-btn" style="border-radius:50px;width:36px;height:36px;" title="Toggle Light / Dark Mode">
                <i class="bi bi-moon-stars-fill text-gold fs-6" id="theme-icon-dark"></i>
                <i class="bi bi-sun-fill text-gold fs-6 d-none" id="theme-icon-light"></i>
            </button>
            @if($unprocessedCount > 0)
            <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="btn btn-warning btn-sm fw-bold d-flex align-items-center gap-2 px-3 py-1" style="font-size:.82rem;border-radius:20px;box-shadow:0 0 12px rgba(255,193,7,0.35);">
                <i class="bi bi-bell-fill"></i>
                <span>{{ $unprocessedCount }} New Order(s) Need Processing</span>
            </a>
            @endif
            <a href="{{ route('home') }}" class="btn btn-outline-gold btn-sm" target="_blank">
                <i class="bi bi-box-arrow-up-right me-1"></i>View Store
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success') || session('error'))
    <div class="px-4 pt-3">
        @if(session('success'))
        <div class="alert auto-dismiss d-flex align-items-center gap-2" style="background:rgba(0,200,83,0.12);border:1px solid rgba(0,200,83,0.3);color:#00ff88;border-radius:var(--mb-radius-sm);">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert auto-dismiss d-flex align-items-center gap-2" style="background:var(--mb-red-dim);border:1px solid rgba(229,57,53,0.3);color:var(--mb-red);border-radius:var(--mb-radius-sm);">
            <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
        </div>
        @endif
    </div>
    @endif

    <div class="p-4">
        @yield('content')
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const themeBtn = document.getElementById('theme-toggle-btn');
    const darkIcon = document.getElementById('theme-icon-dark');
    const lightIcon = document.getElementById('theme-icon-light');

    function updateIcons(theme) {
        if (!darkIcon || !lightIcon) return;
        if (theme === 'dark') {
            darkIcon.classList.add('d-none');
            lightIcon.classList.remove('d-none');
        } else {
            darkIcon.classList.remove('d-none');
            lightIcon.classList.add('d-none');
        }
    }

    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    updateIcons(currentTheme);

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const active = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', active);
            localStorage.setItem('rai_theme', active);
            updateIcons(active);
        });
    }
});
</script>
@stack('scripts')
</body>
</html>
