<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'variant_sku', 'thread_size', 'thread_pitch', 'length_mm',
        'head_type', 'material', 'color', 'finish', 'pack_qty',
        'price', 'sale_price', 'stock_qty', 'low_stock_threshold', 'image_url', 'images', 'is_active',
    ];

    protected $casts = [
        'images'   => 'array',
        'price'    => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_qty > 0 && $this->stock_qty <= $this->low_stock_threshold;
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock_qty > 0;
    }

    public function getLabelAttribute(): string
    {
        $parts = array_filter([
            $this->thread_size,
            $this->length_mm ? $this->length_mm . 'mm' : null,
            $this->color,
            $this->pack_qty > 1 ? $this->pack_qty . 'pc' : null,
        ]);
        return implode(' × ', $parts);
    }
}
