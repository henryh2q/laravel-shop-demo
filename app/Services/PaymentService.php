<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Refund;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Payments\DemoGateway;
use App\Payments\GatewayEvent;
use App\Payments\PaymentGateway;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class PaymentService
{
    public function __construct(private readonly PaymentGateway $gateway) {}

    /**
     * Process a verified webhook event exactly once.
     *
     * @return bool false when the event was a duplicate delivery
     */
    public function handle(GatewayEvent $event): bool
    {
        try {
            $log = WebhookEvent::create([
                'gateway' => $this->gateway->name(),
                'event_id' => $event->id,
                'type' => $event->originalType,
                'payload' => $event->raw,
            ]);
        } catch (UniqueConstraintViolationException) {
            return false; // Already received: gateways retry, we must not double-process.
        }

        try {
            $handled = match ($event->type) {
                GatewayEvent::PAYMENT_SUCCEEDED => $this->markPaid($event->data['session_id'], $event->data['payment_id'] ?? null),
                GatewayEvent::CHECKOUT_EXPIRED => $this->markExpired($event->data['session_id']),
                GatewayEvent::REFUND_UPDATED => $this->syncRefund($event->data['refund_id'], $event->data['status']),
                default => false,
            };

            $log->update(['status' => $handled ? 'processed' : 'ignored']);
        } catch (Throwable $e) {
            $log->update(['status' => 'failed', 'error' => $e->getMessage()]);
            throw $e;
        }

        return true;
    }

    public function markPaid(string $sessionId, ?string $paymentId): bool
    {
        return DB::transaction(function () use ($sessionId, $paymentId) {
            $order = Order::where('gateway_session_id', $sessionId)->lockForUpdate()->first();

            if (! $order || $order->status !== Order::PENDING) {
                return false; // Unknown or already handled: state transitions only happen once.
            }

            $order->update([
                'status' => Order::PAID,
                'gateway_payment_id' => $paymentId,
                'paid_at' => now(),
            ]);

            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::whereKey($item->product_id)
                        ->where('stock', '>=', $item->quantity)
                        ->decrement('stock', $item->quantity);
                }
            }

            return true;
        });
    }

    public function markExpired(string $sessionId): bool
    {
        return (bool) Order::where('gateway_session_id', $sessionId)
            ->where('status', Order::PENDING)
            ->update(['status' => Order::CANCELED]);
    }

    public function refund(Order $order, int $amount, ?User $admin = null, ?string $reason = null): Refund
    {
        $refund = DB::transaction(function () use ($order, $amount, $admin, $reason) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($amount < 1 || $amount > $order->refundableAmount()) {
                throw ValidationException::withMessages(['amount' => __('The refund amount is not valid for this order.')]);
            }

            return $order->refunds()->create([
                'admin_id' => $admin?->id,
                'amount' => $amount,
                'reason' => $reason,
                'status' => 'pending',
            ]);
        });

        try {
            $result = $this->gatewayFor($order)->refund($order->fresh(), $refund);
        } catch (Throwable $e) {
            $refund->update(['status' => 'failed']);
            throw ValidationException::withMessages(['amount' => 'The payment provider rejected the refund: '.$e->getMessage()]);
        }

        $refund->update(['gateway_refund_id' => $result->id]);
        $this->applyRefundStatus($refund, $result->status);

        return $refund->fresh();
    }

    /** Seeded sample orders and offline-demo orders are refunded offline, real ones through the live gateway. */
    private function gatewayFor(Order $order): PaymentGateway
    {
        return in_array($order->gateway, ['demo', 'sample'], true) && $this->gateway->name() !== 'demo'
            ? new DemoGateway((string) config('app.key'))
            : $this->gateway;
    }

    public function syncRefund(string $gatewayRefundId, string $status): bool
    {
        $refund = Refund::where('gateway_refund_id', $gatewayRefundId)->first();

        if (! $refund) {
            return false;
        }

        $this->applyRefundStatus($refund, $status);

        return true;
    }

    private function applyRefundStatus(Refund $refund, string $status): void
    {
        DB::transaction(function () use ($refund, $status) {
            $order = Order::whereKey($refund->order_id)->lockForUpdate()->firstOrFail();

            $refund->update(['status' => $status]);

            $refunded = (int) $order->refunds()->where('status', 'succeeded')->sum('amount');

            $order->update([
                'refunded_amount' => $refunded,
                'status' => match (true) {
                    $refunded >= $order->total => Order::REFUNDED,
                    $refunded > 0 => Order::PARTIALLY_REFUNDED,
                    default => $order->status,
                },
            ]);
        });
    }
}
