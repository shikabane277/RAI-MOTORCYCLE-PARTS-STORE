@extends('layouts.auth')

@section('auth-content')

<div class="auth-title">Reset Password</div>
<p class="auth-subtitle">Enter your new password below.</p>

@if ($errors->any())
<div class="alert mb-3" style="background:var(--mb-red-dim);border:1px solid rgba(229,57,53,0.3);color:var(--mb-red);border-radius:var(--mb-radius-sm);padding:.75rem 1rem;font-size:.88rem;">
    @foreach ($errors->all() as $error)<div><i class="bi bi-x-circle me-1"></i>{{ $error }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               required autocomplete="new-password" placeholder="Min. 8 characters">
    </div>
    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Confirm New Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation"
               class="form-control" required autocomplete="new-password" placeholder="Repeat new password">
    </div>
    <button type="submit" class="btn btn-gold w-100 py-2">
        <i class="bi bi-lock me-1"></i>Reset Password
    </button>
</form>

@endsection
