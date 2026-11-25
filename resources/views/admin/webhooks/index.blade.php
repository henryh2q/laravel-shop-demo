@extends('layouts.admin')
@section('title', 'Webhook log')
@section('content')
<h1 class="mb-1 text-2xl font-bold">Webhook log</h1>
<p class="mb-4 text-sm text-slate-500">Every event is verified (signature) and stored once by event id; repeated deliveries are acknowledged but not processed again.</p>
<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="p-3">Event id</th><th class="p-3">Type</th><th class="p-3">Status</th><th class="p-3">Received</th></tr></thead>
        <tbody class="divide-y">
            @forelse ($events as $event)
                <tr class="hover:bg-slate-50">
                    <td class="p-3 font-mono text-xs"><a href="{{ route('admin.webhooks.show', $event) }}" class="text-brand-600 hover:underline">{{ $event->event_id }}</a></td>
                    <td class="p-3 font-mono text-xs">{{ $event->type }}</td>
                    <td class="p-3"><x-status-badge :status="$event->status" /></td>
                    <td class="p-3 text-slate-500">{{ $event->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-4 text-slate-500">No events yet. Place a test order to see one.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $events->links() }}</div>
@endsection
