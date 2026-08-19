@extends('layouts.auth')

@section('auth-content')

<div class="auth-title">Forgot Password?</div>
<p class="auth-subtitle">Enter your email and we'll send you a reset link.</p>

@if (session('status'))
<div class="alert mb-3" style="background:rgba(0,200,83,0.12);border:1px solid rgba(0,200,83,0.3);color:#00ff88;border-radius:var(--mb-radius-sm);padding:.75rem 1rem;font-size:.88rem;">
    <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
</div>
@endif

@if ($errors->any())
<div class="alert mb-3" style="background:var(--mb-red-dim);border:1px solid rgba(229,57,53,0.3);color:var(--mb-red);border-radius:var(--mb-radius-sm);padding:.75rem 1rem;font-size:.88rem;">
    @foreach ($errors->all() as $error)<div><i class="bi bi-x-circle me-1"></i>{{ $error }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-4">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" required autofocus placeholder="you@example.com">
    </div>
    <button type="submit" class="btn btn-gold w-100 py-2">
        <i class="bi bi-envelope me-1"></i>Send Reset Link
    </button>
</form>

<div class="text-center mt-3">
    <a href="{{ route('login') }}" style="color:var(--mb-muted);font-size:.88rem;">← Back to Sign In</a>
</div>

@endsection
