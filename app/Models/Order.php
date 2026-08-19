<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'guest_name', 'guest_email', 'guest_phone',
        'ship_recipient', 'ship_phone', 'ship_line1', 'ship_barangay', 'ship_city',
        'ship_province', 'ship_region', 'ship_zip',
        'subtotal', 'shipping_fee', 'discount_total', 'grand_total', 'coupon_code',
        'payment_method', 'payment_status', 'status',
        'courier', 'tracking_number', 'notes', 'admin_notes', 'placed_at',
    ];

    protected $casts = [
        'placed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public static function generateOrderNumber(): string
    {
        return 'MB-' . date('Y') . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Guest';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending_payment'  => 'warning',
            'confirmed'        => 'info',
            'processing'       => 'primary',
            'shipped'          => 'info',
            'delivered'        => 'success',
            'completed'        => 'success',
            'cancelled'        => 'danger',
            'return_requested' => 'warning',
            'refunded'         => 'secondary',
            default            => 'secondary',
        };
    }
}
