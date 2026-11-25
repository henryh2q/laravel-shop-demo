<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_renders_in_every_language(): void
    {
        $product = Product::first();

        foreach (['en' => 'Add to cart', 'ja' => 'カートに追加', 'vi' => 'Thêm vào giỏ'] as $locale => $label) {
            $this->get(route('locale', $locale))->assertRedirect();
            $this->get('/')->assertOk()->assertSee($product->translate('name', $locale));
            $this->get(route('products.show', $product))->assertOk()->assertSee($label);
        }
    }

    public function test_demo_banner_is_always_visible(): void
    {
        $this->get('/')->assertSee('Portfolio demo');
    }

    public function test_cms_page_is_served(): void
    {
        $this->get(route('pages.show', 'about'))->assertOk()->assertSee('idempotency key');
    }

    public function test_unknown_locale_is_rejected(): void
    {
        $this->get('/lang/fr')->assertNotFound();
    }
}
