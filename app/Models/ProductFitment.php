<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFitment extends Model
{
    protected $fillable = ['product_id', 'motorcycle_model_id', 'notes'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function motorcycleModel()
    {
        return $this->belongsTo(MotorcycleModel::class);
    }
}
