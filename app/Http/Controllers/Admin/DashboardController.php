<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\WebhookEvent;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $paidStatuses = [Order::PAID, Order::PARTIALLY_REFUNDED, Order::REFUNDED];

        return view('admin.dashboard', [
            'stats' => [
                'revenue' => (int) Order::whereIn('status', $paidStatuses)->sum('total') - (int) Order::sum('refunded_amount'),
                'paid_orders' => Order::whereIn('status', $paidStatuses)->count(),
                'pending_orders' => Order::where('status', Order::PENDING)->count(),
                'low_stock' => Product::active()->where('stock', '<', 5)->count(),
            ],
            'recentOrders' => Order::latest()->limit(8)->get(),
            'recentEvents' => WebhookEvent::latest()->limit(6)->get(),
            'gateway' => app(\App\Payments\PaymentGateway::class)->name(),
        ]);
    }
}
