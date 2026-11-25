@extends('layouts.shop')
@section('title', __('Order :number', ['number' => $order->number]))
@section('content')
@if ($order->status === 'pending')
    <meta http-equiv="refresh" content="3">
@endif
<div class="card mx-auto max-w-xl p-8 text-center">
    @if ($order->status === 'pending')
        <h1 class="text-2xl font-bold">{{ __('Confirming your payment…') }}</h1>
        <p class="mt-2 text-slate-600">{{ __('We are waiting for the payment provider to confirm. This page refreshes automatically.') }}</p>
    @elseif ($order->status === 'canceled')
        <h1 class="text-2xl font-bold">{{ __('This checkout expired') }}</h1>
    @else
        <h1 class="text-2xl font-bold text-emerald-700">{{ __('Thank you! Your order is confirmed.') }}</h1>
    @endif
    <p class="mt-4 text-sm text-slate-500">{{ __('Order') }} <strong>{{ $order->number }}</strong> · <x-status-badge :status="$order->status" /></p>
    <div class="mt-6 text-left">
        @foreach ($order->items as $item)
            <div class="flex justify-between py-1 text-sm"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><span>{{ \App\Support\Money::format($item->line_total) }}</span></div>
        @endforeach
        <div class="mt-2 flex justify-between border-t pt-2 font-semibold"><span>{{ __('Total') }}</span><span>{{ \App\Support\Money::format($order->total) }}</span></div>
    </div>
    <a href="{{ route('home') }}" class="btn-ghost mt-6">{{ __('Continue shopping') }}</a>
</div>
@endsection
