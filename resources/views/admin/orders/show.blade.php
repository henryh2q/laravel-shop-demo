@extends('layouts.admin')
@section('title', $order->number)
@section('content')
<a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-500 hover:underline">← Orders</a>
<div class="mb-6 mt-2 flex flex-wrap items-center gap-3">
    <h1 class="text-2xl font-bold">{{ $order->number }}</h1>
    <x-status-badge :status="$order->status" />
</div>
<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="p-3">Item</th><th class="p-3 text-right">Price</th><th class="p-3 text-right">Qty</th><th class="p-3 text-right">Total</th></tr></thead>
                <tbody class="divide-y">
                    @foreach ($order->items as $item)
                        <tr><td class="p-3">{{ $item->product_name }}</td><td class="p-3 text-right">{{ \App\Support\Money::format($item->unit_price) }}</td><td class="p-3 text-right">{{ $item->quantity }}</td><td class="p-3 text-right">{{ \App\Support\Money::format($item->line_total) }}</td></tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t font-semibold">
                    <tr><td class="p-3" colspan="3">Total</td><td class="p-3 text-right">{{ \App\Support\Money::format($order->total) }}</td></tr>
                    @if ($order->refunded_amount)
                        <tr class="text-red-600"><td class="p-3" colspan="3">Refunded</td><td class="p-3 text-right">−{{ \App\Support\Money::format($order->refunded_amount) }}</td></tr>
                    @endif
                </tfoot>
            </table>
        </div>
        <div class="card">
            <div class="border-b p-4 font-semibold">Refund history</div>
            <ul class="divide-y text-sm">
                @forelse ($order->refunds as $refund)
                    <li class="flex flex-wrap items-center justify-between gap-2 p-3">
                        <span>#{{ $refund->id }} · {{ \App\Support\Money::format($refund->amount) }} · {{ $refund->reason ?: 'No reason' }}<span class="block text-xs text-slate-500">{{ $refund->admin?->name }} · {{ $refund->created_at }} · {{ $refund->gateway_refund_id }}</span></span>
                        <x-status-badge :status="$refund->status" />
                    </li>
                @empty
                    <li class="p-4 text-slate-500">No refunds.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="space-y-6">
        <div class="card space-y-2 p-4 text-sm">
            <div class="font-semibold">Customer</div>
            <div>{{ $order->customer_name }}<br>{{ $order->customer_email }}</div>
            <div class="pt-2 font-semibold">Payment</div>
            <dl class="grid grid-cols-3 gap-1 break-all text-xs">
                <dt class="text-slate-500">Gateway</dt><dd class="col-span-2">{{ $order->gateway }}</dd>
                <dt class="text-slate-500">Session</dt><dd class="col-span-2 font-mono">{{ $order->gateway_session_id }}</dd>
                <dt class="text-slate-500">Payment</dt><dd class="col-span-2 font-mono">{{ $order->gateway_payment_id ?? '—' }}</dd>
                <dt class="text-slate-500">Idem. key</dt><dd class="col-span-2 font-mono">{{ $order->idempotency_key }}</dd>
                <dt class="text-slate-500">Paid at</dt><dd class="col-span-2">{{ $order->paid_at ?? '—' }}</dd>
                <dt class="text-slate-500">Locale</dt><dd class="col-span-2">{{ $order->locale }}</dd>
            </dl>
        </div>
        @if ($order->refundableAmount() > 0)
            <form method="POST" action="{{ route('admin.orders.refund', $order) }}" data-once class="card space-y-3 p-4">
                @csrf
                <div class="font-semibold">Issue refund</div>
                <div><label class="label" for="amount">Amount (USD), max {{ number_format($order->refundableAmount() / 100, 2) }}</label>
                    <input id="amount" name="amount" type="number" step="0.01" min="0.01" max="{{ $order->refundableAmount() / 100 }}" value="{{ number_format($order->refundableAmount() / 100, 2, '.', '') }}" class="input" required></div>
                <div><label class="label" for="reason">Reason</label><input id="reason" name="reason" class="input" maxlength="255"></div>
                <button type="submit" class="btn-danger w-full" data-label="Refunding…">Refund</button>
            </form>
        @endif
    </div>
</div>
@endsection
