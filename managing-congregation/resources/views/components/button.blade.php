@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'icon' => null,
])

@php
$classes = match($variant) {
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'success' => 'btn-success',
    'danger' => 'btn-danger',
    default => 'btn-primary',
};

$sizeClasses = match($size) {
    'sm' => 'min-h-[40px] px-4 py-2 text-sm',
    'md' => 'min-h-[48px] px-6 py-3',
    'lg' => 'min-h-[56px] px-8 py-4 text-lg',
    default => 'min-h-[48px] px-6 py-3',
};
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$classes $sizeClasses"]) }}>
        @if($icon)
            <span class="inline-block mr-2">{!! $icon !!}</span>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "$classes $sizeClasses"]) }}>
        @if($icon)
            <span class="inline-block mr-2">{!! $icon !!}</span>
        @endif
        {{ $slot }}
    </button>
@endif
