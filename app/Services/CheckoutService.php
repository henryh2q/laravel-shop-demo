<?php

namespace App\Services;

use App\Models\Order;
use App\Payments\PaymentGateway;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public const TOKEN_KEY = 'checkout_token';

    public function __construct(
        private readonly Cart $cart,
        private readonly PaymentGateway $gateway,
        private readonly Session $session,
    ) {}

    /** One token per cart state, rendered into the checkout form as the idempotency key. */
    public function token(): string
    {
        if (! $this->session->has(self::TOKEN_KEY)) {
            $this->session->put(self::TOKEN_KEY, (string) Str::uuid());
        }

        return $this->session->get(self::TOKEN_KEY);
    }

    /**
     * Create the order and its hosted checkout. Calling this again with the same
     * token (double click, refresh, back button) returns the existing order and
     * checkout URL rather than creating a second payment.
     *
     * @param  array{name: string, email: string}  $customer
     */
    public function start(string $token, array $customer): Order
    {
        if ($existing = Order::where('idempotency_key', $token)->first()) {
            return $existing;
        }

        $lines = $this->cart->lines();

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages(['cart' => __('Your cart is empty.')]);
        }

        foreach ($lines as $line) {
            if ($line->product->stock < $line->quantity) {
                throw ValidationException::withMessages([
                    'cart' => __('Only :count left of :name.', ['count' => $line->product->stock, 'name' => $line->product->translate('name')]),
                ]);
            }
        }

        try {
            $order = DB::transaction(function () use ($token, $customer, $lines) {
                $subtotal = (int) $lines->sum('line_total');

                $order = Order::create([
                    'number' => 'DS-'.now()->format('ymd').'-'.strtoupper(Str::random(6)),
                    'idempotency_key' => $token,
                    'status' => Order::PENDING,
                    'customer_name' => $customer['name'],
                    'customer_email' => $customer['email'],
                    'locale' => app()->getLocale(),
                    'currency' => 'usd',
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                    'gateway' => $this->gateway->name(),
                ]);

                foreach ($lines as $line) {
                    $order->items()->create([
                        'product_id' => $line->product->id,
                        'product_name' => $line->product->translate('name'),
                        'unit_price' => $line->product->price,
                        'quantity' => $line->quantity,
                        'line_total' => $line->line_total,
                    ]);
                }

                return $order;
            });
        } catch (UniqueConstraintViolationException) {
            // A concurrent request with the same token won the race: reuse its order.
            return Order::where('idempotency_key', $token)->firstOrFail();
        }

        $session = $this->gateway->createCheckout($order);

        $order->update([
            'gateway_session_id' => $session->id,
            'checkout_url' => $session->url,
        ]);

        return $order;
    }
}
