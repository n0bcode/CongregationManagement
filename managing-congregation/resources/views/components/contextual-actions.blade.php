@props([
    'model' => null,
    'actions' => [],
    'layout' => 'dropdown', // dropdown, buttons, menu
])

@php
use App\Services\ContextualActionsService;

if ($model && empty($actions)) {
    $service = app(ContextualActionsService::class);
    $actions = $service->getActions($model);
}
@endphp

@if(count($actions) > 0)
    @if($layout === 'dropdown')
        {{-- Dropdown Layout --}}
        <div x-data="{ open: false }" class="relative" {{ $attributes }}>
            <button
                @click="open = !open"
                @click.away="open = false"
                class="btn-secondary min-h-[48px] px-4 flex items-center gap-2"
            >
                <span>Actions</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="absolute right-0 mt-2 w-56 bg-white border border-stone-300 rounded-lg shadow-lg z-50"
            >
                <div class="py-2">
                    @foreach($actions as $action)
                        @if($action['method'] ?? null === 'DELETE')
                            <form method="POST" action="{{ $action['url'] }}" class="block">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    @if($action['confirm'] ?? null)
                                        onclick="return confirm('{{ $action['confirm'] }}')"
                                    @endif
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-stone-50 flex items-center gap-3 {{ $action['variant'] === 'danger' ? 'text-rose-600' : 'text-slate-700' }}"
                                >
                                    @if($action['icon'] ?? null)
                                        {!! app(ContextualActionsService::class)->getActionIcon($action['icon']) !!}
                                    @endif
                                    <span>{{ $action['label'] }}</span>
                                </button>
                            </form>
                        @else
                            <a
                                href="{{ $action['url'] }}"
                                class="block px-4 py-2 text-sm hover:bg-stone-50 flex items-center gap-3 {{ $action['variant'] === 'danger' ? 'text-rose-600' : 'text-slate-700' }}"
                            >
                                @if($action['icon'] ?? null)
                                    {!! app(ContextualActionsService::class)->getActionIcon($action['icon']) !!}
                                @endif
                                <span>{{ $action['label'] }}</span>
                                @if($action['highlight'] ?? false)
                                    <span class="ml-auto w-2 h-2 bg-amber-500 rounded-full"></span>
                                @endif
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

    @elseif($layout === 'buttons')
        {{-- Button Layout --}}
        <div class="flex flex-wrap gap-2" {{ $attributes }}>
            @foreach($actions as $action)
                @if($action['method'] ?? null === 'DELETE')
                    <form method="POST" action="{{ $action['url'] }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            @if($action['confirm'] ?? null)
                                onclick="return confirm('{{ $action['confirm'] }}')"
                            @endif
                            class="btn-{{ $action['variant'] ?? 'secondary' }} min-h-[48px] px-4 flex items-center gap-2"
                        >
                            @if($action['icon'] ?? null)
                                {!! app(ContextualActionsService::class)->getActionIcon($action['icon']) !!}
                            @endif
                            <span>{{ $action['label'] }}</span>
                        </button>
                    </form>
                @else
                    <a
                        href="{{ $action['url'] }}"
                        class="btn-{{ $action['variant'] ?? 'secondary' }} min-h-[48px] px-4 flex items-center gap-2 {{ $action['highlight'] ?? false ? 'ring-2 ring-amber-500' : '' }}"
                    >
                        @if($action['icon'] ?? null)
                            {!! app(ContextualActionsService::class)->getActionIcon($action['icon']) !!}
                        @endif
                        <span>{{ $action['label'] }}</span>
                        @if($action['highlight'] ?? false)
                            <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>

    @else
        {{-- Menu Layout --}}
        <div class="space-y-1" {{ $attributes }}>
            @foreach($actions as $action)
                @if($action['method'] ?? null === 'DELETE')
                    <form method="POST" action="{{ $action['url'] }}">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            @if($action['confirm'] ?? null)
                                onclick="return confirm('{{ $action['confirm'] }}')"
                            @endif
                            class="w-full text-left px-4 py-3 rounded-lg hover:bg-stone-50 flex items-center gap-3 {{ $action['variant'] === 'danger' ? 'text-rose-600' : 'text-slate-700' }}"
                        >
                            @if($action['icon'] ?? null)
                                {!! app(ContextualActionsService::class)->getActionIcon($action['icon']) !!}
                            @endif
                            <span class="flex-1">{{ $action['label'] }}</span>
                        </button>
                    </form>
                @else
                    <a
                        href="{{ $action['url'] }}"
                        class="block px-4 py-3 rounded-lg hover:bg-stone-50 flex items-center gap-3 text-slate-700"
                    >
                        @if($action['icon'] ?? null)
                            {!! app(ContextualActionsService::class)->getActionIcon($action['icon']) !!}
                        @endif
                        <span class="flex-1">{{ $action['label'] }}</span>
                        @if($action['highlight'] ?? false)
                            <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
    @endif
@endif
