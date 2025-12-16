@props([
    'href' => '#',
    'variant' => 'primary', // primary, danger, neutral
    'ariaLabel' => null,
])

@php
    $variantClasses = [
        'primary' => 'text-amber-600 hover:text-amber-700',
        'danger' => 'text-rose-600 hover:text-rose-700',
        'neutral' => 'text-slate-600 hover:text-slate-700',
    ];
    
    $classes = 'inline-flex items-center font-medium transition-colors focus:outline-none focus:ring-4 focus:ring-offset-2 rounded ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
    
    $focusRingClass = [
        'primary' => 'focus:ring-amber-500',
        'danger' => 'focus:ring-rose-500',
        'neutral' => 'focus:ring-stone-300',
    ];
    
    $classes .= ' ' . ($focusRingClass[$variant] ?? $focusRingClass['primary']);
@endphp

<a 
    href="{{ $href }}" 
    {{ $attributes->merge(['class' => $classes]) }}
    @if($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
>
    @isset($icon)
        <span class="w-4 h-4 mr-1 flex-shrink-0">
            {{ $icon }}
        </span>
    @endisset
    
    {{ $slot }}
</a>
