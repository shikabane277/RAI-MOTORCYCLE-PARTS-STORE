<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RAI MOTORCYCLE PARTS') — Your Trusted Motorcycle Parts Store</title>
    <meta name="description" content="@yield('meta_description', 'RAI MOTORCYCLE PARTS — Premium CNC-machined bolts, fasteners, and motorcycle accessories for Filipino riders. Titanium, stainless, anodized aluminum. Fast shipping via J&T and Ninja Van.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <script>
        (function() {
            var theme = localStorage.getItem('rai_theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- ── Announcement Bar --}}
<div class="announcement-bar text-center">
    <div class="container-fluid px-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-gold fw-semibold">&#x1F3CD;&#xFE0F; FREE SHIPPING on orders &#x20B1;1,500+</span>
            <span class="text-muted-custom">📦 J&amp;T Express Standard &amp; 🛵 Lalamove Express Same-Day (8:00 AM – 4:00 PM)</span>
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
                    <span class="text-gold">RAI</span> <span class="brand-text-main">MOTORCYCLE</span> <span style="color:var(--mb-muted);font-size:.7em;">PARTS</span>
                </span>
            </a>
            <form class="d-none d-lg-flex flex-grow-1 mx-4" action="{{ route('shop.index') }}" method="GET" style="max-width: 480px;">
                <div class="search-bar-wrap">
                    <input type="search" name="search" class="search-input-field"
                           placeholder="Search bolts, levers, foot pegs..." value="{{ request('search') }}">
                    <button class="search-btn" type="submit" aria-label="Search">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
            <button class="navbar-toggler border-0 ms-auto me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <i class="bi bi-list text-gold fs-4"></i>
            </button>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-dark-surface btn-sm d-flex align-items-center justify-content-center" id="theme-toggle-btn" style="border-radius:50px;width:38px;height:38px;" title="Toggle Light / Dark Mode">
                    <i class="bi bi-moon-stars-fill text-gold fs-6" id="theme-icon-dark"></i>
                    <i class="bi bi-sun-fill text-gold fs-6 d-none" id="theme-icon-light"></i>
                </button>
                @auth
                    <a href="{{ route('account.wishlist') }}" class="btn btn-dark-surface btn-sm d-none d-lg-flex align-items-center" style="border-radius:50px;" title="Wishlist"><i class="bi bi-heart"></i></a>
                    <a href="{{ route('account.dashboard') }}" class="btn btn-dark-surface btn-sm d-none d-lg-flex align-items-center" style="border-radius:50px;" title="My Account"><i class="bi bi-person"></i></a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-dark-surface btn-sm d-none d-lg-flex align-items-center" style="border-radius:50px;"><i class="bi bi-person me-1"></i>Login</a>
                @endauth
                <a href="{{ route('cart.index') }}" class="btn btn-dark-surface btn-sm position-relative" style="border-radius:50px;" title="Shopping Cart">
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
                        <ul class="dropdown-menu" style="background:var(--mb-card);border:1px solid var(--mb-border);border-radius:var(--mb-radius);min-width:220px;box-shadow:var(--mb-shadow);">
                            <li><a class="dropdown-item py-2 fw-bold text-gold border-bottom" style="border-color:var(--mb-border)!important;" href="{{ route('shop.index') }}">🔍 All Products</a></li>
                            @forelse($navCategories ?? [] as $cat)
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('shop.category', $cat->slug) }}" style="color:var(--mb-text);">
                                        <span>{{ $cat->icon ?? '🔩' }}</span>
                                        <span>{{ $cat->name }}</span>
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item py-2 text-muted">No categories configured</span></li>
                            @endforelse
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('shop.index') }}?sort=sale">&#x1F525; Deals</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('shop.index') }}?new=1">&#x2728; New Arrivals</a></li>
                    @auth @if(auth()->user()->isStaff())
                        <li class="nav-item"><a class="nav-link text-gold" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Admin Panel</a></li>
                    @endif @endauth
                </ul>
            </div>

            {{-- Mobile Search Field Bar --}}
            <form class="d-flex d-lg-none mt-2 px-1" action="{{ route('shop.index') }}" method="GET">
                <div class="search-bar-wrap w-100">
                    <input type="search" name="search" class="search-input-field"
                           placeholder="Search bolts, levers, foot pegs..." value="{{ request('search') }}">
                    <button class="search-btn" type="submit" aria-label="Search">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
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
    <div class="alert auto-dismiss d-flex flex-column gap-1" style="background:var(--mb-red-dim);border:1px solid rgba(229,57,53,0.3);color:var(--mb-red);border-radius:var(--mb-radius-sm);">
        @foreach($errors->all() as $err)
        <div><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $err }}</div>
        @endforeach
    </div>
    @endif
</div>
@endif

<main class="min-vh-100">
    @yield('content')
</main>

{{-- ── Footer --}}
<footer class="site-footer mt-5">
    <div class="container-xl py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <a class="navbar-brand me-4 d-flex align-items-center gap-2 mb-3" href="{{ route('home') }}">
                    <img src="/images/logo.png" alt="RAI MOTORCYCLE PARTS Logo" style="height:45px;width:45px;object-fit:cover;border-radius:50%;border:1px solid var(--mb-gold);">
                    <span style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1.3rem;">
                        <span class="text-gold">RAI</span> <span class="brand-text-main">MOTORCYCLE</span> <span style="color:var(--mb-muted);font-size:.7em;">PARTS</span>
                    </span>
                </a>
                <p style="color:var(--mb-muted);font-size:.9rem;line-height:1.7;">
                    Your trusted source for premium CNC-machined titanium, stainless, and aluminum motorcycle bolts, hardware, and custom accessories in the Philippines.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="btn btn-dark-surface btn-sm" style="border-radius:50px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-dark-surface btn-sm" style="border-radius:50px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-dark-surface btn-sm" style="border-radius:50px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="footer-heading">Quick Links</div>
                <ul class="footer-links">
                    <li><a href="{{ route('shop.index') }}">Shop All</a></li>
                    <li><a href="{{ route('shop.index') }}?sort=sale">Deals &amp; Sales</a></li>
                    <li><a href="{{ route('shop.index') }}?new=1">New Arrivals</a></li>
                    <li><a href="{{ route('order.track') }}">Track Order</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <div class="footer-heading">Customer Care</div>
                <ul class="footer-links">
                    <li><a href="{{ route('pages.shipping') }}">Shipping Policy</a></li>
                    <li><a href="{{ route('pages.returns') }}">Returns &amp; Warranty</a></li>
                    <li><a href="{{ route('pages.faq') }}">FAQ</a></li>
                    <li><a href="{{ route('pages.contact') }}">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <div class="footer-heading">Contact &amp; Payment</div>
                <p style="color:var(--mb-muted);font-size:.85rem;" class="mb-2"><i class="bi bi-geo-alt-fill text-gold me-2"></i>Quezon City, Metro Manila, Philippines</p>
                <p style="color:var(--mb-muted);font-size:.85rem;" class="mb-2"><i class="bi bi-envelope-fill text-gold me-2"></i>support@raimotorcycleparts.ph</p>
                <p style="color:var(--mb-muted);font-size:.85rem;" class="mb-3"><i class="bi bi-telephone-fill text-gold me-2"></i>+63 917 123 4567 (Viber Available)</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-dark border text-gold p-2">GCash</span>
                    <span class="badge bg-dark border text-gold p-2">QR Ph</span>
                    <span class="badge bg-dark border text-gold p-2">PayMongo</span>
                    <span class="badge bg-dark border text-gold p-2">COD Available</span>
                </div>
            </div>
        </div>
        <hr class="divider-gold">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3">
            <p class="mb-0" style="color:var(--mb-muted);font-size:.8rem;">&copy; {{ date('Y') }} RAI MOTORCYCLE PARTS. All rights reserved.</p>
            <p class="mb-0" style="color:var(--mb-muted);font-size:.8rem;">&#x1F1F5;&#x1F1ED; Made with passion for Filipino riders</p>
        </div>
    </div>
</footer>

{{-- ── Sticky Mobile Bottom Navigation Bar (Shopee / Lazada UX) ── --}}
<div class="mobile-bottom-nav">
    <a href="{{ route('home') }}" class="mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="bi bi-house-door"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('shop.index') }}" class="mobile-nav-item {{ request()->routeIs('shop.*') ? 'active' : '' }}">
        <i class="bi bi-grid-fill"></i>
        <span>Shop</span>
    </a>
    <a href="{{ route('cart.index') }}" class="mobile-nav-item {{ request()->routeIs('cart.*') ? 'active' : '' }}">
        <i class="bi bi-bag"></i>
        <span>Cart</span>
        <span class="mobile-badge cart-badge" style="display:none;">0</span>
    </a>
    @auth
        <a href="{{ route('account.wishlist') }}" class="mobile-nav-item {{ request()->routeIs('account.wishlist') ? 'active' : '' }}">
            <i class="bi bi-heart"></i>
            <span>Wishlist</span>
        </a>
        <a href="{{ route('account.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
            <i class="bi bi-person"></i>
            <span>Account</span>
        </a>
    @else
        <a href="{{ route('login') }}" class="mobile-nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
            <i class="bi bi-person-lock"></i>
            <span>Login</span>
        </a>
    @endauth
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Theme Switcher Logic
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
