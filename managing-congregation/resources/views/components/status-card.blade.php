@props([
    'variant' => 'peace',
    'icon' => null,
    'title' => '',
    'value' => '',
    'description' => null,
])

@php
$variantClasses = match($variant) {
    'peace' => 'status-card-peace',
    'attention' => 'status-card-attention',
    'pending' => 'status-card-pending',
    default => 'status-card-peace',
};

$iconColors = match($variant) {
    'peace' => 'text-emerald-600',
    'attention' => 'text-rose-600',
    'pending' => 'text-amber-600',
    default => 'text-emerald-600',
};
@endphp

<div {{ $attributes->merge(['class' => "status-card $variantClasses"]) }}>
    @if($icon)
        <div class="flex-shrink-0 {{ $iconColors }}">
            <div class="w-12 h-12 flex items-center justify-center text-3xl">
                {!! $icon !!}
            </div>
        </div>
    @endif
    
    <div class="flex-grow">
        <h3 class="text-lg font-heading font-semibold mb-2">{{ $title }}</h3>
        <p class="text-4xl font-bold mb-1">{{ $value }}</p>
        @if($description)
            <p class="text-sm text-slate-600">{{ $description }}</p>
        @endif
        @if($slot->isNotEmpty())
            <div class="mt-3">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
