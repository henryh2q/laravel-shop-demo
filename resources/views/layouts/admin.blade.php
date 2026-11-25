<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">
    <title>@yield('title', 'Dashboard') · Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<div class="flex min-h-screen flex-col md:flex-row">
    <aside class="bg-slate-900 text-slate-300 md:w-56 md:shrink-0">
        <div class="px-5 py-5 text-lg font-bold text-white">Chanoma<span class="text-emerald-400"> 茶の間</span> <span class="text-xs font-normal text-slate-400">admin</span></div>
        <nav class="flex gap-1 overflow-x-auto px-3 pb-3 text-sm md:flex-col">
            @foreach ([
                ['admin.dashboard', 'Dashboard', 'admin.dashboard'],
                ['admin.orders.index', 'Orders', 'admin.orders.*'],
                ['admin.products.index', 'Products', 'admin.products.*'],
                ['admin.pages.index', 'CMS pages', 'admin.pages.*'],
                ['admin.webhooks.index', 'Webhook log', 'admin.webhooks.*'],
            ] as [$route, $label, $pattern])
                <a href="{{ route($route) }}" @class(['whitespace-nowrap rounded-lg px-3 py-2', 'bg-slate-800 text-white' => request()->routeIs($pattern), 'hover:bg-slate-800' => ! request()->routeIs($pattern)])>{{ $label }}</a>
            @endforeach
            <a href="{{ route('home') }}" class="whitespace-nowrap rounded-lg px-3 py-2 hover:bg-slate-800" target="_blank">View store ↗</a>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="w-full whitespace-nowrap rounded-lg px-3 py-2 text-left hover:bg-slate-800">Log out</button></form>
        </nav>
    </aside>
    <main class="flex-1 p-4 md:p-8">
        <div class="mb-4 rounded-lg bg-amber-50 px-4 py-2 text-xs text-amber-800">Portfolio demo. Data is sample data and is reset regularly; payments run in Stripe test mode.</div>
        @if (session('status'))<div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
        @if ($errors->any())<div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
        @yield('content')
    </main>
</div>
</body>
</html>
