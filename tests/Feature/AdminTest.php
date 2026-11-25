<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@demo.test')->sole();
    }

    private function paidOrder(int $total = 4000): Order
    {
        return Order::create([
            'number' => 'DS-PAID-1', 'idempotency_key' => 'paid-1', 'status' => Order::PAID,
            'customer_name' => 'A', 'customer_email' => 'a@example.com', 'subtotal' => $total, 'total' => $total,
            'gateway' => 'demo', 'gateway_session_id' => 'demo_cs_1', 'gateway_payment_id' => 'demo_pi_1',
        ]);
    }

    public function test_guests_and_non_admins_are_kept_out(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_demo_admin_can_log_in(): void
    {
        $this->post(route('admin.login'), ['email' => 'admin@demo.test', 'password' => 'demo1234'])
            ->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Net revenue');
    }

    public function test_every_admin_screen_renders(): void
    {
        $this->actingAs($this->admin());
        $order = Order::first();

        foreach ([
            route('admin.dashboard'), route('admin.orders.index'), route('admin.orders.show', $order),
            route('admin.products.index'), route('admin.products.create'), route('admin.products.edit', Product::first()),
            route('admin.pages.index'), route('admin.pages.create'), route('admin.webhooks.index'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_partial_then_full_refund(): void
    {
        $order = $this->paidOrder();
        $this->actingAs($this->admin());

        $this->post(route('admin.orders.refund', $order), ['amount' => '15.00', 'reason' => 'damaged'])->assertSessionHasNoErrors();
        $this->assertSame(Order::PARTIALLY_REFUNDED, $order->fresh()->status);
        $this->assertSame(1500, $order->fresh()->refunded_amount);

        $this->post(route('admin.orders.refund', $order), ['amount' => '25.00'])->assertSessionHasNoErrors();
        $this->assertSame(Order::REFUNDED, $order->fresh()->status);
        $this->assertSame(0, $order->fresh()->refundableAmount());
    }

    public function test_cannot_refund_more_than_paid(): void
    {
        $order = $this->paidOrder();
        $this->actingAs($this->admin());

        $this->post(route('admin.orders.refund', $order), ['amount' => '40.01'])->assertSessionHasErrors('amount');
        $this->assertSame(0, $order->refunds()->count());
    }

    public function test_cannot_refund_unpaid_order(): void
    {
        $order = $this->paidOrder();
        $order->update(['status' => Order::PENDING]);
        $this->actingAs($this->admin());

        $this->post(route('admin.orders.refund', $order), ['amount' => '1.00'])->assertSessionHasErrors('amount');
    }

    public function test_sample_orders_refund_offline_even_when_stripe_is_active(): void
    {
        $this->app->instance(\App\Payments\PaymentGateway::class, new \App\Payments\StripeGateway(new \Stripe\StripeClient('sk_test_dummy'), 'whsec_x'));
        $order = Order::where('gateway', 'sample')->where('status', Order::PAID)->first();
        $this->actingAs($this->admin());

        $this->post(route('admin.orders.refund', $order), ['amount' => '1.00'])->assertSessionHasNoErrors();
        $this->assertSame(Order::PARTIALLY_REFUNDED, $order->fresh()->status);
    }

    public function test_product_crud_with_translations(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.products.store'), [
            'sku' => 'NEW-001', 'name' => ['en' => 'Tea cup', 'ja' => '湯呑み', 'vi' => 'Chén trà'],
            'description' => ['en' => 'Small cup'], 'price' => '12.50', 'stock' => 5, 'is_active' => '1',
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'NEW-001')->sole();
        $this->assertSame(1250, $product->price);
        $this->assertSame('湯呑み', $product->translate('name', 'ja'));

        $this->put(route('admin.products.update', $product), [
            'sku' => 'NEW-001', 'name' => ['en' => 'Tea cup'], 'price' => '9.99', 'stock' => 0, 'is_active' => '0',
        ])->assertRedirect();
        $this->assertFalse($product->fresh()->is_active);
        $this->get(route('products.show', $product))->assertNotFound();

        $this->delete(route('admin.products.destroy', $product))->assertRedirect();
        $this->assertModelMissing($product);
    }

    public function test_cms_page_edit(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.pages.store'), [
            'slug' => 'faq', 'is_published' => '1',
            'title' => ['en' => 'FAQ', 'ja' => 'よくある質問'], 'body' => ['en' => 'Answers', 'ja' => '回答'],
        ])->assertRedirect(route('admin.pages.index'));

        $this->get(route('locale', 'ja'));
        $this->get(route('pages.show', 'faq'))->assertOk()->assertSee('よくある質問');
    }
}
