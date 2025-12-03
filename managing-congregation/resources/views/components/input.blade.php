@props([
    'label' => null,
    'name' => '',
    'type' => 'text',
    'value' => '',
    'error' => null,
    'required' => false,
    'help' => null,
])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-rose-600">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->except(['class', 'label', 'error', 'help'])->merge(['class' => 'form-input']) }}
        @if($required) required @endif
    />
    
    @if($help)
        <p class="text-sm text-slate-600 mt-1">{{ $help }}</p>
    @endif
    
    @if($error)
        <p class="form-error">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="form-error">{{ $errors->first($name) }}</p>
    @endif
</div>
