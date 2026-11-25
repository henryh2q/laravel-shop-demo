<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">
    <title>{{ __('Demo payment page') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 p-4">
    <form method="POST" action="{{ route('demo-gateway.pay', $session) }}" data-once class="card w-full max-w-sm space-y-4 p-6">
        @csrf
        <div class="badge bg-amber-100 text-amber-800">{{ __('Offline demo gateway (no Stripe keys configured)') }}</div>
        <div class="text-sm text-slate-500">{{ $order->number }}</div>
        <div class="text-3xl font-bold">{{ \App\Support\Money::format($order->total) }}</div>
        <input class="input" value="4242 4242 4242 4242" readonly aria-label="Card number">
        <button type="submit" class="btn-primary w-full" @disabled($order->status !== 'pending')>{{ __('Pay') }}</button>
        <a href="{{ route('cart.index') }}" class="block text-center text-sm text-slate-500 underline">{{ __('Cancel') }}</a>
    </form>
</body>
</html>
