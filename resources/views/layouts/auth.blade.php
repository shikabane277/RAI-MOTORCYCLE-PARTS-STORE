<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RAI MOTORCYCLE PARTS') — Login</title>
    <meta name="description" content="Sign in to your RAI MOTORCYCLE PARTS account to track orders, manage your wishlist, and access exclusive rider deals.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .auth-page {
            min-height: 100vh;
            background: var(--mb-darker);
            display: flex;
            align-items: stretch;
        }
        .auth-left {
            flex: 1;
            background:
                linear-gradient(135deg, rgba(13,13,15,0.92) 0%, rgba(13,13,15,0.6) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="g" patternUnits="userSpaceOnUse" width="20" height="20"><path d="M0 10 L10 0 L20 10 L10 20Z" fill="none" stroke="rgba(245,166,35,0.07)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23g)"/></svg>');
            display: none;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 1024px) {
            .auth-left { display: flex; }
        }
        .auth-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(245,166,35,0.08) 0%, transparent 70%);
            top: -100px; left: -100px;
            border-radius: 50%;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(245,166,35,0.05) 0%, transparent 70%);
            bottom: -80px; right: -80px;
            border-radius: 50%;
        }
        .auth-right {
            width: 100%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.5rem;
            background: var(--mb-dark);
            border-left: 1px solid var(--mb-border);
        }
        @media (min-width: 1024px) {
            .auth-right { padding: 3rem; }
        }
        .auth-logo {
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 2.5rem;
        }
        .auth-title {
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: .4rem;
        }
        .auth-subtitle { color: var(--mb-muted); font-size: .92rem; margin-bottom: 2rem; }
        .auth-divider {
            display: flex; align-items: center; gap: 1rem;
            margin: 1.5rem 0;
            color: var(--mb-muted); font-size: .8rem;
        }
        .auth-divider::before, .auth-divider::after {
            content: ''; flex: 1; height: 1px; background: var(--mb-border);
        }
        .auth-feature { display: flex; align-items: flex-start; gap: .9rem; margin-bottom: 1.5rem; }
        .auth-feature-icon {
            width: 40px; height: 40px; flex-shrink: 0;
            background: var(--mb-gold-dim);
            border: 1px solid rgba(245,166,35,0.3);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
<div class="auth-page">

    {{-- ── Left Panel (shown on desktop) ──────────────────── --}}
    <div class="auth-left">
        <a href="{{ route('home') }}" class="auth-logo d-flex align-items-center gap-2">
            <img src="/images/logo.png" alt="RAI Logo" style="height:44px;width:44px;object-fit:cover;border-radius:50%;border:1px solid var(--mb-gold);">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1.3rem;letter-spacing:.03em;">
                <span class="text-gold">RAI</span> <span class="brand-text-main">MOTORCYCLE</span>
            </span>
        </a>

        <h1 style="font-family:'Rajdhani',sans-serif;font-size:2.5rem;font-weight:700;color:var(--mb-heading);line-height:1.15;margin-bottom:1rem;">
            Precision Parts.<br><span class="text-gold">Premium Experience.</span>
        </h1>
        <p style="color:var(--mb-muted);max-width:380px;line-height:1.7;margin-bottom:2.5rem;">
            CNC-machined bolts, levers, and fasteners for Filipino riders. Sign in to track orders, save your wishlist, and access exclusive deals.
        </p>

        <div class="auth-feature">
            <div class="auth-feature-icon">🔩</div>
            <div>
                <div style="font-weight:600;color:var(--mb-text);margin-bottom:.2rem;">Fitment Finder</div>
                <div style="color:var(--mb-muted);font-size:.85rem;">Browse parts tailored to your exact motorcycle model and year.</div>
            </div>
        </div>
        <div class="auth-feature">
            <div class="auth-feature-icon">📦</div>
            <div>
                <div style="font-weight:600;color:var(--mb-text);margin-bottom:.2rem;">Fast Nationwide Delivery</div>
                <div style="color:var(--mb-muted);font-size:.85rem;">Same-day dispatch for Metro Manila. J&T and Ninja Van nationwide.</div>
            </div>
        </div>
        <div class="auth-feature">
            <div class="auth-feature-icon">🏆</div>
            <div>
                <div style="font-weight:600;color:var(--mb-text);margin-bottom:.2rem;">Loyalty Rewards</div>
                <div style="color:var(--mb-muted);font-size:.85rem;">Earn points on every order. Redeem for discounts on future purchases.</div>
            </div>
        </div>

        <div style="margin-top:auto;padding-top:2rem;border-top:1px solid var(--mb-border);">
            <div style="font-size:.8rem;color:var(--mb-muted);">Trusted by riders across the Philippines</div>
            <div class="d-flex gap-3 mt-2">
                <span class="stars" style="font-size:.85rem;">★★★★★</span>
                <span style="font-size:.8rem;color:var(--mb-muted);">4.9 average from 200+ verified reviews</span>
            </div>
        </div>
    </div>

    {{-- ── Right Panel: Form ────────────────────────────────── --}}
    <div class="auth-right">
        {{-- Mobile logo --}}
        <a href="{{ route('home') }}" class="auth-logo d-lg-none d-flex align-items-center gap-2">
            <img src="/images/logo.png" alt="RAI Logo" style="height:36px;width:36px;object-fit:cover;border-radius:50%;border:1px solid var(--mb-gold);">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1.1rem;">
                <span class="text-gold">RAI</span> <span class="brand-text-main">MOTORCYCLE</span>
            </span>
        </a>

        @yield('auth-content')

        <p class="text-center mt-4" style="font-size:.8rem;color:var(--mb-muted);">
            <a href="{{ route('home') }}" style="color:var(--mb-muted);">← Back to Store</a>
        </p>
    </div>
</div>
</body>
</html>
