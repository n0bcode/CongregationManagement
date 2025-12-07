@props(['disabled' => false])

@php
$hasError = false;
if ($attributes->has('name')) {
    $name = $attributes->get('name');
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $hasError = isset($errors) && $errors->has($errorKey);
}
@endphp

<select @disabled($disabled) {{ $attributes->merge([
    'class' => 'form-select shadow-sm',
    'aria-invalid' => $hasError ? 'true' : 'false',
    'aria-describedby' => $hasError && isset($name) ? "{$name}-error" : null,
]) }}>
    {{ $slot }}
</select>
