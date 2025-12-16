@props([
    'disabled' => false,
    'error' => null,
    'options' => [], // Expects array of value => label or simple array
    'placeholder' => null,
])

@php
    $baseClasses = 'form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5';
    $errorClasses = 'border-rose-300 text-rose-900 focus:border-rose-300 focus:shadow-outline-rose focus:ring-rose-500';
    $defaultClasses = 'border-stone-300 focus:border-amber-500 focus:ring-amber-500';

    $classes = $baseClasses . ' ' . ($error ? $errorClasses : $defaultClasses);
@endphp

<div class="relative">
    <select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>
        @if($placeholder)
            <option value="" disabled selected>{{ $placeholder }}</option>
        @endif
        
        @if(!empty($options))
             @foreach($options as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
             @endforeach
        @endif
        
        {{ $slot }}
    </select>
</div>

@if($error)
    <p class="mt-2 text-sm text-rose-600">{{ $error }}</p>
@endif
