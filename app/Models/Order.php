<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const PARTIALLY_REFUNDED = 'partially_refunded';
    public const REFUNDED = 'refunded';
    public const CANCELED = 'canceled';

    protected $fillable = [
        'number', 'idempotency_key', 'status', 'customer_name', 'customer_email', 'locale', 'currency',
        'subtotal', 'total', 'refunded_amount', 'gateway', 'gateway_session_id', 'gateway_payment_id',
        'checkout_url', 'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'total' => 'integer',
        'refunded_amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class)->latest();
    }

    public function refundableAmount(): int
    {
        if (! in_array($this->status, [self::PAID, self::PARTIALLY_REFUNDED], true)) {
            return 0;
        }

        $reserved = $this->refunds()->whereIn('status', ['pending', 'succeeded'])->sum('amount');

        return max(0, $this->total - (int) $reserved);
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }
}
