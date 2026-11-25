@extends('layouts.admin')
@section('title', $page->exists ? 'Edit page' : 'New page')
@section('content')
<a href="{{ route('admin.pages.index') }}" class="text-sm text-slate-500 hover:underline">← CMS pages</a>
<h1 class="mb-6 mt-2 text-2xl font-bold">{{ $page->exists ? 'Edit page' : 'New page' }}</h1>
<form method="POST" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="card space-y-4 p-6">
    @csrf
    @if ($page->exists) @method('PUT') @endif
    <div class="flex flex-wrap items-end gap-4">
        <div class="flex-1"><label class="label" for="slug">Slug</label><input id="slug" name="slug" value="{{ old('slug', $page->slug) }}" class="input" required></div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published))> Published</label>
    </div>
    @foreach (config('app.supported_locales') as $code => $label)
        <fieldset class="space-y-2 rounded-lg border border-slate-200 p-4">
            <legend class="px-1 text-sm font-semibold">{{ $label }} @if ($code === 'en')<span class="text-red-600">*</span>@endif</legend>
            <input name="title[{{ $code }}]" value="{{ old("title.$code", $page->title[$code] ?? '') }}" placeholder="Title" class="input" @required($code === 'en')>
            <textarea name="body[{{ $code }}]" rows="6" placeholder="Body" class="input" @required($code === 'en')>{{ old("body.$code", $page->body[$code] ?? '') }}</textarea>
        </fieldset>
    @endforeach
    <button class="btn-primary">Save</button>
</form>
@if ($page->exists)
    <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="mt-4" onsubmit="return confirm('Delete this page?')">@csrf @method('DELETE')<button class="text-sm text-red-600 hover:underline">Delete page</button></form>
@endif
@endsection
