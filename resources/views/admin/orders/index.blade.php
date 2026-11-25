@extends('layouts.admin')
@section('title', 'Orders')
@section('content')
<h1 class="mb-4 text-2xl font-bold">Orders</h1>
<form class="mb-4 flex flex-wrap gap-2">
    <input name="q" value="{{ request('q') }}" placeholder="Order number or email" class="input max-w-xs">
    <select name="status" class="input max-w-[12rem]">
        <option value="">All statuses</option>
        @foreach (['pending', 'paid', 'partially_refunded', 'refunded', 'canceled'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ str_replace('_', ' ', $s) }}</option>
        @endforeach
    </select>
    <button class="btn-ghost">Filter</button>
</form>
<div class="card overflow-x-auto">@include('admin.orders._table')</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
