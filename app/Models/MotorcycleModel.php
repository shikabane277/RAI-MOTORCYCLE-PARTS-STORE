<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorcycleModel extends Model
{
    protected $fillable = ['make', 'model', 'year_start', 'year_end', 'engine_cc', 'slug', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_fitments')
                    ->withPivot('notes')
                    ->withTimestamps();
    }

    public function getDisplayNameAttribute(): string
    {
        $years = $this->year_start
            ? ($this->year_end && $this->year_end !== $this->year_start
                ? "{$this->year_start}–{$this->year_end}"
                : $this->year_start)
            : '';
        return trim("{$this->make} {$this->model} {$years}");
    }

    public function scopeByMake($query, string $make)
    {
        return $query->where('make', $make);
    }
}
