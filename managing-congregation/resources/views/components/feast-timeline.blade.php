@props(['events', 'member', 'projectedEvents' => []])

<div class="relative py-8">
    <!-- Horizontal Line -->
    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2"></div>
    
    <div class="flex space-x-12 overflow-x-auto pb-4 px-4 relative items-center">
        @foreach($events as $event)
            @php
                $isPast = $event->started_at->isPast();
                $isToday = $event->started_at->isToday();
                $nodeColor = $isToday ? 'bg-yellow-500' : ($isPast ? 'bg-gray-400' : 'bg-muted-gold');
                $textColor = $isPast ? 'text-gray-500' : 'text-gray-900';
            @endphp
            <div class="flex flex-col items-center flex-shrink-0 relative z-10">
                <div class="w-6 h-6 rounded-full {{ $nodeColor }} border-4 border-white shadow-md" title="{{ $event->notes }}"></div>
                <div class="mt-2 text-sm font-medium {{ $textColor }}">{{ $event->stage->label() }}</div>
                <div class="text-xs {{ $textColor }}">{{ $event->started_at->format('M d, Y') }}</div>
            </div>
        @endforeach
        
        @foreach($projectedEvents as $projected)
            <div class="flex flex-col items-center flex-shrink-0 relative z-10">
                <div class="w-6 h-6 rounded-full bg-gray-300 border-4 border-dashed border-gray-400 shadow-md" title="Projected based on Canon Law"></div>
                <div class="mt-2 text-sm font-medium text-gray-600 italic">{{ $projected['stage']->label() }}</div>
                <div class="text-xs text-gray-500">{{ $projected['date']->format('M d, Y') }}</div>
                <div class="text-xs text-gray-400">(Projected)</div>
            </div>
        @endforeach
        
        @can('create', \App\Models\FormationEvent::class)
            <div class="flex flex-col items-center flex-shrink-0 relative z-10 cursor-pointer group" 
                 x-data="" 
                 @click="$dispatch('open-modal', 'add-formation-event')">
                 <div class="w-6 h-6 rounded-full bg-gray-300 border-4 border-white shadow-md group-hover:bg-gray-400 transition-colors"></div>
                 <div class="mt-2 text-sm font-medium text-gray-500 group-hover:text-gray-700">Add Milestone</div>
            </div>
        @endcan
    </div>
</div>
