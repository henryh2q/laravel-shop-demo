@extends('layouts.shop')
@section('title', __('Shop'))
@section('content')
<section class="relative mb-8 overflow-hidden rounded-2xl bg-slate-900 px-6 py-14 text-white md:px-10 md:py-20">
    <img src="https://images.unsplash.com/photo-1624893464449-22a7e40de7c9?w=1600&h=700&fit=crop&auto=format&q=70" alt="" class="absolute inset-0 size-full object-cover opacity-55">
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-950/50 to-transparent"></div>
    <div class="relative">
    <p class="text-sm font-semibold uppercase tracking-widest text-emerald-300">{{ __('Japanese tea & wagashi') }}</p>
    <h1 class="mt-2 max-w-xl text-3xl font-bold md:text-5xl">{{ __('Matcha, sencha and sweets from Kyoto to your cup') }}</h1>
    <p class="mt-4 max-w-xl text-slate-200">{{ __('A working e-commerce demo: browse, add to cart and pay with a Stripe test card (4242 4242 4242 4242, any future date, any CVC).') }}</p>
    </div>
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
            <div class="overflow-hidden"><x-product-image :product="$product" :size="500" class="aspect-square w-full transition duration-300 group-hover:scale-105 @container" /></div>
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
