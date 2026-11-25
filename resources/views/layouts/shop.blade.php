<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title', __('Home')) · Demo Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="bg-slate-900 px-4 py-2 text-center text-xs text-slate-100">
        {{ __('Portfolio demo built by Hai H. Not a real store: no real products are sold and payments run in test mode.') }}
        <a href="{{ route('admin.login') }}" class="ml-1 underline">{{ __('Try the admin panel') }}</a>
    </div>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-4 px-4 py-4">
            <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight">Demo<span class="text-brand-600">Store</span></a>
            <nav class="flex gap-4 text-sm text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-slate-900">{{ __('Shop') }}</a>
                <a href="{{ route('pages.show', 'about') }}" class="hover:text-slate-900">{{ __('About this demo') }}</a>
            </nav>
            <div class="ml-auto flex items-center gap-3">
                <div class="flex overflow-hidden rounded-lg border border-slate-200 text-xs">
                    @foreach (config('app.supported_locales') as $code => $label)
                        <a href="{{ route('locale', $code) }}" @class(['px-2 py-1', 'bg-slate-900 text-white' => app()->getLocale() === $code, 'hover:bg-slate-100' => app()->getLocale() !== $code])>{{ strtoupper($code) }}</a>
                    @endforeach
                </div>
                <a href="{{ route('cart.index') }}" class="btn-ghost">
                    {{ __('Cart') }}
                    <span class="badge bg-brand-600 text-white">{{ $cartCount }}</span>
                </a>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8">
        @if (session('status'))
            <div class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 py-8 text-center text-xs text-slate-500">
        {{ __('Laravel + Blade + MySQL · Stripe Checkout (test mode) · Custom admin panel · EN / 日本語 / Tiếng Việt') }}
    </footer>
</body>
</html>
