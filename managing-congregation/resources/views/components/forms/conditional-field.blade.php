@props([
    'show' => true,
    'condition' => null, // Alpine.js expression
    'animate' => true,
])

<div 
    @if($condition)
        x-show="{{ $condition }}"
        @if($animate)
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
        @endif
        x-cloak
    @elseif(!$show)
        style="display: none;"
    @endif
    {{ $attributes->merge(['class' => 'conditional-field']) }}
>
    {{ $slot }}
</div>
