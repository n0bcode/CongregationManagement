@props([
    'enabled' => true,
    'showIndicator' => true,
])

<div 
    x-data="unsavedChanges({ enabled: {{ $enabled ? 'true' : 'false' }} })"
    {{ $attributes->merge(['class' => 'form-with-unsaved-warning']) }}
>
    {{-- Unsaved Changes Indicator --}}
    @if($showIndicator)
    <div 
        x-show="hasChanges" 
        x-cloak
        x-transition
        class="fixed top-20 right-6 z-40 bg-amber-100 border border-amber-300 rounded-lg shadow-lg p-4 max-w-sm"
    >
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-amber-900">Unsaved Changes</p>
                <p class="text-xs text-amber-800 mt-1">Don't forget to save your work</p>
            </div>
            <button @click="hasChanges = false" class="text-amber-600 hover:text-amber-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    {{ $slot }}
</div>
