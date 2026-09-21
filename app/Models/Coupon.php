<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'float',
            'min_order_amount' => 'float',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
