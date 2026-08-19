@extends('layouts.auth')

@section('auth-content')

<div class="text-center mb-4">
    <div style="width:64px;height:64px;background:rgba(245,166,35,0.12);border:1px solid rgba(245,166,35,0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.5rem;">
        ✉️
    </div>
    <div class="auth-title" style="font-size:1.4rem;">Verify Your Email</div>
    <p class="auth-subtitle">
        Thanks for signing up! Before getting started, please verify your email address by clicking the link we sent you.
    </p>
</div>

@if (session('status') === 'verification-link-sent')
<div class="alert mb-3" style="background:rgba(0,200,83,0.12);border:1px solid rgba(0,200,83,0.3);color:#00ff88;border-radius:var(--mb-radius-sm);padding:.75rem 1rem;font-size:.88rem;text-align:center;">
    <i class="bi bi-check-circle me-1"></i> A new verification link has been sent to your email.
</div>
@endif

<form method="POST" action="{{ route('verification.send') }}" class="mb-3">
    @csrf
    <button type="submit" class="btn btn-gold w-100 py-2">
        <i class="bi bi-send me-1"></i>Resend Verification Email
    </button>
</form>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-dark-surface w-100">
        <i class="bi bi-box-arrow-left me-1"></i>Logout
    </button>
</form>

@endsection
