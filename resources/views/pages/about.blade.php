@extends('layouts.app')
@section('title','About RAI MOTORCYCLE PARTS')
@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <img src="/images/logo.png" alt="RAI Logo" style="height:64px;width:64px;object-fit:cover;border-radius:50%;border:2px solid var(--mb-gold);">
        <h1 style="font-family:'Rajdhani',sans-serif;color:var(--mb-heading);margin:0;">About RAI MOTORCYCLE PARTS</h1>
    </div>
    <p style="color:var(--mb-muted);max-width:700px;line-height:1.8;">
        RAI MOTORCYCLE PARTS is a Filipino-owned store specializing in CNC-machined motorcycle fasteners, levers, and accessories. Every part is precision-machined, individually inspected, and shipped fast to riders across the Philippines.
    </p>
</div>
@endsection
