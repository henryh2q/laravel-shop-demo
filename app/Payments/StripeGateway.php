<?php

namespace App\Payments;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeGateway implements PaymentGateway
{
    public function __construct(
        private readonly StripeClient $stripe,
        private readonly string $webhookSecret,
    ) {}

    public function name(): string
    {
        return 'stripe';
    }

    public function createCheckout(Order $order): CheckoutSession
    {
        $order->loadMissing('items');

        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'customer_email' => $order->customer_email,
            'client_reference_id' => $order->number,
            'locale' => $order->locale,
            'line_items' => $order->items->map(fn ($item) => [
                'quantity' => $item->quantity,
                'price_data' => [
                    'currency' => $order->currency,
                    'unit_amount' => $item->unit_price,
                    'product_data' => ['name' => $item->product_name],
                ],
            ])->all(),
            'metadata' => ['order_number' => $order->number],
            'payment_intent_data' => ['metadata' => ['order_number' => $order->number]],
            'success_url' => route('checkout.complete', $order).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('cart.index'),
        ], [
            // Same order => same key => Stripe returns the same session instead of a new charge.
            'idempotency_key' => 'checkout-'.$order->idempotency_key,
        ]);

        return new CheckoutSession($session->id, $session->url);
    }

    public function refund(Order $order, Refund $refund): RefundResult
    {
        $stripeRefund = $this->stripe->refunds->create([
            'payment_intent' => $order->gateway_payment_id,
            'amount' => $refund->amount,
            'metadata' => ['order_number' => $order->number, 'refund_id' => $refund->id],
        ], [
            'idempotency_key' => 'refund-'.$refund->id,
        ]);

        return new RefundResult($stripeRefund->id, $this->mapRefundStatus($stripeRefund->status));
    }

    public function parseWebhook(Request $request): GatewayEvent
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                $this->webhookSecret,
            );
        } catch (SignatureVerificationException|UnexpectedValueException $e) {
            throw new InvalidWebhookSignature($e->getMessage(), previous: $e);
        }

        $object = $event->data->object;

        [$type, $data] = match ($event->type) {
            'checkout.session.completed', 'checkout.session.async_payment_succeeded' => $object->payment_status === 'paid'
                ? [GatewayEvent::PAYMENT_SUCCEEDED, ['session_id' => $object->id, 'payment_id' => $object->payment_intent]]
                : [GatewayEvent::UNHANDLED, []],
            'checkout.session.expired' => [GatewayEvent::CHECKOUT_EXPIRED, ['session_id' => $object->id]],
            'refund.created', 'refund.updated', 'refund.failed' => [GatewayEvent::REFUND_UPDATED, [
                'refund_id' => $object->id,
                'status' => $this->mapRefundStatus($object->status),
            ]],
            default => [GatewayEvent::UNHANDLED, []],
        };

        return new GatewayEvent($event->id, $type, $event->type, $data, $event->toArray());
    }

    private function mapRefundStatus(?string $status): string
    {
        return match ($status) {
            'succeeded' => 'succeeded',
            'failed', 'canceled' => 'failed',
            default => 'pending',
        };
    }
}
