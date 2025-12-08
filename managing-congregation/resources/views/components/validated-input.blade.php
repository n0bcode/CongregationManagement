@props(['disabled' => false, 'label', 'name', 'type' => 'text', 'value' => '', 'rules' => '', 'id' => null])

<div x-data="{ 
    field: '{{ $name }}', 
    value: '{{ $value }}',
    rules: '{{ $rules }}',
    id: '{{ $id }}'
}" class="mb-4">
    <label for="{{ $name }}" class="block font-medium text-sm text-gray-700">
        {{ $label }}
        @if(str_contains($rules, 'required'))
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input 
        id="{{ $name }}" 
        name="{{ $name }}" 
        type="{{ $type }}" 
        x-model="value"
        @blur="validate(field, value, rules, id)"
        @input="hasUnsavedChanges = true"
        {{ $disabled ? 'disabled' : '' }} 
        {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1']) !!}
        :class="{ 'border-red-500': hasError(field) }"
    >

    <template x-if="hasError(field)">
        <p class="text-sm text-red-600 mt-1" x-text="getError(field)"></p>
    </template>

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
