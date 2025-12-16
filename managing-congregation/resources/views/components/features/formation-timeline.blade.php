@props(['events', 'projectedEvents' => []])

<div class="flow-root">
    <ul role="list">
        @foreach($events as $event)
            <li>
                <div class="relative {{ $loop->last && count($projectedEvents) === 0 ? '' : 'pb-8' }}">
                    @if(!$loop->last || count($projectedEvents) > 0)
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-stone-200" aria-hidden="true"></span>
                    @endif
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="h-8 w-8 rounded-full bg-sanctuary-gold flex items-center justify-center ring-8 ring-white">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                            <div>
                                <p class="text-sm text-stone-500">
                                    <span class="font-medium text-stone-900">{{ $event->stage->label() }}</span>
                                </p>
                                @if($event->notes)
                                    <p class="mt-1 text-sm text-stone-500">{{ $event->notes }}</p>
                                @endif
                                <div class="mt-2">
                                    @if($event->documents->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($event->documents as $doc)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-stone-100 text-stone-800">
                                                    <svg class="mr-1.5 h-2 w-2 text-stone-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    {{ $doc->file_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <button 
                                            x-data="" 
                                            x-on:click.prevent="$dispatch('open-modal', 'upload-document-{{ $event->id }}')"
                                            class="text-xs text-amber-600 hover:text-amber-700 font-medium"
                                        >
                                            + Upload Document
                                        </button>
                                        @if($event->documents->count() > 0)
                                            <span class="text-stone-300 mx-1">|</span>
                                            <button 
                                                x-data="" 
                                                x-on:click.prevent="$dispatch('open-modal', 'view-documents-{{ $event->id }}')"
                                                class="text-xs text-stone-600 hover:text-stone-700 font-medium"
                                            >
                                                View Documents
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right text-sm whitespace-nowrap text-stone-500">
                                <time datetime="{{ $event->started_at->format('Y-m-d') }}">{{ $event->started_at->format('M d, Y') }}</time>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach

        @foreach($projectedEvents as $event)
            <li>
                <div class="relative {{ $loop->last ? '' : 'pb-8' }}">
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="h-8 w-8 rounded-full bg-stone-100 flex items-center justify-center ring-8 ring-white border-2 border-dashed border-stone-300">
                                <svg class="h-5 w-5 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                            <div>
                                <p class="text-sm text-stone-500">
                                    <span class="font-medium text-stone-500 italic">{{ $event['stage']->label() }} (Projected)</span>
                                </p>
                            </div>
                            <div class="text-right text-sm whitespace-nowrap text-stone-500 italic">
                                <time datetime="{{ $event['date']->format('Y-m-d') }}">{{ $event['date']->format('M d, Y') }}</time>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
