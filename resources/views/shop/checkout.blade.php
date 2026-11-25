@extends('layouts.shop')
@section('title', __('Checkout'))
@section('content')
<h1 class="mb-6 text-2xl font-bold">{{ __('Checkout') }}</h1>
<div class="grid gap-6 md:grid-cols-5">
    <form method="POST" action="{{ route('checkout.store') }}" data-once class="card space-y-4 p-6 md:col-span-3">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="label" for="name">{{ __('Full name') }}</label>
            <input id="name" name="name" value="{{ old('name') }}" required class="input">
        </div>
        <div>
            <label class="label" for="email">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input">
        </div>
        <p class="rounded-lg bg-brand-50 p-3 text-sm text-slate-700">
            {{ __('You will be redirected to a secure payment page. Test card: 4242 4242 4242 4242, any future expiry date and any CVC.') }}
        </p>
        <button type="submit" class="btn-primary w-full" data-label="{{ __('Redirecting…') }}">{{ __('Pay :amount', ['amount' => \App\Support\Money::format($subtotal)]) }}</button>
    </form>
    <aside class="card h-fit p-6 md:col-span-2">
        <h2 class="mb-3 font-semibold">{{ __('Order summary') }}</h2>
        @foreach ($lines as $line)
            <div class="flex justify-between py-1 text-sm">
                <span>{{ $line->product->translate('name') }} × {{ $line->quantity }}</span>
                <span>{{ \App\Support\Money::format($line->line_total) }}</span>
            </div>
        @endforeach
        <div class="mt-3 flex justify-between border-t border-slate-200 pt-3 font-semibold">
            <span>{{ __('Total') }}</span><span>{{ \App\Support\Money::format($subtotal) }}</span>
        </div>
    </aside>
</div>
@endsection
