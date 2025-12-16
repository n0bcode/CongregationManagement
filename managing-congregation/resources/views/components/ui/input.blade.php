@props([
    'disabled' => false,
    'error' => null,
])

@php
    $baseClasses = 'form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5';
    $errorClasses = 'border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-300 focus:shadow-outline-rose focus:ring-rose-500';
    $defaultClasses = 'border-stone-300 placeholder-stone-400 focus:border-amber-500 focus:ring-amber-500';
    
    // Combine classes based on error state
    $classes = $baseClasses . ' ' . ($error ? $errorClasses : $defaultClasses);
@endphp

<div class="relative">
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>

    @if($error)
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
    @endif
</div>

@if($error)
    <p class="mt-2 text-sm text-rose-600">{{ $error }}</p>
@endif
