<?php

namespace App\Providers;

use App\Payments\DemoGateway;
use App\Payments\PaymentGateway;
use App\Payments\StripeGateway;
use App\Services\Cart;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentGateway::class, function ($app) {
            $secret = config('services.stripe.secret');

            // Real Stripe (test mode) when keys are configured, offline demo gateway otherwise.
            return $secret
                ? new StripeGateway(new StripeClient($secret), (string) config('services.stripe.webhook_secret'))
                : new DemoGateway((string) config('app.key'));
        });

        $this->app->scoped(Cart::class, fn ($app) => new Cart($app['session.store']));
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::useTailwind();

        View::composer(['layouts.shop'], function ($view) {
            $view->with('cartCount', app(Cart::class)->count());
        });
    }
}
