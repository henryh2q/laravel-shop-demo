<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Payments\DemoGateway;
use App\Payments\PaymentGateway;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DemoGatewayController extends Controller
{
    public function __construct(private readonly PaymentGateway $gateway)
    {
        abort_unless($gateway instanceof DemoGateway, 404);
    }

    public function show(string $session)
    {
        $order = Order::where('gateway_session_id', $session)->firstOrFail();

        return view('shop.demo-gateway', compact('order', 'session'));
    }

    /**
     * Simulates the gateway: builds a signed event and pushes it through the
     * same webhook pipeline that real Stripe events use.
     */
    public function pay(Request $request, string $session, PaymentService $payments)
    {
        $order = Order::where('gateway_session_id', $session)->firstOrFail();

        $payload = json_encode([
            'id' => 'demo_evt_'.Str::random(20),
            'type' => 'checkout.completed',
            'data' => ['session_id' => $session, 'payment_id' => 'demo_pi_'.Str::random(20)],
        ]);

        $webhook = Request::create(route('webhooks.payments'), 'POST', content: $payload);
        $webhook->headers->set('Demo-Signature', $this->gateway->sign($payload));

        $payments->handle($this->gateway->parseWebhook($webhook));

        return redirect()->route('checkout.complete', $order);
    }
}
