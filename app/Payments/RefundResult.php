<?php

namespace App\Payments;

final readonly class RefundResult
{
    /** @param 'pending'|'succeeded'|'failed' $status */
    public function __construct(public string $id, public string $status) {}
}
