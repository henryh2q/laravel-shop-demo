<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Refund;
use App\Payments\PaymentGateway;
use App\Payments\StripeGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\StripeClient;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_test_secret';

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->instance(PaymentGateway::class, new StripeGateway(new StripeClient('sk_test_dummy'), self::SECRET));
    }

    private function send(array $event, ?string $secret = self::SECRET)
    {
        $payload = json_encode($event);
        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);

        return $this->call('POST', route('webhooks.payments'), server: [
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
            'CONTENT_TYPE' => 'application/json',
        ], content: $payload);
    }

    private function pendingOrder(): Order
    {
        return Order::create([
            'number' => 'DS-TEST-1', 'idempotency_key' => 'key-1', 'status' => Order::PENDING,
            'customer_name' => 'A', 'customer_email' => 'a@example.com', 'subtotal' => 5000, 'total' => 5000,
            'gateway_session_id' => 'cs_test_123',
        ]);
    }

    private function completedEvent(string $id = 'evt_1'): array
    {
        return [
            'id' => $id, 'object' => 'event', 'type' => 'checkout.session.completed',
            'data' => ['object' => ['id' => 'cs_test_123', 'object' => 'checkout.session', 'payment_status' => 'paid', 'payment_intent' => 'pi_123']],
        ];
    }

    public function test_rejects_invalid_signature(): void
    {
        $this->pendingOrder();

        $this->send($this->completedEvent(), 'whsec_wrong')->assertStatus(400);

        $this->assertSame(Order::PENDING, Order::sole()->status);
    }

    public function test_marks_order_paid_and_ignores_replayed_event(): void
    {
        $this->pendingOrder();

        $this->send($this->completedEvent())->assertOk()->assertJson(['duplicate' => false]);
        $this->send($this->completedEvent())->assertOk()->assertJson(['duplicate' => true]);

        $order = Order::sole();
        $this->assertSame(Order::PAID, $order->status);
        $this->assertSame('pi_123', $order->gateway_payment_id);
    }

    public function test_second_completion_event_for_same_session_does_not_change_paid_order(): void
    {
        $this->pendingOrder();
        $this->send($this->completedEvent('evt_1'));
        $paidAt = Order::sole()->paid_at;

        $this->travel(5)->minutes();
        $this->send($this->completedEvent('evt_2'))->assertOk();

        $this->assertEquals($paidAt, Order::sole()->paid_at);
    }

    public function test_expired_checkout_cancels_pending_order(): void
    {
        $this->pendingOrder();

        $this->send(['id' => 'evt_exp', 'object' => 'event', 'type' => 'checkout.session.expired',
            'data' => ['object' => ['id' => 'cs_test_123', 'object' => 'checkout.session']]])->assertOk();

        $this->assertSame(Order::CANCELED, Order::sole()->status);
    }

    public function test_refund_webhook_updates_refund_and_order_status(): void
    {
        $order = $this->pendingOrder();
        $order->update(['status' => Order::PAID]);
        $refund = $order->refunds()->create(['amount' => 5000, 'status' => 'pending', 'gateway_refund_id' => 're_123']);

        $this->send(['id' => 'evt_re', 'object' => 'event', 'type' => 'refund.updated',
            'data' => ['object' => ['id' => 're_123', 'object' => 'refund', 'status' => 'succeeded']]])->assertOk();

        $this->assertSame('succeeded', $refund->fresh()->status);
        $this->assertSame(Order::REFUNDED, $order->fresh()->status);
    }
}
