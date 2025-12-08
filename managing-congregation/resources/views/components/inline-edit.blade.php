@props([
    'id' => null,
    'field' => null,
    'value' => '',
    'type' => 'text', // text, select, textarea
    'options' => [], // For select type
    'livewireMethod' => null, // e.g., 'updateMember'
    'endpoint' => null, // Alternative to livewireMethod
    'formatter' => null, // JS function name for formatting display value
    'placeholder' => '-',
    'canEdit' => true,
    'inputClass' => '',
    'displayClass' => '',
])

@php
$config = [
    'id' => $id,
    'field' => $field,
    'value' => $value,
    'livewireMethod' => $livewireMethod,
    'endpoint' => $endpoint,
    'formatter' => $formatter,
    'placeholder' => $placeholder,
    'canEdit' => $canEdit,
];
@endphp

<div 
    x-data="inlineEdit(@js($config))"
    {{ $attributes->merge(['class' => 'inline-edit-wrapper']) }}
>
    <!-- Display Mode -->
    <div 
        x-show="!editing" 
        @click="startEdit"
        :class="{ 'cursor-pointer hover:bg-stone-50': {{ $canEdit ? 'true' : 'false' }} }"
        class="inline-edit-display {{ $displayClass }} p-2 rounded-lg transition-colors duration-200 group"
    >
        <span x-text="displayValue" class="text-slate-800"></span>
        @if($canEdit)
            <svg class="inline-block w-4 h-4 ml-1 text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        @endif
    </div>

    <!-- Edit Mode -->
    <div x-show="editing" x-cloak class="inline-edit-form">
        @if($type === 'select')
            <!-- Select Input -->
            <div class="flex items-center gap-2">
                <select 
                    x-ref="select"
                    x-model="editValue"
                    @change="save"
                    @blur="cancel"
                    @keydown="handleKeydown"
                    class="form-select min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none {{ $inputClass }}"
                >
                    @foreach($options as $optValue => $optLabel)
                        <option value="{{ $optValue }}">{{ $optLabel }}</option>
                    @endforeach
                </select>
            </div>
        @elseif($type === 'textarea')
            <!-- Textarea Input -->
            <div class="flex flex-col gap-2">
                <textarea 
                    x-ref="input"
                    x-model="editValue"
                    @keydown="handleKeydown"
                    rows="3"
                    class="form-textarea min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none {{ $inputClass }}"
                ></textarea>
                <div class="flex gap-2">
                    <button 
                        @click="save" 
                        :disabled="saving || !hasChanged"
                        class="btn-sm btn-primary min-h-[48px] px-4"
                    >
                        <span x-show="!saving">Save</span>
                        <span x-show="saving">Saving...</span>
                    </button>
                    <button 
                        @click="cancel" 
                        :disabled="saving"
                        class="btn-sm btn-secondary min-h-[48px] px-4"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        @else
            <!-- Text Input -->
            <div class="flex items-center gap-2">
                <input 
                    x-ref="input"
                    type="{{ $type }}"
                    x-model="editValue"
                    @keydown="handleKeydown"
                    @blur.debounce.500ms="cancel"
                    class="form-input min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none {{ $inputClass }}"
                >
                <button 
                    @click="save" 
                    :disabled="saving || !hasChanged"
                    class="btn-sm btn-primary min-h-[48px] px-4"
                    title="Save (Enter)"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
                <button 
                    @click="cancel" 
                    :disabled="saving"
                    class="btn-sm btn-secondary min-h-[48px] px-4"
                    title="Cancel (Esc)"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Error Message -->
        <div x-show="error" x-cloak class="mt-2 text-sm text-rose-600" x-text="error"></div>
    </div>
</div>
