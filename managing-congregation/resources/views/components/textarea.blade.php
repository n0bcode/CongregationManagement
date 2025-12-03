@props([
    'label' => null,
    'name' => '',
    'value' => '',
    'error' => null,
    'required' => false,
    'rows' => 4,
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
    
    <textarea 
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->except(['class', 'label', 'error'])->merge(['class' => 'form-textarea']) }}
        @if($required) required @endif
    >{{ old($name, $value) }}</textarea>
    
    @if($error)
        <p class="form-error">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="form-error">{{ $errors->first($name) }}</p>
    @endif
</div>
