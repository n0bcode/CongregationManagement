@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
    'helpText' => null,
    'showSuccess' => true,
])

@php
$hasError = $errors->has($name);
$value = old($name);
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
    <label for="{{ $name }}" class="block text-lg font-medium text-slate-700 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-rose-600">*</span>
        @endif
    </label>
    @endif

    <div class="relative">
        <input 
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            wire:model.live="{{ $name }}"
            @if($required) required @endif
            placeholder="{{ $placeholder }}"
            {{ $attributes->except(['class', 'label', 'helpText']) }}
            class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none transition-colors
                {{ $hasError ? 'border-rose-500 pr-12' : 'border-stone-300' }}
                {{ $showSuccess && !$hasError && $value ? 'border-emerald-500 pr-12' : '' }}"
        >

        {{-- Success Icon --}}
        @if($showSuccess && !$hasError && $value)
        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        @endif

        {{-- Error Icon --}}
        @if($hasError)
        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        @endif
    </div>

    {{-- Help Text --}}
    @if($helpText && !$hasError)
    <p class="mt-2 text-sm text-slate-600">{{ $helpText }}</p>
    @endif

    {{-- Error Message --}}
    @error($name)
    <p class="mt-2 text-sm text-rose-600 flex items-start gap-2">
        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>{{ $message }}</span>
    </p>
    @enderror
</div>
