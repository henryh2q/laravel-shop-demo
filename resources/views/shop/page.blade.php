@extends('layouts.shop')
@section('title', $page->translate('title'))
@section('content')
<article class="card mx-auto max-w-3xl p-8">
    <h1 class="mb-4 text-2xl font-bold">{{ $page->translate('title') }}</h1>
    <div class="space-y-3 whitespace-pre-line leading-relaxed text-slate-700">{{ $page->translate('body') }}</div>
</article>
@endsection
