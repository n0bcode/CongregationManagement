@props([
    'date' => null,
    'description' => '',
    'category' => null,
    'amount' => '',
    'href' => null,
])

@php
$classes = 'ledger-row flex items-center justify-between p-4 border-b border-stone-200 hover:bg-stone-50 transition-colors min-h-[64px]';
if ($href) {
    $classes .= ' cursor-pointer';
}
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} @if($href) onclick="window.location='{{ $href }}'" @endif>
    {{-- Date Badge (Left) --}}
    @if($date)
        <div class="date-badge flex-shrink-0 w-16 text-center">
            <span class="text-sm text-slate-500 block">{{ $date->format('M') }}</span>
            <span class="text-2xl font-bold text-slate-800 block">{{ $date->format('d') }}</span>
        </div>
    @endif

    {{-- Description (Center) --}}
    <div class="description flex-grow px-4">
        <p class="font-medium text-slate-800">{{ $description }}</p>
        @if($category)
            <p class="text-sm text-slate-500 mt-1">{{ $category }}</p>
        @endif
        @if($slot->isNotEmpty())
            <div class="mt-2 text-sm text-slate-600">
                {{ $slot }}
            </div>
        @endif
    </div>

    {{-- Amount (Right, Bold) --}}
    <div class="amount flex-shrink-0 text-right">
        <span class="text-xl font-bold text-slate-800">{{ $amount }}</span>
    </div>
</div>
