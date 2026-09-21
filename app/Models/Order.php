<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_id',
        'total_amount',
        'status',
        'payment_method',
        'shipping_address_id',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'refund_status',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'refunded_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }
}
