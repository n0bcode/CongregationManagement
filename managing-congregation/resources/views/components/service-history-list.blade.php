@props(['assignments'])

<div class="flow-root">
    @if($assignments->count() > 0)
        <ul role="list" class="-mb-8">
            @foreach($assignments as $assignment)
                <li>
                    <div class="relative pb-8">
                        @if(!$loop->last)
                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-stone-200" aria-hidden="true"></span>
                        @endif
                        <div class="relative flex items-start space-x-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <span class="h-10 w-10 rounded-full {{ $assignment->isActive() ? 'bg-sanctuary-gold' : 'bg-stone-400' }} flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <!-- Role & Community -->
                                        <div class="flex items-center gap-2 mb-1">
                                            @if($assignment->role)
                                                <h4 class="text-lg font-semibold text-slate-900">{{ $assignment->role }}</h4>
                                                <span class="text-slate-400">•</span>
                                            @endif
                                            <p class="text-lg text-slate-700">{{ $assignment->community->name }}</p>
                                        </div>

                                        <!-- Dates -->
                                        <div class="flex items-center gap-2 text-sm text-slate-600">
                                            <time datetime="{{ $assignment->start_date->format('Y-m-d') }}">
                                                {{ $assignment->start_date->format('M d, Y') }}
                                            </time>
                                            <span>→</span>
                                            @if($assignment->end_date)
                                                <time datetime="{{ $assignment->end_date->format('Y-m-d') }}">
                                                    {{ $assignment->end_date->format('M d, Y') }}
                                                </time>
                                            @else
                                                <span class="font-medium text-sanctuary-green">Present</span>
                                            @endif
                                        </div>

                                        <!-- Duration -->
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->isActive() ? 'bg-sanctuary-gold/10 text-amber-800' : 'bg-stone-100 text-stone-700' }}">
                                                <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $assignment->duration_human }}
                                            </span>
                                            @if($assignment->isActive())
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sanctuary-green/10 text-sanctuary-green">
                                                    Active
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-stone-900">No service history</h3>
            <p class="mt-1 text-sm text-stone-500">No assignments have been recorded yet.</p>
        </div>
    @endif
</div>
