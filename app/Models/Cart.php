<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id', 'coupon_code'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getItemCountAttribute(): int
    {
        return $this->items()->sum('qty');
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items()->with('variant')->get()->sum(function ($item) {
            return $item->variant->effective_price * $item->qty;
        });
    }
}
