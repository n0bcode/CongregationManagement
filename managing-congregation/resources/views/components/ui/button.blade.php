@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
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
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
