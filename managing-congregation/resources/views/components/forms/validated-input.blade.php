@props(['disabled' => false, 'label', 'name', 'type' => 'text', 'value' => '', 'rules' => '', 'id' => null])

<div x-data="{ 
    field: '{{ $name }}', 
    value: '{{ $value }}',
    rules: '{{ $rules }}',
    id: '{{ $id }}'
}" class="mb-4">
    <x-ui.label 
        for="{{ $name }}" 
        :value="$label" 
        :required="str_contains($rules, 'required')" 
    />

    <x-ui.input 
        id="{{ $name }}" 
        name="{{ $name }}" 
        type="{{ $type }}" 
        x-model="value"
        @blur="validate(field, value, rules, id)"
        @input="hasUnsavedChanges = true"
        :disabled="$disabled"
        :error="$errors->first($name)"
        {{ $attributes->merge(['class' => 'mt-1 block w-full']) }}
        x-bind:class="{ 'border-rose-300 focus:border-rose-300 focus:ring-rose-500': hasError(field) }"
    />

    <template x-if="hasError(field)">
        <p class="text-sm text-rose-600 mt-1" x-text="getError(field)"></p>
    </template>
</div>
