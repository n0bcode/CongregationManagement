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
                $documentCount = $event->documents()->count();
            @endphp
            <div class="flex flex-col items-center flex-shrink-0 relative z-10">
                <div class="relative">
                    <div class="w-6 h-6 rounded-full {{ $nodeColor }} border-4 border-white shadow-md" title="{{ $event->notes }}"></div>
                    @if($documentCount > 0)
                        <div class="absolute -top-1 -right-1 bg-muted-gold text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold shadow-sm">
                            {{ $documentCount }}
                        </div>
                    @endif
                </div>
                <div class="mt-2 text-sm font-medium {{ $textColor }}">{{ $event->stage->label() }}</div>
                <div class="text-xs {{ $textColor }}">{{ $event->started_at->format('M d, Y') }}</div>
                
                @can('uploadDocument', $event)
                    <button 
                        type="button"
                        class="mt-2 text-xs text-muted-gold hover:text-amber-700 flex items-center gap-1"
                        x-data=""
                        @click="$dispatch('open-modal', 'upload-document-{{ $event->id }}')"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        Upload
                    </button>
                @endcan
                
                @if($documentCount > 0)
                    <button 
                        type="button"
                        class="mt-1 text-xs text-gray-600 hover:text-gray-800"
                        x-data=""
                        @click="$dispatch('open-modal', 'view-documents-{{ $event->id }}')"
                    >
                        View ({{ $documentCount }})
                    </button>
                @endif
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
