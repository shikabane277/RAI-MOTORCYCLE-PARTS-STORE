<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RAI MOTORCYCLE PARTS') — Your Trusted Motorcycle Parts Store</title>
    <meta name="description" content="@yield('meta_description', 'RAI MOTORCYCLE PARTS — Premium CNC-machined bolts, fasteners, and motorcycle accessories for Filipino riders. Titanium, stainless, anodized aluminum. Fast shipping via J&T and Ninja Van.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- ── Announcement Bar --}}
<div class="announcement-bar text-center">
    <div class="container-fluid px-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-gold fw-semibold">&#x1F3CD;&#xFE0F; FREE SHIPPING on orders &#x20B1;1,500+</span>
            <span class="text-muted-custom">🛵 Lalamove Express Same-Day Delivery (Orders 8:00 AM – 4:00 PM)</span>
            <div class="d-flex gap-3">
                <a href="{{ route('order.track') }}" class="text-muted-custom text-decoration-none" style="font-size:.8rem"><i class="bi bi-geo-alt me-1"></i>Track Order</a>
                @auth
                    <a href="{{ route('account.dashboard') }}" class="text-muted-custom text-decoration-none" style="font-size:.8rem"><i class="bi bi-person me-1"></i>My Account</a>
                @else
                    <a href="{{ route('login') }}" class="text-muted-custom text-decoration-none" style="font-size:.8rem"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- ── Header --}}
<header class="site-header" id="site-header">
    <nav class="navbar navbar-expand-lg py-2">
        <div class="container-xl">
            <a class="navbar-brand me-4 d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="/images/logo.png" alt="RAI MOTORCYCLE PARTS Logo" style="height:42px;width:42px;object-fit:cover;border-radius:50%;border:1px solid var(--mb-gold);">
                <span style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1.2rem;letter-spacing:.03em;">
                    <span class="text-gold">RAI</span> <span style="color:#fff;">MOTORCYCLE</span> <span style="color:var(--mb-muted);font-size:.7em;">PARTS</span>
                </span>
            </a>
            <form class="d-none d-lg-flex flex-grow-1 me-4" action="{{ route('shop.index') }}" method="GET">
                <div class="input-group">
                    <input type="search" name="search" class="search-input form-control border-end-0"
                           placeholder="Search bolts, levers, foot pegs..." value="{{ request('search') }}"
                           style="border-radius:50px 0 0 50px;border-right:none!important;">
                    <button class="btn btn-dark-surface px-3" type="submit" style="border-radius:0 50px 50px 0;border:1px solid var(--mb-border);border-left:none;">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
            <button class="navbar-toggler border-0 ms-auto me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <i class="bi bi-list text-gold fs-4"></i>
            </button>
            <div class="d-flex align-items-center gap-2">
                @auth
                    <a href="{{ route('account.wishlist') }}" class="btn btn-dark-surface btn-sm d-none d-lg-flex align-items-center" style="border-radius:50px;"><i class="bi bi-heart"></i></a>
                    <a href="{{ route('account.dashboard') }}" class="btn btn-dark-surface btn-sm d-none d-lg-flex align-items-center" style="border-radius:50px;"><i class="bi bi-person"></i></a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-dark-surface btn-sm d-none d-lg-flex align-items-center" style="border-radius:50px;"><i class="bi bi-person me-1"></i>Login</a>
                @endauth
                <a href="{{ route('cart.index') }}" class="btn btn-dark-surface btn-sm position-relative" style="border-radius:50px;">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="cart-badge" style="display:none;">0</span>
                </a>
            </div>
        </div>
        <div class="container-xl mt-0">
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto gap-1 py-2 py-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="{{ route('shop.index') }}" data-bs-toggle="dropdown">Shop All</a>
                        <ul class="dropdown-menu" style="background:var(--mb-card);border:1px solid var(--mb-border);border-radius:var(--mb-radius);min-width:220px;">
                            <li><a class="dropdown-item py-2" href="{{ route('shop.category','bolts-fasteners') }}" style="color:var(--mb-text);">&#x1F529; Bolts &amp; Fasteners</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('shop.category','nuts-washers') }}" style="color:var(--mb-text);">&#x2699;&#xFE0F; Nuts &amp; Washers</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('shop.category','levers-grips') }}" style="color:var(--mb-text);">&#x1F3CD;&#xFE0F; Levers &amp; Grips</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('shop.category','foot-pegs-rearsets') }}" style="color:var(--mb-text);">&#x1F9B6; Foot Pegs</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('shop.category','frame-sliders') }}" style="color:var(--mb-text);">&#x1F6E1;&#xFE0F; Frame Sliders</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('shop.category','swingarm-spools') }}" style="color:var(--mb-text);">&#x1F3AF; Swingarm Spools</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('shop.category','fluid-caps') }}" style="color:var(--mb-text);">&#x1F4A7; Fluid Caps</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('shop.index') }}?sort=sale">&#x1F525; Deals</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('shop.index') }}?new=1">&#x2728; New Arrivals</a></li>
                    @auth @if(auth()->user()->isStaff())
                        <li class="nav-item"><a class="nav-link text-gold" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Admin</a></li>
                    @endif @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>

{{-- ── Flash Messages --}}
@if(session('success') || session('error') || $errors->any())
<div class="container-xl mt-2">
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
    @if($errors->any())
    <div class="alert auto-dismiss" style="background:var(--mb-red-dim);border:1px solid rgba(229,57,53,0.3);color:var(--mb-red);border-radius:var(--mb-radius-sm);">
        @foreach($errors->all() as $e)<div><i class="bi bi-x-circle me-1"></i>{{ $e }}</div>@endforeach
    </div>
    @endif
</div>
@endif

<main>@yield('content')</main>

{{-- ── Footer --}}
<footer class="site-footer mt-5 pt-5 pb-4">
    <div class="container-xl">
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="footer-heading mb-3 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                    <img src="/images/logo.png" alt="RAI Logo" style="height:36px;width:36px;object-fit:cover;border-radius:50%;border:1px solid var(--mb-gold);">
                    <span class="text-gold">RAI</span> MOTORCYCLE <span class="text-muted-custom" style="font-size:.7em;">PARTS</span>
                </div>
                <p style="color:var(--mb-muted);font-size:.87rem;line-height:1.7;">Precision CNC-machined parts for Filipino riders. Built tough, finished premium, delivered fast.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-tiktok"></i></a>
                    <a href="#" class="social-icon" title="Shopee"><i class="bi bi-shop"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="footer-heading">Shop</div>
                <a href="{{ route('shop.index') }}" class="footer-link">All Products</a>
                <a href="{{ route('shop.category','bolts-fasteners') }}" class="footer-link">Bolts &amp; Fasteners</a>
                <a href="{{ route('shop.category','levers-grips') }}" class="footer-link">Levers &amp; Grips</a>
                <a href="{{ route('shop.category','frame-sliders') }}" class="footer-link">Frame Sliders</a>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="footer-heading">Support</div>
                <a href="{{ route('faq') }}" class="footer-link">FAQs</a>
                <a href="{{ route('shipping') }}" class="footer-link">Shipping &amp; Delivery</a>
                <a href="{{ route('returns') }}" class="footer-link">Returns &amp; Warranty</a>
                <a href="{{ route('order.track') }}" class="footer-link">Track Your Order</a>
                <a href="{{ route('contact') }}" class="footer-link">Contact Us</a>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="footer-heading">Company</div>
                <a href="{{ route('about') }}" class="footer-link">About RAI</a>
                <a href="{{ route('terms') }}" class="footer-link">Terms of Service</a>
                <a href="{{ route('privacy') }}" class="footer-link">Privacy Policy</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-heading">Payment Methods</div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach(['Google Pay','GCash','Maya','COD','Bank Transfer'] as $pm)
                    <span style="background:var(--mb-surface);border:1px solid var(--mb-border);border-radius:6px;padding:.3rem .65rem;font-size:.75rem;color:var(--mb-muted);">{{ $pm }}</span>
                    @endforeach
                </div>
                <div class="footer-heading mt-2">Also Available On</div>
                <a href="#" class="footer-link d-flex align-items-center gap-1 mb-1"><i class="bi bi-shop text-gold"></i> Shopee</a>
                <a href="#" class="footer-link d-flex align-items-center gap-1 mb-1"><i class="bi bi-box text-gold"></i> Lazada</a>
                <a href="#" class="footer-link d-flex align-items-center gap-1"><i class="bi bi-tiktok text-gold"></i> TikTok Shop</a>
            </div>
        </div>
        <hr class="divider-gold">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3">
            <p class="mb-0" style="color:var(--mb-muted);font-size:.8rem;">&copy; {{ date('Y') }} RAI MOTORCYCLE PARTS. All rights reserved.</p>
            <p class="mb-0" style="color:var(--mb-muted);font-size:.8rem;">&#x1F1F5;&#x1F1ED; Made with passion for Filipino riders</p>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch('/cart/count').then(r => r.json()).then(data => {
        document.querySelectorAll('.cart-badge').forEach(b => {
            b.textContent = data.count;
            b.style.display = data.count > 0 ? 'flex' : 'none';
        });
    }).catch(() => {});
});
</script>
@stack('scripts')
</body>
</html>
