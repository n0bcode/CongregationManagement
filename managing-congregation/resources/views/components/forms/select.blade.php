@props([
    'label' => null,
    'name' => '',
    'value' => '',
    'options' => [],
    'placeholder' => null,
    'error' => null,
    'required' => false,
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
    
    <select 
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->except(['class', 'label', 'error', 'options'])->merge(['class' => 'form-select']) }}
        @if($required) required @endif
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    
    @if($error)
        <p class="form-error">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="form-error">{{ $errors->first($name) }}</p>
    @endif
</div>
