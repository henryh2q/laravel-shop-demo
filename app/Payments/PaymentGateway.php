<?php

namespace App\Payments;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function name(): string;

    /** Create (or idempotently re-fetch) a hosted checkout for the order. */
    public function createCheckout(Order $order): CheckoutSession;

    /** Refund part or all of a captured payment. Must be safe to retry. */
    public function refund(Order $order, Refund $refund): RefundResult;

    /**
     * Verify the webhook signature and turn the payload into a gateway-neutral event.
     *
     * @throws InvalidWebhookSignature
     */
    public function parseWebhook(Request $request): GatewayEvent;
}
