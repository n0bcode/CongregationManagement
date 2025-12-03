@props([
    'type' => 'info',
    'dismissible' => false,
    'title' => null,
])

@php
$typeClasses = [
    'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
    'error' => 'bg-rose-50 border-rose-200 text-rose-800',
    'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
];

$icons = [
    'success' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    'error' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    'warning' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
    'info' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
];

$classes = 'p-4 rounded-lg flex items-start space-x-3 border ' . ($typeClasses[$type] ?? $typeClasses['info']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert" x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex-shrink-0">
        {!! $icons[$type] ?? $icons['info'] !!}
    </div>
    <div class="flex-grow">
        @if($title)
            <h4 class="font-semibold mb-1">{{ $title }}</h4>
        @endif
        <div>
            {{ $slot }}
        </div>
    </div>
    @if($dismissible)
        <button 
            type="button" 
            @click="show = false" 
            class="flex-shrink-0 ml-auto -mr-1 -mt-1 p-1 rounded-lg hover:bg-black/5 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
            aria-label="Dismiss alert"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    @endif
</div>
