<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Cart;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private readonly Cart $cart, private readonly CheckoutService $checkout) {}

    public function create()
    {
        $lines = $this->cart->lines();

        if ($lines->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return view('shop.checkout', [
            'lines' => $lines,
            'subtotal' => $this->cart->subtotal(),
            'token' => $this->checkout->token(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:strict', 'max:190'],
        ]);

        // Only the token issued to this session is accepted.
        abort_unless(hash_equals($this->checkout->token(), $data['token']), 419);

        $order = $this->checkout->start($data['token'], $data);

        if (! in_array($order->number, $request->session()->get('my_orders', []), true)) {
            $request->session()->push('my_orders', $order->number);
        }

        if ($order->status !== Order::PENDING) {
            return redirect()->route('checkout.complete', $order);
        }

        return redirect()->away($order->checkout_url);
    }

    public function complete(Request $request, Order $order)
    {
        // Order pages are only visible to the browser session that placed them.
        abort_unless(in_array($order->number, $request->session()->get('my_orders', []), true), 404);

        $paidForCurrentCart = $order->status !== Order::PENDING
            && $request->session()->get(CheckoutService::TOKEN_KEY) === $order->idempotency_key;

        if ($paidForCurrentCart) {
            $this->cart->clear();
        }

        return view('shop.complete', ['order' => $order->load('items')]);
    }
}
