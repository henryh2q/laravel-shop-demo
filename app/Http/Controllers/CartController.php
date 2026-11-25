<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly Cart $cart) {}

    public function index()
    {
        return view('shop.cart', ['lines' => $this->cart->lines(), 'subtotal' => $this->cart->subtotal()]);
    }

    public function add(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $data = $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:'.Cart::MAX_QTY]]);
        $this->cart->add($product, (int) ($data['quantity'] ?? 1));

        return redirect()->route('cart.index')->with('status', __('Added to cart.'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'items' => ['array'],
            'items.*' => ['integer', 'min:0', 'max:'.Cart::MAX_QTY],
        ]);

        foreach ($data['items'] ?? [] as $productId => $quantity) {
            $this->cart->update((int) $productId, (int) $quantity);
        }

        return redirect()->route('cart.index')->with('status', __('Cart updated.'));
    }
}
