<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">
    <title>Admin login</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 p-4">
    <form method="POST" action="{{ route('admin.login') }}" class="card w-full max-w-sm space-y-4 p-6">
        @csrf
        <h1 class="text-xl font-bold">Admin panel</h1>
        <div class="rounded-lg bg-brand-50 p-3 text-sm">Demo account: <strong>admin@demo.test</strong> / <strong>demo1234</strong></div>
        @error('email')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
        <div><label class="label" for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email', 'admin@demo.test') }}" class="input" required></div>
        <div><label class="label" for="password">Password</label><input id="password" name="password" type="password" class="input" required></div>
        <button class="btn-primary w-full">Log in</button>
        <a href="{{ route('home') }}" class="block text-center text-sm text-slate-500 underline">Back to store</a>
    </form>
</body>
</html>
