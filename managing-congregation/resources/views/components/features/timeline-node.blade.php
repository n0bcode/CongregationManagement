@props([
    'event' => null,
    'date' => null,
    'title' => '',
    'isPast' => false,
    'isToday' => false,
    'isFuture' => false,
])

@php
// Determine state from event if provided
if ($event) {
    $date = $event->date ?? $event->started_at ?? null;
    $title = $event->name ?? $event->stage ?? '';
    $isPast = $date && $date->isPast();
    $isToday = $date && $date->isToday();
    $isFuture = $date && $date->isFuture();
}

// Determine circle color based on state
$circleClasses = 'w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm';
if ($isPast) {
    $circleClasses .= ' bg-stone-300';
} elseif ($isToday) {
    $circleClasses .= ' bg-amber-600 ring-4 ring-amber-600/30';
} elseif ($isFuture) {
    $circleClasses .= ' bg-slate-700';
} else {
    $circleClasses .= ' bg-slate-500';
}
@endphp

<div {{ $attributes->merge(['class' => 'timeline-node flex flex-col items-center']) }}>
    <div class="{{ $circleClasses }}">
        @if($date)
            {{ $date->format('d') }}
        @else
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        @endif
    </div>
    
    @if($title || $slot->isNotEmpty())
        <div class="mt-2 text-center max-w-[120px]">
            @if($title)
                <span class="text-xs font-medium text-slate-700 block">{{ $title }}</span>
            @endif
            @if($date)
                <span class="text-xs text-slate-500 block mt-1">{{ $date->format('M Y') }}</span>
            @endif
            @if($slot->isNotEmpty())
                <div class="mt-1 text-xs text-slate-600">
                    {{ $slot }}
                </div>
            @endif
        </div>
    @endif
</div>
