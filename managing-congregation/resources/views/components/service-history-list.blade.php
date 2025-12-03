@props(['assignments'])

<div class="flow-root">
    <ul role="list" class="-mb-8">
        @forelse($assignments as $assignment)
            <li>
                <div class="relative pb-8">
                    @if(!$loop->last)
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                    @endif
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Served at <a href="#" class="font-medium text-gray-900">{{ $assignment->community->name }}</a>
                                </p>
                            </div>
                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                <time datetime="{{ $assignment->start_date->format('Y-m-d') }}">{{ $assignment->start_date->format('M Y') }}</time>
                                -
                                @if($assignment->end_date)
                                    <time datetime="{{ $assignment->end_date->format('Y-m-d') }}">{{ $assignment->end_date->format('M Y') }}</time>
                                @else
                                    <span class="text-green-600 font-medium">Present</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="text-sm text-gray-500 italic">No service history recorded.</li>
        @endforelse
    </ul>
</div>
