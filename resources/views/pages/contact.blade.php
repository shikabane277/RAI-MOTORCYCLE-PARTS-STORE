@extends('layouts.app')
@section('title', 'Contact Us — RAI MOTORCYCLE PARTS')
@section('content')
<div class="container py-5">
    <div style="max-width:850px;" class="mx-auto">
        <h1 style="font-family:'Rajdhani',sans-serif;color:var(--mb-heading);font-weight:700;" class="mb-4 text-center">
            <i class="bi bi-envelope text-gold me-2"></i>Contact Us
        </h1>
        
        <div class="row g-4">
            <div class="col-md-5">
                <div class="dark-card p-4 h-100">
                    <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.2rem;font-weight:700;" class="text-gold mb-3">Get In Touch</h3>
                    <p style="color:var(--mb-text);line-height:1.7;font-size:.9rem;">
                        Have questions about your order, fitment, or need a custom bolt set? Send us a message or reach out on Viber!
                    </p>
                    <ul style="list-style:none;padding:0;color:var(--mb-text);line-height:2.4;font-size:.9rem;" class="mt-4">
                        <li><i class="bi bi-geo-alt text-gold me-2"></i>Quezon City, Metro Manila</li>
                        <li><i class="bi bi-telephone text-gold me-2"></i>+63 917 123 4567 (Viber)</li>
                        <li><i class="bi bi-facebook text-gold me-2"></i>RAI MOTORCYCLE PARTS</li>
                        <li><i class="bi bi-envelope text-gold me-2"></i>support@raimotorcycleparts.ph</li>
                    </ul>
                    <p style="color:var(--mb-muted);font-size:.8rem;margin-top:1.5rem;" class="border-top pt-3 border-secondary">
                        🕒 Operating Hours: 8:00 AM - 6:00 PM (Mon-Sat)
                    </p>
                </div>
            </div>

            <div class="col-md-7">
                <div class="dark-card p-4">
                    <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.2rem;font-weight:700;" class="text-gold mb-3">Send Us A Message</h3>
                    
                    @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label font-bold">Your Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Juan Dela Cruz" required value="{{ auth()->user()->name ?? old('name') }}">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label font-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="juan@gmail.com" required value="{{ auth()->user()->email ?? old('email') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label font-bold">Mobile / Viber #</label>
                                <input type="text" name="phone" class="form-control" placeholder="09171234567" value="{{ auth()->user()->phone ?? old('phone') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-bold">Subject</label>
                            <select name="subject" class="form-select">
                                <option value="Order Inquiry">Order Inquiry / Tracking</option>
                                <option value="Product Fitment">Product Fitment Question</option>
                                <option value="Custom Order">Custom Bolt / Wholesale Order</option>
                                <option value="Returns / Warranty">Returns &amp; Warranty</option>
                                <option value="General Question">General Question</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-bold">Message</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="How can we help you today?" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-gold w-100 py-2.5 font-bold">
                            <i class="bi bi-send me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
