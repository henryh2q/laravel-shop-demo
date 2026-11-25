@extends('layouts.admin')
@section('title', $event->event_id)
@section('content')
<a href="{{ route('admin.webhooks.index') }}" class="text-sm text-slate-500 hover:underline">← Webhook log</a>
<div class="mb-4 mt-2 flex items-center gap-3"><h1 class="font-mono text-lg font-bold">{{ $event->type }}</h1><x-status-badge :status="$event->status" /></div>
@if ($event->error)<div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $event->error }}</div>@endif
<pre class="card overflow-x-auto p-4 text-xs">{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
@endsection
