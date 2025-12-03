@props([
    'events' => [],
    'member' => null,
    'projectedEvents' => [],
])

@php
// Ensure events is always an array
$events = is_array($events) ? $events : [];
$projectedEvents = is_array($projectedEvents) ? $projectedEvents : [];
@endphp

<div {{ $attributes->merge(['class' => 'feast-timeline overflow-x-auto py-4 -mx-6 px-6']) }}>
    <div class="flex space-x-6 min-w-max">
        @forelse($events as $event)
            <x-timeline-node 
                :event="$event"
                :date="$event->date ?? null"
                :title="$event->name ?? ''"
                :isPast="isset($event->date) && $event->date->isPast()"
                :isToday="isset($event->date) && $event->date->isToday()"
                :isFuture="isset($event->date) && $event->date->isFuture()"
            />
        @empty
            <p class="text-sm text-slate-500">No upcoming events</p>
        @endforelse
    </div>
</div>
