@props(['product'])
@php
    $palettes = [
        'kitchen' => ['#fde68a', '#f59e0b'], 'stationery' => ['#bfdbfe', '#3b82f6'],
        'home' => ['#fbcfe8', '#db2777'], 'outdoor' => ['#bbf7d0', '#16a34a'],
    ];
    [$from, $to] = $palettes[$product->category?->slug] ?? ['#e2e8f0', '#64748b'];
@endphp
@if ($product->image_url)
    <img src="{{ $product->image_url }}" alt="{{ $product->translate('name') }}" {{ $attributes->merge(['class' => 'object-cover bg-slate-100']) }} loading="lazy">
@else
    <div {{ $attributes->merge(['class' => 'flex items-center justify-center overflow-hidden']) }} style="background: linear-gradient(135deg, {{ $from }}, {{ $to }})" role="img" aria-label="{{ $product->translate('name') }}">
        <span class="px-3 text-center font-bold leading-tight text-white/95 drop-shadow-sm [font-size:clamp(0.45rem,8cqw,1.75rem)]">{{ $product->translate('name', 'ja') }}</span>
    </div>
@endif
