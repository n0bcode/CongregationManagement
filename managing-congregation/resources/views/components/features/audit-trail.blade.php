@props(['audits'])

<div class="flow-root p-6">
    <ul role="list">
        @forelse($audits as $audit)
            <li>
                <div class="relative {{ $loop->last ? '' : 'pb-8' }}">
                    @if(!$loop->last)
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-stone-200" aria-hidden="true"></span>
                    @endif
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="h-8 w-8 rounded-full bg-stone-100 flex items-center justify-center ring-8 ring-white">
                                @if($audit->action === 'created')
                                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                @elseif($audit->action === 'updated')
                                    <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                @elseif($audit->action === 'deleted')
                                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                @endif
                            </span>
                        </div>
                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                            <div>
                                <p class="text-sm text-stone-500">
                                    <span class="font-medium text-stone-900">{{ $audit->user->name ?? 'System' }}</span>
                                    {{ $audit->action }} this record
                                </p>
                                @if($audit->action === 'updated')
                                    <div class="mt-2 text-xs text-stone-500">
                                        @foreach($audit->new_values ?? [] as $key => $value)
                                            @if(isset($audit->old_values[$key]) && $audit->old_values[$key] !== $value)
                                                <div>
                                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                    <span class="line-through text-red-400">{{ $audit->old_values[$key] }}</span>
                                                    <span class="text-stone-400 mx-1">→</span>
                                                    <span class="text-green-600">{{ $value }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="text-right text-sm whitespace-nowrap text-stone-500">
                                <time datetime="{{ $audit->created_at }}">{{ $audit->created_at->diffForHumans() }}</time>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="py-4 text-center text-sm text-stone-500">
                No history available.
            </li>
        @endforelse
    </ul>
    @if($audits instanceof \Illuminate\Pagination\LengthAwarePaginator && $audits->hasPages())
        <div class="mt-4">
            <x-ui.pagination :paginator="$audits" />
        </div>
    @endif
</div>
