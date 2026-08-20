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
        'meta_title', 'meta_description', 'option_config',
    ];

    protected $casts = [
        'is_featured'    => 'boolean',
        'is_new_arrival' => 'boolean',
        'base_price'     => 'decimal:2',
        'option_config'  => 'array',
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

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $variant = $this->relationLoaded('variants') ? $this->variants->first() : $this->variants()->first();
        if ($variant && $variant->image_url) {
            return $variant->image_url;
        }
        return null;
    }

    public function getParsedOptionGroupsAttribute(): array
    {
        if (!empty($this->option_config) && is_array($this->option_config)) {
            return $this->option_config;
        }

        $variants = $this->relationLoaded('variants') ? $this->variants : $this->variants()->where('is_active', true)->get();
        if ($variants->isEmpty()) {
            return [];
        }

        $tier1Values = [];
        $tier2Values = [];
        $hasMultiTier = false;

        foreach ($variants as $v) {
            $label = $v->label;
            if (str_contains($label, ' - ')) {
                $parts = explode(' - ', $label, 2);
                $hasMultiTier = true;
                $tier1Values[] = [
                    'label' => trim($parts[0]),
                    'image' => $v->image_url,
                    'stock' => $v->stock_qty,
                ];
                $tier2Values[] = [
                    'label' => trim($parts[1]),
                    'image' => $v->image_url,
                    'stock' => $v->stock_qty,
                ];
            } else {
                $tier1Values[] = [
                    'label' => trim($label),
                    'image' => $v->image_url,
                    'stock' => $v->stock_qty,
                ];
            }
        }

        $groups = [];

        // Group 1
        $uniqueT1 = collect($tier1Values)->unique('label')->values()->all();
        $groups[] = [
            'name'          => $hasMultiTier ? 'Size / Specification' : 'Option',
            'display_style' => 'swatch',
            'values'        => array_map(fn($item) => [
                'id'       => \Illuminate\Support\Str::slug($item['label']),
                'label'    => $item['label'],
                'image'    => $item['image'] ?? null,
                'disabled' => false,
            ], $uniqueT1),
        ];

        // Group 2
        if ($hasMultiTier) {
            $uniqueT2 = collect($tier2Values)->unique('label')->values()->all();
            $groups[] = [
                'name'          => 'Color / Finish',
                'display_style' => 'swatch',
                'values'        => array_map(fn($item) => [
                    'id'       => \Illuminate\Support\Str::slug($item['label']),
                    'label'    => $item['label'],
                    'image'    => $item['image'] ?? null,
                    'disabled' => false,
                ], $uniqueT2),
            ];
        }

        return $groups;
    }
}

