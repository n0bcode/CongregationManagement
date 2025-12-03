@props([
    'events' => [],
])

<div {{ $attributes->merge(['class' => 'feast-timeline overflow-x-auto py-4']) }}>
    <div class="flex space-x-4 min-w-max px-4">
        @forelse($events as $event)
            @php
                $isPast = $event->date->isPast();
                $isToday = $event->date->isToday();
                $isFuture = $event->date->isFuture();
                
                $nodeClass = match(true) {
                    $isToday => 'bg-amber-600 ring-4 ring-amber-600/30 scale-110',
                    $isPast => 'bg-stone-300',
                    $isFuture => 'bg-slate-700',
                    default => 'bg-slate-700',
                };
            @endphp
            
            <div class="timeline-node flex flex-col items-center">
                <div class="node-circle {{ $nodeClass }} w-12 h-12 rounded-full flex items-center justify-center text-white font-bold transition-all">
                    {{ $event->date->format('d') }}
                </div>
                <span class="text-xs mt-2 text-center max-w-[80px] {{ $isToday ? 'font-semibold text-amber-900' : 'text-slate-600' }}">
                    {{ $event->name }}
                </span>
                @if($isToday)
                    <span class="text-xs text-amber-600 font-semibold mt-1">Today</span>
                @endif
            </div>
        @empty
            <p class="text-sm text-slate-500 py-4">No upcoming events</p>
        @endforelse
    </div>
</div>
