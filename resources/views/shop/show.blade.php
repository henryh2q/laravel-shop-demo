@extends('layouts.shop')
@section('title', $product->translate('name'))
@section('content')
<div class="grid gap-8 md:grid-cols-2">
    <figure>
        <x-product-image :product="$product" :size="900" class="card aspect-square w-full @container" />
        @if ($credit = $product->imageCredit())
            <figcaption class="mt-2 text-xs text-slate-500">{{ __('Photo') }}: <a href="{{ $credit['url'] }}" class="underline" target="_blank" rel="noopener">{{ $credit['name'] }}</a> / <a href="https://unsplash.com/?utm_source=laravel_shop_demo&utm_medium=referral" class="underline" target="_blank" rel="noopener">Unsplash</a></figcaption>
        @endif
    </figure>
    <div>
        <div class="text-sm text-slate-500">{{ $product->category?->translate('name') }} · SKU {{ $product->sku }}</div>
        <h1 class="mt-1 text-2xl font-bold">{{ $product->translate('name') }}</h1>
        <div class="mt-3 text-2xl font-semibold text-brand-600">{{ \App\Support\Money::format($product->price) }}</div>
        <p class="mt-4 whitespace-pre-line text-slate-700">{{ $product->translate('description') }}</p>
        <p class="mt-4 text-sm {{ $product->stock > 0 ? 'text-emerald-700' : 'text-red-600' }}">
            {{ $product->stock > 0 ? __(':count in stock', ['count' => $product->stock]) : __('Out of stock') }}
        </p>
        @if ($product->stock > 0)
            <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-6 flex gap-2">
                @csrf
                <input type="number" name="quantity" value="1" min="1" max="{{ min(20, $product->stock) }}" class="input w-24">
                <button class="btn-primary">{{ __('Add to cart') }}</button>
            </form>
        @endif
    </div>
</div>
@endsection
