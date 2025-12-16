@props([
    'variant' => 'default', // default, error, success, warning
    'id' => null, // For aria-describedby linking
])

@php
    $variantClasses = [
        'default' => 'text-slate-500',
        'error' => 'text-rose-600',
        'success' => 'text-emerald-600',
        'warning' => 'text-amber-600',
    ];
    
    $classes = 'text-sm mt-1 ' . ($variantClasses[$variant] ?? $variantClasses['default']);
@endphp

<p {{ $attributes->merge(['class' => $classes, 'id' => $id]) }}>
    {{ $slot }}
</p>
