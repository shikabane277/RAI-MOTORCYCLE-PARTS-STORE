<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'recipient_name', 'phone',
        'line1', 'barangay', 'city', 'province', 'region', 'zip_code', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->line1, $this->barangay, $this->city, $this->province, $this->zip_code,
        ]));
    }
}
