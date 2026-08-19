@extends('layouts.auth')

@section('auth-content')

<div class="auth-title">Confirm Password</div>
<p class="auth-subtitle">This is a secure area. Please confirm your password to continue.</p>

@if ($errors->any())
<div class="alert mb-3" style="background:var(--mb-red-dim);border:1px solid rgba(229,57,53,0.3);color:var(--mb-red);border-radius:var(--mb-radius-sm);padding:.75rem 1rem;font-size:.88rem;">
    @foreach ($errors->all() as $error)<div><i class="bi bi-x-circle me-1"></i>{{ $error }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf
    <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               required autocomplete="current-password" placeholder="Enter your current password">
    </div>
    <button type="submit" class="btn btn-gold w-100 py-2">
        <i class="bi bi-shield-check me-1"></i>Confirm Password
    </button>
</form>

@endsection
