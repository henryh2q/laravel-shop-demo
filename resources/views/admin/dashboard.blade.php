@extends('layouts.admin')
@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Dashboard</h1>
    <span class="badge bg-slate-200 text-slate-700">Gateway: {{ $gateway }}</span>
</div>
<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    @foreach ([
        ['Net revenue', \App\Support\Money::format($stats['revenue'])],
        ['Paid orders', $stats['paid_orders']],
        ['Pending checkouts', $stats['pending_orders']],
        ['Low stock products', $stats['low_stock']],
    ] as [$label, $value])
        <div class="card p-4"><div class="text-sm text-slate-500">{{ $label }}</div><div class="mt-1 text-2xl font-bold">{{ $value }}</div></div>
    @endforeach
</div>
<div class="grid gap-6 lg:grid-cols-3">
    <div class="card overflow-x-auto lg:col-span-2">
        <div class="border-b p-4 font-semibold">Recent orders</div>
        @include('admin.orders._table', ['orders' => $recentOrders])
    </div>
    <div class="card">
        <div class="border-b p-4 font-semibold">Latest webhook events</div>
        <ul class="divide-y text-sm">
            @forelse ($recentEvents as $event)
                <li class="flex items-center justify-between p-3"><a href="{{ route('admin.webhooks.show', $event) }}" class="font-mono text-xs hover:underline">{{ $event->type }}</a><x-status-badge :status="$event->status" /></li>
            @empty
                <li class="p-4 text-slate-500">No events yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
