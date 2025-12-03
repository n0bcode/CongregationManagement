@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-stone-200 p-6']) }}>
    @if($title)
        <div class="mb-4 {{ $subtitle ? 'pb-4 border-b border-stone-200' : '' }}">
            <h3 class="text-lg font-heading font-semibold text-slate-800">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-sm text-slate-600 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    <div>
        {{ $slot }}
    </div>
</div>
