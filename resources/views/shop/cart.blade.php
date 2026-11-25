@extends('layouts.shop')
@section('title', __('Cart'))
@section('content')
<h1 class="mb-6 text-2xl font-bold">{{ __('Your cart') }}</h1>
@if ($lines->isEmpty())
    <div class="card p-8 text-center text-slate-500">
        {{ __('Your cart is empty.') }} <a href="{{ route('home') }}" class="text-brand-600 underline">{{ __('Continue shopping') }}</a>
    </div>
@else
    <form method="POST" action="{{ route('cart.update') }}" class="card divide-y divide-slate-100">
        @csrf @method('PATCH')
        @foreach ($lines as $line)
            <div class="flex items-center gap-4 p-4">
                <x-product-image :product="$line->product" :size="160" class="size-16 shrink-0 rounded-lg @container" />
                <div class="flex-1">
                    <a href="{{ route('products.show', $line->product) }}" class="font-medium hover:underline">{{ $line->product->translate('name') }}</a>
                    <div class="text-sm text-slate-500">{{ \App\Support\Money::format($line->product->price) }}</div>
                </div>
                <input type="number" name="items[{{ $line->product->id }}]" value="{{ $line->quantity }}" min="0" max="20" class="input w-20" aria-label="{{ __('Quantity') }}">
                <div class="w-24 text-right font-semibold">{{ \App\Support\Money::format($line->line_total) }}</div>
            </div>
        @endforeach
        <div class="flex flex-wrap items-center justify-between gap-4 p-4">
            <button class="btn-ghost">{{ __('Update cart') }}</button>
            <div class="text-lg">{{ __('Subtotal') }}: <strong>{{ \App\Support\Money::format($subtotal) }}</strong></div>
        </div>
    </form>
    <div class="mt-4 text-right">
        <a href="{{ route('checkout.create') }}" class="btn-primary">{{ __('Proceed to checkout') }}</a>
    </div>
@endif
@endsection
