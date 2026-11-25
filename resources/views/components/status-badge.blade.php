@props(['status'])
@php
    $classes = [
        'pending' => 'bg-amber-100 text-amber-800',
        'paid' => 'bg-emerald-100 text-emerald-800',
        'partially_refunded' => 'bg-sky-100 text-sky-800',
        'refunded' => 'bg-slate-200 text-slate-700',
        'canceled' => 'bg-red-100 text-red-700',
        'succeeded' => 'bg-emerald-100 text-emerald-800',
        'processed' => 'bg-emerald-100 text-emerald-800',
        'failed' => 'bg-red-100 text-red-700',
        'ignored' => 'bg-slate-100 text-slate-600',
        'received' => 'bg-amber-100 text-amber-800',
    ][$status] ?? 'bg-slate-100 text-slate-700';
@endphp
<span {{ $attributes->merge(['class' => "badge $classes"]) }}>{{ __(str_replace('_', ' ', $status)) }}</span>
