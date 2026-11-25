<?php

namespace App\Payments;

final readonly class GatewayEvent
{
    public const PAYMENT_SUCCEEDED = 'payment.succeeded';
    public const CHECKOUT_EXPIRED = 'checkout.expired';
    public const REFUND_UPDATED = 'refund.updated';
    public const UNHANDLED = 'unhandled';

    /**
     * @param  array<string, mixed>  $data  normalized fields (session_id, payment_id, refund_id, status)
     * @param  array<string, mixed>  $raw  original payload, stored for the admin event log
     */
    public function __construct(
        public string $id,
        public string $type,
        public string $originalType,
        public array $data,
        public array $raw,
    ) {}
}
