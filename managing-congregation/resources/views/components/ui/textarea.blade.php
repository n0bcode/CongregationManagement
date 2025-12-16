@props([
    'disabled' => false,
    'error' => null,
])

@php
    $baseClasses = 'form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5';
    $errorClasses = 'border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-300 focus:shadow-outline-rose focus:ring-rose-500';
    $defaultClasses = 'border-stone-300 placeholder-stone-400 focus:border-amber-500 focus:ring-amber-500';

    $classes = $baseClasses . ' ' . ($error ? $errorClasses : $defaultClasses);
@endphp

<div class="relative">
    <textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>{{ $slot }}</textarea>
</div>

@if($error)
    <p class="mt-2 text-sm text-rose-600">{{ $error }}</p>
@endif
