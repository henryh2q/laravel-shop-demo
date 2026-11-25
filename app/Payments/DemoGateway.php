<?php

namespace App\Payments;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Offline gateway used when no Stripe keys are configured. It mimics a hosted
 * checkout page plus HMAC-signed webhooks, so the full flow (webhook, dedup,
 * refund) can be exercised without any external account.
 */
class DemoGateway implements PaymentGateway
{
    public function __construct(private readonly string $secret) {}

    public function name(): string
    {
        return 'demo';
    }

    public function createCheckout(Order $order): CheckoutSession
    {
        $id = $order->gateway_session_id ?? 'demo_cs_'.Str::random(24);

        return new CheckoutSession($id, route('demo-gateway.show', ['session' => $id]));
    }

    public function refund(Order $order, Refund $refund): RefundResult
    {
        return new RefundResult($refund->gateway_refund_id ?? 'demo_re_'.Str::random(20), 'succeeded');
    }

    public function parseWebhook(Request $request): GatewayEvent
    {
        $payload = $request->getContent();

        if (! hash_equals($this->sign($payload), (string) $request->header('Demo-Signature'))) {
            throw new InvalidWebhookSignature('Invalid demo signature.');
        }

        $event = json_decode($payload, true, flags: JSON_THROW_ON_ERROR);

        $type = match ($event['type']) {
            'checkout.completed' => GatewayEvent::PAYMENT_SUCCEEDED,
            'checkout.expired' => GatewayEvent::CHECKOUT_EXPIRED,
            default => GatewayEvent::UNHANDLED,
        };

        return new GatewayEvent($event['id'], $type, $event['type'], $event['data'] ?? [], $event);
    }

    public function sign(string $payload): string
    {
        return hash_hmac('sha256', $payload, $this->secret);
    }
}
