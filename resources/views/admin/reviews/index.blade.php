@extends('layouts.admin')
@section('title', 'Reviews')
@section('page-title', 'Review Moderation')

@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-gold);margin-bottom:1rem;">
            Pending Approval ({{ $pending->count() }})
        </h2>
        @forelse($pending as $review)
        <div class="dark-card p-3 mb-3">
            <div class="d-flex gap-3 align-items-start">
                <div class="reviewer-avatar">{{ substr($review->user?->name ?? 'A', 0, 1) }}</div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong style="font-size:.9rem;color:var(--mb-text);">{{ $review->user?->name ?? 'Anonymous' }}</strong>
                            <div class="stars" style="font-size:.75rem;">@for($s=1;$s<=5;$s++)<i class="bi bi-star{{ $s<=$review->rating?'-fill':'' }}"></i>@endfor</div>
                        </div>
                        <span style="font-size:.72rem;color:var(--mb-muted);">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    @if($review->bike_model)<div style="font-size:.75rem;color:var(--mb-muted);">&#x1F3CD;&#xFE0F; {{ $review->bike_model }}</div>@endif
                    <div style="font-size:.85rem;color:var(--mb-text);margin:.5rem 0;">{{ $review->comment }}</div>
                    <div style="font-size:.75rem;color:var(--mb-muted);">Product: <a href="{{ route('product.show',$review->product->slug) }}" style="color:var(--mb-gold);">{{ $review->product->name }}</a></div>
                    <div class="d-flex gap-2 mt-2">
                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm" style="background:rgba(0,200,83,0.15);color:var(--mb-green);border:1px solid rgba(0,200,83,0.3);"><i class="bi bi-check-lg me-1"></i>Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.hide', $review) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm" style="background:var(--mb-red-dim);color:var(--mb-red);border:1px solid rgba(229,57,53,0.3);"><i class="bi bi-eye-slash me-1"></i>Hide</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="dark-card p-4 text-center" style="color:var(--mb-green);">
            <i class="bi bi-check-circle fs-2"></i>
            <div class="mt-2">No pending reviews!</div>
        </div>
        @endforelse
    </div>

    <div class="col-lg-6">
        <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;color:var(--mb-muted);margin-bottom:1rem;">
            Approved (recent 20)
        </h2>
        @forelse($approved as $review)
        <div class="dark-card p-3 mb-3">
            <div class="d-flex gap-2 align-items-start">
                <div class="reviewer-avatar" style="width:32px;height:32px;font-size:.75rem;">{{ substr($review->user?->name ?? 'A', 0, 1) }}</div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong style="font-size:.85rem;color:var(--mb-text);">{{ $review->user?->name ?? 'Anonymous' }}</strong>
                        <div class="stars" style="font-size:.72rem;">@for($s=1;$s<=5;$s++)<i class="bi bi-star{{ $s<=$review->rating?'-fill':'' }}"></i>@endfor</div>
                    </div>
                    <div style="font-size:.82rem;color:var(--mb-text);">{{ Str::limit($review->comment, 100) }}</div>
                    {{-- Reply form --}}
                    @if(!$review->admin_reply)
                    <details class="mt-2">
                        <summary style="font-size:.78rem;color:var(--mb-gold);cursor:pointer;">+ Add Reply</summary>
                        <form method="POST" action="{{ route('admin.reviews.reply', $review) }}" class="mt-2">
                            @csrf
                            <textarea name="reply" class="form-control mb-2" rows="2" style="font-size:.82rem;"></textarea>
                            <button class="btn btn-sm btn-outline-gold">Post Reply</button>
                        </form>
                    </details>
                    @else
                    <div class="mt-1 p-2" style="background:var(--mb-gold-dim);border-radius:6px;font-size:.78rem;color:var(--mb-gold);">
                        <i class="bi bi-reply me-1"></i><strong>Reply:</strong> {{ $review->admin_reply }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div style="color:var(--mb-muted);font-size:.88rem;">No approved reviews yet.</div>
        @endforelse
    </div>
</div>
@endsection
