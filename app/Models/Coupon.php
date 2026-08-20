<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_spend', 'usage_limit', 'usage_count',
        'starts_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
        'value'      => 'decimal:2',
        'min_spend'  => 'decimal:2',
    ];

    public function isValid(float $cartSubtotal = 0): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return false;
        if ($cartSubtotal < $this->min_spend) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal, float $shippingFee = 0): float
    {
        if ($this->type === 'free_shipping') {
            return $shippingFee > 0 ? $shippingFee : ($this->value > 0 ? $this->value : 89.00);
        }
        if ($this->type === 'percentage') {
            return round($subtotal * ($this->value / 100), 2);
        }
        return min($this->value, $subtotal);
    }
}
