<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_sent(): void
    {
        $this->get('/')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeaderMissing('X-Powered-By')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_custom_404_page(): void
    {
        $this->get('/does-not-exist')->assertNotFound()->assertSee('Page not found')->assertDontSee('Symfony');
    }

    public function test_errors_never_leak_details_in_production(): void
    {
        config(['app.debug' => false]);
        Route::get('/boom', fn () => throw new \RuntimeException('secret-internal-detail'))->middleware('web');

        $this->get('/boom')
            ->assertStatus(500)
            ->assertSee('Something went wrong')
            ->assertDontSee('secret-internal-detail')
            ->assertDontSee('RuntimeException');
    }
}
