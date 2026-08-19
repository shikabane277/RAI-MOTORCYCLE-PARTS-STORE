@extends('layouts.auth')

@section('auth-content')

<div class="auth-title">Create Account</div>
<p class="auth-subtitle">Join RAI MOTORCYCLE PARTS — free, fast, and built for riders</p>

{{-- Validation Errors --}}
@if ($errors->any())
<div class="alert mb-3" style="background:var(--mb-red-dim);border:1px solid rgba(229,57,53,0.3);color:var(--mb-red);border-radius:var(--mb-radius-sm);padding:.75rem 1rem;font-size:.88rem;">
    @foreach ($errors->all() as $error)
        <div><i class="bi bi-x-circle me-1"></i>{{ $error }}</div>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input
            id="name"
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name') }}"
            required
            autofocus
            autocomplete="name"
            placeholder="Juan dela Cruz"
        >
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input
            id="email"
            type="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email') }}"
            required
            autocomplete="username"
            placeholder="you@example.com"
        >
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">Phone Number <span style="color:var(--mb-muted);font-size:.8rem;">(Optional)</span></label>
        <input
            id="phone"
            type="text"
            name="phone"
            class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone') }}"
            placeholder="09XX XXX XXXX"
        >
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input
            id="password"
            type="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            required
            autocomplete="new-password"
            placeholder="Min. 8 characters"
        >
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            class="form-control"
            required
            autocomplete="new-password"
            placeholder="Repeat your password"
        >
    </div>

    <button type="submit" class="btn btn-gold w-100 py-2 mb-3">
        <i class="bi bi-person-plus me-1"></i>Create My Account
    </button>

    <p style="font-size:.75rem;color:var(--mb-muted);text-align:center;">
        By registering, you agree to our
        <a href="{{ route('terms') }}" style="color:var(--mb-gold);">Terms of Service</a> and
        <a href="{{ route('privacy') }}" style="color:var(--mb-gold);">Privacy Policy</a>.
    </p>
</form>

<div class="auth-divider">or register with</div>

<div class="d-flex flex-column gap-2 mb-3">
    <a href="{{ route('auth.social.redirect', 'google') }}" class="btn w-100 d-flex align-items-center justify-content-center gap-2 py-2"
       style="background:var(--mb-surface);border:1px solid var(--mb-border);color:var(--mb-text);font-size:.9rem;font-weight:500;border-radius:var(--mb-radius-sm);transition:all .2s ease;">
        <svg width="18" height="18" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
        </svg>
        Sign up with Google
    </a>
</div>

<div class="text-center" style="font-size:.9rem;color:var(--mb-muted);">
    Already have an account?
    <a href="{{ route('login') }}" class="text-gold fw-semibold ms-1">Sign In</a>
</div>

@endsection
