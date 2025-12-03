@props([
    'active' => false,
    'icon' => null,
])

@php
$classes = $active 
    ? 'nav-link nav-link-active' 
    : 'nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <span class="flex-shrink-0 w-6 h-6">
            {!! $icon !!}
        </span>
    @endif
    <span>{{ $slot }}</span>
</a>
