<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    public $timestamps = false; // only has created_at

    protected $fillable = [
        'product_variant_id', 'change_qty', 'stock_after', 'reason', 'reference', 'created_by', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
