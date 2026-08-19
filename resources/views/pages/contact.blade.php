@extends('layouts.app')
@section('title', 'Contact Us — RAI MOTORCYCLE PARTS')
@section('content')
<div class="container py-5">
    <div style="max-width:700px;">
        <h1 style="font-family:'Rajdhani',sans-serif;color:#fff;font-weight:700;" class="mb-3">
            <i class="bi bi-envelope text-gold me-2"></i>Contact Us
        </h1>
        <div class="dark-card p-4">
            <p style="color:var(--mb-text);line-height:1.8;font-size:.95rem;">
                Have questions about your order or need help finding the right part? Reach out to us:
            </p>
            <ul style="list-style:none;padding:0;color:var(--mb-text);line-height:2.2;font-size:.95rem;">
                <li><i class="bi bi-geo-alt text-gold me-2"></i><strong>Shop:</strong> RAI MOTORCYCLE PARTS</li>
                <li><i class="bi bi-telephone text-gold me-2"></i><strong>Viber:</strong> 09XX-XXX-XXXX</li>
                <li><i class="bi bi-facebook text-gold me-2"></i><strong>FB Page:</strong> RAI MOTORCYCLE PARTS</li>
                <li><i class="bi bi-envelope text-gold me-2"></i><strong>Email:</strong> support@raimotorcycleparts.ph</li>
            </ul>
            <p style="color:var(--mb-muted);font-size:.85rem;margin-top:1rem;">
                We typically respond within 1-2 hours during business hours (8:00 AM - 6:00 PM, Mon-Sat).
            </p>
        </div>
    </div>
</div>
@endsection
