<x-card title="Upcoming Events" subtitle="Next 30 days">
    <div class="flow-root">
        <ul role="list" class="-my-5 divide-y divide-stone-200">
            @forelse($data['events'] as $event)
                <li class="py-4 hover:bg-stone-50 transition-colors -mx-6 px-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                <span class="text-xs font-bold">{{ $event->reminder_date->format('M') }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 truncate">
                                {{ $event->title }}
                            </p>
                            <p class="text-sm text-slate-500 truncate">
                                {{ $event->reminder_date->format('l, F j, Y') }}
                            </p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-stone-100 text-stone-800">
                                {{ $event->reminder_date->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="py-4 text-sm text-slate-500 text-center">No upcoming events.</li>
            @endforelse
        </ul>
    </div>
    
    <div class="mt-6 -mb-2 text-center">
        <a href="#" class="text-sm font-medium text-amber-600 hover:text-amber-700">
            View calendar
        </a>
    </div>
</x-card>
