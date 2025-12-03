@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title)
        <div class="card-header">
            <h3 class="text-xl font-heading font-semibold text-slate-800">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-sm text-slate-600 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    <div class="{{ $padding ? '' : '-m-6' }}">
        {{ $slot }}
    </div>
</div>
