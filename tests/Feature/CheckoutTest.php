<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\WebhookEvent;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function startCheckout(Product $product, int $qty = 2): array
    {
        $this->post(route('cart.add', $product), ['quantity' => $qty])->assertRedirect(route('cart.index'));
        $this->get(route('checkout.create'))->assertOk();
        $token = session(CheckoutService::TOKEN_KEY);

        return ['token' => $token, 'name' => 'Test Buyer', 'email' => 'buyer@example.com'];
    }

    public function test_checkout_creates_order_with_server_side_prices(): void
    {
        $product = Product::factory()->create(['price' => 1250]);
        $form = $this->startCheckout($product);

        $response = $this->post(route('checkout.store'), $form);

        $order = Order::sole();
        $response->assertRedirect($order->checkout_url);
        $this->assertSame(2500, $order->total);
        $this->assertSame(Order::PENDING, $order->status);
    }

    public function test_double_submit_reuses_the_same_order_and_checkout(): void
    {
        $product = Product::factory()->create();
        $form = $this->startCheckout($product);

        $first = $this->post(route('checkout.store'), $form);
        $second = $this->post(route('checkout.store'), $form);

        $this->assertSame(1, Order::count());
        $this->assertSame($first->headers->get('Location'), $second->headers->get('Location'));
    }

    public function test_changing_the_cart_starts_a_new_attempt(): void
    {
        $product = Product::factory()->create();
        $form = $this->startCheckout($product);
        $this->post(route('checkout.store'), $form);

        $this->post(route('cart.add', $product));
        $this->get(route('checkout.create'));

        $this->assertNotSame($form['token'], session(CheckoutService::TOKEN_KEY));
    }

    public function test_forged_token_is_rejected(): void
    {
        $product = Product::factory()->create();
        $form = $this->startCheckout($product);

        $this->post(route('checkout.store'), [...$form, 'token' => (string) \Illuminate\Support\Str::uuid()])->assertStatus(419);
        $this->assertSame(0, Order::count());
    }

    public function test_paying_marks_order_paid_once_and_decrements_stock_once(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $form = $this->startCheckout($product, 3);
        $this->post(route('checkout.store'), $form);
        $order = Order::sole();

        $this->post(route('demo-gateway.pay', $order->gateway_session_id))->assertRedirect(route('checkout.complete', $order));
        $this->post(route('demo-gateway.pay', $order->gateway_session_id));

        $order->refresh();
        $this->assertSame(Order::PAID, $order->status);
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertSame(['processed', 'ignored'], WebhookEvent::orderBy('id')->pluck('status')->all());

        $this->get(route('checkout.complete', $order))->assertOk()->assertSee($order->number);
        $this->assertSame(0, app(\App\Services\Cart::class)->count());
    }

    public function test_order_page_is_private_to_the_buyer_session(): void
    {
        $product = Product::factory()->create();
        $this->post(route('checkout.store'), $this->startCheckout($product));
        $order = Order::sole();

        $this->flushSession();
        $this->get(route('checkout.complete', $order))->assertNotFound();
    }

    public function test_cannot_buy_more_than_stock(): void
    {
        $product = Product::factory()->create(['stock' => 1]);
        $form = $this->startCheckout($product, 2);

        $this->post(route('checkout.store'), $form)->assertSessionHasErrors('cart');
        $this->assertSame(0, Order::count());
    }
}
