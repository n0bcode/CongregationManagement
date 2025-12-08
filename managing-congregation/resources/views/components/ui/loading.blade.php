@props([
    'text' => 'Loading...',
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-center py-8']) }}>
    <div class="text-center">
        <div class="loading-spinner mx-auto mb-3"></div>
        <p class="text-sm text-slate-600">{{ $text }}</p>
    </div>
</div>
