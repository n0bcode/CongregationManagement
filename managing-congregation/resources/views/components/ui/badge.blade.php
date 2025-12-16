@props([
    'variant' => 'neutral', // primary, success, warning, danger, neutral
    'size' => 'md', // sm, md
])

@php
    $baseClasses = 'inline-flex items-center font-medium rounded-full';

    $variants = [
        'primary' => 'bg-amber-100 text-amber-800',
        'success' => 'bg-emerald-100 text-emerald-800',
        'warning' => 'bg-amber-100 text-amber-800', // Intentional duplication if amber is primary
        'danger'  => 'bg-rose-100 text-rose-800',
        'neutral' => 'bg-stone-100 text-stone-800',
    ];

    $sizes = [
        'sm' => 'px-2.5 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['neutral']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
