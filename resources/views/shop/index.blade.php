@extends('layouts.shop')
@section('title', __('Shop'))
@section('content')
<section class="mb-8 rounded-2xl bg-gradient-to-br from-brand-600 to-slate-900 px-6 py-10 text-white">
    <h1 class="text-3xl font-bold">{{ __('Everyday goods, delivered nowhere') }}</h1>
    <p class="mt-2 max-w-xl text-slate-200">{{ __('A working e-commerce demo: browse, add to cart and pay with a Stripe test card (4242 4242 4242 4242, any future date, any CVC).') }}</p>
</section>

<form method="GET" class="mb-6 flex flex-wrap items-center gap-2">
    <a href="{{ route('home') }}" @class(['btn', 'bg-slate-900 text-white' => ! request('category'), 'btn-ghost' => request('category')])>{{ __('All') }}</a>
    @foreach ($categories as $category)
        <a href="{{ route('home', ['category' => $category->slug]) }}" @class(['btn', 'bg-slate-900 text-white' => request('category') === $category->slug, 'btn-ghost' => request('category') !== $category->slug])>{{ $category->translate('name') }}</a>
    @endforeach
    <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Search products') }}" class="input w-full sm:ml-auto sm:max-w-xs">
</form>

<div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
    @forelse ($products as $product)
        <a href="{{ route('products.show', $product) }}" class="card group overflow-hidden">
            <x-product-image :product="$product" class="aspect-square w-full transition group-hover:scale-105 @container" />
            <div class="p-3">
                <div class="text-xs text-slate-500">{{ $product->category?->translate('name') }}</div>
                <div class="line-clamp-2 font-medium">{{ $product->translate('name') }}</div>
                <div class="mt-1 font-semibold text-brand-600">{{ \App\Support\Money::format($product->price) }}</div>
            </div>
        </a>
    @empty
        <p class="col-span-full text-slate-500">{{ __('No products found.') }}</p>
    @endforelse
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
