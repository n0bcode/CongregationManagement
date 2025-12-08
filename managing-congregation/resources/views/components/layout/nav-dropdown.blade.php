@props(['label', 'active' => false, 'align' => 'left'])

@php
$alignmentClasses = match ($align) {
    'right' => 'ltr:origin-top-right rtl:origin-top-left end-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-left rtl:origin-top-right start-0',
};

// Active state classes for the trigger
$triggerClasses = $active
    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-amber-600 text-sm font-medium leading-5 text-amber-600 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-700 hover:text-slate-900 hover:border-stone-300 focus:outline-none focus:text-slate-900 focus:border-stone-300 transition duration-150 ease-in-out';
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false" @keydown.escape.window="open = false">
    {{-- Dropdown Trigger --}}
    <button
        @click="open = !open"
        type="button"
        class="{{ $triggerClasses }}"
        aria-haspopup="true"
        :aria-expanded="open.toString()"
    >
        <span>{{ $label }}</span>
        
        {{-- Chevron Icon --}}
        <svg 
            class="ms-2 -me-0.5 h-4 w-4 transition-transform duration-200" 
            :class="{'rotate-180': open}"
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown Menu --}}
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 w-48 rounded-lg shadow-lg {{ $alignmentClasses }} border border-stone-200"
        style="display: none;"
        role="menu"
        @click="open = false"
    >
        <div class="rounded-lg ring-1 ring-black ring-opacity-5 py-1 bg-white">
            {{ $slot }}
        </div>
    </div>
</div>
