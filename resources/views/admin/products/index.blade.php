@extends('layouts.admin')
@section('title', 'Products')
@section('content')
<div class="mb-4 flex flex-wrap items-center gap-2">
    <h1 class="text-2xl font-bold">Products</h1>
    <form class="ml-auto"><input name="q" value="{{ request('q') }}" placeholder="Search name or SKU" class="input"></form>
    <a href="{{ route('admin.products.create') }}" class="btn-primary">New product</a>
</div>
<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="p-3">Product</th><th class="p-3">SKU</th><th class="p-3">Category</th><th class="p-3 text-right">Price</th><th class="p-3 text-right">Stock</th><th class="p-3">Status</th><th class="p-3"></th></tr></thead>
        <tbody class="divide-y">
            @foreach ($products as $product)
                <tr class="hover:bg-slate-50">
                    <td class="p-3"><div class="flex items-center gap-3"><x-product-image :product="$product" :size="120" class="size-10 shrink-0 rounded @container" /><div>{{ $product->translate('name', 'en') }}<div class="text-xs text-slate-500">{{ $product->translate('name', 'ja') }}</div></div></div></td>
                    <td class="p-3 font-mono text-xs">{{ $product->sku }}</td>
                    <td class="p-3">{{ $product->category?->translate('name', 'en') }}</td>
                    <td class="p-3 text-right">{{ \App\Support\Money::format($product->price) }}</td>
                    <td @class(['p-3 text-right', 'font-semibold text-red-600' => $product->stock < 5])>{{ $product->stock }}</td>
                    <td class="p-3">{!! $product->is_active ? '<span class="badge bg-emerald-100 text-emerald-800">active</span>' : '<span class="badge bg-slate-100 text-slate-600">hidden</span>' !!}</td>
                    <td class="p-3 text-right"><a href="{{ route('admin.products.edit', $product) }}" class="text-brand-600 hover:underline">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
