@extends('layouts.admin')
@section('title', 'CMS pages')
@section('content')
<div class="mb-4 flex items-center"><h1 class="text-2xl font-bold">CMS pages</h1><a href="{{ route('admin.pages.create') }}" class="btn-primary ml-auto">New page</a></div>
<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="p-3">Slug</th><th class="p-3">Title (EN)</th><th class="p-3">Languages</th><th class="p-3">Status</th><th class="p-3"></th></tr></thead>
        <tbody class="divide-y">
            @foreach ($pages as $page)
                <tr>
                    <td class="p-3 font-mono text-xs"><a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="hover:underline">/pages/{{ $page->slug }}</a></td>
                    <td class="p-3">{{ $page->translate('title', 'en') }}</td>
                    <td class="p-3">{{ strtoupper(implode(' · ', array_keys($page->title))) }}</td>
                    <td class="p-3">{!! $page->is_published ? '<span class="badge bg-emerald-100 text-emerald-800">published</span>' : '<span class="badge bg-slate-100 text-slate-600">draft</span>' !!}</td>
                    <td class="p-3 text-right"><a href="{{ route('admin.pages.edit', $page) }}" class="text-brand-600 hover:underline">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
