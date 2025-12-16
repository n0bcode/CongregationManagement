@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'loading' => false, // NEW: Loading state
    'ariaLabel' => null, // NEW: Accessibility label
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-4 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg';

$variantClasses = [
    'primary' => 'bg-amber-600 text-white hover:bg-amber-700 focus:ring-amber-500',
    'secondary' => 'bg-white text-slate-700 border border-stone-300 hover:bg-stone-50 focus:ring-amber-500',
    'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500',
    'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-500',
    'ghost' => 'bg-transparent text-slate-700 hover:bg-stone-100 focus:ring-amber-500',
];

$sizeClasses = [
    'sm' => 'min-h-[40px] px-4 py-2 text-sm',
    'md' => 'min-h-[48px] px-6 py-3 text-base',
    'lg' => 'min-h-[56px] px-8 py-4 text-lg',
];

$classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);

// Disable button if loading
$isDisabled = $disabled || $loading;
@endphp

@if($href && !$loading)
    <a 
        href="{{ $href }}" 
        {{ $attributes->merge(['class' => $classes]) }}
        @if($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
    >
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}" 
        {{ $isDisabled ? 'disabled' : '' }} 
        {{ $attributes->merge(['class' => $classes]) }}
        @if($loading) aria-busy="true" @endif
        @if($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
    >
        @if($loading)
            {{-- Loading Spinner --}}
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        
        {{ $slot }}
    </button>
@endif
