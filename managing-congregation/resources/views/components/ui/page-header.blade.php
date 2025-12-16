@props([
    'title' => null,
    'subtitle' => null,
    'backUrl' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        {{-- Title Section --}}
        <div class="flex items-center gap-4">
            @if($backUrl)
                <x-ui.button 
                    variant="ghost" 
                    href="{{ $backUrl }}" 
                    aria-label="{{ __('Go back') }}"
                    class="flex-shrink-0"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </x-ui.button>
            @endif
            
            <div>
                @if($title)
                    <h1 class="text-3xl font-bold text-slate-800">{{ $title }}</h1>
                @endif
                
                @if($subtitle)
                    <p class="text-base text-slate-600 mt-1">{{ $subtitle }}</p>
                @endif
                
                {{-- Allow custom title content via default slot if no title prop --}}
                @if(!$title && !$subtitle)
                    {{ $slot }}
                @endif
            </div>
        </div>
        
        {{-- Actions Section --}}
        @isset($actions)
            <div class="flex items-center gap-3">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
