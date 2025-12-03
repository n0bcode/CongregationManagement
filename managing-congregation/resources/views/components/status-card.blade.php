@props([
    'variant' => 'peace',
    'icon' => null,
    'title' => '',
    'value' => '',
    'description' => null,
])

@php
$variantClasses = [
    'peace' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
    'attention' => 'bg-rose-50 border-rose-200 text-rose-900',
    'pending' => 'bg-amber-50 border-amber-200 text-amber-900',
    'info' => 'bg-blue-50 border-blue-200 text-blue-900',
];

$iconColors = [
    'peace' => 'text-emerald-600',
    'attention' => 'text-rose-600',
    'pending' => 'text-amber-600',
    'info' => 'text-blue-600',
];

$classes = 'border-2 rounded-lg p-6 ' . ($variantClasses[$variant] ?? $variantClasses['peace']);
$iconColor = $iconColors[$variant] ?? $iconColors['peace'];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-start">
        @if($icon)
            <div class="mr-4 w-12 h-12 {{ $iconColor }} flex-shrink-0">
                {!! $icon !!}
            </div>
        @endif
        <div class="flex-grow">
            <h3 class="text-lg font-heading font-semibold mb-2">{{ $title }}</h3>
            <p class="text-4xl font-bold mb-1">{{ $value }}</p>
            @if($description)
                <p class="text-sm opacity-75 mt-2">{{ $description }}</p>
            @endif
            @if($slot->isNotEmpty())
                <div class="mt-4">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</div>
