<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DemoGatewayController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/products/{product}', [ShopController::class, 'show'])->name('products.show');
Route::get('/pages/{slug}', [ShopController::class, 'page'])->name('pages.show');
Route::get('/lang/{locale}', LocaleController::class)->name('locale');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart', [CartController::class, 'update'])->name('cart.update');

Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
Route::get('/orders/{order}/complete', [CheckoutController::class, 'complete'])->name('checkout.complete');

// Offline stand-in for Stripe's hosted checkout (only used when no Stripe keys are set).
Route::get('/demo-gateway/{session}', [DemoGatewayController::class, 'show'])->name('demo-gateway.show');
Route::post('/demo-gateway/{session}', [DemoGatewayController::class, 'pay'])->name('demo-gateway.pay');

Route::post('/webhooks/payments', WebhookController::class)->name('webhooks.payments');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'create'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'store'])->middleware('throttle:10,1');
    Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::resource('products', Admin\ProductController::class)->except('show');
        Route::resource('pages', Admin\PageController::class)->except('show');
        Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/refunds', [Admin\OrderController::class, 'refund'])->name('orders.refund');
        Route::get('webhook-events', [Admin\WebhookEventController::class, 'index'])->name('webhooks.index');
        Route::get('webhook-events/{webhookEvent}', [Admin\WebhookEventController::class, 'show'])->name('webhooks.show');
    });
});
