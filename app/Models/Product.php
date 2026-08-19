<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'brand_id', 'category_id', 'description', 'short_description',
        'base_price', 'weight_grams', 'status', 'is_featured', 'is_new_arrival', 'views',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_featured'    => 'boolean',
        'is_new_arrival' => 'boolean',
        'base_price'     => 'decimal:2',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function motorcycleModels()
    {
        return $this->belongsToMany(MotorcycleModel::class, 'product_fitments')
                    ->withPivot('notes')
                    ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    public function getLowestPriceAttribute(): float
    {
        return $this->variants()->where('is_active', true)->min('price') ?? $this->base_price;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('status', 'active');
    }

    public function scopeNewArrivals($query)
    {
        return $query->where('is_new_arrival', true)->where('status', 'active');
    }

    public function scopeBestSellers($query)
    {
        return $query->orderByDesc('views');
    }

    public function getTotalStockAttribute(): int
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->sum('stock_qty');
        }
        return $this->variants()->sum('stock_qty');
    }
}

