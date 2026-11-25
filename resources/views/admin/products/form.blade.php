@extends('layouts.admin')
@section('title', $product->exists ? 'Edit product' : 'New product')
@section('content')
<a href="{{ route('admin.products.index') }}" class="text-sm text-slate-500 hover:underline">← Products</a>
<h1 class="mb-6 mt-2 text-2xl font-bold">{{ $product->exists ? 'Edit product' : 'New product' }}</h1>
<form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="grid gap-6 lg:grid-cols-3">
    @csrf
    @if ($product->exists) @method('PUT') @endif
    <div class="card space-y-4 p-6 lg:col-span-2">
        @foreach (config('app.supported_locales') as $code => $label)
            <fieldset class="space-y-2 rounded-lg border border-slate-200 p-4">
                <legend class="px-1 text-sm font-semibold">{{ $label }} @if ($code === 'en')<span class="text-red-600">*</span>@endif</legend>
                <input name="name[{{ $code }}]" value="{{ old("name.$code", $product->name[$code] ?? '') }}" placeholder="Name" class="input" @required($code === 'en')>
                <textarea name="description[{{ $code }}]" rows="3" placeholder="Description" class="input">{{ old("description.$code", $product->description[$code] ?? '') }}</textarea>
            </fieldset>
        @endforeach
    </div>
    <div class="card h-fit space-y-4 p-6">
        <div><label class="label" for="sku">SKU</label><input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" class="input" required></div>
        <div><label class="label" for="category_id">Category</label>
            <select id="category_id" name="category_id" class="input"><option value="">None</option>
                @foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->translate('name', 'en') }}</option>@endforeach
            </select></div>
        <div><label class="label" for="price">Price (USD)</label><input id="price" name="price" type="number" step="0.01" value="{{ old('price', $product->price ? $product->price / 100 : '') }}" class="input" required></div>
        <div><label class="label" for="stock">Stock</label><input id="stock" name="stock" type="number" value="{{ old('stock', $product->stock ?? 0) }}" class="input" required></div>
        <div><label class="label" for="image_url">Image URL</label><input id="image_url" name="image_url" type="url" value="{{ old('image_url', $product->image_url) }}" class="input"></div>
        <label class="flex items-center gap-2 text-sm"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Visible in store</label>
        <button class="btn-primary w-full">Save</button>
    </div>
</form>
@if ($product->exists)
    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="mt-4" data-confirm="Delete this product?">@csrf @method('DELETE')<button class="text-sm text-red-600 hover:underline">Delete product</button></form>
@endif
@endsection
