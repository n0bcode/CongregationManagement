@props([
    'variant' => 'info', // info, success, warning, error
    'title' => null,
])

@php
    $baseClasses = 'rounded-md p-4';
    
    $variants = [
        'info' => 'bg-blue-50 border border-blue-200',
        'success' => 'bg-emerald-50 border border-emerald-200',
        'warning' => 'bg-amber-50 border border-amber-200',
        'error' => 'bg-rose-50 border border-rose-200',
    ];

    $textColors = [
        'info' => 'text-blue-700',
        'success' => 'text-emerald-700',
        'warning' => 'text-amber-700',
        'error' => 'text-rose-700',
    ];
    
    $titleColors = [
        'info' => 'text-blue-800',
        'success' => 'text-emerald-800',
        'warning' => 'text-amber-800',
        'error' => 'text-rose-800',
    ];

    $iconColors = [
        'info' => 'text-blue-400',
        'success' => 'text-emerald-400',
        'warning' => 'text-amber-400',
        'error' => 'text-rose-400',
    ];

    $wrapperClass = $variants[$variant] ?? $variants['info'];
    $textClass = $textColors[$variant] ?? $textColors['info'];
    $titleClass = $titleColors[$variant] ?? $titleColors['info'];
    $iconClass = $iconColors[$variant] ?? $iconColors['info'];
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass]) }}>
    <div class="flex">
        <div class="flex-shrink-0">
             @if($variant === 'success')
                <svg class="h-5 w-5 {{ $iconClass }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            @elseif($variant === 'warning')
                <svg class="h-5 w-5 {{ $iconClass }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            @elseif($variant === 'error')
                <svg class="h-5 w-5 {{ $iconClass }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            @else
                <svg class="h-5 w-5 {{ $iconClass }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            @endif
        </div>
        <div class="ml-3">
            @if($title)
                <h3 class="text-sm font-medium {{ $titleClass }}">{{ $title }}</h3>
            @endif
            <div class="mt-2 text-sm {{ $textClass }}">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
