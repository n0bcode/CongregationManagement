@props([
    'date' => null,
    'description' => '',
    'category' => null,
    'amount' => '',
    'href' => null,
])

@php
$tag = $href ? 'a' : 'div';
$attributes = $href ? $attributes->merge(['href' => $href]) : $attributes;
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'ledger-row']) }}>
    {{-- Date Badge (Left) --}}
    @if($date)
        <div class="flex-shrink-0 w-16 text-center">
            <span class="text-xs text-slate-500 uppercase">{{ $date->format('M') }}</span>
            <span class="text-2xl font-bold block text-slate-800">{{ $date->format('d') }}</span>
        </div>
    @endif
    
    {{-- Description (Center) --}}
    <div class="flex-grow px-4">
        <p class="font-medium text-slate-800">{{ $description }}</p>
        @if($category)
            <p class="text-sm text-slate-500 mt-1">{{ $category }}</p>
        @endif
    </div>
    
    {{-- Amount (Right, Bold) --}}
    <div class="flex-shrink-0 text-right">
        <span class="text-xl font-bold text-slate-800">{{ $amount }}</span>
    </div>
</{{ $tag }}>
